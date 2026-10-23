<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteksi Admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: v_login.php");
    exit();
}

require_once '../Model/m_aspirasi.php';
$modelAspirasi = new m_aspirasi();

// Tangkap parameter ID dari URL dengan benar
$id_pelaporan = $_GET['id'] ?? 0;
$detail = $modelAspirasi ? $modelAspirasi->getAspirasiById($id_pelaporan) : null;

// Fallback variabel
$cur_status  = $detail['status'] ?? 'Menunggu';
$keterangan  = $detail['ket'] ?? $detail['keterangan'] ?? '-';
$feedback    = $detail['feedback'] ?? $detail['tanggapan'] ?? '';
$bukti_siswa = $detail['bukti'] ?? $detail['foto'] ?? $detail['bukti_siswa'] ?? '';
$bukti_admin = $detail['bukti_admin'] ?? '';

// Setup Styling berdasarkan Status
$statusText = strtolower($cur_status);
if ($statusText == 'proses') {
    $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200/60';
    $dotColor = 'bg-blue-500 animate-pulse';

    $alertBg = 'bg-emerald-50 border-emerald-200';
    $alertText = 'text-emerald-700';
    $alertIcon = 'fa-solid fa-check-circle text-emerald-500';
    $alertMsg = 'Setelah tanggapan dikirim, status akan otomatis berubah menjadi <b>SELESAI</b>.';
} elseif ($statusText == 'selesai') {
    $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200/60';
    $dotColor = 'bg-emerald-500';
} else {
    $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200/60';
    $dotColor = 'bg-amber-500';

    $alertBg = 'bg-blue-50 border-blue-200';
    $alertText = 'text-blue-700';
    $alertIcon = 'fa-solid fa-spinner text-blue-500 animate-spin-slow';
    $alertMsg = 'Setelah tanggapan dikirim, status akan otomatis berubah menjadi <b>PROSES</b>.';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindak Lanjut Aspirasi - Admin Sarpras</title>
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

<body class="bg-[#F8FAFC] font-sans min-h-screen flex flex-col text-slate-800 antialiased selection:bg-indigo-500 selection:text-white">

    <!-- HEADER (GLASSMORPHISM) -->
    <header class="bg-white/70 backdrop-blur-lg border-b border-slate-200/60 px-6 py-4 flex justify-between items-center sticky top-0 z-30 transition-all">
        <div class="flex items-center gap-4">
            <a href="v_admin.php" class="bg-white hover:bg-slate-50 border border-slate-200 p-2.5 rounded-xl text-slate-700 shadow-sm transition-all focus:ring-2 focus:ring-indigo-500/20 group" title="Kembali ke Dashboard">
                <i class="fa-solid fa-arrow-left group-hover:-translate-x-0.5 transition-transform"></i>
            </a>
            <div>
                <h2 class="text-lg font-bold text-slate-800">Tindak Lanjut Laporan</h2>
                <p class="text-[11px] text-slate-500 font-medium hidden sm:block">Panel penanganan pengaduan sarana & prasarana</p>
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

    <main class="flex-1 max-w-6xl w-full mx-auto p-5 sm:p-8">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

            <!-- KOLOM KIRI: DETAIL ASPIRASI SISWA -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Kartu Informasi Laporan -->
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 overflow-hidden relative">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-indigo-500 to-purple-500"></div>

                    <div class="p-6">
                        <div class="flex justify-between items-start mb-6">
                            <div>
                                <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">ID Laporan</span>
                                <h3 class="text-2xl font-black text-slate-800">#<?= htmlspecialchars($id_pelaporan) ?></h3>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full font-bold text-[10px] border shadow-sm <?= $badgeBg ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $dotColor ?>"></span>
                                <?= htmlspecialchars($cur_status) ?>
                            </span>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase block mb-1.5"><i class="fa-solid fa-comment-dots mr-1"></i> Keluhan Siswa</span>
                                <p class="text-slate-700 text-sm leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                    <?= nl2br(htmlspecialchars($keterangan)) ?>
                                </p>
                            </div>

                            <?php if (!empty($bukti_siswa)): ?>
                                <div class="pt-2">
                                    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase block mb-2"><i class="fa-solid fa-paperclip mr-1"></i> Dokumentasi Siswa</span>

                                    <div class="bg-slate-50 p-2 rounded-2xl border border-slate-100 flex items-center justify-center">
                                        <?php if (method_exists($modelAspirasi, 'isVideoFile') && $modelAspirasi->isVideoFile($bukti_siswa)): ?>
                                            <video src="../uploads/<?= htmlspecialchars($bukti_siswa) ?>" class="w-full max-h-60 object-contain rounded-xl" controls></video>
                                        <?php else: ?>
                                            <a href="../uploads/<?= htmlspecialchars($bukti_siswa) ?>" target="_blank" class="block w-full" title="Klik untuk memperbesar">
                                                <img src="../uploads/<?= htmlspecialchars($bukti_siswa) ?>" alt="Bukti Siswa" class="w-full max-h-60 object-cover rounded-xl hover:opacity-90 transition shadow-sm">
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="pt-2">
                                    <span class="text-[10px] font-bold tracking-widest text-slate-400 uppercase block mb-2"><i class="fa-solid fa-paperclip mr-1"></i> Dokumentasi Siswa</span>
                                    <div class="bg-slate-50 p-6 rounded-2xl border border-slate-100 border-dashed flex flex-col items-center justify-center text-slate-400">
                                        <i class="fa-solid fa-image-slash text-2xl mb-2"></i>
                                        <span class="text-xs font-medium">Tidak ada lampiran</span>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: FORM TANGGAPAN ADMIN -->
            <div class="lg:col-span-7">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200/60 p-6 sm:p-8">

                    <div class="mb-6 border-b border-slate-100 pb-4">
                        <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-reply-all text-indigo-500"></i> Form Tindak Lanjut
                        </h3>
                        <p class="text-xs text-slate-500 mt-1">Berikan tanggapan dan lampirkan bukti perbaikan sarana prasarana.</p>
                    </div>

                    <form action="../Controller/c_aspirasi.php?action=tanggapi" method="POST" enctype="multipart/form-data" class="space-y-6">
                        <input type="hidden" name="id_aspirasi" value="<?= htmlspecialchars($id_pelaporan) ?>">

                        <!-- Alert Perubahan Status -->
                        <?php if ($statusText !== 'selesai'): ?>
                            <div class="<?= $alertBg ?> <?= $alertText ?> p-4 rounded-2xl text-xs sm:text-sm border flex items-start gap-3 shadow-sm">
                                <i class="<?= $alertIcon ?> mt-0.5 text-base"></i>
                                <div class="leading-relaxed">
                                    <?= $alertMsg ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Textarea Tanggapan -->
                        <div>
                            <label class="block text-xs font-bold tracking-wide text-slate-700 mb-2">CATATAN / TANGGAPAN ADMIN</label>
                            <textarea name="tanggapan" rows="5" placeholder="Tuliskan progres atau hasil perbaikan sarpras secara detail di sini..." class="w-full text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded-2xl p-4 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all shadow-inner" required><?= htmlspecialchars($feedback) ?></textarea>
                        </div>

                        <!-- Input File Admin -->
                        <div>
                            <label class="block text-xs font-bold tracking-wide text-slate-700 mb-2">LAMPIRKAN BUKTI PENANGANAN (FOTO/VIDEO)</label>
                            <div class="relative">
                                <input type="file" name="bukti_admin" accept="image/*,video/*" class="block w-full text-sm text-slate-500 
                                    file:mr-4 file:py-3 file:px-5 
                                    file:rounded-xl file:border-0 
                                    file:text-xs file:font-bold 
                                    file:bg-indigo-50 file:text-indigo-600 
                                    hover:file:bg-indigo-100 hover:file:text-indigo-700
                                    border border-slate-200 rounded-2xl bg-slate-50 file:transition-colors cursor-pointer" required>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-2 ml-1">* Wajib dilampirkan sebagai bukti laporan telah ditangani.</p>
                        </div>

                        <!-- Tombol Submit -->
                        <div class="pt-4 border-t border-slate-100">
                            <button type="submit" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-2xl text-sm transition-all shadow-md shadow-indigo-600/20 hover:shadow-lg hover:shadow-indigo-600/30 hover:-translate-y-0.5 flex items-center justify-center gap-2">
                                <i class="fa-solid fa-paper-plane text-xs"></i> Simpan Tanggapan
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </main>

</body>

</html>