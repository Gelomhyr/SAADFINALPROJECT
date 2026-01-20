<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('receptionist');
require_once __DIR__ . '/../includes/storage.php';

$active = 'rx_requests';
$page_title = 'Requests';
$header_title = 'Appointment Requests';
$subtitle = 'Confirm, reschedule, or cancel patient requests.';

$requests = read_json('appointment_requests.json', []);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'] ?? '';
  $action = $_POST['action'] ?? '';
  $finalDate = $_POST['finalDate'] ?? '';
  $finalTime = $_POST['finalTime'] ?? '';
  $cancelReason = trim($_POST['cancelReason'] ?? '');

  foreach ($requests as &$r) {
    if (($r['id'] ?? '') !== $id) continue;

    if (in_array($action, ['CONFIRM','RESCHEDULE'], true)) {
      if (!$finalDate || !$finalTime) continue;

      $r['status'] = ($action === 'CONFIRM') ? 'CONFIRMED' : 'RESCHEDULED';
      $r['finalDate'] = $finalDate;
      $r['finalTime'] = $finalTime;
      $r['updatedAt'] = now_iso();

      // notifications
      $name = $r['fullName'] ?? 'Patient';
      $proc = $r['procedure'] ?? 'Procedure';
      $msg = "{$name} appointment {$r['status']}: {$finalDate} {$finalTime} ({$proc}).";

      $notifications = read_json('notifications.json', []);
      $notifications[] = ['id'=>uuid(),'audience'=>'owner','type'=>'in_app','message'=>$msg,'createdAt'=>now_iso(),'isRead'=>false];
      $notifications[] = ['id'=>uuid(),'audience'=>'receptionist','type'=>'in_app','message'=>$msg,'createdAt'=>now_iso(),'isRead'=>false];
      $notifications[] = ['id'=>uuid(),'audience'=>'patient','type'=>'in_app','message'=>"Your appointment is {$r['status']} on {$finalDate} {$finalTime}.",'createdAt'=>now_iso(),'isRead'=>false];
      write_json('notifications.json', $notifications);

      // message logs (simulated)
      $logs = read_json('message_logs.json', []);
      $mobile = $r['mobile'] ?? '';
      $email = $r['email'] ?? '';
      $logs[] = ['id'=>uuid(),'channel'=>'sms','to'=>$mobile ?: 'PATIENT_MOBILE','message'=>"{$msg}",'createdAt'=>now_iso(),'relatedRequestId'=>$id,'status'=>'sent'];
      if ($email) {
        $logs[] = ['id'=>uuid(),'channel'=>'email','to'=>$email,'message'=>"{$msg}",'createdAt'=>now_iso(),'relatedRequestId'=>$id,'status'=>'sent'];
      }
      write_json('message_logs.json', $logs);

      // schedule reminders only for confirmed
      if ($r['status'] === 'CONFIRMED') {
        $rem = read_json('reminder_logs.json', []);
        $dt = strtotime($finalDate . ' ' . $finalTime);
        if ($dt) {
          $rem[] = ['id'=>uuid(),'relatedRequestId'=>$id,'schedule'=>'24h','scheduledFor'=>date('c',$dt-24*3600),'channel'=>'sms','createdAt'=>now_iso(),'status'=>'scheduled'];
          $rem[] = ['id'=>uuid(),'relatedRequestId'=>$id,'schedule'=>'2h','scheduledFor'=>date('c',$dt-2*3600),'channel'=>'sms','createdAt'=>now_iso(),'status'=>'scheduled'];
          write_json('reminder_logs.json', $rem);
        }
      }

      // =====================
      // Persist to Patients + Appointments (JSON)
      // =====================
      $patients = read_json('patients.json', []);
      $appointments = read_json('appointments.json', []);

      // Try to find an existing patient by mobile or email
      $existingPatientId = '';
      foreach ($patients as $p) {
        $pm = (string)($p['mobile'] ?? '');
        $pe = (string)($p['email'] ?? '');
        if (($mobile && $pm && $pm === $mobile) || ($email && $pe && $pe === $email)) {
          $existingPatientId = (string)($p['id'] ?? '');
          break;
        }
      }

      if (!$existingPatientId) {
        $existingPatientId = uuid();
        $patients[] = [
          'id' => $existingPatientId,
          'fullName' => (string)($r['fullName'] ?? ''),
          'mobile' => (string)($r['mobile'] ?? ''),
          'email' => (string)($r['email'] ?? ''),
          'address' => '',
          'createdAt' => now_iso(),
        ];
      }

      $r['patientId'] = $existingPatientId;
      write_json('patients.json', $patients);

      // Upsert appointment by requestId
      $found = false;
      foreach ($appointments as &$a) {
        if ((string)($a['requestId'] ?? '') !== $id) continue;
        $a['patientId'] = $existingPatientId;
        $a['patientName'] = (string)($r['fullName'] ?? '');
        $a['date'] = $finalDate;
        $a['time'] = $finalTime;
        $a['reason'] = (string)($r['procedure'] ?? ($r['notes'] ?? ''));
        $a['status'] = $r['status'];
        $a['updatedAt'] = now_iso();
        $found = true;
        break;
      }
      unset($a);

      if (!$found) {
        $appointments[] = [
          'id' => uuid(),
          'requestId' => $id,
          'patientId' => $existingPatientId,
          'patientName' => (string)($r['fullName'] ?? ''),
          'date' => $finalDate,
          'time' => $finalTime,
          'reason' => (string)($r['procedure'] ?? ($r['notes'] ?? '')),
          'status' => $r['status'],
          'createdAt' => now_iso(),
          'updatedAt' => now_iso(),
        ];
      }
      write_json('appointments.json', $appointments);

    } elseif ($action === 'CANCEL') {
      $r['status'] = 'CANCELLED';
      $r['cancelReason'] = $cancelReason ?: 'Cancelled by clinic.';
      $r['updatedAt'] = now_iso();

      $name = $r['fullName'] ?? 'Patient';
      $msg = "Appointment CANCELLED: {$name}.";

      $notifications = read_json('notifications.json', []);
      $notifications[] = ['id'=>uuid(),'audience'=>'owner','type'=>'in_app','message'=>$msg,'createdAt'=>now_iso(),'isRead'=>false];
      $notifications[] = ['id'=>uuid(),'audience'=>'patient','type'=>'in_app','message'=>"Your appointment request was cancelled. Reason: {$r['cancelReason']}",'createdAt'=>now_iso(),'isRead'=>false];
      write_json('notifications.json', $notifications);

      $logs = read_json('message_logs.json', []);
      $mobile = $r['mobile'] ?? '';
      $logs[] = ['id'=>uuid(),'channel'=>'sms','to'=>$mobile ?: 'PATIENT_MOBILE','message'=>"{$msg} Reason: {$r['cancelReason']}",'createdAt'=>now_iso(),'relatedRequestId'=>$id,'status'=>'sent'];
      write_json('message_logs.json', $logs);

      // Mark any related appointment as cancelled
      $appointments = read_json('appointments.json', []);
      foreach ($appointments as &$a) {
        if ((string)($a['requestId'] ?? '') !== $id) continue;
        $a['status'] = 'CANCELLED';
        $a['updatedAt'] = now_iso();
        break;
      }
      unset($a);
      write_json('appointments.json', $appointments);
    }

    break;
  }
  unset($r);

  write_json('appointment_requests.json', $requests);
  header('Location: ' . url_path('receptionist/requests.php'));
  exit;
}

