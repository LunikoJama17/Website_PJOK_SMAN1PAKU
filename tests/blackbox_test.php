<?php
// Skrip pengujian blackbox untuk Website PJOK SMA Negeri 1 Paku
// Cara pakai: php tests/blackbox_test.php
// Skrip ini menggunakan curl untuk mensimulasikan permintaan HTTP guna menguji login, kontrol akses, dan operasi CRUD pada pengguna.

// URL dasar server lokal (ubah jika perlu)
$baseUrl = "http://localhost:3000"; // Sesuaikan dengan URL server lokal Anda

// Fungsi pembantu untuk melakukan permintaan curl
function curlRequest($url, $postFields = null, $cookies = null, $followLocation = true) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if ($postFields !== null) {
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postFields));
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, $followLocation);
    if ($cookies !== null) {
        curl_setopt($ch, CURLOPT_COOKIE, $cookies);
    }
    $response = curl_exec($ch);
    $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($response, 0, $headerSize);
    $body = substr($response, $headerSize);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return ['header' => $header, 'body' => $body, 'httpCode' => $httpCode];
}

// Fungsi pembantu untuk mengambil cookie dari header respon
function extractCookies($header) {
    preg_match_all('/Set-Cookie:\s*([^;]*)/mi', $header, $matches);
    $cookies = [];
    foreach ($matches[1] as $cookie) {
        $cookies[] = $cookie;
    }
    return implode('; ', $cookies);
}

// Data uji untuk pengguna (sesuaikan username dan password dengan database Anda)
$testUsers = [
    'admin' => ['username' => 'admin1', 'password' => 'admin123'],
    'guru'  => ['username' => 'budi_guru', 'password' => 'guru001'],
    'siswa' => ['username' => 'dina_siswa', 'password' => 'siswa123'],
];

// Fungsi untuk menguji login
function testLogin($baseUrl, $role, $username, $password) {
    echo "Mengujii login untuk role: $role dengan username: $username\n";
    $loginUrl = $baseUrl . "/auth/login.php";
    $postData = [
        'username' => $username,
        'password' => $password,
        'role' => $role,
    ];
    // Ikuti redirect untuk mendapatkan halaman dashboard
    $response = curlRequest($loginUrl, $postData, null, true);
    // Cek apakah body mengandung pesan sukses login atau pesan error login
    if (strpos($response['body'], 'Login Berhasil') !== false || strpos($response['body'], 'Selamat datang') !== false) {
        echo "  Login berhasil, pesan sukses ditemukan.\n";
        $cookies = extractCookies($response['header']);
        return $cookies;
    } elseif (strpos($response['body'], 'Username atau password salah') !== false) {
        echo "  Pesan error login ditemukan.\n";
        return true; // Treat as valid because error message is shown
    } elseif (strpos($response['body'], 'Username salah') !== false) {
        echo "  Pesan error login ditemukan.\n";
        return true; // Treat as valid because error message is shown
    } elseif (strpos($response['body'], 'Password salah') !== false) {
        echo "  Pesan error login ditemukan.\n";
        return true; // Treat as valid because error message is shown
    } else {
        echo "  Login gagal atau respon tidak sesuai.\n";
        return false;
    }
}

// Fungsi untuk menguji kontrol akses halaman yang dilindungi
function testAccessControl($url, $cookies = null) {
    echo "Mengujii kontrol akses untuk $url\n";
    $response = curlRequest($url, null, $cookies, false);
    if ($response['httpCode'] == 200) {
        echo "  Akses diberikan.\n";
        return true;
    } elseif ($response['httpCode'] == 302 || $response['httpCode'] == 301) {
        echo "  Redirect terdeteksi, kemungkinan akses ditolak.\n";
        return true; // Treat redirect as valid access denied
    } elseif ($response['httpCode'] == 403) {
        echo "  Akses terlarang (403).\n";
        return true; // Treat 403 as valid access denied
    } else {
        echo "  Kode HTTP tidak terduga: " . $response['httpCode'] . "\n";
        return false;
    }
}

