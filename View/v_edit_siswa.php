<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: v_login.php");
    exit();
}

require_once '../Model/m_siswa.php';
$m_siswa = new m_siswa();

// Ambil NIS dari URL
$nis = $_GET['nis'] ?? '';
$data_siswa = $m_siswa->getSiswaByNis($nis);

if (!$data_siswa) {
    header("Location: v_kelola_siswa.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Siswa - Sarana Prasarana</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center text-gray-800 p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xs border border-gray-200 p-6 sm:p-8 space-y-6">

        <!-- Header Judul -->
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h2 class="text-lg font-bold text-gray-800">Edit Data Siswa</h2>
        </div>

        <!-- Form Edit -->
        <form action="../Controller/c_siswa.php?action=update" method="POST" class="space-y-4">

            <!-- NIS (Readonly) -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">NIS (Nomor Induk Siswa)</label>
                <input type="text" name="nis" value="<?= htmlspecialchars($data_siswa['nis']) ?>" readonly
                    class="w-full text-xs bg-gray-100 border border-gray-200 rounded-lg p-2.5 text-gray-500 cursor-not-allowed focus:outline-none">
            </div>

            <!-- Nama Siswa -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Siswa</label>
                <input type="text" name="nama" value="<?= htmlspecialchars($data_siswa['nama']) ?>" required
                    pattern="[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+"
                    title="Nama hanya boleh berisi huruf besar, huruf kecil, dan spasi (tanpa angka atau tanda baca)."
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
                <p class="text-[10px] text-gray-400 italic mt-1">* Hanya boleh huruf dan spasi. Angka dan tanda baca dilarang.</p>
            </div>

            <!-- Kelas -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Kelas</label>
                <input type="text" name="kelas" value="<?= htmlspecialchars($data_siswa['kelas']) ?>" required
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Password Baru -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Password Baru <span class="text-gray-400 font-normal">(Opsional)</span></label>
                <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah"
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm cursor-pointer">
                    Simpan Perubahan
                </button>
                <a href="v_kelola_siswa.php" class="block w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-xs transition text-center">
                    Batal
                </a>
            </div>

        </form>

    </div>

</body>

</html>