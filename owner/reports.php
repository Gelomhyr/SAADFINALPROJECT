<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_reports';
$page_title = 'Reports';
$header_title = 'Reports';
$subtitle = 'Simple analytics + CSV exports (demo).';

$requests = read_json('appointment_requests.json', []);

$today = date('Y-m-d');
$week_start = date('Y-m-d', strtotime('monday this week'));
$month_start = date('Y-m-01');

$week_total = 0;
$month_total = 0;
$by_day = [];
$by_reason = [];
foreach ($requests as $r) {
  $date = $r['finalDate'] ?? $r['preferredDate'] ?? '';
  if (!$date) continue;
  if ($date >= $week_start) $week_total++;
  if ($date >= $month_start) $month_total++;
  $by_day[$date] = ($by_day[$date] ?? 0) + 1;
  $reason = $r['procedure'] ?? 'Unknown';
  $by_reason[$reason] = ($by_reason[$reason] ?? 0) + 1;
}

arsort($by_day);
$peak_day = array_key_first($by_day) ?? '-';
arsort($by_reason);
$top_reason = array_key_first($by_reason) ?? '-';

function csv_escape($v) {
  $v = (string)$v;
  if (strpbrk($v, ",\n\r") !== false) {
    $v = '"' . str_replace('"', '""', $v) . '"';
  }
  return $v;
}

// CSV download endpoints
if (isset($_GET['download']) && $_GET['download'] === 'requests') {
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="appointment_requests.csv"');
  $out = fopen('php://output', 'w');
  fputcsv($out, ['id','fullName','mobile','email','preferredDate','preferredTime','finalDate','finalTime','procedure','status','createdAt']);
  foreach ($requests as $r) {
    fputcsv($out, [
      $r['id'] ?? '',
      $r['fullName'] ?? '',
      $r['mobile'] ?? '',
      $r['email'] ?? '',
      $r['preferredDate'] ?? '',
      $r['preferredTime'] ?? '',
      $r['finalDate'] ?? '',
      $r['finalTime'] ?? '',
      $r['procedure'] ?? '',
      $r['status'] ?? '',
      $r['createdAt'] ?? '',
    ]);
  }
  fclose($out);
  exit;
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="grid gap-4 md:grid-cols-4">
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Weekly Total</div>
    <div class="text-3xl font-extrabold mt-1"><?= (int)$week_total ?></div>
    <div class="text-xs text-slate-400 mt-1">From <?= htmlspecialchars($week_start) ?> to today</div>
  </div>
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Monthly Total</div>
    <div class="text-3xl font-extrabold mt-1"><?= (int)$month_total ?></div>
    <div class="text-xs text-slate-400 mt-1">Since <?= htmlspecialchars($month_start) ?></div>
  </div>
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Peak Day</div>
    <div class="text-xl font-bold mt-2"><?= htmlspecialchars($peak_day) ?></div>
    <div class="text-xs text-slate-400 mt-1">Highest request volume</div>
  </div>
  <div class="bg-white border rounded-2xl p-5">
    <div class="text-sm text-slate-500">Most Common Procedure</div>
    <div class="text-xl font-bold mt-2"><?= htmlspecialchars($top_reason) ?></div>
    <div class="text-xs text-slate-400 mt-1">Top reason/procedure</div>
  </div>
</div>

<div class="bg-white border rounded-2xl mt-4 overflow-hidden">
  <div class="p-5 flex items-center justify-between">
    <div>
      <div class="text-lg font-semibold">Exports</div>
      <div class="text-sm text-slate-500">Download data for documentation/demo</div>
    </div>
    <a href="?download=requests" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Export Requests CSV</a>
  </div>
  <div class="px-5 pb-5 text-sm text-slate-600">
    Use this CSV for Excel/Google Sheets import during capstone defense. In production, this would come from a database.
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
