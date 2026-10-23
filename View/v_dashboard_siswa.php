<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah sudah login
if (!isset($_SESSION['role'])) {
    header("Location: v_login.php");
    exit();
}

// 2. Jika ADMIN mencoba masuk lewat ubah URL:
if ($_SESSION['role'] === 'admin') {
    echo "<script>
        window.history.back();
    </script>";
    exit();
}

// 3. Pastikan rolenya benar-benar siswa
if ($_SESSION['role'] !== 'siswa') {
    header("Location: v_login.php");
    exit();
}

// Ambil id_siswa dari session yang valid (berupa primary key angka)
$id_siswa   = $_SESSION['id_siswa'] ?? '';
$nis_login  = $_SESSION['nis'] ?? $_SESSION['username'] ?? '';
$nama_login = $_SESSION['nama'] ?? 'Siswa';

require_once '../Model/m_aspirasi.php';
require_once '../Model/m_tanggapan.php';
$modelAspirasi = new m_aspirasi();
$modelTanggapan = new m_tanggapan();

// Ambil data untuk statistik siswa menggunakan $id_siswa
$all_aspirasi     = $modelAspirasi->getAspirasiBySiswa($id_siswa);
$aspirasi_aktif   = $modelAspirasi->getAspirasiAktifBySiswa($id_siswa);
$aspirasi_selesai = $modelTanggapan->getHistoryBySiswa($id_siswa);

$total_semua   = count($all_aspirasi);
$total_aktif   = count($aspirasi_aktif);
$total_selesai = count($aspirasi_selesai);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Siswa - Portal Pengaduan Sarpras</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome -->
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

