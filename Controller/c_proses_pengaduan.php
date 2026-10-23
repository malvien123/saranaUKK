<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../Model/m_aspirasi.php';

class c_pengaduan
{
    private $model;

    public function __construct()
    {
        $this->model = new m_aspirasi();
    }

    public function kirimPengaduan()
    {
        // Proteksi Halaman: Pastikan hanya siswa yang bisa mengakses
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'siswa') {
            header("Location: ../View/v_login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_siswa = $_SESSION['id_siswa'] ?? null; 
            
            if (empty($id_siswa)) {
                echo "<script>alert('Gagal: Sesi siswa tidak valid, silakan login ulang.'); window.location.href='../View/v_login.php';</script>";
                exit();
            }

            $id_kategori = intval($_POST['kategori'] ?? 1);
            $lokasi      = trim($_POST['lokasi'] ?? '');
            $keterangan  = trim($_POST['keterangan'] ?? '');

            if (empty($lokasi) || empty($keterangan)) {
                echo "<script>alert('Harap isi lokasi dan keterangan dengan lengkap!'); window.location.href='../View/v_siswa.php';</script>";
                exit();
            }

            // --- PROSES UPLOAD FILE BUKTI ---
            $bukti_siswa = null;

            if (isset($_FILES['bukti_siswa']) && $_FILES['bukti_siswa']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['bukti_siswa']['tmp_name'];
                $fileName      = $_FILES['bukti_siswa']['name'];
                $fileSize      = $_FILES['bukti_siswa']['size'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $maxFileSize = 20 * 1024 * 1024; // 20 MB
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'mp4', 'mkv', 'avi', 'mov'];

                if ($fileSize > $maxFileSize) {
                    echo "<script>alert('Ukuran file terlalu besar! Maksimal 20MB.'); window.location.href='../View/v_siswa.php';</script>";
                    exit();
                }

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = 'siswa_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadFileDir = '../uploads/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $bukti_siswa = $newFileName;
                    } else {
                        echo "<script>alert('Gagal mengunggah file bukti!'); window.location.href='../View/v_siswa.php';</script>";
                        exit();
                    }
                } else {
                    echo "<script>alert('Format file tidak didukung!'); window.location.href='../View/v_siswa.php';</script>";
                    exit();
                }
            }

            // --- SIMPAN KE DATABASE ---
            $simpan = $this->model->tambahAspirasi($id_siswa, $id_kategori, $lokasi, $keterangan, $bukti_siswa);

            if ($simpan) {
                echo "<script>alert('Pengaduan berhasil dikirim!'); window.location.href='../View/v_siswa.php?status=sukses';</script>";
            } else {
                echo "<script>alert('Gagal menyimpan pengaduan ke database!'); window.location.href='../View/v_siswa.php?status=gagal';</script>";
            }
        } else {
            header("Location: ../View/v_siswa.php");
            exit();
        }
    }
}

// Eksekusi Otomatis
$controller = new c_pengaduan();
$controller->kirimPengaduan();
?>