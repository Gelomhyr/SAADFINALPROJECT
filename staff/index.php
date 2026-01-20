<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';

$u = auth_user();
if ($u) {
  if (($u['role'] ?? '') === 'receptionist') { header('Location: ' . url_path('receptionist/dashboard.php')); exit; }
  if (($u['role'] ?? '') === 'owner') { header('Location: ' . url_path('owner/dashboard.php')); exit; }
}

$page_title = 'Staff Login - P&J Tenarte';
$active = '';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="max-w-4xl mx-auto px-4">
  <div class="bg-white rounded-2xl shadow-sm border p-8">
    <div class="flex items-center gap-3 mb-6">
      <img src="<?= asset_path('P&J.png') ?>" class="h-12 w-auto" alt="logo" />
      <div>
        <h1 class="text-2xl font-bold text-slate-800">Staff Portal</h1>
        <p class="text-slate-600">Select your role to continue.</p>
      </div>
    </div>

    <div class="grid md:grid-cols-2 gap-4">
      <a href="login.php?role=receptionist" class="group border rounded-xl p-5 hover:bg-slate-50 transition">
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-xl bg-blue-50 flex items-center justify-center text-brand-blue">
            <i class="fa-solid fa-user-nurse text-xl"></i>
          </div>
          <div>
            <div class="font-semibold text-slate-800">Receptionist</div>
            <div class="text-sm text-slate-500">Manage appointment requests</div>
          </div>
        </div>
      </a>

      <a href="login.php?role=owner" class="group border rounded-xl p-5 hover:bg-slate-50 transition">
        <div class="flex items-center gap-3">
          <div class="h-12 w-12 rounded-xl bg-amber-50 flex items-center justify-center text-amber-700">
            <i class="fa-solid fa-crown text-xl"></i>
          </div>
          <div>
            <div class="font-semibold text-slate-800">Owner</div>
            <div class="text-sm text-slate-500">View dashboard and requests</div>
          </div>
        </div>
      </a>
    </div>

    <div class="mt-6 text-sm text-slate-500">
      <div class="font-semibold text-slate-700 mb-1">Demo Credentials</div>
      <ul class="list-disc pl-5">
        <li>Receptionist: <span class="font-mono">receptionist</span> / <span class="font-mono">receptionist123</span></li>
        <li>Owner: <span class="font-mono">owner</span> / <span class="font-mono">owner123</span></li>
      </ul>
    </div>
  </div>
</section>

<!-- Intentionally no public site footer on staff login page -->
</main>
</body>
</html>
