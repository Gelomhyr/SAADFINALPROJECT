<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_requests';
$page_title = 'Requests';
$header_title = 'Appointment Requests';
$subtitle = 'Read-only view of all patient submissions.';

$requests = read_json('appointment_requests.json', []);
usort($requests, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

$logs = read_json('message_logs.json', []);
$rem = read_json('reminder_logs.json', []);

require_once __DIR__ . '/../includes/portal_header.php';
?>
<div class="grid md:grid-cols-3 gap-4">
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Message Logs</div>
    <div class="text-3xl font-bold mt-1"><?= count($logs) ?></div>
    <div class="text-xs text-slate-500 mt-1">Simulated SMS/Email sent</div>
  </div>
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Reminder Logs</div>
    <div class="text-3xl font-bold mt-1"><?= count($rem) ?></div>
    <div class="text-xs text-slate-500 mt-1">Scheduled reminders (24h/2h)</div>
  </div>
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Tip</div>
    <div class="text-sm mt-2">To generate reminders: Receptionist must CONFIRM a request with final date/time.</div>
  </div>
</div>

<div class="mt-6 bg-white border rounded-2xl overflow-hidden">
  <div class="p-5 flex items-center justify-between">
    <div>
      <div class="text-lg font-semibold">All Requests</div>
      <div class="text-sm text-slate-500">View patient details and status updates</div>
    </div>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Patient</th>
          <th class="text-left px-5 py-3">Preferred</th>
          <th class="text-left px-5 py-3">Final Schedule</th>
          <th class="text-left px-5 py-3">Procedure</th>
          <th class="text-left px-5 py-3">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($requests)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No requests yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($requests as $r): $st = $r['status'] ?? 'PENDING'; ?>
          <tr class="border-t">
            <td class="px-5 py-3 font-medium">
              <?= htmlspecialchars($r['fullName'] ?? '-') ?><br>
              <span class="text-xs text-slate-500"><?= htmlspecialchars($r['mobile'] ?? '') ?></span>
            </td>
            <td class="px-5 py-3"><?= htmlspecialchars(($r['preferredDate'] ?? '') . ' ' . ($r['preferredTime'] ?? '')) ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars(($r['finalDate'] ?? '-') . ' ' . ($r['finalTime'] ?? '')) ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($r['procedure'] ?? '-') ?></td>
            <td class="px-5 py-3">
              <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $st==='PENDING'?'bg-amber-50 text-amber-700':($st==='CONFIRMED'?'bg-emerald-50 text-emerald-700':($st==='CANCELLED'?'bg-red-50 text-red-700':'bg-slate-100 text-slate-700')) ?>">
                <?= htmlspecialchars($st) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="grid lg:grid-cols-2 gap-4 mt-6">
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-lg font-semibold">Message Logs (Latest 8)</div>
    <div class="text-sm text-slate-500 mb-3">Shows simulated SMS/Email notifications</div>
    <?php
      usort($logs, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));
      $logs8 = array_slice($logs, 0, 8);
    ?>
    <div class="space-y-2">
      <?php if (!count($logs8)): ?>
        <div class="text-sm text-slate-500">No logs yet.</div>
      <?php endif; ?>
      <?php foreach ($logs8 as $l): ?>
        <div class="border rounded-xl p-3">
          <div class="text-xs text-slate-500 uppercase"><?= htmlspecialchars($l['channel'] ?? '') ?></div>
          <div class="text-sm font-medium"><?= htmlspecialchars($l['to'] ?? '') ?></div>
          <div class="text-sm text-slate-700 mt-1"><?= htmlspecialchars($l['message'] ?? '') ?></div>
          <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($l['createdAt'] ?? '') ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="bg-white border rounded-2xl p-5">
    <div class="text-lg font-semibold">Reminder Logs (Latest 8)</div>
    <div class="text-sm text-slate-500 mb-3">Scheduled reminders (24h / 2h)</div>
    <?php
      usort($rem, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));
      $rem8 = array_slice($rem, 0, 8);
    ?>
    <div class="space-y-2">
      <?php if (!count($rem8)): ?>
        <div class="text-sm text-slate-500">No reminders yet.</div>
      <?php endif; ?>
      <?php foreach ($rem8 as $rr): ?>
        <div class="border rounded-xl p-3">
          <div class="text-xs text-slate-500">Schedule: <span class="font-semibold"><?= htmlspecialchars($rr['schedule'] ?? '') ?></span> • Channel: <?= htmlspecialchars($rr['channel'] ?? '') ?></div>
          <div class="text-sm mt-1">Scheduled For: <span class="font-medium"><?= htmlspecialchars($rr['scheduledFor'] ?? '') ?></span></div>
          <div class="text-xs text-slate-500 mt-1">Status: <?= htmlspecialchars($rr['status'] ?? '') ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
