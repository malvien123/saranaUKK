<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
    header("Location: v_login.php");
    exit();
}

// Ambil id_siswa dari session yang valid (berupa primary key angka)
$id_siswa   = $_SESSION['id_siswa'] ?? '';
$nis_login  = $_SESSION['nis'] ?? $_SESSION['username'] ?? '';
$nama_login = $_SESSION['nama'] ?? 'Siswa';

require_once '../Model/m_aspirasi.php';
$modelAspirasi = new m_aspirasi();

// Panggil fungsi khusus aktif dengan menggunakan $id_siswa
$list_history = $modelAspirasi->getAspirasiAktifBySiswa($id_siswa);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaduan Aktif - Portal Siswa</title>
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
                <a href="v_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-pen-to-square text-base w-5 group-hover:text-white transition-colors"></i> Form Aspirasi
                </a>
                <a href="v_history_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-cyan-600/10 text-cyan-400 border border-cyan-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5"></i> <span class="font-bold">Pengaduan </span>
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
                    <h2 class="text-lg font-bold text-slate-800">Laporan Aktif</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Pantau status laporan yang sedang diproses admin</p>
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

            <!-- PAGE TITLE & ACTIONS -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="text-2xl font-black text-slate-800 tracking-tight">Status Pengaduan Saya</h3>
                    <p class="text-sm text-slate-500 mt-1">Laporan di bawah ini sedang dalam antrean atau sedang ditangani oleh petugas.</p>
                </div>
                <a href="v_siswa.php" class="inline-flex items-center justify-center gap-2 bg-white border border-cyan-200 text-cyan-600 hover:bg-cyan-50 font-bold px-5 py-2.5 rounded-xl text-sm transition-all shadow-sm shrink-0">
                    <i class="fa-solid fa-plus text-xs"></i> Buat Laporan Baru
                </a>
            </div>

            <!-- DATA CONTAINER -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">

                <!-- DESKTOP TABLE VIEW -->
                <div class="hidden lg:block overflow-x-auto min-h-[400px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-200/80">
                                <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap w-20 text-center">ID</th>
                                <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Detail Laporan</th>
                                <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap text-center">Status</th>
                                <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Tanggapan & Tindak Lanjut Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (!empty($list_history)): ?>
                                <?php foreach ($list_history as $row):
                                    $status = $row['status'] ?? 'Menunggu';
                                    if (strtolower($status) == 'proses') {
                                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
                                        $dotColor = 'bg-blue-500 animate-pulse';
                                    } else {
                                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
                                        $dotColor = 'bg-amber-500';
                                    }

                                    $waktu = $row['created_at'] ?? '-';
                                    $keterangan = $row['ket'] ?? '-';
                                    $feedback = $row['feedback'] ?? '';

                                    $bukti_siswa_raw = trim($row['bukti_siswa'] ?? '');
                                    $web_siswa_url = !empty($bukti_siswa_raw) ? '../uploads/' . $bukti_siswa_raw : '';

                                    $bukti_admin_raw = trim($row['bukti_admin'] ?? '');
                                    $web_admin_url = !empty($bukti_admin_raw) ? '../uploads/' . $bukti_admin_raw : '';
                                ?>
                                    <tr class="hover:bg-slate-50/50 transition-all group">
                                        <!-- KOLOM 1: ID -->
                                        <td class="px-6 py-6 text-center align-top">
                                            <span class="inline-block bg-slate-100 text-slate-500 font-bold px-2 py-1 rounded-lg text-xs border border-slate-200">
                                                #<?= htmlspecialchars($row['id_aspirasi']) ?>
                                            </span>
                                        </td>

                                        <!-- KOLOM 2: Laporan Siswa -->
                                        <td class="px-6 py-6 align-top">
                                            <div class="flex gap-4">
                                                <div class="flex-1">
                                                    <div class="font-bold text-slate-800 text-sm mb-1 group-hover:text-cyan-600 transition-colors">
                                                        <?= htmlspecialchars($row['nama_kategori'] ?? '-') ?>
                                                    </div>
                                                    <div class="flex items-center gap-3 text-[11px] font-medium text-slate-400 mb-3">
                                                        <span title="Lokasi"><i class="fa-solid fa-location-dot text-slate-300 mr-1"></i> <?= htmlspecialchars($row['lokasi']) ?></span>
                                                        <span title="Waktu"><i class="fa-regular fa-clock text-slate-300 mr-1"></i> <?= $waktu != '-' ? date('d M Y, H:i', strtotime($waktu)) : '-' ?></span>
                                                    </div>
                                                    <p class="text-slate-600 text-xs leading-relaxed bg-slate-50 p-3 rounded-xl border border-slate-100/60">
                                                        <?= nl2br(htmlspecialchars($keterangan)) ?>
                                                    </p>
                                                </div>
                                                <?php if (!empty($bukti_siswa_raw)): ?>
                                                    <a href="<?= htmlspecialchars($web_siswa_url) ?>" target="_blank" class="shrink-0 w-24 h-24 rounded-2xl overflow-hidden border border-slate-200 relative group/img shadow-sm block">
                                                        <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity z-10">
                                                            <i class="fa-solid fa-magnifying-glass-plus text-white"></i>
                                                        </div>
                                                        <img src="<?= htmlspecialchars($web_siswa_url) ?>" alt="Bukti" class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-500">
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>

                                        <!-- KOLOM 3: Status -->
                                        <td class="px-6 py-6 text-center align-top whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-[10px] border shadow-sm <?= $badgeBg ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span>
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>

                                        <!-- KOLOM 4: Tanggapan Admin -->
                                        <td class="px-6 py-6 align-top">
                                            <div class="flex gap-4">
                                                <div class="w-8 h-8 rounded-full bg-slate-100 border border-slate-200 text-slate-400 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-user-shield text-xs"></i>
                                                </div>
                                                <div class="flex-1">
                                                    <?php if (!empty($feedback)): ?>
                                                        <p class="text-slate-700 text-xs leading-relaxed mb-3">
                                                            <?= nl2br(htmlspecialchars($feedback)) ?>
                                                        </p>
                                                    <?php else: ?>
                                                        <p class="text-slate-400 text-xs italic mt-1.5">
                                                            Menunggu tindak lanjut dari petugas...
                                                        </p>
                                                    <?php endif; ?>

                                                    <?php if (!empty($bukti_admin_raw)): ?>
                                                        <a href="<?= htmlspecialchars($web_admin_url) ?>" target="_blank" class="inline-block w-20 h-20 rounded-xl overflow-hidden border border-slate-200 relative group/img shadow-sm mt-1">
                                                            <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition-opacity z-10">
                                                                <i class="fa-solid fa-magnifying-glass-plus text-white text-xs"></i>
                                                            </div>
                                                            <img src="<?= htmlspecialchars($web_admin_url) ?>" alt="Bukti Admin" class="w-full h-full object-cover group-hover/img:scale-110 transition-transform duration-500">
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="p-20 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mb-5">
                                                <i class="fa-solid fa-box-open text-5xl text-slate-300"></i>
                                            </div>
                                            <h4 class="text-lg font-bold text-slate-700">Kosong</h4>
                                            <p class="text-sm text-slate-500 mt-1 max-w-sm">Tidak ada laporan Anda yang sedang aktif / diproses saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="block lg:hidden bg-slate-50/30">
                    <?php if (!empty($list_history)): ?>
                        <div class="p-4 space-y-5">
                            <?php foreach ($list_history as $row):
                                $status = $row['status'] ?? 'Menunggu';
                                if (strtolower($status) == 'proses') {
                                    $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
                                    $dotColor = 'bg-blue-500 animate-pulse';
                                } else {
                                    $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
                                    $dotColor = 'bg-amber-500';
                                }

                                $waktu = $row['created_at'] ?? '-';
                                $bukti_siswa_raw = trim($row['bukti_siswa'] ?? '');
                                $web_siswa_url = !empty($bukti_siswa_raw) ? '../uploads/' . $bukti_siswa_raw : '';
                                $bukti_admin_raw = trim($row['bukti_admin'] ?? '');
                                $web_admin_url = !empty($bukti_admin_raw) ? '../uploads/' . $bukti_admin_raw : '';
                            ?>
                                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden relative">
                                    <!-- Status Line -->
                                    <div class="absolute left-0 top-0 bottom-0 w-1 <?= strtolower($status) == 'proses' ? 'bg-blue-500' : 'bg-amber-500' ?>"></div>

                                    <div class="p-5 pl-6">
                                        <!-- Header Card -->
                                        <div class="flex justify-between items-start mb-4">
                                            <div>
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span class="text-[10px] font-bold text-slate-400">#<?= htmlspecialchars($row['id_aspirasi']) ?></span>
                                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full font-bold text-[9px] border <?= $badgeBg ?>">
                                                        <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span> <?= htmlspecialchars($status) ?>
                                                    </span>
                                                </div>
                                                <h4 class="font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></h4>
                                                <p class="text-[10px] font-medium text-slate-400 mt-1 flex items-center gap-1"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($row['lokasi']) ?></p>
                                            </div>
                                        </div>

                                        <!-- Deskripsi & Bukti Siswa -->
                                        <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-100 mb-4">
                                            <p class="text-slate-600 text-xs leading-relaxed"><?= nl2br(htmlspecialchars($row['ket'] ?? '')) ?></p>
                                            <?php if (!empty($bukti_siswa_raw)): ?>
                                                <div class="mt-3">
                                                    <a href="<?= htmlspecialchars($web_siswa_url) ?>" target="_blank" class="inline-block relative rounded-xl overflow-hidden border border-slate-200">
                                                        <img src="<?= htmlspecialchars($web_siswa_url) ?>" alt="Bukti" class="h-20 w-auto object-cover">
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Tanggapan Admin -->
                                        <div class="border-t border-slate-100 pt-4">
                                            <div class="flex items-start gap-3">
                                                <div class="w-7 h-7 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center shrink-0">
                                                    <i class="fa-solid fa-user-shield text-[10px]"></i>
                                                </div>
                                                <div class="flex-1 pt-0.5">
                                                    <span class="block text-[10px] font-bold text-slate-400 mb-1">Tanggapan Admin:</span>
                                                    <?php if (!empty($row['feedback'])): ?>
                                                        <p class="text-slate-700 text-xs leading-relaxed"><?= nl2br(htmlspecialchars($row['feedback'])) ?></p>
                                                    <?php else: ?>
                                                        <p class="text-slate-400 text-xs italic">Belum ada tanggapan...</p>
                                                    <?php endif; ?>

                                                    <?php if (!empty($bukti_admin_raw)): ?>
                                                        <div class="mt-2">
                                                            <a href="<?= htmlspecialchars($web_admin_url) ?>" target="_blank" class="inline-block relative rounded-xl overflow-hidden border border-slate-200">
                                                                <img src="<?= htmlspecialchars($web_admin_url) ?>" alt="Bukti Admin" class="h-16 w-auto object-cover">
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="p-16 text-center">
                            <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-700">Tidak Ada Laporan Aktif</p>
                            <p class="text-xs text-slate-500 mt-1">Anda tidak memiliki laporan yang sedang diproses.</p>
                        </div>
                    <?php endif; ?>
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