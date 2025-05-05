<?php
session_start();
require_once "../database.php";
include "../includes/alerts.php";

function checkLogin($conn, $role, $username, $password) {
    if ($role == 'admin') {
        $query = mysqli_query($conn, "SELECT * FROM admin WHERE username_admin='$username'");
        $user = mysqli_fetch_assoc($query);
        if ($user && $password == $user['password_admin']) {
            $_SESSION['id'] = $user['id_admin'];
            $_SESSION['role'] = 'admin';
            $_SESSION['nama'] = $user['nama_admin'];
            showSweetAlert('success', 'Login Berhasil', 'Selamat datang ' . $user['nama_admin'], '../pages/admin/dashboard.php');
            exit();
        }
    } elseif ($role == 'guru') {
        $query = mysqli_query($conn, "SELECT * FROM guru WHERE username_guru='$username'");
        $user = mysqli_fetch_assoc($query);
        if ($user && $password == $user['password_guru']) {
            $_SESSION['id'] = $user['id_guru'];
            $_SESSION['role'] = 'guru';
            $_SESSION['nama'] = $user['nama_guru'];
            showSweetAlert('success', 'Login Berhasil', 'Selamat datang ' . $user['nama_guru'], '../pages/guru/dashboard.php');
            exit();
        }
    } elseif ($role == 'siswa') {
        $query = mysqli_query($conn, "SELECT * FROM siswa WHERE username_siswa='$username'");
        $user = mysqli_fetch_assoc($query);
        if ($user && $password == $user['password_siswa']) {
            $_SESSION['id'] = $user['id_siswa'];
            $_SESSION['role'] = 'siswa';
            $_SESSION['nama'] = $user['nama_siswa'];
            $_SESSION['kelas'] = $user['kelas'];
            showSweetAlert('success', 'Login Berhasil', 'Selamat datang ' . $user['nama_siswa'], '../pages/siswa/dashboard.php');
            exit();
        }
    }

    showSweetAlert('error', 'Login Gagal', 'Username atau Password salah.', 'login.php');
}

if (isset($_SESSION['role'])) {
    header("Location: ../pages/" . $_SESSION['role'] . "/dashboard.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    checkLogin($conn, $role, $username, $password);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login | SMA Negeri 1 Paku</title>
  <link rel="icon" type="image/png" href="https://disdikbud.banyuasinkab.go.id/wp-content/uploads/sites/269/2022/11/Logo-Tut-Wuri-Handayani-PNG-Warna.png">
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    /* Tambahan animasi dengan Tailwind (jika tidak pakai plugin) */
    .fade-in {
      opacity: 0;
      transform: translateY(20px);
      animation: fadeInUp 0.8s ease-out forwards;
    }

    @keyframes fadeInUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>

<body class="min-h-screen flex items-center justify-center relative bg-fixed bg-cover bg-center" style="background-image: url('/assets/images/SMAN 1 PAKU.jpg');">
  <div class="absolute inset-0 bg-black/50 z-0"></div>

  <div class="relative z-10 w-[90%] max-w-sm p-8 rounded-2xl backdrop-blur-md bg-white/20 shadow-lg flex flex-col items-center fade-in">
    <div class="w-24 h-24 rounded-full overflow-hidden bg-white flex items-center justify-center mb-6">
      <img src="https://disdikbud.banyuasinkab.go.id/wp-content/uploads/sites/269/2022/11/cropped-Logo-Tut-Wuri-Handayani-PNG-Warna-1.png"
           alt="Tut Wuri Handayani" class="object-cover w-full h-full">
    </div>

    <h2 class="text-center text-2xl font-semibold text-black mb-6">Login Website PJOK SMA Negeri 1 Paku</h2>

    <form action="login.php" method="POST" class="w-full">
      <div class="mb-4">
        <input type="text" name="username" placeholder="Username"
          class="w-full px-4 py-2 rounded-lg bg-white/60 placeholder-gray-500 text-black focus:outline-none focus:ring-2 focus:ring-blue-300" required>
      </div>

      <div class="mb-4 relative">
        <input type="password" name="password" id="password"
          placeholder="Password"
          class="w-full px-4 py-2 rounded-lg bg-white/60 placeholder-gray-500 text-black focus:outline-none focus:ring-2 focus:ring-blue-300 pr-10" required>
        <button type="button" onclick="togglePassword()" class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-600 hover:text-black focus:outline-none">
          <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />
          </svg>
        </button>
      </div>

      <div class="mb-6">
        <select name="role"
          class="w-full px-4 py-2 rounded-lg bg-white/60 text-black placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-300 hover:bg-blue-200 transition duration-300 ease-in-out" required>
          <option value="" disabled selected hidden>Login Sebagai</option>
          <option value="admin">Admin</option>
          <option value="guru">Guru</option>
          <option value="siswa">Siswa</option>
        </select>
      </div>

      <button type="submit"
        class="w-full py-2 rounded-lg bg-blue-500 hover:bg-blue-600 transition-colors duration-300 text-white font-semibold">
        Login
      </button>
    </form>
  </div>

  <!-- Blur Transparan Bagian Bawah -->
  <div class="fixed bottom-0 left-0 w-full h-40 backdrop-blur-md bg-white/10 ring-1 ring-white/20 shadow-inner pointer-events-none z-0"></div>

  <!-- Toggle Password Script -->
  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      const eyeIcon = document.getElementById("eyeIcon");
      const isHidden = passwordInput.type === "password";

      passwordInput.type = isHidden ? "text" : "password";

      eyeIcon.innerHTML = isHidden
        ? `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.956 9.956 0 012.184-3.293m3.186-2.191A9.956 9.956 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-1.23 2.592M15 12a3 3 0 11-6 0 3 3 0 016 0zM3 3l18 18" />`
        : `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-.275.884-.67 1.718-1.175 2.478M15.5 16.5L19 20M9 16.5L5 20" />`;
    }
  </script>

  <footer id="page-footer" class="bg-transparent backdrop-blur text-white py-20">
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

    const observer = new ResizeObserver(adjustFooter);
    observer.observe(document.body);
    window.addEventListener('load', adjustFooter);
    window.addEventListener('resize', adjustFooter);
  </script>
</body>
</html>
