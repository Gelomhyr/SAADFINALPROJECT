<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('receptionist');
require_once __DIR__ . '/../includes/storage.php';

$active = 'rx_add';
$page_title = 'Add Patient';
$header_title = 'Add Patient';
$subtitle = 'Register a new patient record (demo).';

$patients = read_json('patients.json', []);
$saved = false;
$err = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['fullName'] ?? '');
  $mobile = trim($_POST['mobile'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $address = trim($_POST['address'] ?? '');

  if ($name === '' || $mobile === '') {
    $err = 'Full name and mobile are required.';
  } else {
    $patients[] = [
      'id' => uuid(),
      'fullName' => $name,
      'mobile' => $mobile,
      'email' => $email,
      'address' => $address,
      'createdAt' => now_iso(),
    ];
    write_json('patients.json', $patients);
    $saved = true;
  }
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="max-w-3xl">
  <?php if ($saved): ?>
    <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900">
      Patient saved successfully.
      <a class="underline ml-2" href="patients.php">View Patients</a>
    </div>
  <?php endif; ?>
  <?php if ($err): ?>
    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-rose-900">
      <?= htmlspecialchars($err) ?>
    </div>
  <?php endif; ?>

  <div class="bg-white border rounded-2xl p-6">
    <form method="POST" class="grid gap-4">
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Full Name *</label>
          <input name="fullName" required class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="Juan Dela Cruz" />
        </div>
        <div>
          <label class="text-sm font-medium">Mobile *</label>
          <input name="mobile" required class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="09xxxxxxxxx" />
        </div>
      </div>
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm font-medium">Email</label>
          <input name="email" type="email" class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="email@example.com" />
        </div>
        <div>
          <label class="text-sm font-medium">Address</label>
          <input name="address" class="mt-1 w-full rounded-xl border px-3 py-2" placeholder="City / Barangay" />
        </div>
      </div>
      <div class="flex gap-2">
        <button class="rounded-xl bg-blue-600 text-white px-4 py-2">Save Patient</button>
        <a href="dashboard.php" class="rounded-xl border px-4 py-2">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
