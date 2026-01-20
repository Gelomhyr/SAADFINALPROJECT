<?php
require_once __DIR__ . '/init.php';
require_once __DIR__ . '/storage.php';
require_once __DIR__ . '/auth.php';

$u = auth_user();
$role = $u['role'] ?? '';

// Page vars (safe defaults)
$page_title = $page_title ?? 'Staff Portal';
$header_title = $header_title ?? $page_title;
$subtitle = $subtitle ?? '';
$active = $active ?? '';

// Sidebar links by role
$nav = [];
if ($role === 'receptionist') {
  $nav = [
    ['key' => 'rx_dashboard', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge', 'href' => url_path('receptionist/dashboard.php')],
    ['key' => 'rx_requests',  'label' => 'Requests',  'icon' => 'fa-solid fa-inbox', 'href' => url_path('receptionist/requests.php')],
    ['key' => 'rx_add',       'label' => 'Add Patient', 'icon' => 'fa-solid fa-user-plus', 'href' => url_path('receptionist/add_patient.php')],
    ['key' => 'rx_patients',  'label' => 'Patients', 'icon' => 'fa-solid fa-users', 'href' => url_path('receptionist/patients.php')],
    ['key' => 'rx_appts',     'label' => 'Appointments', 'icon' => 'fa-solid fa-calendar-check', 'href' => url_path('receptionist/appointments.php')],
  ];
}
if ($role === 'owner') {
  $nav = [
    ['key' => 'own_dashboard', 'label' => 'Dashboard', 'icon' => 'fa-solid fa-gauge', 'href' => url_path('owner/dashboard.php')],
    ['key' => 'own_appts',     'label' => 'Appointments', 'icon' => 'fa-solid fa-calendar-check', 'href' => url_path('owner/appointments.php')],
    ['key' => 'own_patients',  'label' => 'Patients', 'icon' => 'fa-solid fa-users', 'href' => url_path('owner/patients.php')],
    ['key' => 'own_reports',   'label' => 'Reports', 'icon' => 'fa-solid fa-chart-line', 'href' => url_path('owner/reports.php')],
    ['key' => 'own_settings',  'label' => 'Settings', 'icon' => 'fa-solid fa-gear', 'href' => url_path('owner/settings.php')],
  ];
}

// Unread notifications badge (optional)
$notifications = read_json('notifications.json', []);
$unread = 0;
foreach ($notifications as $n) {
  if (($n['audience'] ?? '') === $role && !($n['isRead'] ?? false)) $unread++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>
  <link rel="icon" type="image/png" sizes="32x32" href="<?= asset_path('LOGO.png') ?>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <style>
    body { font-family: 'Inter', sans-serif; }
    .bg-brand { background-color: #1e88e5; }
    .text-brand { color: #1e88e5; }
  </style>
</head>

<body class="bg-slate-100 text-slate-900">

<!-- Mobile sidebar overlay -->
<div id="portalOverlay" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>

<div class="min-h-screen flex">
  <!-- Sidebar -->
  <aside id="portalSidebar" class="fixed md:static inset-y-0 left-0 z-50 w-72 md:w-72 bg-slate-950 text-white transform -translate-x-full md:translate-x-0 transition-transform duration-200 flex flex-col">
    <div class="p-5 border-b border-white/10 flex items-center gap-3">
      <div class="h-10 w-10 rounded-xl bg-brand flex items-center justify-center font-bold">P&J</div>
      <div>
        <div class="font-semibold leading-tight">P&J Tenarte</div>
        <div class="text-xs text-white/60 capitalize"><?= htmlspecialchars($role ?: 'staff') ?> portal</div>
      </div>
      <button id="portalClose" class="ml-auto md:hidden text-white/70 hover:text-white" aria-label="Close menu">
        <i class="fa-solid fa-xmark text-lg"></i>
      </button>
    </div>

    <nav class="p-3 space-y-1">
      <?php foreach ($nav as $item): ?>
        <?php $is = ($item['key'] === $active); ?>
        <a href="<?= $item['href'] ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl transition
          <?= $is ? 'bg-white/10 ring-1 ring-white/10' : 'hover:bg-white/5' ?>">
          <i class="<?= htmlspecialchars($item['icon']) ?> w-5 text-white/80"></i>
          <span class="font-medium"><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>

      <div class="pt-3 mt-3 border-t border-white/10"></div>
      <a href="<?= url_path('index.php') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition">
        <i class="fa-solid fa-house w-5 text-white/80"></i>
        <span class="font-medium">Back to Website</span>
      </a>
      <a href="<?= url_path('staff/logout.php') ?>" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-white/5 transition text-red-200">
        <i class="fa-solid fa-right-from-bracket w-5"></i>
        <span class="font-medium">Logout</span>
      </a>
    </nav>

    <div class="mt-auto p-5 border-t border-white/10">
      <div class="text-sm font-semibold"><?= htmlspecialchars($u['name'] ?? 'Staff') ?></div>
      <div class="text-xs text-white/60"><?= htmlspecialchars($u['email'] ?? '') ?></div>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 md:ml-0">
    <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex items-center justify-between sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button id="portalOpen" class="md:hidden h-10 w-10 rounded-lg border border-slate-200 bg-white hover:bg-slate-50" aria-label="Open menu">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div>
          <?php if ($subtitle): ?><div class="text-xs text-slate-500"><?= htmlspecialchars($subtitle) ?></div><?php endif; ?>
          <div class="text-xl font-bold"><?= htmlspecialchars($header_title) ?></div>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <a href="<?= ($role === 'receptionist') ? url_path('receptionist/requests.php') : url_path('owner/dashboard.php') ?>" class="relative h-10 w-10 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 flex items-center justify-center" title="Notifications">
          <i class="fa-regular fa-bell"></i>
          <?php if ($unread > 0): ?>
            <span class="absolute -top-2 -right-2 h-5 min-w-[20px] px-1 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">
              <?= (int)$unread ?>
            </span>
          <?php endif; ?>
        </a>
        <div class="hidden sm:flex items-center gap-3">
          <div class="text-right">
            <div class="text-sm font-semibold"><?= htmlspecialchars($u['name'] ?? '') ?></div>
            <div class="text-xs text-slate-500 capitalize"><?= htmlspecialchars($role) ?></div>
          </div>
          <div class="h-10 w-10 rounded-full bg-slate-200 flex items-center justify-center">
            <i class="fa-solid fa-user"></i>
          </div>
        </div>
      </div>
    </header>

    <main class="p-4 md:p-8">

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('portalSidebar');
    const overlay = document.getElementById('portalOverlay');
    const openBtn = document.getElementById('portalOpen');
    const closeBtn = document.getElementById('portalClose');

    function open() {
      if (!sidebar || !overlay) return;
      sidebar.classList.remove('-translate-x-full');
      overlay.classList.remove('hidden');
    }
    function close() {
      if (!sidebar || !overlay) return;
      sidebar.classList.add('-translate-x-full');
      overlay.classList.add('hidden');
    }

    openBtn?.addEventListener('click', open);
    closeBtn?.addEventListener('click', close);
    overlay?.addEventListener('click', close);
  });
</script>
