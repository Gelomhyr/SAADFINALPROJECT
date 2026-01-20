<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_dashboard';
$page_title = 'Owner Dashboard';
$header_title = 'Dashboard';
$subtitle = 'Overview of clinic performance and analytics';

$requests = read_json('appointment_requests.json', []);

$today = date('Y-m-d');
$total = count($requests);
$pending = count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'PENDING'));
$confirmed = count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'CONFIRMED'));
$cancelled = count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'CANCELLED'));

$todayConfirmed = count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'CONFIRMED' && (($r['finalDate'] ?? ($r['preferredDate'] ?? '')) === $today)));

// Basic monthly count
$monthPrefix = date('Y-m-');
$thisMonth = count(array_filter($requests, function($r) use ($monthPrefix) {
  $d = $r['finalDate'] ?? ($r['preferredDate'] ?? '');
  return $d && str_starts_with($d, $monthPrefix);
}));

$completionRate = $total ? round(($confirmed / $total) * 100) : 0;

// Build last 7 days counts for chart
$days = [];
for ($i=6; $i>=0; $i--) {
  $d = date('Y-m-d', strtotime("-$i day"));
  $days[] = $d;
}
$counts = array_fill(0, count($days), 0);
foreach ($requests as $r) {
  $d = $r['finalDate'] ?? ($r['preferredDate'] ?? '');
  $idx = array_search($d, $days, true);
  if ($idx !== false) $counts[$idx]++;
}

// Status breakdown for donut
$statusLabels = ['Pending','Confirmed','Rescheduled','Cancelled'];
$statusCounts = [
  $pending,
  $confirmed,
  count(array_filter($requests, fn($r) => ($r['status'] ?? '') === 'RESCHEDULED')),
  $cancelled,
];

// Unread notifications
$notifications = read_json('notifications.json', []);
$unreadCount = 0;
foreach ($notifications as $n) {
  if (($n['audience'] ?? '') === 'owner' && empty($n['isRead'])) $unreadCount++;
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-blue-50 flex items-center justify-center text-brand-blue"><i class="fa-regular fa-calendar"></i></div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= $todayConfirmed ?></div>
        <div class="text-sm text-slate-500">Today</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-700"><i class="fa-regular fa-user"></i></div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= $total ?></div>
        <div class="text-sm text-slate-500">Total Requests</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-teal-50 flex items-center justify-center text-teal-700"><i class="fa-solid fa-arrow-trend-up"></i></div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= $thisMonth ?></div>
        <div class="text-sm text-slate-500">This Month</div>
      </div>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700"><i class="fa-solid fa-wave-square"></i></div>
      <div>
        <div class="text-2xl font-extrabold text-slate-900 leading-none"><?= $completionRate ?>%</div>
        <div class="text-sm text-slate-500">Completion Rate</div>
      </div>
    </div>
  </div>
</div>

<div class="mt-6 grid lg:grid-cols-2 gap-4">
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xl font-extrabold text-slate-900">Weekly Appointments</div>
        <div class="text-sm text-slate-500">Last 7 days</div>
      </div>
    </div>
    <div class="mt-4">
      <canvas id="weeklyChart" height="160"></canvas>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div>
      <div class="text-xl font-extrabold text-slate-900">Status Breakdown</div>
      <div class="text-sm text-slate-500">Pending vs confirmed vs rescheduled</div>
    </div>
    <div class="mt-4">
      <canvas id="statusChart" height="160"></canvas>
    </div>
  </div>
</div>

<div class="mt-6 grid lg:grid-cols-2 gap-4">
  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xl font-extrabold text-slate-900">Owner Notifications</div>
        <div class="text-sm text-slate-500">Unread: <span class="font-semibold"><?= $unreadCount ?></span></div>
      </div>
      <a href="<?= url_path('owner/requests.php') ?>" class="inline-flex items-center gap-2 rounded-xl bg-brand-blue px-4 py-2 text-white font-semibold hover:opacity-95">
        <i class="fa-solid fa-list"></i> View Requests
      </a>
    </div>
    <div class="mt-4 space-y-3">
      <?php
        $items = array_values(array_filter($notifications, fn($n) => ($n['audience'] ?? '') === 'owner'));
        usort($items, fn($a,$b) => strcmp(($b['createdAt'] ?? ''), ($a['createdAt'] ?? '')));
        $items = array_slice($items, 0, 5);
      ?>
      <?php if (!count($items)): ?>
        <div class="text-slate-500">No notifications yet.</div>
      <?php else: ?>
        <?php foreach ($items as $n): ?>
          <div class="rounded-xl border border-slate-200 p-3 flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-semibold text-slate-900"><?= htmlspecialchars($n['message'] ?? '') ?></div>
              <div class="text-xs text-slate-500"><?= htmlspecialchars($n['createdAt'] ?? '') ?></div>
            </div>
            <?php if (empty($n['isRead'])): ?>
              <span class="h-2 w-2 rounded-full bg-rose-500 mt-1"></span>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>

  <div class="rounded-2xl border border-slate-200 bg-white p-5">
    <div>
      <div class="text-xl font-extrabold text-slate-900">Quick Summary</div>
      <div class="text-sm text-slate-500">Read-only portal. Receptionist confirms/reschedules appointments.</div>
    </div>
    <div class="mt-4 grid grid-cols-2 gap-3">
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Pending</div>
        <div class="text-2xl font-extrabold text-slate-900"><?= $pending ?></div>
      </div>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Confirmed</div>
        <div class="text-2xl font-extrabold text-slate-900"><?= $confirmed ?></div>
      </div>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">Cancelled</div>
        <div class="text-2xl font-extrabold text-slate-900"><?= $cancelled ?></div>
      </div>
      <div class="rounded-xl border border-slate-200 p-4">
        <div class="text-sm text-slate-500">All Requests</div>
        <div class="text-2xl font-extrabold text-slate-900"><?= $total ?></div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const days = <?= json_encode(array_map(fn($d) => date('D', strtotime($d)), $days)) ?>;
  const counts = <?= json_encode($counts) ?>;
  const statusLabels = <?= json_encode($statusLabels) ?>;
  const statusCounts = <?= json_encode($statusCounts) ?>;

  const weeklyCtx = document.getElementById('weeklyChart');
  if (weeklyCtx) {
    new Chart(weeklyCtx, {
      type: 'bar',
      data: { labels: days, datasets: [{ label: 'Appointments', data: counts }] },
      options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  }

  const statusCtx = document.getElementById('statusChart');
  if (statusCtx) {
    new Chart(statusCtx, {
      type: 'doughnut',
      data: { labels: statusLabels, datasets: [{ data: statusCounts }] },
      options: { plugins: { legend: { position: 'bottom' } } }
    });
  }
</script>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
