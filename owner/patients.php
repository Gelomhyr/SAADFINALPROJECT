<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_patients';
$page_title = 'Patients';
$header_title = 'Patients';
$subtitle = 'Read-only patient list derived from requests.';

$requests = read_json('appointment_requests.json', []);
$map = [];
foreach ($requests as $r) {
  $key = trim(($r['mobile'] ?? '') . '|' . ($r['fullName'] ?? ''));
  if ($key === '|') continue;
  if (!isset($map[$key])) {
    $map[$key] = [
      'name' => $r['fullName'] ?? '',
      'mobile' => $r['mobile'] ?? '',
      'email' => $r['email'] ?? '',
      'last' => '',
      'count' => 0,
    ];
  }
  $map[$key]['count']++;
  $date = $r['finalDate'] ?? $r['preferredDate'] ?? '';
  if ($date && $date > ($map[$key]['last'] ?? '')) $map[$key]['last'] = $date;
}
$patients = array_values($map);
usort($patients, fn($a,$b) => strcmp(($b['last'] ?? ''), ($a['last'] ?? '')));

$q = trim($_GET['q'] ?? '');
if ($q !== '') {
  $patients = array_values(array_filter($patients, function($p) use ($q) {
    $hay = strtolower(($p['name'] ?? '').' '.($p['mobile'] ?? '').' '.($p['email'] ?? ''));
    return str_contains($hay, strtolower($q));
  }));
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="bg-white border rounded-2xl overflow-hidden">
  <div class="p-5 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <div class="text-lg font-semibold">Patient Records</div>
      <div class="text-sm text-slate-500">This demo version builds patient list from submitted requests.</div>
    </div>
    <form method="get" class="flex items-center gap-2">
      <input name="q" value="<?= htmlspecialchars($q) ?>" placeholder="Search name/mobile" class="px-3 py-2 rounded-lg border w-64" />
      <button class="px-3 py-2 rounded-lg bg-slate-900 text-white">Search</button>
    </form>
  </div>
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Name</th>
          <th class="text-left px-5 py-3">Mobile</th>
          <th class="text-left px-5 py-3">Email</th>
          <th class="text-left px-5 py-3">Last Appointment</th>
          <th class="text-left px-5 py-3">Total Requests</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($patients)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No patient records yet.</td></tr>
        <?php endif; ?>
        <?php foreach ($patients as $p): ?>
          <tr class="border-t">
            <td class="px-5 py-3 font-medium"><?= htmlspecialchars($p['name'] ?: '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($p['mobile'] ?: '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($p['email'] ?: '-') ?></td>
            <td class="px-5 py-3"><?= htmlspecialchars($p['last'] ?: '-') ?></td>
            <td class="px-5 py-3"><?= (int)($p['count'] ?? 0) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
