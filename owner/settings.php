<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$active = 'ow_settings';
$page_title = 'Settings';
$header_title = 'Settings';
$subtitle = 'UI-only toggles stored locally (demo).';

$settings = read_json('settings.json', [
  'email_alerts' => true,
  'sms_alerts' => true,
  'inapp_alerts' => true,
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $settings['email_alerts'] = isset($_POST['email_alerts']);
  $settings['sms_alerts'] = isset($_POST['sms_alerts']);
  $settings['inapp_alerts'] = isset($_POST['inapp_alerts']);
  write_json('settings.json', $settings);
  $saved = true;
}

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="bg-white border rounded-2xl p-5 max-w-2xl">
  <div class="text-lg font-semibold">Clinic Settings</div>
  <div class="text-sm text-slate-500">These toggles are stored locally for demo purposes.</div>

  <?php if (!empty($saved)): ?>
    <div class="mt-4 p-3 rounded-xl bg-emerald-50 text-emerald-700 border border-emerald-200 text-sm">
      Settings saved.
    </div>
  <?php endif; ?>

  <form method="post" class="mt-4 space-y-4">
    <div class="flex items-center justify-between p-4 rounded-xl border">
      <div>
        <div class="font-medium">Email alerts</div>
        <div class="text-sm text-slate-500">Notify owner/receptionist by email (simulated)</div>
      </div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="email_alerts" class="h-5 w-5" <?= !empty($settings['email_alerts']) ? 'checked' : '' ?> />
      </label>
    </div>
    <div class="flex items-center justify-between p-4 rounded-xl border">
      <div>
        <div class="font-medium">SMS alerts</div>
        <div class="text-sm text-slate-500">Send SMS reminders/updates (simulated)</div>
      </div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="sms_alerts" class="h-5 w-5" <?= !empty($settings['sms_alerts']) ? 'checked' : '' ?> />
      </label>
    </div>
    <div class="flex items-center justify-between p-4 rounded-xl border">
      <div>
        <div class="font-medium">In-app alerts</div>
        <div class="text-sm text-slate-500">Show notifications in dashboard</div>
      </div>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="inapp_alerts" class="h-5 w-5" <?= !empty($settings['inapp_alerts']) ? 'checked' : '' ?> />
      </label>
    </div>
    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">Save Settings</button>
  </form>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
