<?php
require_once __DIR__ . '/../includes/auth.php';
require_role('owner');
require_once __DIR__ . '/../includes/storage.php';

$page_title = 'Logs';
$active = 'requests';

$message_logs = read_json('message_logs.json', []);
$reminder_logs = read_json('reminder_logs.json', []);

usort($message_logs, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));
usort($reminder_logs, fn($a,$b) => strcmp($b['createdAt'] ?? '', $a['createdAt'] ?? ''));

require_once __DIR__ . '/../includes/portal_header.php';
?>

<div class="bg-white border rounded-2xl p-5">
  <h2 class="text-lg font-bold text-slate-800">SMS / Email Logs (Simulated)</h2>
  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="text-left text-slate-500">
        <tr><th class="py-2">Date</th><th class="py-2">Channel</th><th class="py-2">To</th><th class="py-2">Message</th></tr>
      </thead>
      <tbody class="divide-y">
        <?php if (!$message_logs): ?>
          <tr><td colspan="4" class="py-4 text-slate-500">No messages yet.</td></tr>
        <?php endif; ?>
        <?php foreach (array_slice($message_logs,0,25) as $m): ?>
          <tr>
            <td class="py-3 text-xs text-slate-500"><?= htmlspecialchars($m['createdAt'] ?? '') ?></td>
            <td class="py-3"><span class="px-2 py-1 rounded-full text-xs bg-slate-100"><?= htmlspecialchars(strtoupper($m['channel'] ?? '')) ?></span></td>
            <td class="py-3"><?= htmlspecialchars($m['to'] ?? '') ?></td>
            <td class="py-3"><?= htmlspecialchars($m['message'] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<div class="mt-6 bg-white border rounded-2xl p-5">
  <h2 class="text-lg font-bold text-slate-800">Reminder Logs (Simulated)</h2>
  <div class="mt-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="text-left text-slate-500">
        <tr><th class="py-2">Created</th><th class="py-2">Request ID</th><th class="py-2">Schedule</th><th class="py-2">Scheduled For</th><th class="py-2">Status</th></tr>
      </thead>
      <tbody class="divide-y">
        <?php if (!$reminder_logs): ?>
          <tr><td colspan="5" class="py-4 text-slate-500">No reminders scheduled yet.</td></tr>
        <?php endif; ?>
        <?php foreach (array_slice($reminder_logs,0,25) as $r): ?>
          <tr>
            <td class="py-3 text-xs text-slate-500"><?= htmlspecialchars($r['createdAt'] ?? '') ?></td>
            <td class="py-3"><?= htmlspecialchars($r['relatedRequestId'] ?? '') ?></td>
            <td class="py-3"><?= htmlspecialchars($r['schedule'] ?? '') ?></td>
            <td class="py-3"><?= htmlspecialchars($r['scheduledFor'] ?? '') ?></td>
            <td class="py-3"><span class="px-2 py-1 rounded-full text-xs bg-slate-100"><?= htmlspecialchars($r['status'] ?? '') ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <p class="mt-4 text-xs text-slate-500">These logs represent the future real integration for SMS/Email reminders (24h and 2h before the appointment).</p>
</div>

<?php require_once __DIR__ . '/../includes/portal_footer.php'; ?>
