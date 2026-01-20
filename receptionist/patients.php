<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('receptionist');
require_once __DIR__ . '/../includes/storage.php';

$active = 'rx_patients';
$page_title = 'Patients';
$header_title = 'Patients';
$subtitle = 'View and edit patient records saved in JSON (demo).';

$patients = read_json('patients.json', []);
$appointments = read_json('appointments.json', []);

// Map appointments count + last appointment per patient
$apptByPatient = [];
foreach ($appointments as $a) {
  $pid = $a['patientId'] ?? '';
  if (!$pid) continue;
  $apptByPatient[$pid][] = $a;
}
foreach ($apptByPatient as &$list) {
  usort($list, fn($x,$y) => strcmp($y['date'] ?? '', $x['date'] ?? ''));
}
unset($list);

// Handle update
$flash = '';
$flashType = 'success';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
  $id = $_POST['id'] ?? '';
  $fullName = trim($_POST['fullName'] ?? '');
  $mobile = trim($_POST['mobile'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $address = trim($_POST['address'] ?? '');

  if ($id && $fullName !== '' && $mobile !== '') {
    foreach ($patients as &$p) {
      if (($p['id'] ?? '') !== $id) continue;
      $p['fullName'] = $fullName;
      $p['mobile'] = $mobile;
      $p['email'] = $email;
      $p['address'] = $address;
      $p['updatedAt'] = now_iso();
      break;
    }
    unset($p);
    write_json('patients.json', $patients);
    $flash = 'Patient updated successfully.';
  } else {
    $flash = 'Full name and mobile are required.';
    $flashType = 'error';
  }
}

// Search
$q = trim($_GET['q'] ?? '');
if ($q !== '') {
  $qq = mb_strtolower($q);
  $patients = array_values(array_filter($patients, function($p) use ($qq) {
    $name = mb_strtolower((string)($p['fullName'] ?? ''));
    $mobile = mb_strtolower((string)($p['mobile'] ?? ''));
    return str_contains($name, $qq) || str_contains($mobile, $qq);
  }));
}

// newest first
usort($patients, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

require_once __DIR__ . '/../includes/portal_header.php';
?>

<?php if ($flash): ?>
  <div class="mb-4 rounded-xl border <?= $flashType==='success'?'border-emerald-200 bg-emerald-50 text-emerald-900':'border-rose-200 bg-rose-50 text-rose-900' ?> px-4 py-3">
    <?= htmlspecialchars($flash) ?>
  </div>
<?php endif; ?>

<div class="bg-white border rounded-2xl overflow-hidden">
  <div class="p-5 border-b flex flex-col md:flex-row md:items-center md:justify-between gap-3">
    <div>
      <div class="text-sm text-slate-600">Search by name or mobile.</div>
    </div>
    <form method="GET" class="flex gap-2 w-full md:w-auto">
      <input name="q" value="<?= htmlspecialchars($q) ?>" class="w-full md:w-72 border rounded-xl px-3 py-2" placeholder="Search...">
      <button class="px-4 py-2 rounded-xl bg-slate-900 text-white">Search</button>
    </form>
  </div>

  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-slate-50 text-slate-600">
        <tr>
          <th class="text-left px-5 py-3">Patient</th>
          <th class="text-left px-5 py-3">Contact</th>
          <th class="text-left px-5 py-3">Last Appointment</th>
          <th class="text-left px-5 py-3">Total Appointments</th>
          <th class="text-left px-5 py-3">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!count($patients)): ?>
          <tr><td colspan="5" class="px-5 py-6 text-slate-500">No patients yet. Add one in <a class="text-blue-700 underline" href="add_patient.php">Add Patient</a>.</td></tr>
        <?php endif; ?>
        <?php foreach ($patients as $p):
          $id = $p['id'] ?? '';
          $list = $apptByPatient[$id] ?? [];
          $last = $list[0] ?? null;
          $lastText = $last ? (($last['date'] ?? '').' '.($last['time'] ?? '').' • '.($last['reason'] ?? '')) : '-';
        ?>
          <tr class="border-t align-top">
            <td class="px-5 py-3 font-semibold text-slate-800">
              <?= htmlspecialchars($p['fullName'] ?? '-') ?>
              <div class="text-xs text-slate-500 mt-1">ID: <span class="font-mono"><?= htmlspecialchars(substr($id,0,8)) ?></span></div>
            </td>
            <td class="px-5 py-3">
              <div><?= htmlspecialchars($p['mobile'] ?? '-') ?></div>
              <?php if (!empty($p['email'])): ?><div class="text-xs text-slate-500"><?= htmlspecialchars($p['email']) ?></div><?php endif; ?>
            </td>
            <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($lastText) ?></td>
            <td class="px-5 py-3"><span class="inline-flex items-center px-2 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold"><?= count($list) ?></span></td>
            <td class="px-5 py-3">
              <details class="group">
                <summary class="cursor-pointer text-blue-700 font-semibold">View / Edit</summary>
                <div class="mt-3 p-4 rounded-xl border bg-slate-50">
                  <form method="POST" class="grid gap-3">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <div class="grid md:grid-cols-2 gap-3">
                      <div>
                        <label class="text-xs font-semibold text-slate-600">Full Name *</label>
                        <input name="fullName" value="<?= htmlspecialchars($p['fullName'] ?? '') ?>" class="mt-1 w-full border rounded-xl px-3 py-2" required>
                      </div>
                      <div>
                        <label class="text-xs font-semibold text-slate-600">Mobile *</label>
                        <input name="mobile" value="<?= htmlspecialchars($p['mobile'] ?? '') ?>" class="mt-1 w-full border rounded-xl px-3 py-2" required>
                      </div>
                    </div>
                    <div class="grid md:grid-cols-2 gap-3">
                      <div>
                        <label class="text-xs font-semibold text-slate-600">Email</label>
                        <input name="email" type="email" value="<?= htmlspecialchars($p['email'] ?? '') ?>" class="mt-1 w-full border rounded-xl px-3 py-2">
                      </div>
                      <div>
                        <label class="text-xs font-semibold text-slate-600">Address</label>
                        <input name="address" value="<?= htmlspecialchars($p['address'] ?? '') ?>" class="mt-1 w-full border rounded-xl px-3 py-2">
                      </div>
                    </div>
                    <div class="flex gap-2">
                      <button class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold">Save Changes</button>
                      <a class="px-4 py-2 rounded-xl border" href="appointments.php?pid=<?= urlencode($id) ?>">View Appointments</a>
                    </div>
                  </form>
                </div>
              </details>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
