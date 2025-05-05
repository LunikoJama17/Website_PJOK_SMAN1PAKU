<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$guru_name = isset($_SESSION['nama']) ? $_SESSION['nama'] : 'Guru';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Guru</title>
  <link rel="icon" type="image/png" href="https://disdikbud.banyuasinkab.go.id/wp-content/uploads/sites/269/2022/11/Logo-Tut-Wuri-Handayani-PNG-Warna.png">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- Tailwind Config: kustom warna/font jika perlu -->
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: '#1D4ED8', // biru indigo
            secondary: '#F59E0B', // kuning
          },
        },
      },
    }
  </script>

  <!-- SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-gray-100 font-sans text-gray-800">

<!-- Navbar -->
<nav class="bg-indigo-600 shadow-sm mb-6 sticky top-0 z-50">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="flex justify-between items-center py-4">
      <!-- Judul dengan ikon gambar -->
<a href="/pages/siswa/dashboard.php" class="flex items-center gap-2 text-xl font-bold text-white hover:text-indigo-200 transition">
  <img src="https://disdikbud.banyuasinkab.go.id/wp-content/uploads/sites/269/2022/11/cropped-Logo-Tut-Wuri-Handayani-PNG-Warna-1.png" alt="Logo Tut Wuri" class="w-6 h-6 object-contain">
  Dashboard
</a>

      <!-- Hamburger (Mobile) -->
      <button id="mobile-menu-button"
              class="md:hidden relative z-50 w-10 h-10 text-white flex items-center justify-center px-2 py-1 rounded-md hover:bg-white hover:text-indigo-600 transition">
        <svg id="hamburger-icon" class="w-6 h-6" fill="none" stroke="currentColor"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M4 6h16M4 12h16M4 18h16"/></svg>
        <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M6 18L18 6M6 6l12 12"/></svg>
      </button>

      <!-- Desktop Menu -->
      <div class="hidden md:flex items-center space-x-4 ml-auto">
        <a href="/pages/guru/materi/index.php"
           class="text-white px-4 py-2 rounded-md hover:bg-green-500 hover:text-white transition">Kelola Materi</a>

        <!-- Profile Button -->
        <button id="profile-popup-button"
                class="flex items-center text-white hover:bg-white hover:text-indigo-600 px-3 py-2 rounded-md transition">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
               viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M5.121 17.804A9.935 9.935 0 0112 15c2.21 0 4.243.72 5.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
          </svg>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile Menu -->
<div id="mobile-menu"
     class="fixed top-12 right-0 h-full w-1/2 bg-indigo-600 bg-opacity-85 z-40 transform translate-x-full transition-transform duration-300 md:hidden pt-6">
  <div class="flex flex-col px-4 space-y-4">
    <a href="/pages/guru/materi/index.php"
       class="text-white px-4 py-2 rounded-md hover:bg-green-500 hover:text-white transition">Kelola Materi</a>
    
    <!-- Profil Guru -->
    <div class="bg-white bg-opacity-10 rounded-md p-3 text-white">
      <p class="font-semibold"><?php echo htmlspecialchars($guru_name); ?></p>
      <p class="text-sm text-gray-200">Guru</p>
      <a href="/auth/logout.php" class="block mt-2 text-red-400 hover:text-red-600">Logout</a>
    </div>
  </div>
</div>

<!-- Profile Pop-Up Modal -->
<div id="profile-popup"
     class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
  <div class="bg-white rounded-lg w-80 p-6 relative shadow-xl">
    <!-- Close Button -->
    <button id="close-profile-popup"
            class="absolute top-2 right-2 text-gray-500 hover:text-red-500 transition">
      <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
           viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="M6 18L18 6M6 6l12 12" />
      </svg>
    </button>

    <!-- Content -->
    <h2 class="text-center text-lg font-bold mb-2 text-gray-800">Profil</h2>
    <p class="text-center mb-1 font-semibold">
      <?php echo htmlspecialchars($guru_name); ?>
    </p>
    <p class="text-center mb-4 font-semibold">Guru</p>
    <a href="/auth/logout.php"
       class="block w-full text-center bg-red-500 text-white py-2 rounded-md hover:bg-red-600 transition">
      Logout
    </a>
  </div>
</div>

<!-- JavaScript -->
<script>
  const menu = document.getElementById('mobile-menu');
  const menuBtn = document.getElementById('mobile-menu-button');
  const hamburgerIcon = document.getElementById('hamburger-icon');
  const closeIcon = document.getElementById('close-icon');

  const profileBtn = document.getElementById('profile-popup-button');
  const profilePopup = document.getElementById('profile-popup');
  const closePopup = document.getElementById('close-profile-popup');

  // Hamburger Menu Toggle
  menuBtn.addEventListener('click', () => {
    menu.classList.toggle('translate-x-full');
    menu.classList.toggle('translate-x-0');
    hamburgerIcon.classList.toggle('hidden');
    closeIcon.classList.toggle('hidden');
  });

  // Profile Modal Toggle
  profileBtn.addEventListener('click', () => {
    profilePopup.classList.remove('hidden');
  });

  closePopup.addEventListener('click', () => {
    profilePopup.classList.add('hidden');
  });

  // Close profile modal when clicking outside
  window.addEventListener('click', (e) => {
    if (e.target === profilePopup) {
      profilePopup.classList.add('hidden');
    }
  });

  // Auto close mobile menu when clicking a link
  const mobileLinks = document.querySelectorAll('#mobile-menu a');
  mobileLinks.forEach(link => {
    link.addEventListener('click', () => {
      menu.classList.add('translate-x-full');
      hamburgerIcon.classList.remove('hidden');
      closeIcon.classList.add('hidden');
    });
  });
</script>
