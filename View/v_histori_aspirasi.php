<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah sudah login
if (!isset($_SESSION['role'])) {
    header("Location: v_login.php");
    exit();
}

// 2. Jika SISWA mencoba masuk lewat ubah URL:
if ($_SESSION['role'] === 'siswa') {
    echo "<script>
        window.history.back();
    </script>";
    exit();
}

// 3. Pastikan rolenya benar-benar admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: v_login.php");
    exit();
}

require_once '../Model/m_aspirasi.php';
$modelAspirasi = new m_aspirasi();

// Konfigurasi Paginasi (10 data per halaman)
$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

// Ambil total data selesai/ditolak dan data per halaman
$totalData  = $modelAspirasi->getTotalAspirasiSelesai();
$totalPages = ($totalData > 0) ? ceil($totalData / $limit) : 1;

// Ambil data sesuai halaman aktif
$list_selesai = $modelAspirasi->getAspirasiSelesaiLimit($limit, $offset);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histori Arsip - Admin Sarpras</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Custom Scrollbar for Webkit */
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

<body class="bg-[#F8FAFC] font-sans min-h-screen flex flex-col md:flex-row text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- OVERLAY SIDEBAR MOBILE -->
    <div id="sidebarOverlay" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-40 hidden md:hidden transition-all duration-300 opacity-0"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-50 h-screen w-72 bg-[#0F172A] text-slate-300 flex flex-col justify-between -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0 border-r border-slate-800/60 shadow-xl md:shadow-none">
        <div class="overflow-y-auto overflow-x-hidden">
            <!-- LOGO -->
            <div class="p-7 border-b border-white/5 flex items-center gap-4">
                <div class="bg-gradient-to-br from-indigo-500 to-blue-600 p-3 rounded-xl text-white font-bold shadow-lg shadow-indigo-500/30">
                    <i class="fa-solid fa-school-flag text-lg"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-base tracking-wide">V-Dashboard</h1>
                    <p class="text-[11px] text-indigo-300 font-medium tracking-wider uppercase mt-0.5">Admin Sarpras</p>
                </div>
            </div>

            <div class="px-6 py-4 text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-2">Menu Utama</div>

            <nav class="px-4 space-y-1.5 text-sm font-medium">
                <a href="v_admin.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-border-all text-base w-5 group-hover:text-white transition-colors"></i> Dashboard
                </a>
                <a href="v_histori_aspirasi.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5"></i> <span class="font-bold">Histori Selesai</span>
                </a>

                <div class="px-2 py-3 text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-2">Manajemen Data</div>

                <a href="v_kelola_siswa.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-users text-base w-5 group-hover:text-white transition-colors"></i> Kelola Siswa
                </a>
                <a href="v_kelola_kategori.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-tags text-base w-5 group-hover:text-white transition-colors"></i> Kelola Kategori
                </a>
            </nav>
        </div>
        <div class="p-5 border-t border-white/5">
            <a href="v_logout.php" onclick="return confirm('Sesi Anda akan diakhiri. Lanjutkan?')" class="flex items-center justify-center gap-3 w-full px-4 py-3 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white border border-rose-500/20 transition-all text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-power-off"></i> Keluar Aplikasi
            </a>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="flex-1 flex flex-col min-w-0 min-h-screen relative">

        <!-- HEADER (GLASSMORPHISM) -->
        <header class="bg-white/70 backdrop-blur-lg border-b border-slate-200/60 px-6 py-4 flex justify-between items-center sticky top-0 z-30 transition-all">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden bg-white hover:bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500/20">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Histori & Arsip</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Rekam jejak pengaduan yang telah selesai atau ditolak</p>
                </div>
            </div>

            <!-- Right Profile -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex flex-col items-end">
                    <span class="text-sm font-bold text-slate-800"><?= htmlspecialchars($_SESSION['username'] ?? $_SESSION['nama'] ?? 'Administrator') ?></span>
                    <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md font-bold uppercase tracking-wider mt-0.5"><?= htmlspecialchars($_SESSION['role'] ?? 'admin') ?></span>
                </div>
                <div class="bg-gradient-to-br from-indigo-600 to-purple-600 text-white font-bold w-10 h-10 rounded-2xl flex items-center justify-center text-sm shadow-md shadow-indigo-500/30 border border-white/20 shrink-0">
                    <?= strtoupper(substr($_SESSION['username'] ?? $_SESSION['nama'] ?? 'Admin', 0, 1)) ?>
                </div>
            </div>
        </header>

        <div class="p-5 sm:p-8 space-y-8 max-w-[1400px] w-full mx-auto">

            <!-- PAGE BANNER -->
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-slate-900 to-slate-800 z-0"></div>
                <div class="absolute -top-24 -right-12 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl z-0 group-hover:bg-indigo-400/20 transition-all duration-700"></div>

                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <span class="inline-flex items-center gap-2 py-1 px-3 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-[10px] font-bold tracking-widest text-indigo-300 mb-3 backdrop-blur-md">
                            <i class="fa-solid fa-box-archive"></i> ARSIP PENGADUAN
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">Histori Arsip</h2>
                        <p class="text-sm text-slate-300 max-w-xl leading-relaxed font-medium">Daftar seluruh laporan sarana prasarana yang telah selesai ditangani maupun yang ditolak oleh sistem.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center shrink-0 min-w-[120px]">
                        <p class="text-[10px] text-indigo-200 font-bold tracking-widest uppercase mb-1">Total Arsip</p>
                        <h3 class="text-3xl font-black text-white"><?= $totalData ?></h3>
                    </div>
                </div>
            </div>

            <!-- TABLE WRAPPER -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">

                <div class="p-5 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Daftar Arsip Selesai & Ditolak</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Laporan di bawah ini sudah berstatus final (Selesai atau Ditolak).</p>
                    </div>
                </div>

                <!-- DESKTOP TABLE VIEW -->
                <div class="hidden lg:block overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">ID & Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Pelapor</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Kategori & Lokasi</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Keluhan Siswa</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanggapan & Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($list_selesai)): ?>
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-700">Belum ada arsip</h4>
                                            <p class="text-sm text-slate-500 mt-1 max-w-sm">Belum ada histori pengaduan saat ini.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($list_selesai as $row):
                                    $bukti_siswa_raw = trim($row['bukti_siswa'] ?? '');
                                    $web_siswa_url = !empty($bukti_siswa_raw) ? '../uploads/' . $bukti_siswa_raw : '';

                                    $bukti_admin_raw = trim($row['bukti_admin'] ?? '');
                                    $web_admin_url = !empty($bukti_admin_raw) ? '../uploads/' . $bukti_admin_raw : '';

                                    $feedback_text = trim($row['feedback'] ?? $row['feedback_admin'] ?? '');
                                    $status_laporan = strtolower($row['status'] ?? 'selesai');

                                    // Pengaturan warna badge dan kotak tanggapan berdasarkan status (Ditolak = Merah, Selesai = Hijau)
                                    if ($status_laporan === 'ditolak') {
                                        $badge_bg   = 'bg-rose-50 text-rose-700 border-rose-200/60';
                                        $dot_color  = 'bg-rose-500';
                                        $box_bg     = 'bg-rose-50/50 border-rose-200';
                                        $text_color = 'text-rose-700';
                                    } else {
                                        $badge_bg   = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
                                        $dot_color  = 'bg-emerald-500';
                                        $box_bg     = 'bg-emerald-50/50 border-emerald-200';
                                        $text_color = 'text-emerald-700';
                                    }

                                    $gradients = ['from-rose-400 to-red-500', 'from-blue-400 to-indigo-500', 'from-emerald-400 to-teal-500', 'from-purple-400 to-fuchsia-500', 'from-amber-400 to-orange-500'];
                                    $gradClass = $gradients[($row['id_siswa'] ?? 0) % count($gradients)];
                                ?>
                                    <tr class="hover:bg-slate-50/80 transition-all duration-200 group relative align-top">
                                        <!-- ID & Waktu -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-block bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-md text-[11px] border border-slate-200/80">
                                                #<?= htmlspecialchars($row['id_aspirasi']) ?>
                                            </span>
                                            <div class="text-[10px] font-medium text-slate-400 mt-2 flex items-center gap-1.5">
                                                <i class="fa-regular fa-clock text-slate-300"></i> <?= htmlspecialchars($row['created_at'] ?? '-') ?>
                                            </div>
                                        </td>

                                        <!-- Pelapor -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br <?= $gradClass ?> text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0">
                                                    <?= strtoupper(substr($row['nama_siswa'] ?? 'S', 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <span class="block font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa') ?></span>
                                                    <span class="block text-[11px] text-slate-500 mt-0.5">NIS: <?= htmlspecialchars($row['id_siswa'] ?? '-') ?> <span class="text-slate-300 mx-0.5">•</span> <?= htmlspecialchars($row['kelas'] ?? '-') ?></span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Kategori & Lokasi -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100/50 inline-block mb-1.5">
                                                <?= htmlspecialchars($row['nama_kategori'] ?? '-') ?>
                                            </span>
                                            <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1">
                                                <i class="fa-solid fa-location-dot text-slate-300"></i> <?= htmlspecialchars($row['lokasi']) ?>
                                            </div>
                                        </td>

                                        <!-- Keluhan Siswa -->
                                        <td class="px-6 py-4 min-w-[200px] max-w-xs">
                                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2" title="<?= htmlspecialchars($row['ket']) ?>">
                                                <?= htmlspecialchars($row['ket']) ?>
                                            </p>
                                            <?php if (!empty($bukti_siswa_raw)): ?>
                                                <a href="../uploads/<?= htmlspecialchars($bukti_siswa_raw) ?>" target="_blank" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50 transition-all shadow-sm">
                                                    <i class="fa-solid fa-image text-indigo-400"></i> Lampiran Siswa
                                                </a>
                                            <?php endif; ?>
                                        </td>

                                        <!-- Status -->
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-[10px] border shadow-sm <?= $badge_bg ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $dot_color ?>"></span>
                                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                            </span>
                                        </td>

                                        <!-- Tanggapan & Bukti -->
                                        <td class="px-6 py-4 min-w-[200px] max-w-xs">
                                            <?php if (!empty($feedback_text)): ?>
                                                <p class="text-slate-600 text-xs line-clamp-2 border-l-2 <?= $box_bg ?> pl-3 italic py-1" title="<?= htmlspecialchars($feedback_text) ?>">
                                                    "<?= htmlspecialchars($feedback_text) ?>"
                                                </p>
                                            <?php else: ?>
                                                <span class="text-xs text-slate-400 italic">Belum ada tanggapan</span>
                                            <?php endif; ?>

                                            <?php if (!empty($bukti_admin_raw)): ?>
                                                <a href="../uploads/<?= htmlspecialchars($bukti_admin_raw) ?>" target="_blank" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 <?= $box_bg ?> rounded-lg text-[10px] font-bold <?= $text_color ?> hover:opacity-80 transition-all shadow-sm">
                                                    <i class="fa-solid fa-paperclip"></i> Bukti Perbaikan
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="block lg:hidden divide-y divide-slate-100 bg-slate-50/30">
                    <?php if (empty($list_selesai)): ?>
                        <div class="p-10 text-center">
                            <i class="fa-solid fa-box-open text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-700">Belum ada arsip</p>
                        </div>
                    <?php else: ?>
                        <div class="p-4 space-y-4">
                            <?php foreach ($list_selesai as $row):
                                $bukti_siswa_raw = trim($row['bukti_siswa'] ?? '');
                                $bukti_admin_raw = trim($row['bukti_admin'] ?? '');
                                $feedback_text = trim($row['feedback'] ?? $row['feedback_admin'] ?? '');
                                $status_laporan = strtolower($row['status'] ?? 'selesai');

                                if ($status_laporan === 'ditolak') {
                                    $badge_bg   = 'bg-rose-50 text-rose-700 border-rose-200/60';
                                    $dot_color  = 'bg-rose-500';
                                    $box_bg     = 'bg-rose-50/50 border-rose-100';
                                    $text_color = 'text-rose-700';
                                } else {
                                    $badge_bg   = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
                                    $dot_color  = 'bg-emerald-500';
                                    $box_bg     = 'bg-emerald-50/50 border-emerald-100';
                                    $text_color = 'text-emerald-700';
                                }

                                $gradients = ['from-rose-400 to-red-500', 'from-blue-400 to-indigo-500', 'from-emerald-400 to-teal-500', 'from-purple-400 to-fuchsia-500', 'from-amber-400 to-orange-500'];
                                $gradClass = $gradients[($row['id_siswa'] ?? 0) % count($gradients)];
                            ?>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                                    <div class="absolute left-0 top-0 bottom-0 w-1 <?= $dot_color ?>"></div>

                                    <div class="flex justify-between items-start gap-3 mb-4 pl-2">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gradient-to-br <?= $gradClass ?> text-white flex items-center justify-center font-bold text-sm shadow-sm shrink-0">
                                                <?= strtoupper(substr($row['nama_siswa'] ?? 'S', 0, 1)) ?>
                                            </div>
                                            <div>
                                                <span class="font-bold text-slate-800 text-sm block"><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa') ?></span>
                                                <span class="text-[10px] text-slate-500 font-medium">ID: #<?= htmlspecialchars($row['id_aspirasi']) ?> • <?= htmlspecialchars($row['created_at'] ?? '-') ?></span>
                                            </div>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full font-bold text-[9px] shrink-0 inline-flex items-center gap-1.5 border <?= $badge_bg ?>">
                                            <span class="w-1.5 h-1.5 rounded-full <?= $dot_color ?>"></span> <?= htmlspecialchars(ucfirst($row['status'])) ?>
                                        </span>
                                    </div>

                                    <div class="pl-2">
                                        <div class="inline-flex items-center gap-1.5 mb-1.5 text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                                            <i class="fa-solid fa-tag"></i> <?= htmlspecialchars($row['nama_kategori'] ?? '-') ?>
                                        </div>
                                        <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1 mb-2">
                                            <i class="fa-solid fa-location-dot text-slate-300"></i> <?= htmlspecialchars($row['lokasi']) ?>
                                        </div>
                                        <p class="text-slate-600 text-xs leading-relaxed mb-3">
                                            <span class="font-semibold text-slate-700 block mb-0.5">Keluhan:</span>
                                            <?= htmlspecialchars($row['ket']) ?>
                                        </p>
                                    </div>

                                    <div class="pl-2 pt-3 border-t border-slate-100">
                                        <span class="font-semibold <?= $text_color ?> block text-[10px] uppercase tracking-wide mb-1"><i class="fa-solid fa-reply"></i> Tanggapan Admin:</span>
                                        <p class="text-slate-600 text-xs italic <?= $box_bg ?> p-2.5 rounded-lg border">
                                            <?= !empty($feedback_text) ? htmlspecialchars($feedback_text) : 'Belum ada tanggapan' ?>
                                        </p>

                                        <div class="flex flex-wrap gap-2 mt-3">
                                            <?php if (!empty($bukti_siswa_raw)): ?>
                                                <a href="../uploads/<?= htmlspecialchars($bukti_siswa_raw) ?>" target="_blank" class="flex-1 bg-white border border-slate-200 text-slate-600 px-3 py-2 rounded-xl font-bold text-[10px] shadow-sm hover:bg-slate-50 flex items-center justify-center gap-1.5">
                                                    <i class="fa-solid fa-image text-slate-400"></i> File Siswa
                                                </a>
                                            <?php endif; ?>

                                            <?php if (!empty($bukti_admin_raw)): ?>
                                                <a href="../uploads/<?= htmlspecialchars($bukti_admin_raw) ?>" target="_blank" class="flex-1 <?= $box_bg ?> border text-xs font-bold px-3 py-2 rounded-xl flex items-center justify-center gap-1.5 <?= $text_color ?>">
                                                    <i class="fa-solid fa-paperclip"></i> Bukti Admin
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <div class="p-4 sm:px-6 sm:py-5 border-t border-slate-200 bg-white flex flex-col sm:flex-row justify-between items-center gap-4">
                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-400 text-center sm:text-left">
                            Halaman <span class="text-indigo-600"><?= $page ?></span> dari <?= $totalPages ?>
                            <span class="block sm:inline sm:ml-2 normal-case tracking-normal font-medium text-slate-400">(Total: <?= $totalData ?> arsip)</span>
                        </span>
                        <div class="flex gap-1.5 bg-slate-50 p-1 rounded-xl border border-slate-200/60">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 font-bold text-xs transition-colors shadow-sm border border-slate-200/50"><i class="fa-solid fa-chevron-left"></i></a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-lg font-bold text-xs transition-all shadow-sm <?= $page == $i ? 'bg-indigo-600 text-white shadow-indigo-500/30' : 'bg-white border border-slate-200/50 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600' ?>"><?= $i ?></a>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="?page=<?= $page + 1 ?>" class="w-8 h-8 flex items-center justify-center bg-white rounded-lg text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 font-bold text-xs transition-colors shadow-sm border border-slate-200/50"><i class="fa-solid fa-chevron-right"></i></a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <!-- Footer -->
            <div class="text-center pb-6">
                <p class="text-[10px] font-medium text-slate-400">
                    &copy; <?= date('Y') ?> Sistem Pengaduan Sarpras. Dibuat dengan <i class="fa-solid fa-heart text-rose-400 mx-0.5"></i> untuk sekolah.
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