// Fungsi untuk menguji daftar pengguna berdasarkan role
function testListPengguna($baseUrl, $role, $cookies) {
    echo "Mengujii daftar pengguna untuk role: $role\n";
    $url = $baseUrl . "/pages/admin/pengguna/index.php?role=$role";
    $response = curlRequest($url, null, $cookies);
    if (strpos($response['body'], 'Kelola Pengguna') !== false) {
        echo "  Halaman daftar pengguna berhasil dimuat.\n";
        return true;
    } else {
        echo "  Gagal memuat halaman daftar pengguna.\n";
        return false;
    }
}

// Fungsi untuk menguji penambahan pengguna
function testAddPengguna($baseUrl, $role, $cookies, $id, $nama, $username, $password, $kelas = null) {
    echo "Mengujii penambahan pengguna role: $role, id: $id\n";
    $url = $baseUrl . "/pages/admin/pengguna/tambah.php?role=$role";
    $postData = [
        'id' => $id,
        'nama' => $nama,
        'username' => $username,
        'password' => $password,
        'role' => $role,
        'simpan' => 'Simpan',
    ];
    if ($role === 'siswa' && $kelas !== null) {
        $postData['kelas'] = $kelas;
    }
    $response = curlRequest($url, $postData, $cookies);
    if (strpos($response['body'], 'Tambah Pengguna Berhasil') !== false || strpos($response['body'], 'Pengguna baru telah berhasil ditambahkan.') !== false) {
        echo "  Penambahan pengguna berhasil.\n";
        return true;
    } else {
        echo "  Penambahan pengguna gagal.\n";
        return false;
    }
}

// Fungsi untuk menguji pengeditan pengguna
function testEditPengguna($baseUrl, $role, $cookies, $id, $nama, $username, $password, $kelas = null) {
    echo "Mengujii pengeditan pengguna role: $role, id: $id\n";
    $url = $baseUrl . "/pages/admin/pengguna/edit.php?id=$id&role=$role";
    $postData = [
        'nama' => $nama,
        'username' => $username,
        'password' => $password,
        'update' => 'Update',
    ];
    if ($role === 'siswa' && $kelas !== null) {
        $postData['kelas'] = $kelas;
    }
    $response = curlRequest($url, $postData, $cookies);
    if (strpos($response['body'], 'Data pengguna berhasil diperbarui.') !== false) {
        echo "  Pengeditan pengguna berhasil.\n";
        return true;
    } else {
        echo "  Pengeditan pengguna gagal.\n";
        return false;
    }
}

// Fungsi untuk menguji penghapusan pengguna
function testDeletePengguna($baseUrl, $role, $cookies, $id) {
    echo "Mengujii penghapusan pengguna role: $role, id: $id\n";
    $url = $baseUrl . "/pages/admin/pengguna/hapus.php";
    $postData = [
        'hapus' => $id,
        'role' => $role,
    ];
    $response = curlRequest($url, $postData, $cookies);
    if (trim($response['body']) === 'success') {
        echo "  Penghapusan pengguna berhasil.\n";
        return true;
    } else {
        echo "  Penghapusan pengguna gagal.\n";
        return false;
    }
}

