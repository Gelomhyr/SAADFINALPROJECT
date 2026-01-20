<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_appointments';
$page_title = 'Appointments';
$header_title = 'Appointments';
$subtitle = 'Read-only schedule view (confirmed/rescheduled).';

$requests = read_json('appointment_requests.json', []);
$appointments = array_values(array_filter($requests, fn($r) => in_array(($r['status'] ?? ''), ['CONFIRMED','RESCHEDULED'], true)));
usort($appointments, function($a,$b){
  $ad = ($a['finalDate'] ?? $a['preferredDate'] ?? '');
  $bd = ($b['finalDate'] ?? $b['preferredDate'] ?? '');
  return strcmp($bd.' '.$b['finalTime'], $ad.' '.$a['finalTime']);
});

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="bg-white border rounded-2xl overflow-hidden">
  <div class="p-5 flex items-center justify-between">
    <div>
      <div class="text-lg font-semibold">Appointment List</div>
      <div class="text-sm text-slate-500">Generated from confirmed/rescheduled requests</div>
    </div>
    <a href="<?= url_path('owner/dashboard.php') ?>" class="text-sm px-3 py-2 rounded-lg border hover:bg-slate-50">Back to Dashboard</a>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Date</th>
          <th class="text-left px-5 py-3">Time</th>
          <th class="text-left px-5 py-3">Patient</th>
          <th class="text-left px-5 py-3">Procedure</th>
          <th class="text-left px-5 py-3">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($appointments)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No confirmed appointments yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($appointments as $r): ?>
          <?php
            $date = $r['finalDate'] ?? $r['preferredDate'] ?? '';
            $time = $r['finalTime'] ?? $r['preferredTime'] ?? '';
            $st = $r['status'] ?? 'CONFIRMED';
          ?>
          <tr class="border-t">
            <td class="px-5 py-3 font-medium"><?= htmlspecialchars($date ?: '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($time ?: '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($r['fullName'] ?? '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($r['procedure'] ?? '-') ?></td>
            <td class="px-5 py-3">
              <span class="px-2 py-1 rounded-full text-xs font-semibold <?= $st==='CONFIRMED'?'bg-emerald-50 text-emerald-700':'bg-indigo-50 text-indigo-700' ?>">
                <?= htmlspecialchars($st) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
