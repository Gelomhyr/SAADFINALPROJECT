<?php require_once __DIR__ . '/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($page_title) ?></title>

  <link rel="icon" type="image/png" sizes="32x32" href="<?= asset_path('LOGO.png') ?>">


  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

  <style>
    body { font-family: 'Inter', sans-serif; }
    .text-brand-dark { color: #0f172a; }
    .text-brand-blue { color: #1e88e5; }
    .bg-brand-blue { background-color: #1e88e5; }
    .bg-brand-light { background-color: #f0f9ff; }
    .hover-bg-brand-dark:hover { background-color: #1565c0; }
    html { scroll-behavior: smooth; }
  </style>
</head>
<body class="bg-gray-50 text-gray-700">

  <!-- HEADER / NAVIGATION -->
  <nav class="bg-white shadow-sm fixed w-full z-50 top-0">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-20 items-center">
        <!-- Logo -->
        <a href="<?= url_path('index.php') ?>" class="flex-shrink-0 flex items-center gap-3">
          <img src="<?= asset_path('logo.png') ?>" onerror="this.onerror=null;this.src='<?= asset_path('LOGO.png') ?>';" alt="Logo" class="h-12 w-auto object-contain" />
          <span class="font-bold text-xl md:text-2xl text-slate-800 tracking-tight">P&J Tenarte Dental Clinic</span>
        </a>

        <!-- Desktop Menu -->
        <div class="hidden md:flex space-x-8 items-center">
          <a href="<?= url_path('index.php') ?>" class="<?= is_active('home', $active) ?>">Home</a>
          <a href="<?= url_path('about.php') ?>" class="<?= is_active('about', $active) ?>">About</a>
          <a href="<?= url_path('services.php') ?>" class="<?= is_active('services', $active) ?>">Services</a>
          <a href="<?= url_path('appointment.php') ?>" class="<?= is_active('appointment', $active) ?>">Appointment</a>
          <a href="<?= url_path('contact.php') ?>" class="<?= is_active('contact', $active) ?>">Contact</a>

          <a href="<?= url_path('staff/index.php') ?>" class="text-gray-600 hover:text-brand-blue font-medium transition">Staff Login</a>

          <a href="<?= url_path('appointment.php') ?>" class="bg-brand-blue text-white px-5 py-2.5 rounded-md font-medium hover:bg-blue-700 transition shadow-sm">
            Book Appointment
          </a>
        </div>

        <!-- Mobile menu button -->
        <div class="md:hidden flex items-center">
          <button id="mobile-menu-btn" class="text-gray-500 hover:text-gray-700 focus:outline-none" aria-label="Open menu">
            <i class="fa-solid fa-bars text-2xl"></i>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile Menu (Hidden by default) -->
    <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
      <a href="<?= url_path('index.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">Home</a>
      <a href="<?= url_path('about.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">About</a>
      <a href="<?= url_path('services.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">Services</a>
      <a href="<?= url_path('appointment.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">Appointment</a>
      <a href="<?= url_path('contact.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">Contact</a>
      <a href="<?= url_path('staff/index.php') ?>" class="block py-2 px-4 text-sm hover:bg-gray-100">Staff Login</a>
      <a href="<?= url_path('appointment.php') ?>" class="block py-2 px-4 text-sm font-semibold text-brand-blue hover:bg-gray-100">Book Appointment</a>
    </div>
  </nav>

  <script>
    // Mobile Menu Toggle
    document.addEventListener('DOMContentLoaded', () => {
      const btn = document.getElementById('mobile-menu-btn');
      const menu = document.getElementById('mobile-menu');
      if (btn && menu) {
        btn.addEventListener('click', () => menu.classList.toggle('hidden'));
      }
    });
  </script>

  <!-- Page Content Wrapper (push content below fixed nav) -->
  <main class="pt-24">