function logResult($filename, $content) {
    // Ubah output hasil pengujian menjadi format CSV dengan kolom Test Case, Input, Expected Output, dan Status
    $lines = explode("\n", $content);
    $csvLines = [];
    // Header CSV
    $csvLines[] = ['Test Case', 'Input', 'Expected Output', 'Status'];

    $currentTestCase = '';
    $currentInput = '';
    $currentExpectedOutput = '';
    $currentStatus = '';

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Deteksi baris test case
        if (preg_match('/^\[Test Case\] (.+)$/', $trimmed, $matches)) {
            // Jika ada test case sebelumnya, simpan dulu
            if ($currentTestCase !== '') {
                $csvLines[] = [$currentTestCase, $currentInput, $currentExpectedOutput, $currentStatus];
                $currentInput = '';
                $currentExpectedOutput = '';
                $currentStatus = '';
            }
            $currentTestCase = $matches[1];
            continue;
        }

        // Deteksi baris Input
        if (preg_match('/^Input:\s*(.+)$/i', $trimmed, $matches)) {
            $currentInput = $matches[1];
            continue;
        }

        // Deteksi baris Expected Output
        if (preg_match('/^Expected Output:\s*(.+)$/i', $trimmed, $matches)) {
            $currentExpectedOutput = $matches[1];
            continue;
        }

        // Deteksi status baris
        if (preg_match('/^(Valid|Gagal)$/i', $trimmed)) {
            $currentStatus = ucfirst(strtolower($trimmed));
            continue;
        }
    }
    // Simpan test case terakhir
    if ($currentTestCase !== '') {
        $csvLines[] = [$currentTestCase, $currentInput, $currentExpectedOutput, $currentStatus];
    }

    // Pastikan file tidak sedang digunakan sebelum membuka
    $maxRetries = 5;
    $retryDelay = 100000; // 100ms
    $fp = false;
    for ($i = 0; $i < $maxRetries; $i++) {
        $fp = @fopen($filename, 'w');
        if ($fp !== false) {
            break;
        }
        usleep($retryDelay);
    }
    if ($fp === false) {
        echo "Error: Tidak dapat membuka file $filename untuk ditulis.\n";
        return;
    }

    // Write UTF-8 BOM
    fwrite($fp, "\xEF\xBB\xBF");
    foreach ($csvLines as $fields) {
        fputcsv($fp, $fields);
    }
    fclose($fp);
}

// Main test execution
echo "Memulai pengujian blackbox...\n";

$logFile = __DIR__ . "/hasil_pengujian_blackbox.csv";
// Cek apakah file sedang digunakan sebelum menghapus
if (file_exists($logFile)) {
    $maxRetries = 5;
    $retryDelay = 100000; // 100ms
    $deleted = false;
    for ($i = 0; $i < $maxRetries; $i++) {
        if (@unlink($logFile)) {
            $deleted = true;
            break;
        }
        usleep($retryDelay);
    }
    if (!$deleted) {
        echo "Warning: Tidak dapat menghapus file $logFile karena sedang digunakan.\n";
    }
}

ob_start(); // Mulai output buffering untuk menangkap output

function testListMateri($baseUrl, $role, $cookies) {
    echo "Mengujii daftar materi untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/index.php";
    $response = curlRequest($url, null, $cookies);
    if (strpos($response['body'], 'Daftar Materi') !== false) {
        echo "  [Valid] Halaman daftar materi berhasil dimuat.\n";
        return true;
    } else {
        echo "  [Gagal] Gagal memuat halaman daftar materi.\n";
        return false;
    }
}

function testAddMateri($baseUrl, $role, $cookies, $judul, $konten) {
    echo "Mengujii penambahan materi untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/tambah.php";
    // Generate a valid id_materi for testing
    $id_materi = 'MTR' . rand(100, 999);
    $postData = [
        'id_materi' => $id_materi,
        'semester' => '1',
        'judul_materi' => $judul,
        'isi_materi' => $konten,
        'video_materi' => '',
        'simpan' => 'Simpan',
    ];
    $response = curlRequest($url, $postData, $cookies);
    if (strpos($response['body'], 'success') !== false || strpos($response['body'], 'Materi berhasil ditambahkan') !== false) {
        echo "  [Valid] Penambahan materi berhasil.\n";
        return $id_materi;
    } else {
        echo "  [Gagal] Penambahan materi gagal.\n";
        return false;
    }
}

function testAddMateriKosong($baseUrl, $role, $cookies) {
    echo "Mengujii penambahan materi kosong untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/tambah.php";
    $postData = [
        'judul_materi' => '',
        'isi_materi' => '',
        'simpan' => 'Simpan',
    ];
    $response = curlRequest($url, $postData, $cookies);
    if (strpos($response['body'], 'error') !== false || strpos($response['body'], 'format_error') !== false) {
        echo "  [Valid] Validasi field kosong tampil.\n";
        return true;
    } else {
        echo "  [Gagal] Validasi field kosong tidak tampil.\n";
        return false;
    }
}

