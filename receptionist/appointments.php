<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('receptionist');
require_once __DIR__ . '/../includes/storage.php';

$active = 'rx_appts';
$page_title = 'Appointments';
$header_title = 'Appointments';
$subtitle = 'All confirmed/rescheduled appointments.';

$appointments = read_json('appointments.json', []);
$patients = read_json('patients.json', []);

$patientMap = [];
foreach ($patients as $p) {
  $patientMap[$p['id'] ?? ''] = $p;
}

// Filters
$status = strtoupper(trim($_GET['status'] ?? ''));
$date = trim($_GET['date'] ?? '');
$q = trim($_GET['q'] ?? '');
$pid = trim($_GET['pid'] ?? '');

$filtered = array_values(array_filter($appointments, function($a) use ($status,$date,$q,$pid,$patientMap) {
  if ($status && strtoupper((string)($a['status'] ?? '')) !== $status) return false;
  if ($date && (string)($a['date'] ?? '') !== $date) return false;
  if ($pid && (string)($a['patientId'] ?? '') !== $pid) return false;
  if ($q) {
    $qq = mb_strtolower($q);
    $p = $patientMap[$a['patientId'] ?? ''] ?? null;
    $name = mb_strtolower((string)($p['fullName'] ?? ''));
    $mobile = mb_strtolower((string)($p['mobile'] ?? ''));
    $reason = mb_strtolower((string)($a['reason'] ?? ''));
    if (!str_contains($name, $qq) && !str_contains($mobile, $qq) && !str_contains($reason, $qq)) return false;
  }
  return true;
}));

// Sort by date/time
usort($filtered, function($a,$b){
  $ad = ($a['date'] ?? '') . ' ' . ($a['time'] ?? '');
  $bd = ($b['date'] ?? '') . ' ' . ($b['time'] ?? '');
  return strcmp($bd, $ad);
});

function badge_classes(string $status): string {
  $s = strtoupper($status);
  return match($s) {
    'CONFIRMED' => 'bg-emerald-100 text-emerald-800',
    'RESCHEDULED' => 'bg-amber-100 text-amber-800',
    'CANCELLED' => 'bg-rose-100 text-rose-800',
    default => 'bg-slate-100 text-slate-700',
  };
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="bg-white border rounded-2xl overflow-hidden">
  <div class="p-5 border-b">
    <form method="GET" class="grid md:grid-cols-4 gap-3">
      <input name="q" value="<?= htmlspecialchars($q) ?>" class="border rounded-xl px-3 py-2" placeholder="Search name, mobile, reason">
      <input type="date" name="date" value="<?= htmlspecialchars($date) ?>" class="border rounded-xl px-3 py-2">
      <select name="status" class="border rounded-xl px-3 py-2">
        <option value="">All Status</option>
        <?php foreach (['CONFIRMED','RESCHEDULED','CANCELLED'] as $opt): ?>
          <option value="<?= $opt ?>" <?= $status===$opt?'selected':'' ?>><?= $opt ?></option>
        <?php endforeach; ?>
      </select>
      <div class="flex gap-2">
        <button class="flex-1 px-4 py-2 rounded-xl bg-slate-900 text-white">Filter</button>
        <a class="px-4 py-2 rounded-xl border" href="appointments.php">Reset</a>
      </div>
      <?php if ($pid): ?><input type="hidden" name="pid" value="<?= htmlspecialchars($pid) ?>"><?php endif; ?>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Date</th>
          <th class="text-left px-5 py-3">Time</th>
          <th class="text-left px-5 py-3">Patient</th>
          <th class="text-left px-5 py-3">Reason</th>
          <th class="text-left px-5 py-3">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($filtered)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No appointments found. Confirm requests in <a class="text-blue-700 underline" href="requests.php">Requests</a>.</td></tr>
        <?php endif; ?>
        <?php foreach ($filtered as $a):
          $p = $patientMap[$a['patientId'] ?? ''] ?? null;
          $name = $p ? ($p['fullName'] ?? '-') : ($a['patientName'] ?? '-');
        ?>
          <tr class="border-t">
            <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($a['date'] ?? '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($a['time'] ?? '-') ?></td>
            <td class="px-5 py-3">
              <div class="font-semibold text-slate-800"><?= htmlspecialchars($name) ?></div>
              <?php if ($p && !empty($p['mobile'])): ?><div class="text-xs text-slate-500"><?= htmlspecialchars($p['mobile']) ?></div><?php endif; ?>
            </td>
            <td class="px-5 py-3"><?= htmlspecialchars($a['reason'] ?? '-') ?></td>
            <td class="px-5 py-3">
              <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-semibold <?= badge_classes((string)($a['status'] ?? '')) ?>">
                <?= htmlspecialchars(strtoupper((string)($a['status'] ?? ''))) ?>
              </span>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
