<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: v_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Siswa - Sarana Prasarana</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center text-gray-800 p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xs border border-gray-200 p-6 sm:p-8 space-y-6">

        <!-- Header Judul -->
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h2 class="text-lg font-bold text-gray-800">Tambah Data Siswa Baru</h2>
        </div>

        <!-- Form Tambah -->
        <form action="../Controller/c_siswa.php?action=tambah" method="POST" class="space-y-4">

            <!-- NIS -->
            <!-- Contoh pada input NIS di Form Tambah Siswa / Login -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" required
                    maxlength="10" minlength="10"
                    pattern="[0-9]{10}"
                    title="NIS harus persis 10 digit angka"
                    placeholder="Masukkan 10 digit NIS"
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5">
            </div>

            <!-- Nama Siswa -->
            <div class="space-y-1.5">
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Nama Siswa</label>
                <input type="text"
                    name="nama"
                    required
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+"
                    title="Nama hanya boleh berisi huruf besar, huruf kecil, dan spasi (tanpa angka atau tanda baca)."
                    placeholder="Nama Lengkap (Contoh: Budi Santoso)"
                    class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 outline-none focus:bg-white focus:border-indigo-500 transition-all">
                <p class="text-[10px] text-slate-400 italic">* Hanya boleh huruf besar, huruf kecil, dan spasi. Angka dan tanda baca dilarang.</p>
            </div>

            <!-- Kelas -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kelas</label>
                <input type="text" name="kelas" required placeholder="Contoh: XII RPL 1"
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Password Login -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password Login</label>
                <input type="password" name="password" required placeholder="Masukkan Password"
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm cursor-pointer">
                    Simpan Data
                </button>
                <a href="v_kelola_siswa.php" class="block w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-xs transition text-center">
                    Batal
                </a>
            </div>

        </form>

    </div>

</body>

</html>