<?php
$page_title = 'P&J Tenarte Dental Clinic - Appointment';
$active = 'appointment';

require_once __DIR__ . '/includes/storage.php';

$appointment_booked = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Basic validation
  $full_name = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $service = trim($_POST['service'] ?? '');
  $date = trim($_POST['date'] ?? '');
  $time = trim($_POST['time'] ?? '');
  $message = trim($_POST['message'] ?? '');

  if ($full_name === '') $errors[] = 'Full Name is required.';
  if ($phone === '') $errors[] = 'Contact Number is required.';
  if ($service === '') $errors[] = 'Service Type is required.';
  if ($date === '') $errors[] = 'Preferred Date is required.';
  if ($time === '') $errors[] = 'Preferred Time is required.';
  if ($message === '') $errors[] = 'Reason/Message is required.';

  // Email is optional, but if provided it must be valid
  if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Please enter a valid Email Address (or leave it blank).';
  }

  if (empty($errors)) {
    // Save to simple JSON storage (acts like a mini database)
    $requests = read_json('appointment_requests.json', []);
    $requests[] = [
      'id' => uuid(),
      'fullName' => $full_name,
      'mobile' => $phone,
      'email' => $email,
      'preferredDate' => $date,
      'preferredTime' => $time,
      'service' => $service,
      'notes' => $message,
      'status' => 'PENDING',
      'createdAt' => now_iso()
    ];
    write_json('appointment_requests.json', $requests);

    // PRG pattern: redirect after successful POST to prevent resubmission
    header('Location: appointment.php?success=1');
    exit;
  }
}

if (isset($_GET['success']) && $_GET['success'] === '1') {
  $appointment_booked = true;
}

require_once __DIR__ . '/includes/header.php';
?>

<section class="py-20 bg-brand-light">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

    <div class="text-center mb-10">
      <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-3">Book an Appointment</h2>
      <p class="text-gray-600">Fill out the form and our clinic staff will contact you to confirm your schedule.</p>
    </div>

    <?php if ($appointment_booked): ?>
      <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-5 mb-8">
        <p class="font-semibold">Your appointment request has been received! ✅</p>
        <p class="text-sm mt-1">We’ll contact you soon to confirm your preferred schedule.</p>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="bg-red-50 border border-red-200 text-red-800 rounded-xl p-5 mb-8">
        <p class="font-semibold mb-2">Please fix the following:</p>
        <ul class="list-disc pl-5 text-sm space-y-1">
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
      <form method="POST" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Full Name <span class="text-red-500">*</span></label>
            <input name="full_name" value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Contact Number <span class="text-red-500">*</span></label>
            <input name="phone" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Email Address (optional)</label>
            <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Service Type <span class="text-red-500">*</span></label>
            <select name="service" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 bg-white focus:outline-none focus:ring-2 focus:ring-blue-200">
              <option value="">Select a service</option>
              <?php
                $services = [
                  'Oral Cleaning & Checkups',
                  'Tooth Extraction',
                  'Teeth Whitening',
                  'Tooth Filling',
                  'Braces Consultation',
                  'Digital X-ray Consultation'
                ];
                $sel = $_POST['service'] ?? '';
                foreach ($services as $s) {
                  $selected = ($sel === $s) ? 'selected' : '';
                  echo '<option ' . $selected . '>' . htmlspecialchars($s) . '</option>';
                }
              ?>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Preferred Date <span class="text-red-500">*</span></label>
            <input type="date" name="date" value="<?= htmlspecialchars($_POST['date'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-2">Preferred Time <span class="text-red-500">*</span></label>
            <input type="time" name="time" value="<?= htmlspecialchars($_POST['time'] ?? '') ?>" required
              class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200" />
          </div>
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-700 mb-2">Reason / Message <span class="text-red-500">*</span></label>
          <textarea name="message" rows="5" required
            class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-200"><?= htmlspecialchars($_POST['message'] ?? '') ?></textarea>
          <p class="text-xs text-gray-500 mt-2">Example: Tooth pain, cleaning, braces inquiry, etc.</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 sm:items-center sm:justify-between">
          <p class="text-sm text-gray-500">By submitting, you agree to be contacted for appointment confirmation.</p>
          <button type="submit" class="bg-brand-blue text-white px-8 py-3 rounded-md font-semibold hover:bg-blue-700 transition shadow-sm">
            Submit Request
          </button>
        </div>
      </form>
    </div>

    <div class="mt-10 text-center">
      <p class="text-sm text-gray-600">Want to talk to us directly? Visit the <a href="contact.php" class="text-brand-blue font-semibold hover:underline">Contact page</a>.</p>
    </div>

  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
