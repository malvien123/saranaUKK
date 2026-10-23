<?php
require_once '../Model/m_aspirasi.php';

class c_aspirasi
{
    private $m_aspirasi;

    public function __construct()
    {
        $this->m_aspirasi = new m_aspirasi();
    }

    // 1. Proses Tambah Aspirasi (dari Form Siswa + Upload Foto)
    public function tambahAspirasi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $id_siswa    = $_SESSION['id_siswa'] ?? '';
            $id_kategori = trim($_POST['id_kategori'] ?? '');
            $lokasi      = trim($_POST['lokasi'] ?? '');
            $keterangan  = trim($_POST['keterangan'] ?? '');

            // --- PROSES UPLOAD FOTO SISWA ---
            $nama_file_siswa = null;
            if (isset($_FILES['bukti_siswa']) && $_FILES['bukti_siswa']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['bukti_siswa']['tmp_name'];
                $nama_asal = $_FILES['bukti_siswa']['name'];
                $ekstensi = strtolower(pathinfo($nama_asal, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov'];

                if (in_array($ekstensi, $allowed)) {
                    $nama_file_siswa = time() . '_' . uniqid() . '.' . $ekstensi;
                    $target_dir = "../uploads/";

                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    move_uploaded_file($tmp_name, $target_dir . $nama_file_siswa);
                }
            }

            if (!empty($id_siswa) && !empty($keterangan)) {
                $this->m_aspirasi->tambahAspirasi($id_siswa, $id_kategori, $lokasi, $keterangan, $nama_file_siswa);
                header("Location: ../View/v_siswa.php?status=success");
                exit;
            } else {
                header("Location: ../View/v_siswa.php?status=failed");
                exit;
            }
        }
    }

    // 2. Proses Tanggapi Aspirasi (dari Form Admin + Upload Foto Bukti Admin)
    public function tanggapiAspirasi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $id_aspirasi = trim($_POST['id_aspirasi'] ?? '');
            $tanggapan   = trim($_POST['tanggapan'] ?? '');
            $id_admin    = $_SESSION['id_admin'] ?? 1;

            $status_sekarang = $this->m_aspirasi->cekStatusAspirasi($id_aspirasi);

            if ($status_sekarang === 'Selesai' || $status_sekarang === 'Ditolak') {
                $_SESSION['flash_message'] = "Gagal: Laporan ini sudah berstatus final dan tidak bisa diubah lagi.";
                header("Location: ../View/v_admin.php?status=sudah_final");
                exit;
            }

            // --- LOGIKA STATUS BERTAHAP OTOMATIS ---
            $status_baru = $status_sekarang; 
            if (strtolower($status_sekarang) === 'menunggu') {
                $status_baru = 'Proses';
            } elseif (strtolower($status_sekarang) === 'proses') {
                $status_baru = 'Selesai';
            }

            // --- PROSES UPLOAD FOTO ADMIN ---
            $nama_file_admin = null;
            if (isset($_FILES['bukti_admin']) && $_FILES['bukti_admin']['error'] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['bukti_admin']['tmp_name'];
                $nama_asal = $_FILES['bukti_admin']['name'];
                $ekstensi = strtolower(pathinfo($nama_asal, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov'];

                if (in_array($ekstensi, $allowed)) {
                    $nama_file_admin = 'admin_' . time() . '_' . uniqid() . '.' . $ekstensi;
                    $target_dir = "../uploads/";

                    if (!is_dir($target_dir)) {
                        mkdir($target_dir, 0777, true);
                    }

                    move_uploaded_file($tmp_name, $target_dir . $nama_file_admin);
                }
            }

            $this->m_aspirasi->tanggapiAspirasi($id_aspirasi, $status_baru, $tanggapan, $id_admin, $nama_file_admin);
            
            $_SESSION['flash_message'] = "Tanggapan berhasil disimpan! Status otomatis menjadi: " . $status_baru;
            header("Location: ../View/v_admin.php?status=updated");
            exit;
        }
    }

    // 3. Proses Tolak Aspirasi Langsung (Admin)
    public function tolakAspirasi()
    {
        if (isset($_GET['id'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $id_aspirasi = $_GET['id'];
            $id_admin    = $_SESSION['id_admin'] ?? 1;

            $status_sekarang = $this->m_aspirasi->cekStatusAspirasi($id_aspirasi);
            if ($status_sekarang === 'Selesai' || $status_sekarang === 'Ditolak') {
                $_SESSION['flash_message'] = "Gagal: Laporan ini sudah berstatus final.";
                header("Location: ../View/v_admin.php");
                exit;
            }

            $alasan = "Pengaduan ditolak oleh admin karena tidak sesuai dengan ketentuan sarana prasarana sekolah.";
            $this->m_aspirasi->tolakAspirasi($id_aspirasi, $id_admin, $alasan);

            $_SESSION['flash_message'] = "Pengaduan berhasil ditolak dan dipindahkan ke histori arsip.";
            header("Location: ../View/v_admin.php");
            exit;
        }
    }

    // 4. Proses Hapus Aspirasi (Admin)
    public function hapusAspirasi()
    {
        if (isset($_GET['id'])) {
            $id_aspirasi = $_GET['id'];
            $this->m_aspirasi->hapusAspirasi($id_aspirasi);
            header("Location: ../View/v_admin.php?status=deleted");
            exit;
        }
    }
}

// EKSEKUSI LANGSUNG BERDASARKAN PARAMETER ACTION DI URL
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$controller = new c_aspirasi();
$action     = $_GET['action'] ?? '';
$role       = $_SESSION['role'] ?? '';

// Proteksi akses: tambah = siswa, tanggapi/tolak/hapus = admin
if ($action === 'tambah' && $role !== 'siswa') {
    header("Location: ../View/v_login.php");
    exit;
}
if (in_array($action, ['tanggapi', 'tolak', 'hapus'], true) && $role !== 'admin') {
    header("Location: ../View/v_login.php");
    exit;
}

if ($action === 'tambah') {
    $controller->tambahAspirasi();
} elseif ($action === 'tanggapi') {
    $controller->tanggapiAspirasi();
} elseif ($action === 'tolak') {
    $controller->tolakAspirasi();
} elseif ($action === 'hapus') {
    $controller->hapusAspirasi();
}
?>