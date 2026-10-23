<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: v_login.php");
    exit();
}

require_once '../Model/m_kategori.php';
$modelKategori = new m_kategori();

// Tangkap ID dari URL
$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: v_kelola_kategori.php");
    exit();
}

// Ambil data kategori berdasarkan ID untuk ditampilkan di form
$kategori = $modelKategori->getKategoriById($id);
if (!$kategori) {
    echo "<script>alert('Data kategori tidak ditemukan!'); window.location='v_kelola_kategori.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Kategori - Admin Sarpras</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center text-gray-800 p-6">

    <div class="max-w-md w-full bg-white rounded-2xl shadow-xs border border-gray-200 p-6 sm:p-8 space-y-6">

        <!-- Header Judul -->
        <div class="flex justify-between items-center border-b border-gray-100 pb-4">
            <h2 class="text-lg font-bold text-gray-800">Edit Kategori Sarpras</h2>
        </div>

        <!-- Form Edit Kategori -->
        <form action="../Controller/c_proses_kategori.php?action=update" method="POST" class="space-y-4">
            <!-- Kirim ID secara hidden agar proses update tahu baris mana yang diubah -->
            <input type="hidden" name="id_kategori" value="<?= (int) $kategori['id_kategori'] ?>">

            <!-- Nama Kategori -->
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nama Kategori</label>
                <input type="text" name="nama_kategori" value="<?= htmlspecialchars($kategori['nama_kategori'] ?? '') ?>" required
                    class="w-full text-xs bg-gray-50 border border-gray-200 rounded-lg p-2.5 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:bg-white transition">
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-2 space-y-2">
                <button type="submit" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg text-xs transition shadow-sm cursor-pointer">
                    Simpan Perubahan
                </button>
                <a href="v_kelola_kategori.php" class="block w-full py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium rounded-lg text-xs transition text-center">
                    Batal
                </a>
            </div>

        </form>

    </div>

</body>

</html>