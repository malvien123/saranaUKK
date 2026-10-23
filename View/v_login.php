<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika sudah login, langsung arahkan ke halaman sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: v_admin.php");
        exit();
    } else if ($_SESSION['role'] === 'siswa') {
        header("Location: v_siswa.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Sarana Prasarana</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-slate-100 flex justify-center items-center min-h-screen">

    <div class="bg-white p-8 sm:p-10 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        
        <!-- Header / Logo Title -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-14 h-14 bg-blue-600 text-white rounded-xl shadow-md mb-4 text-2xl font-bold">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <h2 class="text-2xl font-bold text-gray-800 tracking-tight">Login Pengaduan</h2>
            <p class="text-sm text-gray-500 mt-1">Pengaduan Sarana & Prasarana Sekolah</p>
        </div>

        <!-- TAMPILAN ALERT LOGIN GAGAL -->
        <?php if (isset($_SESSION['error_login'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6 text-sm flex items-start gap-3 animate-pulse">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <div>
                    <span class="block sm:inline font-medium"><?= htmlspecialchars($_SESSION['error_login']) ?></span>
                </div>
            </div>
            <?php unset($_SESSION['error_login']); // Hapus session agar tidak muncul terus saat refresh ?>
        <?php endif; ?>
        <!-- AKHIR ALERT LOGIN GAGAL -->

        <!-- Form Login -->
        <form action="../Controller/c_proses_login.php" method="POST" class="space-y-5">
            <div>
                <label for="username" class="block mb-2 text-sm font-semibold text-gray-700">Username / NIS</label>
                <input type="text" id="username" name="username" placeholder="Masukkan Username / NIS" required autocomplete="off"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition duration-200">
            </div>

            <div>
                <label for="password" class="block mb-2 text-sm font-semibold text-gray-700">Password</label>
                <input type="password" id="password" name="password" placeholder="Masukkan Password" required
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-300 rounded-lg text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition duration-200">
            </div>
            
            <button type="submit" 
                class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition duration-200 cursor-pointer text-sm tracking-wide">
                Masuk ke Sistem
            </button>
        </form>

        <!-- Footer Card -->
        <div class="text-center mt-6">
            <p class="text-xs text-gray-400">&copy; <?= date('Y'); ?> Manajemen Sarpras Sekolah</p>
        </div>

    </div>

</body>

</html>