<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Cek apakah sudah login
if (!isset($_SESSION['role'])) {
    header("Location: v_login");
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
    header("Location: v_login");
    exit();
}

require_once '../Model/m_kategori.php';
$modelKategori = new m_kategori();
$list_kategori = $modelKategori->getAllKategori();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kategori Sarpras - Admin Sarpras</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- FontAwesome untuk Ikon -->
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

    <!-- ALERT BERHASIL / GAGAL -->
    <?php if (isset($_SESSION['flash_message'])): ?>
        <div id="toastAlert" class="fixed top-6 right-6 z-[9999] bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl flex items-start gap-4 transition-all duration-500 border border-slate-700/50">
            <div class="bg-emerald-500/20 text-emerald-400 p-2.5 rounded-xl shrink-0">
                <i class="fa-solid fa-check-circle text-lg"></i>
            </div>
            <div class="mt-0.5">
                <h4 class="font-bold text-sm tracking-wide">Berhasil</h4>
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
                <a href="v_admin" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-border-all text-base w-5 group-hover:text-white transition-colors"></i> Dashboard
                </a>
                <a href="v_histori_aspirasi" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-clock-rotate-left text-base w-5 group-hover:text-white transition-colors"></i> Histori Selesai
                </a>

                <div class="px-2 py-3 text-[10px] font-bold text-slate-500 tracking-widest uppercase mt-2">Manajemen Data</div>

                <a href="v_kelola_siswa" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl text-slate-400 hover:bg-white/5 hover:text-slate-200 transition-all group">
                    <i class="fa-solid fa-users text-base w-5 group-hover:text-white transition-colors"></i> Kelola Siswa
                </a>
                <a href="v_kelola_kategori" class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl bg-indigo-600/10 text-indigo-400 border border-indigo-500/20 shadow-inner transition-all">
                    <i class="fa-solid fa-tags text-base w-5"></i> <span class="font-bold">Kelola Kategori</span>
                </a>
            </nav>
        </div>
        <div class="p-5 border-t border-white/5">
            <a href="v_logout" onclick="return confirm('Sesi Anda akan diakhiri. Lanjutkan?')" class="flex items-center justify-center gap-3 w-full px-4 py-3 rounded-xl bg-rose-500/10 text-rose-400 hover:bg-rose-500 hover:text-white border border-rose-500/20 transition-all text-sm font-semibold shadow-sm">
                <i class="fa-solid fa-power-off"></i> Keluar Aplikasi
            </a>
        </div>
    </aside>

    <!-- KONTEN UTAMA -->
    <main class="flex-1 flex flex-col min-w-0 min-h-screen relative">

        <!-- HEADER (GLASSMORPHISM) -->
        <header class="bg-white/70 backdrop-blur-lg border-b border-slate-200/60 px-6 py-4 flex justify-between items-center sticky top-0 z-30 transition-all">
            <div class="flex items-center gap-4">
                <button onclick="toggleSidebar()" class="md:hidden bg-white hover:bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500/20">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Manajemen Kategori</h2>
                    <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Klasifikasi pelaporan sarana dan prasarana</p>
                </div>
            </div>

            <!-- Profil Kanan -->
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
                <div class="absolute inset-0 bg-gradient-to-br from-violet-900/80 via-slate-900 to-purple-900/90 z-0"></div>
                <div class="absolute -top-24 -right-12 w-64 h-64 bg-fuchsia-500/10 rounded-full blur-3xl z-0 group-hover:bg-fuchsia-400/20 transition-all duration-700"></div>
                <div class="absolute top-0 right-0 w-full h-full bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGNpcmNsZSBjeD0iMSIgY3k9IjEiIHI9IjEiIGZpbGw9InJnYmEoMjU1LDI1NSwyNTUsMC4wNSkiLz48L3N2Zz4=')] z-0"></div>

                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                    <div>
                        <span class="inline-flex items-center gap-2 py-1 px-3 rounded-full bg-violet-500/20 border border-violet-500/30 text-[10px] font-bold tracking-widest text-violet-300 mb-3 backdrop-blur-md">
                            <i class="fa-solid fa-tags"></i> LABEL SISTEM
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-bold tracking-tight text-white mb-2">Kelola Kategori Sarpras</h2>
                        <p class="text-sm text-slate-300 max-w-xl leading-relaxed font-medium">Buat dan atur jenis kategori pengaduan (misal: Fasilitas Umum, Lab Komputer) agar laporan siswa lebih terstruktur dan mudah ditindaklanjuti.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/10 p-4 rounded-2xl text-center shrink-0 min-w-[120px]">
                        <p class="text-[10px] text-violet-200 font-bold tracking-widest uppercase mb-1">Total Kategori</p>
                        <h3 class="text-3xl font-black text-white"><?= count($list_kategori) ?></h3>
                    </div>
                </div>
            </div>

            <!-- TABLE WRAPPER -->
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden flex flex-col">

                <!-- TOOLBAR (Pencarian & Tombol Tambah) -->
                <div class="p-5 sm:px-6 sm:py-5 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <!-- KOLOM PENCARIAN -->
                    <div class="relative w-full sm:w-80">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-slate-400">
                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                        </span>
                        <input type="text" id="inputPencarianKategori" onkeyup="cariKategori()" placeholder="Cari nama kategori..." class="w-full bg-white border border-slate-200 text-slate-700 text-xs rounded-xl pl-10 pr-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-sm">
                    </div>

                    <a href="v_tambah_kategori" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2.5 px-5 rounded-xl text-xs transition-all shadow-md shadow-indigo-600/20 hover:shadow-lg hover:shadow-indigo-600/30 hover:-translate-y-0.5 shrink-0">
                        <i class="fa-solid fa-plus text-[10px]"></i> Tambah Kategori Baru
                    </a>
                </div>

                <!-- DESKTOP TABLE VIEW -->
                <div class="hidden md:block overflow-x-auto min-h-[300px]">
                    <table class="w-full text-left border-collapse" id="tabelKategori">
                        <thead>
                            <tr class="bg-white border-b border-slate-200">
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap w-20 text-center">No</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 whitespace-nowrap">Nama Kategori Sarpras</th>
                                <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($list_kategori)): ?>
                                <tr id="rowKosongDefault">
                                    <td colspan="3" class="p-16 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                                <i class="fa-solid fa-tags text-4xl text-slate-300"></i>
                                            </div>
                                            <h4 class="text-base font-bold text-slate-700">Tidak ada kategori</h4>
                                            <p class="text-sm text-slate-500 mt-1 max-w-sm">Belum ada kategori yang ditambahkan. Silakan klik tombol "Tambah Kategori Baru".</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php $no = 1;
                                foreach ($list_kategori as $row):
                                    $iconColors = ['text-rose-500 bg-rose-50', 'text-blue-500 bg-blue-50', 'text-emerald-500 bg-emerald-50', 'text-purple-500 bg-purple-50', 'text-amber-500 bg-amber-50'];
                                    $colorClass = $iconColors[($no - 1) % count($iconColors)];
                                ?>
                                    <tr class="item-kategori hover:bg-slate-50/80 transition-all duration-200 group relative">
                                        <td class="px-6 py-4 text-center font-bold text-slate-400 text-xs">
                                            <?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap nama-kategori">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl <?= $colorClass ?> flex items-center justify-center font-bold text-base shadow-sm shrink-0 border border-slate-100">
                                                    <i class="fa-solid fa-hashtag"></i>
                                                </div>
                                                <div>
                                                    <span class="block font-bold text-slate-800 text-sm group-hover:text-indigo-600 transition-colors">
                                                        <?= htmlspecialchars($row['nama_kategori'] ?? $row['kategori'] ?? '-') ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center gap-2 opacity-80 group-hover:opacity-100 transition-opacity">
                                                <a href="v_edit_kategori?id=<?= $row['id_kategori'] ?>" class="w-9 h-9 rounded-xl bg-white border border-amber-200 text-amber-500 hover:bg-amber-50 hover:border-amber-400 hover:shadow-sm transition-all flex items-center justify-center" title="Edit Kategori">
                                                    <i class="fa-solid fa-pen text-xs"></i>
                                                </a>
                                                <a href="../Controller/c_proses_kategori.php?action=hapus&id=<?= $row['id_kategori'] ?>" onclick="return confirm('Peringatan: Menghapus kategori ini dapat berdampak pada laporan siswa yang sudah menggunakan kategori tersebut. Yakin ingin menghapus?')" class="w-9 h-9 rounded-xl bg-white border border-rose-200 text-rose-500 hover:bg-rose-50 hover:border-rose-400 hover:shadow-sm transition-all flex items-center justify-center" title="Hapus Kategori">
                                                    <i class="fa-solid fa-trash-can text-xs"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            <!-- Baris Pesan Jika Hasil Pencarian Tidak Ditemukan -->
                            <tr id="rowTidakDitemukan" class="hidden">
                                <td colspan="3" class="p-12 text-center text-slate-400 text-xs italic font-medium">
                                    <i class="fa-solid fa-circle-exclamation mr-1.5"></i> Kategori yang dicari tidak ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- MOBILE CARD VIEW -->
                <div class="block md:hidden divide-y divide-slate-100 bg-slate-50/30" id="containerMobileCard">
                    <?php if (empty($list_kategori)): ?>
                        <div class="p-10 text-center">
                            <i class="fa-solid fa-tags text-4xl mb-3 text-slate-300"></i>
                            <p class="text-sm font-bold text-slate-700">Belum ada kategori</p>
                        </div>
                    <?php else: $no = 1; ?>
                        <div class="p-4 space-y-4">
                            <?php foreach ($list_kategori as $row):
                                $iconColors = ['text-rose-500 bg-rose-50', 'text-blue-500 bg-blue-50', 'text-emerald-500 bg-emerald-50', 'text-purple-500 bg-purple-50', 'text-amber-500 bg-amber-50'];
                                $colorClass = $iconColors[($no - 1) % count($iconColors)];
                            ?>
                                <div class="item-card-mobile bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-center mb-4">
                                        <div class="flex items-center gap-3.5 flex-1 min-w-0">
                                            <div class="w-12 h-12 rounded-xl <?= $colorClass ?> border border-slate-100 flex items-center justify-center font-bold text-xl shadow-sm shrink-0">
                                                <i class="fa-solid fa-hashtag"></i>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 text-sm line-clamp-2 leading-snug">
                                                    <?= htmlspecialchars($row['nama_kategori'] ?? $row['kategori'] ?? '-') ?>
                                                </h4>
                                            </div>
                                        </div>
                                        <div class="text-xs font-bold text-slate-300 shrink-0 ml-3">
                                            #<?= str_pad($no++, 2, '0', STR_PAD_LEFT) ?>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 pt-3 border-t border-slate-100">
                                        <a href="v_edit_kategori?id=<?= $row['id_kategori'] ?>" class="flex-1 flex items-center justify-center gap-2 bg-white border border-amber-200 text-amber-600 px-3 py-2.5 rounded-xl font-bold text-[11px] shadow-sm hover:bg-amber-50">
                                            <i class="fa-solid fa-pen"></i> Edit
                                        </a>
                                        <a href="../Controller/c_proses_kategori.php?action=hapus&id=<?= $row['id_kategori'] ?>" onclick="return confirm('Peringatan: Menghapus kategori ini dapat berdampak pada laporan siswa yang sudah menggunakan kategori tersebut. Yakin ingin menghapus?')" class="flex-none flex items-center justify-center bg-white border border-rose-200 text-rose-500 px-4 py-2.5 rounded-xl font-bold text-[11px] shadow-sm hover:bg-rose-50">
                                            <i class="fa-solid fa-trash-can"></i> Hapus
                                        </a>
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

        // FUNGSI PENCARIAN KATEGORI REALTIME
        function cariKategori() {
            const keyword = document.getElementById('inputPencarianKategori').value.toLowerCase();

            // Filter tabel desktop
            const rows = document.querySelectorAll('#tabelKategori .item-kategori');
            let ditemukanDesktop = 0;

            rows.forEach(row => {
                const textRow = row.textContent.toLowerCase();
                if (textRow.includes(keyword)) {
                    row.style.display = "";
                    ditemukanDesktop++;
                } else {
                    row.style.display = "none";
                }
            });

            // Tampilkan pesan kosong jika tidak ditemukan
            const rowTidakDitemukan = document.getElementById('rowTidakDitemukan');
            if (rowTidakDitemukan) {
                if (ditemukanDesktop === 0 && rows.length > 0) {
                    rowTidakDitemukan.classList.remove('hidden');
                } else {
                    rowTidakDitemukan.classList.add('hidden');
                }
            }

            // Filter kartu mobile
            const cards = document.querySelectorAll('#containerMobileCard .item-card-mobile');
            cards.forEach(card => {
                const textCard = card.textContent.toLowerCase();
                if (textCard.includes(keyword)) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            });
        }
    </script>
</body>

</html>