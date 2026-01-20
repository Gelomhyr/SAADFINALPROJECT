  </main>

  <!-- FOOTER -->
  <footer class="bg-brand-blue text-white py-6 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
      <div class="flex items-center gap-3">
        <!-- Footer logo: make background look transparent on blue using blend mode -->
        <img src="<?= asset_path('LOGO.png') ?>" alt="P&J Tenarte Dental Clinic Logo" class="h-12 w-auto object-contain" />
        <span class="text-sm">© <?= date('Y') ?> P&J Tenarte Dental Clinic. All rights reserved.</span>
      </div>

      <div class="flex gap-6 text-sm">
        <a href="#" class="hover:text-blue-200">Privacy Policy</a>
        <a href="#" class="hover:text-blue-200">Terms</a>
        <a href="<?= url_path('contact.php') ?>" class="hover:text-blue-200">Contact</a>
      </div>
    </div>
  </footer>
</body>
</html>