<body class="bg-[#F8FAFC] font-sans min-h-screen flex flex-col md:flex-row text-slate-800 antialiased selection:bg-blue-500 selection:text-white">

    <!-- ALERT LOGIN BERHASIL -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="toastAlert" class="fixed top-6 right-6 z-[9999] bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-start gap-4 transition-all duration-500 border border-slate-700/50">
            <div class="bg-emerald-500/20 text-emerald-400 p-2.5 rounded-xl shrink-0">
                <i class="fa-solid fa-check-circle text-lg"></i>
            </div>
            <div class="mt-0.5">
                <h4 class="font-bold text-sm tracking-wide">Sukses!</h4>
                <p class="text-xs text-slate-300 mt-1 leading-relaxed"><?= htmlspecialchars($_SESSION['flash_message']) ?></p>
            </div>
            <button onclick="document.getElementById('toastAlert').remove()" class="ml-4 text-slate-400 hover:text-white transition mt-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <?php unset($_SESSION['flash_message']); ?>
        <script>
            setTimeout(() => {
                const toast = document.getElementById('toastAlert');
                if (toast) {
                    toast.classList.add('opacity-0', 'translate-x-full');
                    setTimeout(() => toast.remove(), 500);
                }
            }, 4000);
        </script>
    <?php endif; ?>

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
                <a href="v_dashboard_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-house-chimney text-base w-5"></i> <span class="font-bold">Dashboard</span>
                </a>
                <a href="v_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-pen-to-square text-base w-5 group-hover:text-white transition-colors"></i> Form Aspirasi
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
                    <h2 class="text-lg font-bold text-slate-800">Ruang Siswa</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Portal pengaduan fasilitas sekolah</p>
                </div>
            </div>

            <!-- Header Profile (Mobile Right) -->
            <div class="md:hidden flex items-center">
                <div class="bg-gradient-to-br from-cyan-500 to-blue-500 text-white font-bold w-9 h-9 rounded-full flex items-center justify-center text-xs shadow-md border border-white/20">
                    <?= strtoupper(substr($nama_login, 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 space-y-8 max-w-[1400px] w-full mx-auto">

            <!-- WELCOME BANNER (PREMIUM) -->
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden group">
                <!-- Elemen Latar Belakang Dekoratif -->
                <div class="absolute inset-0 bg-gradient-to-br from-blue-900/80 via-slate-900 to-cyan-900/90 z-0"></div>
                <div class="absolute -top-24 -right-12 w-64 h-64 bg-cyan-500/20 rounded-full blur-3xl z-0 group-hover:bg-cyan-400/30 transition-all duration-700"></div>
                <div class="absolute -bottom-24 right-32 w-64 h-64 bg-blue-500/20 rounded-full blur-3xl z-0 group-hover:bg-blue-400/30 transition-all duration-700"></div>
                <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] z-0"></div>

                <div class="relative z-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                    <div>
                        <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/10 text-[10px] font-bold tracking-widest text-cyan-200 mb-3 backdrop-blur-md">
                            <i class="fa-solid fa-hand-wave mr-1"></i> DASHBOARD SISWA
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">Halo, <?= htmlspecialchars($nama_login); ?>!</h2>
                        <p class="text-sm text-slate-300 max-w-2xl leading-relaxed font-medium">Bantu kami menjaga kenyamanan sekolah. Laporkan fasilitas sarana dan prasarana yang rusak atau membutuhkan perbaikan melalui portal ini.</p>
                    </div>
                    <div class="w-full lg:w-auto flex shrink-0">
                        <a href="v_siswa.php" class="w-full sm:w-auto text-center bg-white text-slate-900 hover:bg-cyan-50 font-bold px-6 py-3.5 rounded-2xl text-sm transition-all shadow-[0_0_20px_rgba(255,255,255,0.2)] hover:shadow-[0_0_30px_rgba(255,255,255,0.4)] flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus-circle"></i> Buat Pengaduan Baru
                        </a>
                    </div>
                </div>
            </div>

            <!-- STATS CARDS -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
                <!-- Card 1: Total -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mb-1">Total Laporan Saya</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $total_semua; ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30 relative z-10 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-layer-group"></i>
                    </div>
                </div>

                <!-- Card 2: Proses -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-amber-50 to-orange-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mb-1">Sedang Diproses</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $total_aktif; ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-500/30 relative z-10 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-hourglass-half"></i>
                    </div>
                </div>

                <!-- Card 3: Selesai -->
                <div class="bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex items-center justify-between group relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-24 h-24 bg-gradient-to-br from-emerald-50 to-teal-50 rounded-bl-full -z-0 opacity-50 group-hover:scale-110 transition-transform"></div>
                    <div class="relative z-10">
                        <p class="text-[10px] text-slate-500 font-bold tracking-widest uppercase mb-1">Telah Diselesaikan</p>
                        <h3 class="text-3xl font-black text-slate-800"><?= $total_selesai; ?></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30 relative z-10 group-hover:rotate-12 transition-transform">
                        <i class="fa-solid fa-check-double"></i>
                    </div>
                </div>
            </div>

            <!-- TABEL AKTIVITAS TERBARU -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">

                <div class="p-5 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Aktivitas Laporan Terbaru</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium hidden sm:block">Status terkini dari pengaduan yang baru saja Anda kirimkan.</p>
                    </div>
                    <a href="v_history_siswa.php" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-[11px] px-4 py-2 rounded-xl shadow-sm transition-colors flex items-center gap-1.5 shrink-0">
                        Lihat Semua <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>

                <!-- DESKTOP TABLE VIEW -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap w-16 text-center">No</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Waktu & Kategori</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Deskripsi Laporan</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center w-40">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (!empty($all_aspirasi)): ?>
                                <?php $no = 1;
                                foreach (array_slice($all_aspirasi, 0, 5) as $row):
                                    $status = $row['status'] ?? 'Menunggu';
                                    if (strtolower($status) == 'proses') {
                                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
                                        $dotColor = 'bg-blue-500 animate-pulse';
                                    } elseif (strtolower($status) == 'selesai') {
                                        $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
                                        $dotColor = 'bg-emerald-500';
                                    } else {
                                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
                                        $dotColor = 'bg-amber-500';
                                    }
                                ?>
                                    <tr class="hover:bg-slate-50/80 transition-all duration-200 group">
                                        <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs align-top">
                                            <?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <div class="font-bold text-slate-800 text-sm mb-1">
                                                <?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?>
                                            </div>
                                            <div class="text-[11px] font-medium text-slate-400 flex items-center gap-1.5 mb-1">
                                                <i class="fa-regular fa-clock text-slate-300"></i> <?= date('d M Y, H:i', strtotime($row['created_at'])); ?>
                                            </div>
                                            <div class="text-[11px] text-slate-500 flex items-center gap-1.5">
                                                <i class="fa-solid fa-location-dot text-slate-300"></i> <?= htmlspecialchars($row['lokasi']); ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 align-top">
                                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2" title="<?= htmlspecialchars($row['ket']); ?>">
                                                <?= htmlspecialchars($row['ket']); ?>
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-center align-top whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-[10px] border shadow-sm <?= $badgeBg ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span>
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-clipboard-list text-4xl text-slate-300"></i>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-700">Belum Ada Pengaduan</h4>
                                            <p class="text-sm text-slate-500 mt-1 max-w-sm">Anda belum pernah membuat laporan kerusakan sarana prasarana.</p>
                                            <a href="v_siswa.php" class="mt-4 bg-cyan-50 text-cyan-700 font-bold px-4 py-2 rounded-xl text-xs hover:bg-cyan-100 transition">Buat Laporan Sekarang</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="block lg:hidden divide-y divide-slate-100 bg-slate-50/30">
                    <?php if (empty($all_aspirasi)): ?>
                        <div class="p-10 text-center">
                            <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-700">Belum ada pengaduan</p>
                        </div>
                    <?php else: $no = 1; ?>
                        <div class="p-4 space-y-4">
                            <?php foreach (array_slice($all_aspirasi, 0, 5) as $row):
                                $status = $row['status'] ?? 'Menunggu';
                                if (strtolower($status) == 'proses') {
                                    $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
                                    $dotColor = 'bg-blue-500 animate-pulse';
                                } elseif (strtolower($status) == 'selesai') {
                                    $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
                                    $dotColor = 'bg-emerald-500';
                                } else {
                                    $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
                                    $dotColor = 'bg-amber-500';
                                }
                            ?>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow relative overflow-hidden">
                                    <!-- Status Line Indicator -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 <?= strpos($dotColor, 'blue') !== false ? 'bg-blue-500' : (strpos($dotColor, 'emerald') !== false ? 'bg-emerald-500' : 'bg-amber-500') ?>"></div>

                                    <div class="pl-2">
                                        <div class="flex justify-between items-start mb-3">
                                            <div>
                                                <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['nama_kategori'] ?? '-'); ?></h4>
                                                <p class="text-[10px] font-medium text-slate-400 mt-0.5"><?= date('d M Y', strtotime($row['created_at'])); ?></p>
                                            </div>
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full font-bold text-[9px] border <?= $badgeBg ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span> <?= htmlspecialchars($status) ?>
                                            </span>
                                        </div>

                                        <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                            <div class="text-[10px] text-slate-500 flex items-center gap-1.5 mb-1.5 pb-1.5 border-b border-slate-200/60">
                                                <i class="fa-solid fa-location-dot text-slate-400"></i> <?= htmlspecialchars($row['lokasi']); ?>
                                            </div>
                                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2">
                                                <?= htmlspecialchars($row['ket']); ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Footer -->
            <div class="text-center pb-6 mt-4">
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