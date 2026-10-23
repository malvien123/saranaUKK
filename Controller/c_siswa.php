<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hanya admin yang boleh mengelola data siswa
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../View/v_login.php");
    exit();
}

require_once '../Model/m_siswa.php';

class c_siswa
{
    private $m_siswa;

    public function __construct()
    {
        $this->m_siswa = new m_siswa();
    }

    public function tambahSiswa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nis      = trim($_POST['nis'] ?? '');
            $nama     = trim($_POST['nama'] ?? '');
            $kelas    = trim($_POST['kelas'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // --- VALIDASI NAMA (Hanya huruf kapital, huruf kecil, dan spasi) ---
            if (!empty($nama) && !$this->m_siswa->isValidNama($nama)) {
                $_SESSION['flash_message'] = "Gagal: Nama siswa hanya boleh berisi huruf dan spasi (tidak boleh ada angka atau tanda baca)!";
                header("Location: ../View/v_tambah_siswa.php");
                exit;
            }

            if (!empty($nis) && !empty($password)) {
                // Panggil model dan tangkap hasilnya (true/false)
                $berhasil = $this->m_siswa->tambahSiswa($nis, $nama, $kelas, $password);

                if ($berhasil) {
                    $_SESSION['flash_message'] = "Data siswa berhasil ditambahkan!";
                    header("Location: ../View/v_kelola_siswa.php?status=added");
                    exit;
                } else {
                    // Jika gagal karena validasi 10 digit ditolak model
                    $_SESSION['flash_message'] = "Gagal! NIS harus berupa angka dan wajib persis 10 digit.";
                    header("Location: ../View/v_tambah_siswa.php");
                    exit;
                }
            }
        }
    }

    public function updateSiswa()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nis      = trim($_POST['nis'] ?? '');
            $nama     = trim($_POST['nama'] ?? '');
            $kelas    = trim($_POST['kelas'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // --- VALIDASI NAMA (Hanya huruf kapital, huruf kecil, dan spasi) ---
            if (!empty($nama) && !$this->m_siswa->isValidNama($nama)) {
                $_SESSION['flash_message'] = "Gagal memperbarui: Nama siswa hanya boleh berisi huruf dan spasi (tidak boleh ada angka atau tanda baca)!";
                header("Location: ../View/v_kelola_siswa.php");
                exit;
            }

            // Panggil model dan tangkap hasilnya (true/false)
            $berhasil = $this->m_siswa->updateSiswa($nis, $nama, $kelas, $password);

            if ($berhasil) {
                $_SESSION['flash_message'] = "Data siswa berhasil diperbarui!";
                header("Location: ../View/v_kelola_siswa.php?status=updated");
                exit;
            } else {
                // Jika gagal karena validasi 10 digit ditolak model
                $_SESSION['flash_message'] = "Gagal memperbarui! NIS harus berupa angka dan wajib persis 10 digit.";
                header("Location: ../View/v_kelola_siswa.php");
                exit;
            }
        }
    }

    public function hapusSiswa()
    {
        if (isset($_GET['nis'])) {
            $nis = $_GET['nis'];

            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Panggil fungsi hapus dari model dan tangkap hasilnya (true/false)
            $berhasil = $this->m_siswa->hapusSiswa($nis);

            if ($berhasil) {
                $_SESSION['flash_message'] = "Data siswa berhasil dihapus!";
            } else {
                $_SESSION['flash_message'] = "Gagal menghapus! Siswa ini masih memiliki riwayat pengaduan/aspirasi di sistem.";
            }

            header("Location: ../View/v_kelola_siswa.php?status=deleted");
            exit;
        }
    }
}

// EKSEKUSI OTOMATIS BERDASARKAN PARAMETER ACTION DI URL
$controller = new c_siswa();
$action     = $_GET['action'] ?? '';

if ($action === 'tambah') {
    $controller->tambahSiswa();
} elseif ($action === 'update') {
    $controller->updateSiswa();
} elseif ($action === 'hapus') {
    $controller->hapusSiswa();
}
