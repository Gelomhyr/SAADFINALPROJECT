<?php
$page_title = 'P&J Tenarte Dental Clinic - Home';
$active = 'home';
require_once __DIR__ . '/includes/header.php';
?>

<!-- HERO SECTION -->
<section class="pt-4 pb-16 md:pt-8 md:pb-24 bg-brand-light">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
      <!-- Text Content -->
      <div>
        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-slate-900 leading-tight mb-6">
          Bright Smiles Start <br /> with <span class="text-brand-blue">Smart Care</span>
        </h1>
        <p class="text-lg text-gray-600 mb-8 max-w-lg">
          Trusted dental care powered by modern technology and professional service. We ensure your smile is healthy and confident.
        </p>
        <div class="flex flex-col sm:flex-row gap-4">
          <a href="appointment.php" class="bg-brand-blue text-white px-8 py-3 rounded-md font-semibold text-center hover:bg-blue-700 transition shadow-md">
            Book an Appointment
          </a>
          <a href="about.php" class="bg-white text-gray-700 border border-gray-300 px-8 py-3 rounded-md font-semibold text-center hover:bg-gray-50 transition">
            Learn More
          </a>
        </div>

        <div class="mt-10 grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="bg-white/70 backdrop-blur rounded-xl p-4 border border-white">
            <p class="font-bold text-slate-900">Modern Tools</p>
            <p class="text-sm text-gray-600">Digital diagnostics and patient-first tech.</p>
          </div>
          <div class="bg-white/70 backdrop-blur rounded-xl p-4 border border-white">
            <p class="font-bold text-slate-900">Gentle Care</p>
            <p class="text-sm text-gray-600">Comfort-focused procedures and guidance.</p>
          </div>
          <div class="bg-white/70 backdrop-blur rounded-xl p-4 border border-white">
            <p class="font-bold text-slate-900">Fast Booking</p>
            <p class="text-sm text-gray-600">Quick appointment request submission.</p>
          </div>
        </div>
      </div>

      <!-- Image -->
      <div class="relative">
        <img
          src="https://images.unsplash.com/photo-1606811841689-23dfddce3e95?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80"
          alt="Dentist and Patient"
          class="rounded-2xl shadow-xl w-full object-cover h-[400px] md:h-[500px]"
        />

        <!-- Decorative element -->
        <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-lg shadow-lg hidden md:block">
          <div class="flex items-center gap-3">
            <div class="bg-green-100 p-2 rounded-full text-green-600">
              <i class="fa-solid fa-star"></i>
            </div>
            <div>
              <p class="font-bold text-slate-900">4.9/5 Rating</p>
              <p class="text-xs text-gray-500">Based on patient reviews</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- QUICK LINKS -->
<section class="py-14 bg-white">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <a href="services.php" class="p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 bg-blue-100 text-brand-blue rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-tooth"></i>
          </div>
          <div>
            <p class="font-bold text-slate-900">View Services</p>
            <p class="text-sm text-gray-600">See what we offer.</p>
          </div>
        </div>
      </a>

      <a href="appointment.php" class="p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 bg-blue-100 text-brand-blue rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-calendar-check"></i>
          </div>
          <div>
            <p class="font-bold text-slate-900">Book Appointment</p>
            <p class="text-sm text-gray-600">Request a schedule.</p>
          </div>
        </div>
      </a>

      <a href="contact.php" class="p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition">
        <div class="flex items-center gap-3">
          <div class="w-11 h-11 bg-blue-100 text-brand-blue rounded-xl flex items-center justify-center">
            <i class="fa-solid fa-phone"></i>
          </div>
          <div>
            <p class="font-bold text-slate-900">Contact Us</p>
            <p class="text-sm text-gray-600">Ask questions anytime.</p>
          </div>
        </div>
      </a>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
