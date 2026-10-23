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
require_once '../Model/m_siswa.php';
require_once '../Model/m_kategori.php';

$modelAspirasi = new m_aspirasi();
$m_siswa = new m_siswa();
$modelKategori = new m_kategori();

$tanggal     = $_GET['tanggal'] ?? '';
$bulan       = $_GET['bulan'] ?? '';
$id_kategori = $_GET['id_kategori'] ?? '';

$limit  = 10;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page > 1) ? ($page * $limit) - $limit : 0;

$allAspirasi = $modelAspirasi->getAllAspirasi();
$allSiswa = $m_siswa->getAllSiswa() ?? [];
$allKategori = $modelKategori->getAllKategori();
$aspirasiSelesai = $modelAspirasi->getAspirasiSelesai();

if (!empty($tanggal) || !empty($bulan) || !empty($id_kategori)) {
    $allFilteredAktif = $modelAspirasi->getFilteredAspirasi('', $id_kategori, $bulan, $tanggal);
    $allFilteredAktif = array_values(array_filter($allFilteredAktif, function ($item) {
        $st = strtolower($item['status'] ?? '');
        return $st !== 'selesai' && $st !== 'ditolak';
    }));

    $totalData = count($allFilteredAktif);
    $totalPages = ($totalData > 0) ? ceil($totalData / $limit) : 1;

    $aspirasiAktif = array_slice($allFilteredAktif, $offset, $limit);
} else {
    $totalData = $modelAspirasi->getTotalAspirasiAktif('', $bulan, $tanggal);
    $totalPages = ($totalData > 0) ? ceil($totalData / $limit) : 1;
    $aspirasiAktif = $modelAspirasi->getAspirasiAktifLimit($limit, $offset, $id_kategori, $bulan, $tanggal);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Pengaduan Sarpras</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
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

    <!-- ALERT BERHASIL / GAGAL -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="toastAlert" class="fixed top-6 right-6 z-[9999] bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-start gap-4 transition-all duration-500 border border-slate-700/50">
            <div class="bg-emerald-500/20 text-emerald-400 p-2.5 rounded-xl shrink-0">
                <i class="fa-solid fa-check-circle text-lg"></i>
            </div>
            <div class="mt-0.5">
                <h4 class="font-bold text-sm tracking-wide">Pemberitahuan</h4>
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

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed md:sticky top-0 left-0 z-50 h-screen w-72 bg-[#0F172A] text-slate-300 flex flex-col justify-between -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out shrink-0 border-r border-slate-800/60 shadow-xl md:shadow-none">
        <div class="overflow-y-auto overflow-x-hidden">
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
                <a href="v_admin.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-border-all text-base w-5"></i> <span class="font-bold">Dashboard</span>
                </a>
                <a href="v_histori_aspirasi.php" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5 group-hover:text-white transition-colors"></i> Histori Selesai
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

    <main class="flex-1 flex flex-col min-w-0 min-h-screen relative">
        <header class="bg-white/70 backdrop-blur-lg border-b border-slate-200/60 px-6 py-4 flex justify-between items-center sticky top-0 z-30 transition-all">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden bg-white hover:bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500/20">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Ringkasan Sistem</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Pantau pergerakan data pengaduan harian</p>
                </div>
            </div>
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
            <!-- WELCOME BANNER -->
            <div class="bg-slate-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-slate-900/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-900/80 via-slate-900 to-blue-900/90 z-0"></div>
                <div class="relative z-10">
                    <span class="inline-block py-1 px-3 rounded-full bg-white/10 border border-white/10 text-[10px] font-bold tracking-widest text-indigo-200 mb-3 backdrop-blur-md">DASHBOARD AKTIF</span>
                    <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">Selamat Datang, <?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?> 👋</h2>
                    <p class="text-sm text-slate-300 max-w-xl leading-relaxed font-medium">Tanggapi atau tolak laporan masuk agar sarana dan prasarana sekolah tetap terpelihara dengan baik.</p>
                </div>
                <div class="relative z-10 w-full sm:w-auto flex shrink-0">
                    <a href="v_histori_aspirasi.php" class="w-full sm:w-auto text-center bg-white text-slate-900 hover:bg-indigo-50 font-bold px-6 py-3.5 rounded-2xl text-sm transition-all shadow-md flex items-center justify-center gap-2">
                        Histori Arsip <i class="fa-solid fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- STATS CARDS DIPERBARUI (INTERAKTIF) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 sm:gap-6">
                <!-- Card 1 -->
                <div class="group bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-orange-500/10 hover:-translate-y-1.5 transition-all duration-300 flex items-center justify-between cursor-default">
                    <div>
                        <p class="text-xs text-slate-500 font-bold tracking-wide uppercase mb-1 group-hover:text-orange-500 transition-colors duration-300">Menunggu & Proses</p>
                        <h3 class="text-3xl font-black text-slate-800 group-hover:text-orange-600 transition-colors duration-300"><?= count(array_filter($allAspirasi, fn($item) => !in_array(strtolower($item['status'] ?? ''), ['selesai', 'ditolak']))) ?> <span class="text-sm font-medium text-slate-400 group-hover:text-orange-400/80 transition-colors duration-300">Laporan</span></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-orange-500/30 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-file-signature"></i>
                    </div>
                </div>

                <!-- Card 2 -->
                <div class="group bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:-translate-y-1.5 transition-all duration-300 flex items-center justify-between cursor-default">
                    <div>
                        <p class="text-xs text-slate-500 font-bold tracking-wide uppercase mb-1 group-hover:text-emerald-500 transition-colors duration-300">Arsip (Selesai/Ditolak)</p>
                        <h3 class="text-3xl font-black text-slate-800 group-hover:text-emerald-600 transition-colors duration-300"><?= count($aspirasiSelesai) ?> <span class="text-sm font-medium text-slate-400 group-hover:text-emerald-400/80 transition-colors duration-300">Laporan</span></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-400 to-teal-500 text-white flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/30 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-box-archive"></i>
                    </div>
                </div>

                <!-- Card 3 -->
                <div class="group bg-white p-6 rounded-3xl border border-slate-200/60 shadow-sm hover:shadow-xl hover:shadow-blue-500/10 hover:-translate-y-1.5 transition-all duration-300 flex items-center justify-between cursor-default">
                    <div>
                        <p class="text-xs text-slate-500 font-bold tracking-wide uppercase mb-1 group-hover:text-blue-500 transition-colors duration-300">Siswa Terdaftar</p>
                        <h3 class="text-3xl font-black text-slate-800 group-hover:text-blue-600 transition-colors duration-300"><?= count($allSiswa) ?> <span class="text-sm font-medium text-slate-400 group-hover:text-blue-400/80 transition-colors duration-300">Orang</span></h3>
                    </div>
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center text-2xl shadow-lg shadow-blue-500/30 group-hover:scale-110 group-hover:-rotate-6 transition-all duration-300">
                        <i class="fa-solid fa-users"></i>
                    </div>
                </div>
            </div>

            <!-- TABLE WRAPPER -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">
                <div class="p-5 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col xl:flex-row xl:items-center justify-between gap-5">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Daftar Pengaduan Masuk</h3>
                        <p class="text-xs text-slate-500 mt-1 font-medium">Kelola, tindak lanjuti, atau tolak laporan siswa yang tidak sesuai.</p>
                    </div>

                    <form method="GET" action="" class="flex flex-col sm:flex-row items-center gap-3 w-full xl:w-auto">
                        <div class="relative w-full sm:w-36">
                            <i class="fa-regular fa-calendar absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal) ?>" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl py-2.5 pl-9 pr-3 outline-none shadow-sm">
                        </div>
                        <div class="relative w-full sm:w-40">
                            <select name="bulan" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl py-2.5 px-3 outline-none shadow-sm appearance-none cursor-pointer">
                                <option value="">Semua Bulan</option>
                                <?php for ($m = 1; $m <= 12; $m++): $m_val = str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                                    <option value="<?= $m_val ?>" <?= $bulan == $m_val ? 'selected' : '' ?>><?= DateTime::createFromFormat('!m', $m)->format('F') ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="relative w-full sm:w-48">
                            <select name="id_kategori" class="w-full text-xs text-slate-700 bg-white border border-slate-200 rounded-xl py-2.5 px-3 outline-none shadow-sm appearance-none cursor-pointer">
                                <option value="">Semua Kategori</option>
                                <?php foreach ($allKategori as $kat): ?>
                                    <option value="<?= $kat['id_kategori'] ?>" <?= $id_kategori == $kat['id_kategori'] ? 'selected' : '' ?>><?= htmlspecialchars($kat['nama_kategori']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="flex gap-2 w-full sm:w-auto">
                            <button type="submit" class="flex-1 sm:flex-none bg-slate-800 hover:bg-slate-900 text-white font-semibold py-2.5 px-5 rounded-xl text-xs transition-all shadow-sm">Filter</button>
                            <a href="v_admin.php" class="bg-white hover:bg-slate-100 border border-slate-200 text-slate-600 font-semibold py-2.5 px-3.5 rounded-xl text-xs flex items-center justify-center shadow-sm"><i class="fa-solid fa-rotate-right"></i></a>
                        </div>
                    </form>
                </div>

                <!-- DESKTOP TABLE VIEW -->
                <div class="hidden lg:block overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-white border-b border-slate-200">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">ID & Waktu</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Pelapor</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Keluhan & Lokasi</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Status</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400">Tanggapan</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-right">Aksi Penanganan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($aspirasiAktif)): ?>
                                <tr>
                                    <td colspan="6" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-box-open text-4xl text-slate-300"></i>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-700">Belum ada pengaduan</h4>
                                            <p class="text-sm text-slate-500 mt-1">Data aspirasi dengan kriteria tersebut tidak ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($aspirasiAktif as $row): ?>
                                    <?php
                                    $file_bukti = trim($row['bukti_siswa'] ?? '');
                                    $file_bukti_admin = trim($row['bukti_admin'] ?? '');
                                    $statusText = strtolower($row['status'] ?? '');

                                    if ($statusText == 'proses') {
                                        $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
                                        $dotColor = 'bg-blue-500 animate-pulse';
                                    } else {
                                        $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
                                        $dotColor = 'bg-amber-500';
                                    }

                                    $gradients = ['from-rose-400 to-red-500', 'from-blue-400 to-indigo-500', 'from-emerald-400 to-teal-500', 'from-purple-400 to-fuchsia-500', 'from-amber-400 to-orange-500'];
                                    $gradClass = $gradients[($row['id_siswa'] ?? 0) % count($gradients)];
                                    ?>
                                    <tr class="hover:bg-slate-50/80 transition-all duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <span class="inline-block bg-slate-100 text-slate-600 font-bold px-2.5 py-1 rounded-md text-[11px] border border-slate-200/80">#<?= $row['id_aspirasi'] ?></span>
                                            <div class="text-[10px] font-medium text-slate-400 mt-2 flex items-center gap-1.5"><i class="fa-regular fa-clock text-slate-300"></i> <?= $row['created_at'] ?? '-' ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap align-top">
                                            <div class="flex items-center gap-3">
                                                <div class="w-9 h-9 rounded-full bg-gradient-to-br <?= $gradClass ?> text-white flex items-center justify-center font-bold text-xs shadow-sm shrink-0"><?= strtoupper(substr($row['nama_siswa'] ?? 'S', 0, 1)) ?></div>
                                                <div>
                                                    <span class="block font-bold text-slate-800 text-sm"><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa') ?></span>
                                                    <span class="block text-[11px] text-slate-500 mt-0.5">NIS: <?= htmlspecialchars($row['id_siswa'] ?? '-') ?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 min-w-[250px] align-top">
                                            <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100/50"><?= htmlspecialchars($row['nama_kategori'] ?? '-') ?></span>
                                            <div class="text-[10px] text-slate-500 font-medium flex items-center gap-1 mt-1"><i class="fa-solid fa-location-dot text-slate-300"></i> <?= htmlspecialchars($row['lokasi']) ?></div>
                                            <p class="text-slate-600 text-xs leading-relaxed line-clamp-2 mt-1"><?= htmlspecialchars($row['ket']) ?></p>
                                            <?php if (!empty($file_bukti)): ?>
                                                <a href="../uploads/<?= htmlspecialchars($file_bukti) ?>" target="_blank" class="inline-flex items-center gap-1.5 mt-2 px-2.5 py-1.5 bg-white border border-slate-200 rounded-lg text-[10px] font-bold text-slate-500 hover:text-indigo-600 shadow-sm"><i class="fa-solid fa-image text-indigo-400"></i> Foto Lampiran</a>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap align-top text-center">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-[10px] border shadow-sm <?= $badgeBg ?>">
                                                <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span> <?= htmlspecialchars($row['status']) ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 min-w-[200px] align-top">
                                            <?php if (!empty($row['feedback'])): ?>
                                                <p class="text-slate-600 text-xs line-clamp-2 border-l-2 border-slate-200 pl-3"><?= htmlspecialchars($row['feedback']) ?></p>
                                            <?php else: ?>
                                                <span class="text-slate-400 text-[11px] italic">Menunggu respons</span>
                                            <?php endif; ?>
                                        </td>

                                        <!-- KOLOM AKSI PREMIUM (DESKTOP) -->
                                        <td class="px-6 py-4 align-top text-right whitespace-nowrap">
                                            <div class="flex items-center justify-end gap-3">

                                                <!-- Tombol Tolak (Pill + Hover Anim) -->
                                                <a href="../Controller/c_aspirasi.php?action=tolak&id=<?= $row['id_aspirasi'] ?>"
                                                    onclick="return confirm('Yakin ingin menolak pengaduan ini? Data akan langsung masuk ke histori sebagai Ditolak.')"
                                                    class="group flex items-center gap-1.5 px-4 py-2.5 rounded-full bg-white border border-rose-200 text-rose-600 text-[11px] font-bold hover:bg-rose-500 hover:border-rose-500 hover:text-white transition-all duration-300 shadow-sm hover:shadow-md hover:shadow-rose-500/30"
                                                    title="Tolak Pengaduan">
                                                    <i class="fa-solid fa-xmark group-hover:rotate-90 transition-transform duration-300"></i> Tolak
                                                </a>

                                                <!-- Tombol Tindak Lanjuti (Pill + Gradient + Glow + Icon Slide) -->
                                                <a href="v_tanggapan.php?id=<?= $row['id_aspirasi'] ?>"
                                                    class="group relative overflow-hidden flex items-center gap-2 px-5 py-2.5 rounded-full bg-gradient-to-r from-indigo-600 to-blue-600 text-white text-[11px] font-bold shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-300"
                                                    title="Proses / Tindak Lanjuti">
                                                    <span class="relative z-10 flex items-center gap-1.5">
                                                        Proses <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1.5 transition-transform duration-300"></i>
                                                    </span>
                                                    <!-- Efek Kilau Background Hover -->
                                                    <div class="absolute inset-0 bg-white/20 translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-in-out z-0"></div>
                                                </a>

                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="block lg:hidden divide-y divide-slate-100 bg-slate-50/30">
                    <?php if (empty($aspirasiAktif)): ?>
                        <div class="p-10 text-center">
                            <p class="text-sm font-bold text-slate-700">Belum ada laporan</p>
                        </div>
                    <?php else: ?>
                        <div class="p-4 space-y-4">
                            <?php foreach ($aspirasiAktif as $row): ?>
                                <?php
                                $file_bukti = trim($row['bukti_siswa'] ?? '');
                                $statusText = strtolower($row['status'] ?? '');
                                $badgeBg = ($statusText == 'proses') ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700';
                                ?>
                                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 relative overflow-hidden">
                                    <div class="flex justify-between items-start gap-3 mb-3">
                                        <div>
                                            <span class="font-bold text-slate-800 text-sm block"><?= htmlspecialchars($row['nama_siswa'] ?? 'Siswa') ?></span>
                                            <span class="text-[10px] text-slate-500">ID: #<?= $row['id_aspirasi'] ?> • <?= date('d M Y', strtotime($row['created_at'])) ?></span>
                                        </div>
                                        <span class="px-2.5 py-1 rounded-full font-bold text-[9px] border <?= $badgeBg ?>"><?= htmlspecialchars($row['status']) ?></span>
                                    </div>
                                    <p class="text-slate-600 text-xs mb-4"><?= htmlspecialchars($row['ket']) ?></p>

                                    <!-- TOMBOL AKSI PREMIUM (MOBILE) -->
                                    <div class="flex gap-3 pt-4 border-t border-slate-100">

                                        <!-- Tombol Tolak Mobile -->
                                        <a href="../Controller/c_aspirasi.php?action=tolak&id=<?= $row['id_aspirasi'] ?>"
                                            onclick="return confirm('Tolak laporan ini?')"
                                            class="flex-1 group flex items-center justify-center gap-1.5 bg-rose-50 border border-transparent text-rose-600 hover:bg-rose-500 hover:text-white py-3 rounded-2xl font-bold text-xs transition-all duration-300 active:scale-95 shadow-sm">
                                            <i class="fa-solid fa-xmark group-hover:rotate-90 transition-transform duration-300"></i> Tolak
                                        </a>

                                        <!-- Tombol Tindak Lanjuti Mobile (Lebih Lebar / flex-[2]) -->
                                        <a href="v_tanggapan.php?id=<?= $row['id_aspirasi'] ?>"
                                            class="flex-[2] group flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-blue-600 text-white shadow-md shadow-indigo-500/30 hover:shadow-lg hover:shadow-indigo-500/50 py-3 rounded-2xl font-bold text-xs transition-all duration-300 active:scale-95 hover:-translate-y-0.5">
                                            Tindak Lanjuti <i class="fa-solid fa-arrow-right-long group-hover:translate-x-1.5 transition-transform duration-300"></i>
                                        </a>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- PAGINATION -->
                <?php if ($totalPages > 1): ?>
                    <div class="p-4 sm:px-6 sm:py-5 border-t border-slate-200 bg-white flex justify-between items-center">
                        <span class="text-[11px] font-bold text-slate-400">Halaman <?= $page ?> dari <?= $totalPages ?></span>
                        <div class="flex gap-1">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <a href="?page=<?= $i ?>" class="w-8 h-8 flex items-center justify-center rounded-xl font-bold text-xs <?= $page == $i ? 'bg-indigo-600 text-white shadow-md' : 'bg-white border text-slate-600 hover:bg-slate-50' ?> transition-colors"><?= $i ?></a>
                            <?php endfor; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        }
    </script>
</body>

</html>