// sort newest
usort($requests, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

require_once __DIR__ . '/../includes/portal_header.php';
?>
<div class="bg-white border rounded-2xl overflow-hidden">
  <div class="p-5">
    <div class="text-sm text-slate-500">Tip: Confirm adds reminders (24h & 2h) in Reminder Logs.</div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Patient</th>
          <th class="text-left px-5 py-3">Preferred</th>
          <th class="text-left px-5 py-3">Procedure</th>
          <th class="text-left px-5 py-3">Status</th>
          <th class="text-left px-5 py-3">Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($requests)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No requests yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($requests as $r): $id = $r['id'] ?? ''; $st = $r['status'] ?? 'PENDING'; ?>
          <tr class="border-t align-top">
            <td class="px-5 py-3 font-medium">
              <?= htmlspecialchars($r['fullName'] ?? '-') ?><br>
              <span class="text-xs text-slate-500"><?= htmlspecialchars($r['mobile'] ?? '') ?></span>
            </td>
            <td class="px-5 py-3"><?= htmlspecialchars(($r['preferredDate'] ?? '') . ' ' . ($r['preferredTime'] ?? '')) ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($r['procedure'] ?? '-') ?></td>
            <td class="px-5 py-3">
              <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $st==='PENDING'?'bg-amber-50 text-amber-700':($st==='CONFIRMED'?'bg-emerald-50 text-emerald-700':($st==='CANCELLED'?'bg-red-50 text-red-700':'bg-slate-100 text-slate-700')) ?>">
                <?= htmlspecialchars($st) ?>
              </span>
              <?php if (!empty($r['finalDate']) && !empty($r['finalTime'])): ?>
                <div class="text-xs text-slate-500 mt-1">Final: <?= htmlspecialchars($r['finalDate'].' '.$r['finalTime']) ?></div>
              <?php endif; ?>
            </td>
            <td class="px-5 py-3">
              <form method="POST" class="space-y-2">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <div class="grid grid-cols-2 gap-2">
                  <input type="date" name="finalDate" class="border rounded-lg px-2 py-1" value="<?= htmlspecialchars($r['finalDate'] ?? '') ?>">
                  <input type="time" name="finalTime" class="border rounded-lg px-2 py-1" value="<?= htmlspecialchars($r['finalTime'] ?? '') ?>">
                </div>
                <div class="flex flex-wrap gap-2">
                  <button name="action" value="CONFIRM" class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold">Confirm</button>
                  <button name="action" value="RESCHEDULE" class="px-3 py-1.5 rounded-lg bg-slate-800 text-white text-xs font-semibold">Reschedule</button>
                </div>
                <details>
                  <summary class="cursor-pointer text-xs text-red-700">Cancel</summary>
                  <textarea name="cancelReason" class="w-full border rounded-lg p-2 mt-2 text-xs" rows="2" placeholder="Reason (optional)"></textarea>
                  <button name="action" value="CANCEL" class="mt-2 px-3 py-1.5 rounded-lg bg-red-600 text-white text-xs font-semibold">Confirm Cancel</button>
                </details>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
