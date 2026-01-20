<?php
$page_title = 'P&J Tenarte Dental Clinic - Contact';
$active = 'contact';

require_once __DIR__ . '/includes/storage.php';

$message_sent = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($name === '') $errors[] = 'Your Name is required.';
  if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'A valid Email is required.';
  if ($message === '') $errors[] = 'Your Message is required.';

  if (empty($errors)) {
    $msgs = read_json('contact_messages.json', []);
    $msgs[] = [
      'id' => uuid(),
      'name' => $name,
      'email' => $email,
      'message' => $message,
      'createdAt' => now_iso()
    ];
    write_json('contact_messages.json', $msgs);

    header('Location: contact.php?sent=1');
    exit;
  }
}

if (isset($_GET['sent']) && $_GET['sent'] === '1') {
  $message_sent = true;
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-20 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-12">
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Get in Touch</h2>
      <p class="text-gray-600">Have questions or need to reach us? We're here to help.</p>
    </div>

    <?php if ($message_sent): ?>
      <div class="max-w-3xl mx-auto bg-green-50 border border-green-200 text-green-800 rounded-xl p-5 mb-10">
        <p class="font-semibold">Message sent successfully! ✅</p>
        <p class="text-sm mt-1">Thank you. We'll get back to you as soon as possible.</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="max-w-3xl mx-auto bg-red-50 border border-red-200 text-red-800 rounded-xl p-5 mb-10">
        <p class="font-semibold mb-2">Please fix the following:</p>
        <ul class="list-disc pl-5 text-sm space-y-1">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
      <!-- Contact Info -->
      <div class="space-y-8">
        <div class="bg-gray-50 border border-gray-100 rounded-2xl p-8">
          <h3 class="text-xl font-bold text-slate-900 mb-6">Clinic Information</h3>
          <div class="space-y-4 text-gray-700">
            <p class="flex items-start gap-3"><i class="fa-solid fa-location-dot text-brand-blue mt-1"></i><span><strong>Address:</strong> (Put clinic address here)</span></p>
            <p class="flex items-start gap-3"><i class="fa-solid fa-phone text-brand-blue mt-1"></i><span><strong>Phone:</strong> (Put phone number here)</span></p>
            <p class="flex items-start gap-3"><i class="fa-solid fa-envelope text-brand-blue mt-1"></i><span><strong>Email:</strong> (Put clinic email here)</span></p>
            <p class="flex items-start gap-3"><i class="fa-solid fa-clock text-brand-blue mt-1"></i><span><strong>Hours:</strong> Mon–Sat, 9:00 AM – 6:00 PM</span></p>
          </div>
        </div>

        <div class="bg-gray-50 border border-gray-100 rounded-2xl overflow-hidden">
          <div class="p-6">
            <h3 class="text-xl font-bold text-slate-900">Map</h3>
            <p class="text-sm text-gray-600 mt-1">Update the map embed to your clinic location.</p>
          </div>
          <div class="aspect-video">
            <iframe
              title="Google Map"
              class="w-full h-full"
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade"
              src="https://www.google.com/maps?q=Imus%20Cavite&output=embed">
            </iframe>
          </div>
        </div>
      </div>

      <!-- Contact Form -->
      <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
        <h3 class="text-xl font-bold text-slate-900 mb-6">Send us a Message</h3>
        <form method="POST" class="space-y-5">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Your Name <span class="text-red-500">*</span></label>
            <input name="name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email Address <span class="text-red-500">*</span></label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Message <span class="text-red-500">*</span></label>
            <textarea name="message" rows="6" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="bg-brand-blue text-white px-8 py-3 rounded-md font-semibold hover:bg-blue-700 transition shadow-sm w-full">
            Send Message
          </button>

          <p class="text-xs text-gray-500 text-center">We will never share your information without permission.</p>
        </form>
      </div>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