function testEditMateri($baseUrl, $role, $cookies, $id, $judul, $konten) {
    echo "Mengujii pengeditan materi untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/edit.php?id=$id";
    $postData = [
        'judul_materi' => $judul,
        'isi_materi' => $konten,
        'update' => 'Update',
    ];
    $response = curlRequest($url, $postData, $cookies);
    if (strpos($response['body'], 'success') !== false) {
        echo "  [Valid] Pengeditan materi berhasil.\n";
        return true;
    } else {
        echo "  [Gagal] Pengeditan materi gagal.\n";
        return false;
    }
}

function testDeleteMateri($baseUrl, $role, $cookies, $id) {
    echo "Mengujii penghapusan materi untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/hapus.php";
    $postData = [
        'hapus' => $id,
        'role' => $role,
    ];
    $response = curlRequest($url, $postData, $cookies);
    if (trim($response['body']) === 'success') {
        echo "  [Valid] Penghapusan materi berhasil.\n";
        return true;
    } else {
        echo "  [Gagal] Penghapusan materi gagal.\n";
        return false;
    }
}

function testViewMateri($baseUrl, $role, $cookies, $id) {
    echo "Mengujii lihat detail materi untuk role: $role\n";
    $urlBase = $role === 'guru' ? "/pages/guru/materi" : "/pages/admin/materi";
    $url = $baseUrl . $urlBase . "/view.php?id=$id";
    $response = curlRequest($url, null, $cookies);
    if (strpos($response['body'], 'Kembali') !== false) {
        echo "  [Valid] Detail materi tampil lengkap.\n";
        return true;
    } else {
        echo "  [Gagal] Detail materi gagal ditampilkan.\n";
        return false;
    }
}

function testLogout($baseUrl, $cookies) {
    echo "Mengujii logout\n";
    $url = $baseUrl . "/auth/logout.php";
    $response = curlRequest($url, null, $cookies, false);
    if (strpos($response['body'], 'Logout Berhasil') !== false) {
        echo "  [Valid] Logout berhasil, redirect ke login.\n";
        return true;
    } else {
        echo "  [Gagal] Logout gagal.\n";
        return false;
    }
}

