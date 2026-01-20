<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('receptionist');
require_once __DIR__ . '/../includes/storage.php';

$active = 'rx_dashboard';
$page_title = 'Receptionist Dashboard';
$header_title = 'Dashboard';
$subtitle = "Welcome back! Here's what's happening today.";

$requests = read_json('appointment_requests.json', []);

// Status buckets
$pending = array_values(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'PENDING'));
$confirmed = array_values(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'CONFIRMED'));

// Derived metrics
$today = date('Y-m-d');
$todaySchedule = array_values(array_filter($confirmed, fn($r) => ($r['finalDate'] ?? ($r['preferredDate'] ?? '')) === $today));
$upcoming = array_values(array_filter($confirmed, function($r) use ($today) {
  $d = $r['finalDate'] ?? ($r['preferredDate'] ?? '');
  return $d && $d > $today;
}));

// "Patients" = unique mobile numbers (demo)
$mobiles = [];
foreach ($requests as $r) {
  $m = trim((string)($r['mobile'] ?? ''));
  if ($m !== '') $mobiles[$m] = true;
}
$totalPatients = count($mobiles);

// Latest schedule cards
usort($todaySchedule, function($a, $b) {
  return strcmp(($a['finalTime'] ?? ($a['preferredTime'] ?? '')), ($b['finalTime'] ?? ($b['preferredTime'] ?? '')));
});

require_once __DIR__ . '/../includes/portal_header.php';
?>

<!-- KPI cards (match the dark-sidebar UI screenshots) -->
<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-brand-blue">
        <i class="fa-regular fa-calendar"></i>
      </div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= count($todaySchedule) ?></div>
        <div class="text-sm text-slate-500">Today's Schedule</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700">
        <i class="fa-regular fa-folder-open"></i>
      </div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= count($pending) ?></div>
        <div class="text-sm text-slate-500">Pending Requests</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700">
        <i class="fa-regular fa-user"></i>
      </div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= $totalPatients ?></div>
        <div class="text-sm text-slate-500">Total Patients</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-700">
        <i class="fa-regular fa-clock"></i>
      </div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= count($upcoming) ?></div>
        <div class="text-sm text-slate-500">Upcoming</div>
      </div>
    </div>
  </div>
</div>

<!-- Quick actions -->
<div class="mt-5 grid lg:grid-cols-2 gap-4">
  <a href="<?= url_path('receptionist/add_patient.php') ?>" class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between hover:border-slate-300 transition">
    <div class="flex items-center gap-4">
      <div class="h-12 w-12 rounded-2xl bg-brand-blue text-white flex items-center justify-center">
        <i class="fa-solid fa-user-plus"></i>
      </div>
      <div>
        <div class="font-extrabold text-slate-900">Add New Patient</div>
        <div class="text-sm text-slate-500">Register a new patient record</div>
      </div>
    </div>
    <i class="fa-solid fa-arrow-right text-slate-400"></i>
  </a>

  <a href="<?= url_path('receptionist/requests.php') ?>" class="rounded-2xl border border-slate-200 bg-white p-5 flex items-center justify-between hover:border-slate-300 transition">
    <div class="flex items-center gap-4">
      <div class="h-12 w-12 rounded-2xl bg-amber-50 text-amber-700 flex items-center justify-center">
        <i class="fa-solid fa-inbox"></i>
      </div>
      <div>
        <div class="font-extrabold text-slate-900">View Requests</div>
        <div class="text-sm text-slate-500"><?= count($pending) ?> pending requests</div>
      </div>
    </div>
    <i class="fa-solid fa-arrow-right text-slate-400"></i>
  </a>
</div>

<!-- Today's schedule (simple cards) -->
<div class="mt-6 rounded-2xl border border-slate-200 bg-white overflow-hidden">
  <div class="px-6 py-5 flex items-center justify-between border-b">
    <div>
      <div class="text-xl font-extrabold text-slate-900">Today's Schedule</div>
      <div class="text-sm text-slate-500"><?= date('l, F j, Y') ?></div>
    </div>
    <a href="<?= url_path('receptionist/appointments.php') ?>" class="text-brand-blue font-semibold hover:underline">View All <i class="fa-solid fa-arrow-right text-xs"></i></a>
  </div>

  <div class="p-6 space-y-4">
    <?php if (!count($todaySchedule)): ?>
      <div class="text-slate-500">No confirmed appointments for today yet.</div>
    <?php else: ?>
      <?php foreach (array_slice($todaySchedule, 0, 5) as $r): ?>
        <?php
          $name = $r['fullName'] ?? '-';
          $time = $r['finalTime'] ?? ($r['preferredTime'] ?? '');
          $proc = $r['procedure'] ?? '-';
        ?>
        <div class="rounded-2xl border border-slate-200 p-4 flex items-center justify-between">
          <div class="flex items-center gap-4">
            <div class="h-12 w-12 rounded-2xl bg-slate-50 flex items-center justify-center text-slate-600">
              <i class="fa-regular fa-clock"></i>
            </div>
            <div>
              <div class="font-extrabold text-slate-900"><?= htmlspecialchars($name) ?>
                <span class="ml-2 inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-bold text-brand-blue">SCHEDULED</span>
              </div>
              <div class="text-sm text-slate-500"><?= htmlspecialchars($time) ?> &nbsp;•&nbsp; <?= htmlspecialchars($proc) ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
