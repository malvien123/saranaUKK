<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Halaman: Pastikan hanya siswa yang bisa mengakses
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: v_login.php");
    exit();
}

$nis_login  = $_SESSION['nis'] ?? $_SESSION['username'] ?? '';
$nama_login = $_SESSION['nama'] ?? 'Siswa';

// Load model aspirasi untuk mengambil data kategori secara dinamis dari database
require_once '../Model/m_kategori.php';
$modelKategori = new m_kategori();
$list_kategori = $modelKategori->getAllKategori();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Aspirasi Siswa - Pengaduan Sarpras</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome untuk Ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>

<body class="bg-[#F8FAFC] font-sans min-h-screen flex flex-col md:flex-row text-slate-800 antialiased selection:bg-cyan-500 selection:text-white">

    <!-- OVERLAY SIDEBAR MOBILE -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-all duration-300 opacity-0"></div>

    <!-- SIDEBAR SISWA -->
    <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-50 h-screen w-72 bg-[#0F172A] text-slate-300 flex flex-col justify-between -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0 border-r border-slate-800/60 shadow-xl md:shadow-none">
        <div class="overflow-y-auto overflow-x-hidden">
            <!-- LOGO -->
            <div class="p-7 border-b border-white/5 flex items-center gap-4">
                <div class="bg-gradient-to-br from-blue-500 to-cyan-500 p-3 rounded-xl text-white font-bold shadow-lg shadow-cyan-500/30">
                    <i class="fa-solid fa-school text-lg"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-base tracking-wide">V-Dashboard</h1>
                    <p class="text-[11px] text-cyan-300 font-medium tracking-wider uppercase mt-0.5">Portal Siswa</p>
                </div>
            </div>

            <div class="px-6 py-4 text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-2">Menu Utama</div>

            <nav class="px-4 space-y-1.5 text-sm font-medium">
                <a href="v_dashboard_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-house-chimney text-base w-5 group-hover:text-white transition-colors"></i> Dashboard
                </a>
                <a href="v_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-pen-to-square text-base w-5"></i> <span class="font-bold">Form Aspirasi</span>
                </a>
                <a href="v_history_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5 group-hover:text-white transition-colors"></i> Pengaduan 
                </a>
                <a href="v_history_selesai_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-circle-check text-base w-5 group-hover:text-white transition-colors"></i> Riwayat 
                </a>
            </nav>
        </div>

        <!-- User Profile & Logout -->
        <div class="p-5 border-t border-white/5">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="w-10 h-10 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-cyan-400 font-bold shrink-0">
                    <?= strtoupper(substr($nama_login, 0, 1)) ?>
                </div>
                <div class="overflow-hidden">
                    <div class="text-white font-bold text-sm truncate"><?= htmlspecialchars($nama_login); ?></div>
                    <div class="text-[11px] text-slate-400 truncate">NIS: <?= htmlspecialchars($nis_login); ?></div>
                </div>
            </div>
            <a href="v_logout.php" onclick="return confirm('Apakah Anda yakin ingin keluar dari sistem?')" class="flex items-center justify-center gap-3 w-full px-4 py-3 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white border border-rose-500/20 transition-all text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-power-off"></i> Keluar Aplikasi
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0 min-h-screen relative">

        <!-- HEADER (GLASSMORPHISM) -->
        <header class="bg-white/70 backdrop-blur-lg border-b border-slate-200/60 px-6 py-4 flex justify-between items-center sticky top-0 z-30 transition-all">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden bg-white hover:bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 shadow-sm transition-all focus:ring-2 focus:ring-cyan-500/20">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Form Laporan</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Sampaikan kendala fasilitas sarana dan prasarana</p>
                </div>
            </div>

            <!-- Header Profile (Mobile Right) -->
            <div class="md:hidden flex items-center">
                <div class="bg-gradient-to-br from-cyan-500 to-blue-500 text-white font-bold w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-md border border-white/20">
                    <?= strtoupper(substr($nama_login, 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 max-w-4xl w-full mx-auto my-auto">

            <!-- NOTIFIKASI STATUS (Jika ada feedback dari Controller) -->
            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] === 'sukses'): ?>
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-start sm:items-center gap-4 shadow-sm animate-fade-in-down">
                        <div class="bg-emerald-100 p-2 rounded-full shrink-0">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Laporan Berhasil Terkirim!</h4>
                            <p class="text-xs mt-0.5 opacity-90">Petugas sarpras akan segera meninjau dan memproses pengaduan Anda. Pantau terus statusnya di menu Riwayat.</p>
                        </div>
                    </div>
                <?php elseif ($_GET['status'] === 'gagal'): ?>
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl flex items-start sm:items-center gap-4 shadow-sm animate-fade-in-down">
                        <div class="bg-rose-100 p-2 rounded-full shrink-0">
                            <i class="fa-solid fa-triangle-exclamation text-xl"></i>
                        </div>
                        <div>
                            <h4 class="font-bold text-sm">Gagal Mengirim Laporan</h4>
                            <p class="text-xs mt-0.5 opacity-90">Pastikan semua data yang dimasukkan sudah benar dan ukuran file lampiran tidak melebihi batas sistem.</p>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden relative">
                <!-- Aksen Garis Atas -->
                <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-blue-500 to-cyan-500"></div>

                <div class="p-6 sm:p-10">
                    <div class="border-b border-slate-100 pb-5 mb-6 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">Formulir Pengaduan Sarpras</h3>
                            <p class="text-xs text-slate-500 mt-1.5 font-medium">Isi detail kerusakan secara akurat agar mudah ditindaklanjuti.</p>
                        </div>
                        <i class="fa-solid fa-bullhorn text-4xl text-slate-100 hidden sm:block"></i>
                    </div>

                    <!-- ENCTYPE MULTIPART DIBUTUHKAN UNTUK UPLOAD FILE -->
                    <form action="../Controller/c_proses_pengaduan.php" method="POST" enctype="multipart/form-data" class="space-y-6">

                        <!-- Input NIS hidden agar tetap terkirim ke $_POST['nis'] -->
                        <input type="hidden" name="nis" value="<?= htmlspecialchars($nis_login); ?>">

                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Nama Pelapor</label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input type="text" value="<?= htmlspecialchars($nama_login); ?> (NIS: <?= htmlspecialchars($nis_login); ?>)" readonly disabled class="w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-500 cursor-not-allowed">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Kategori Sarana -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Kategori Kerusakan</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400 pointer-events-none">
                                        <i class="fa-solid fa-layer-group"></i>
                                    </span>
                                    <select name="kategori" required class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all shadow-sm appearance-none cursor-pointer">
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php if (!empty($list_kategori)): ?>
                                            <?php foreach ($list_kategori as $kat): ?>
                                                <option value="<?= htmlspecialchars($kat['id_kategori']) ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                        <i class="fa-solid fa-chevron-down text-xs"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Lokasi Detail -->
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Lokasi Detail</label>
                                <div class="relative">
                                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-slate-400">
                                        <i class="fa-solid fa-location-dot"></i>
                                    </span>
                                    <input type="text" name="lokasi" placeholder="Misal: Toilet Lt. 2 / Lab Komputer A" required class="w-full pl-11 pr-4 py-3 bg-white border border-slate-200 rounded-2xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all shadow-sm">
                                </div>
                            </div>
                        </div>

                        <!-- Keterangan Laporan -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2">Keterangan / Deskripsi Masalah</label>
                            <textarea name="keterangan" placeholder="Jelaskan secara rinci kerusakan yang terjadi..." required rows="5" class="w-full p-4 bg-white border border-slate-200 rounded-2xl text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-cyan-500/20 focus:border-cyan-500 transition-all shadow-sm resize-none"></textarea>
                        </div>

                        <!-- BUKTI FOTO / VIDEO -->
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 uppercase tracking-widest mb-2 flex items-center justify-between">
                                Lampirkan Bukti <span class="text-slate-400 text-[9px] bg-slate-100 px-2 py-0.5 rounded font-medium normal-case">Opsional tapi disarankan</span>
                            </label>
                            <div class="relative mt-1">
                                <input type="file" name="bukti_siswa" accept="image/*,video/*" class="block w-full text-sm text-slate-500 
                                    file:mr-4 file:py-3 file:px-5 
                                    file:rounded-xl file:border-0 
                                    file:text-xs file:font-bold 
                                    file:bg-cyan-50 file:text-cyan-600 
                                    hover:file:bg-cyan-100 hover:file:text-cyan-700
                                    border border-slate-200 rounded-2xl bg-slate-50 file:transition-colors cursor-pointer shadow-sm">
                            </div>
                            <p class="text-[10px] font-medium text-slate-400 mt-2 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info"></i> Format didukung: Gambar (JPG, PNG) atau Video (MP4).
                            </p>
                        </div>

                        <div class="pt-4 mt-8 border-t border-slate-100">
                            <button type="submit" name="submit_pengaduan" class="w-full inline-flex items-center justify-center gap-2 bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-700 hover:to-blue-700 text-white font-bold py-4 px-6 rounded-2xl text-sm transition-all shadow-md shadow-cyan-600/20 hover:shadow-lg hover:shadow-cyan-600/30 hover:-translate-y-0.5 cursor-pointer">
                                <i class="fa-solid fa-paper-plane"></i> Kirim Laporan Pengaduan
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center pb-6 mt-6">
                <p class="text-[10px] font-medium text-slate-400">
                    &copy; <?= date('Y') ?> Portal Siswa Pengaduan Sarpras. Dibuat dengan <i class="fa-solid fa-heart text-rose-400 mx-0.5"></i> untuk sekolah.
                </p>
            </div>

        </div>
    </main>

    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');

            sidebar.classList.toggle('-translate-x-full');
            if (overlay.classList.contains('hidden')) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => overlay.classList.add('hidden'), 300);
            }
        }
    </script>
</body>

</html>