<?php
require_once __DIR__ . '/../includes/init.php';
require_once __DIR__ . '/../includes/auth.php';

$role = $_GET['role'] ?? 'receptionist';
if (!in_array($role, ['receptionist','owner'], true)) { $role = 'receptionist'; }

$users = require __DIR__ . '/../includes/users.php';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = trim($_POST['username'] ?? '');
  $password = (string)($_POST['password'] ?? '');

  if (!isset($users[$username])) {
    $error = 'Invalid username or password.';
  } else {
    $u = $users[$username];
    if (($u['role'] ?? '') !== $role) {
      $error = 'Wrong portal selected for this account.';
    } elseif (($u['password'] ?? '') !== $password) {
      $error = 'Invalid username or password.';
    } else {
      login_user($u['role'], $u['name'], $username);
      header('Location: ' . ($role === 'receptionist' ? url_path('receptionist/dashboard.php') : url_path('owner/dashboard.php')));
      exit;
    }
  }
}

$page_title = ucfirst($role) . ' Login - P&J Tenarte';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="max-w-xl mx-auto px-4">
  <div class="bg-white rounded-2xl shadow-sm border p-8">
    <div class="flex items-center justify-between mb-4">
      <div>
        <h1 class="text-2xl font-bold text-slate-800"><?= htmlspecialchars(ucfirst($role)) ?> Login</h1>
        <p class="text-slate-600 text-sm">Enter your staff credentials to continue.</p>
      </div>
      <a href="<?= url_path('staff/index.php') ?>" class="text-sm text-brand-blue hover:underline">Back</a>
    </div>

    <?php if ($error): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-100 text-red-700 text-sm">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-4">
      <div>
        <label class="text-sm font-medium text-slate-700">Username</label>
        <input name="username" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="<?= $role === 'receptionist' ? 'receptionist' : 'owner' ?>">
      </div>
      <div>
        <label class="text-sm font-medium text-slate-700">Password</label>
        <input type="password" name="password" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-200" placeholder="••••••••">
      </div>
      <button class="w-full bg-brand-blue text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
        Login
      </button>
    </form>

    <p class="mt-6 text-xs text-slate-500">Demo-only portal (no real backend). Data is read from saved JSON files in <span class="font-mono">/data</span>.</p>
  </div>
</section>

<!-- Intentionally omit the public footer on staff login pages -->
</main>
</body>
</html>
