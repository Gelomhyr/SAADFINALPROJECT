<?php
$page_title = 'P&J Tenarte Dental Clinic - Services';
$active = 'services';
require_once __DIR__ . '/includes/header.php';
?>

<section class="py-20 bg-gray-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="text-center mb-16">
      <h2 class="text-3xl font-bold text-slate-900 mb-4">Our Dental Services</h2>
      <p class="text-gray-600 max-w-2xl mx-auto">
        Comprehensive dental care tailored to your needs, using modern technology and proven techniques.
      </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-solid fa-tooth text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Tooth Extraction</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          Quick, safe, and professional tooth removal with minimal discomfort and fast recovery.
        </p>
      </div>

      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-solid fa-wand-magic-sparkles text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Teeth Whitening</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          Achieve a brighter, whiter smile in just one visit with our advanced whitening treatments.
        </p>
      </div>

      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-regular fa-face-smile text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Oral Cleaning & Checkups</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          Regular preventive care to maintain optimal oral health for patients of all ages.
        </p>
      </div>

      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-solid fa-x-ray text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Digital X-ray Consultation</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          State-of-the-art diagnostic imaging for accurate treatment planning and monitoring.
        </p>
      </div>

      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-solid fa-crown text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Dental Restoration</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          Comprehensive restorative procedures including fillings, crowns, and bridges.
        </p>
      </div>

      <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition group">
        <div class="w-12 h-12 bg-blue-100 text-brand-blue rounded-lg flex items-center justify-center mb-6 group-hover:bg-brand-blue group-hover:text-white transition">
          <i class="fa-solid fa-heart-pulse text-xl"></i>
        </div>
        <h3 class="text-xl font-bold text-slate-900 mb-3">Emergency Dental Care</h3>
        <p class="text-gray-500 text-sm leading-relaxed">
          Prompt attention for urgent dental issues to relieve pain and prevent complications.
        </p>
      </div>
    </div>

    <div class="text-center mt-14">
      <a href="appointment.php" class="inline-flex items-center gap-2 bg-brand-blue text-white px-6 py-3 rounded-md font-semibold hover:bg-blue-700 transition shadow-sm">
        Book an Appointment <i class="fa-solid fa-calendar-check"></i>
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
