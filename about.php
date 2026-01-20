<?php
$page_title = 'P&J Tenarte Dental Clinic - About';
$active = 'about';
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-20 bg-white">
  <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
    <h2 class="text-3xl font-bold text-slate-900 mb-6">About P&J Tenarte Dental Clinic</h2>
    <p class="text-gray-600 mb-12 leading-relaxed">
      At P&J Tenarte Dental Clinic, we believe that everyone deserves a healthy, confident smile.
      Our clinic was founded on the principles of compassionate care, professional excellence, and
      continuous innovation in dental health.
    </p>

    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm mb-12">
      <h3 class="text-xl font-bold text-slate-800 mb-4">Our Mission</h3>
      <p class="text-gray-600">
        We are committed to providing exceptional dental care in a warm, welcoming environment.
        By combining the latest technology with personalized attention, we ensure that every patient
        receives the highest quality treatment tailored to their individual needs.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-left">
      <div class="flex items-start gap-4 p-4 border rounded-xl hover:shadow-md transition">
        <div class="text-brand-blue mt-1"><i class="fa-regular fa-circle-check text-xl"></i></div>
        <div>
          <h4 class="font-semibold text-slate-900">Passion for Wellness</h4>
          <p class="text-sm text-gray-500">Established with a passion for dental wellness.</p>
        </div>
      </div>
      <div class="flex items-start gap-4 p-4 border rounded-xl hover:shadow-md transition">
        <div class="text-brand-blue mt-1"><i class="fa-regular fa-circle-check text-xl"></i></div>
        <div>
          <h4 class="font-semibold text-slate-900">Patient Comfort</h4>
          <p class="text-sm text-gray-500">Led by professionals dedicated to patient comfort.</p>
        </div>
      </div>
      <div class="flex items-start gap-4 p-4 border rounded-xl hover:shadow-md transition">
        <div class="text-brand-blue mt-1"><i class="fa-regular fa-circle-check text-xl"></i></div>
        <div>
          <h4 class="font-semibold text-slate-900">Digital Innovation</h4>
          <p class="text-sm text-gray-500">Empowering smiles through digital dental innovation.</p>
        </div>
      </div>
      <div class="flex items-start gap-4 p-4 border rounded-xl hover:shadow-md transition">
        <div class="text-brand-blue mt-1"><i class="fa-regular fa-circle-check text-xl"></i></div>
        <div>
          <h4 class="font-semibold text-slate-900">Modern Equipment</h4>
          <p class="text-sm text-gray-500">Modern equipment and advanced diagnostic tools.</p>
        </div>
      </div>
    </div>

    <div class="mt-14">
      <a href="services.php" class="inline-flex items-center gap-2 bg-brand-blue text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition shadow-sm">
        Explore Services <i class="fa-solid fa-arrow-right"></i>
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
