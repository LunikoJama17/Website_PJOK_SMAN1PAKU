<body class="relative min-h-screen flex flex-col">
  <!-- Konten utama -->
  <main id="main-content" class="p-6 pb-20">
    <div class="w-full">
      <!-- Tempatkan kontenmu di sini -->
    </div>
  </main>

  <!-- Footer -->
  <footer id="page-footer" class="bg-indigo-600 text-white py-6">
    <div class="max-w-7xl mx-auto px-6 text-center">
      <p>&copy; 2025 Website PJOK SMAN 1 Paku | Jonathan & Luniko Jama | Universitas Palangka Raya</p>
    </div>
  </footer>

  <script>
    function adjustFooter() {
      const footer = document.getElementById('page-footer');
      const bodyHeight = document.body.scrollHeight;
      const windowHeight = window.innerHeight;

      if (bodyHeight <= windowHeight) {
        footer.classList.add('absolute', 'bottom-0', 'left-0', 'w-full');
      } else {
        footer.classList.remove('absolute', 'bottom-0', 'left-0', 'w-full');
      }
    }

    // Gunakan ResizeObserver untuk pantau perubahan ukuran konten
    const observer = new ResizeObserver(adjustFooter);
    observer.observe(document.body);

    // Pastikan juga dijalankan setelah semua konten dimuat
    window.addEventListener('load', adjustFooter);
    window.addEventListener('resize', adjustFooter);
  </script>
</body>