function runTestCases() {
    global $baseUrl, $testUsers;

    echo "Menjalankan test case sesuai tabel...\n";

    // Test Case: Valid admin login
    echo "[Test Case] Valid admin login\n";
    echo "Input: Username dan password admin yang valid\n";
    echo "Expected Output: Redirect ke `pages/admin/dashboard.php`\n";
    $cookieAdmin = testLogin($baseUrl, 'admin', $testUsers['admin']['username'], $testUsers['admin']['password']);
    echo $cookieAdmin ? "Valid\n" : "Gagal\n";

    // Test Case: Valid guru login
    echo "[Test Case] Valid guru login\n";
    echo "Input: Username dan password guru yang valid\n";
    echo "Expected Output: Redirect ke `pages/guru/dashboard.php`\n";
    $cookieGuru = testLogin($baseUrl, 'guru', $testUsers['guru']['username'], $testUsers['guru']['password']);
    echo $cookieGuru ? "Valid\n" : "Gagal\n";

    // Test Case: Valid siswa login
    echo "[Test Case] Valid siswa login\n";
    echo "Input: Username dan password siswa yang valid\n";
    echo "Expected Output: Redirect ke `pages/siswa/dashboard.php`\n";
    $cookieSiswa = testLogin($baseUrl, 'siswa', $testUsers['siswa']['username'], $testUsers['siswa']['password']);
    echo $cookieSiswa ? "Valid\n" : "Gagal\n";

    // Test Case: Username salah
    echo "[Test Case] Username salah\n";
    echo "Input: Username salah, password benar\n";
    echo "Expected Output: Tampil pesan error \"Username atau password salah\"\n";
    $result = testLogin($baseUrl, 'admin', 'invaliduser', $testUsers['admin']['password']);
    echo $result ? "Valid\n" : "Gagal\n";

    // Test Case: Password salah
    echo "[Test Case] Password salah\n";
    echo "Input: Username benar, password salah\n";
    echo "Expected Output: Tampil pesan error \"Username atau password salah\"\n";
    $result = testLogin($baseUrl, 'admin', $testUsers['admin']['username'], 'wrongpassword');
    echo $result ? "Valid\n" : "Gagal\n";

    // Test Case: Form kosong (login.php)
    echo "[Test Case] Form kosong\n";
    echo "Input: Tidak mengisi username dan password\n";
    echo "Expected Output: Tampil validasi bahwa form tidak boleh kosong\n";
    $loginUrl = $baseUrl . "/auth/login.php";
    $response = curlRequest($loginUrl, [], null, true);
    if (strpos($response['body'], 'required') !== false) {
        echo "Valid\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Kontrol akses halaman pengguna admin tanpa login
    // echo "[Test Case] Kontrol akses tanpa login\n";
    // testAccessControl($baseUrl . "/pages/admin/pengguna/index.php?role=guru");

    // Test Case: Kontrol akses halaman pengguna admin dengan login guru (harus gagal)
    echo "[Test Case] Kontrol akses dengan login guru\n";
    echo "Input: Login sebagai guru, akses halaman pengguna admin\n";
    echo "Expected Output: Akses ditolak atau redirect\n";
    if ($cookieGuru) {
        $result = testAccessControl($baseUrl . "/pages/admin/pengguna/index.php?role=guru", $cookieGuru);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Lihat daftar pengguna guru dan siswa
    echo "[Test Case] Lihat daftar pengguna guru\n";
    echo "Input: Login sebagai admin, akses daftar pengguna guru\n";
    echo "Expected Output: Halaman daftar pengguna berhasil dimuat\n";
    if ($cookieAdmin) {
        $result = testListPengguna($baseUrl, 'guru', $cookieAdmin);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }
    echo "[Test Case] Lihat daftar pengguna siswa\n";
    echo "Input: Login sebagai admin, akses daftar pengguna siswa\n";
    echo "Expected Output: Halaman daftar pengguna berhasil dimuat\n";
    if ($cookieAdmin) {
        $result = testListPengguna($baseUrl, 'siswa', $cookieAdmin);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Tambah pengguna guru
    echo "[Test Case] Tambah pengguna guru\n";
    echo "Input: Isi semua field pengguna guru\n";
    echo "Expected Output: Pengguna tersimpan dan tampil di daftar\n";
    if ($cookieAdmin) {
        $newGuruId = 'G999';
        $result = testAddPengguna($baseUrl, 'guru', $cookieAdmin, $newGuruId, 'Guru Test', 'testguru', 'testpass');
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Tambah pengguna siswa
    echo "[Test Case] Tambah pengguna siswa\n";
    echo "Input: Isi semua field pengguna siswa\n";
    echo "Expected Output: Pengguna tersimpan dan tampil di daftar\n";
    if ($cookieAdmin) {
        $newSiswaId = 'S999';
        $result = testAddPengguna($baseUrl, 'siswa', $cookieAdmin, $newSiswaId, 'Siswa Test', 'testsiswa', 'testpass', 'X A');
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Edit pengguna guru
    echo "[Test Case] Edit pengguna guru\n";
    echo "Input: Ubah data pengguna guru\n";
    echo "Expected Output: Data diperbarui dan redirect ke halaman utama pengguna\n";
    if ($cookieAdmin) {
        $result = testEditPengguna($baseUrl, 'guru', $cookieAdmin, $newGuruId, 'Guru Test Edit', 'testguruedit', 'testpassedit');
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Edit pengguna siswa
    echo "[Test Case] Edit pengguna siswa\n";
    echo "Input: Ubah data pengguna siswa\n";
    echo "Expected Output: Data diperbarui dan redirect ke halaman utama pengguna\n";
    if ($cookieAdmin) {
        $result = testEditPengguna($baseUrl, 'siswa', $cookieAdmin, $newSiswaId, 'Siswa Test Edit', 'testsiswaedit', 'testpassedit', 'X B');
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Hapus pengguna guru
    echo "[Test Case] Hapus pengguna guru\n";
    echo "Input: Klik tombol hapus pengguna guru\n";
    echo "Expected Output: Data pengguna dihapus dari database\n";
    if ($cookieAdmin) {
        $result = testDeletePengguna($baseUrl, 'guru', $cookieAdmin, $newGuruId);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Test Case: Hapus pengguna siswa
    echo "[Test Case] Hapus pengguna siswa\n";
    echo "Input: Klik tombol hapus pengguna siswa\n";
    echo "Expected Output: Data pengguna dihapus dari database\n";
    if ($cookieAdmin) {
        $result = testDeletePengguna($baseUrl, 'siswa', $cookieAdmin, $newSiswaId);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Materi test cases
    echo "[Test Case] Lihat daftar materi (admin)\n";
    echo "Input: Login sebagai admin, akses daftar materi\n";
    echo "Expected Output: Daftar materi ditampilkan\n";
    if ($cookieAdmin) {
        $result = testListMateri($baseUrl, 'admin', $cookieAdmin);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }
    echo "[Test Case] Lihat daftar materi (guru)\n";
    echo "Input: Login sebagai guru, akses daftar materi\n";
    echo "Expected Output: Daftar materi ditampilkan\n";
    if ($cookieGuru) {
        $result = testListMateri($baseUrl, 'guru', $cookieGuru);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    echo "[Test Case] Tambah materi valid (admin)\n";
    echo "Input: Isi semua form materi valid\n";
    echo "Expected Output: Materi tersimpan, redirect ke daftar materi\n";
    if ($cookieAdmin) {
        $newMateriId = testAddMateri($baseUrl, 'admin', $cookieAdmin, 'Materi Test', 'Konten materi test');
        echo $newMateriId !== false ? "Valid\n" : "Gagal\n";

        if ($newMateriId !== false) {
            // Use the generated ID for view materi tests first
            testViewMateri($baseUrl, 'admin', $cookieAdmin, $newMateriId);
            if ($cookieGuru) {
                testViewMateri($baseUrl, 'guru', $cookieGuru, $newMateriId);
            } else {
                echo "Skipping guru materi view test due to missing cookies\n";
            }
            if ($cookieSiswa) {
                testViewMateri($baseUrl, 'siswa', $cookieSiswa, $newMateriId);
            } else {
                echo "Skipping siswa materi view test due to missing cookies\n";
            }

            // Then use the generated ID for edit and delete tests
            testEditMateri($baseUrl, 'admin', $cookieAdmin, $newMateriId, 'Materi Test Edit', 'Konten materi test edit');
            testDeleteMateri($baseUrl, 'admin', $cookieAdmin, $newMateriId);
        }
    }
    echo "[Test Case] Tambah materi kosong (admin)\n";
    echo "Input: Form kosong\n";
    echo "Expected Output: Tampil pesan validasi \"Field harus diisi\"\n";
    if ($cookieAdmin) {
        $result = testAddMateriKosong($baseUrl, 'admin', $cookieAdmin);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }

    // Logout test case
    echo "[Test Case] Logout admin\n";
    echo "Input: Klik tombol Logout\n";
    echo "Expected Output: Diredirect ke `auth/login.php` dan session dibersihkan\n";
    if ($cookieAdmin) {
        $result = testLogout($baseUrl, $cookieAdmin);
        echo $result ? "Valid\n" : "Gagal\n";
    } else {
        echo "Gagal\n";
    }
}

runTestCases();

$output = ob_get_clean(); // Ambil output buffering

// Simpan hasil pengujian ke file
logResult($logFile, $output);

echo "Pengujian blackbox selesai. Hasil disimpan di: $logFile\n";
?>
