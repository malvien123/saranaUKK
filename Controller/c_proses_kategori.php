<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Panggil model yang sesuai (m_kategori)
require_once '../Model/m_kategori.php';

class c_kategori
{
    private $modelKategori;

    public function __construct()
    {
        // Instansiasi class m_kategori
        $this->modelKategori = new m_kategori();
    }

    public function kelolaKategori()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ../View/v_login.php");
            exit();
        }

        $action = $_POST['action'] ?? $_GET['action'] ?? '';

        if ($action == 'tambah') {
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if ($nama_kategori === '') {
                $_SESSION['flash_message'] = "Gagal: Nama kategori tidak boleh kosong.";
            } else {
                $hasil = $this->modelKategori->tambahKategori($nama_kategori);
                if ($hasil === "sukses") {
                    $_SESSION['flash_message'] = "Kategori berhasil ditambahkan.";
                } elseif ($hasil === "duplikat") {
                    $_SESSION['flash_message'] = "Gagal: Kategori \"" . $nama_kategori . "\" sudah ada.";
                } else {
                    $_SESSION['flash_message'] = "Terjadi kesalahan saat menambahkan kategori.";
                }
            }
            header("Location: ../View/v_kelola_kategori.php");
            exit();
        } elseif ($action == 'update') {
            $id_kategori   = $_POST['id_kategori'] ?? '';
            $nama_kategori = trim($_POST['nama_kategori'] ?? '');
            if ($id_kategori === '' || $nama_kategori === '') {
                $_SESSION['flash_message'] = "Gagal: Data kategori tidak lengkap.";
            } else {
                $hasil = $this->modelKategori->updateKategori($id_kategori, $nama_kategori);
                if ($hasil === "sukses") {
                    $_SESSION['flash_message'] = "Kategori berhasil diperbarui.";
                } elseif ($hasil === "duplikat") {
                    $_SESSION['flash_message'] = "Gagal: Kategori \"" . $nama_kategori . "\" sudah ada.";
                } else {
                    $_SESSION['flash_message'] = "Terjadi kesalahan saat memperbarui kategori.";
                }
            }
            header("Location: ../View/v_kelola_kategori.php");
            exit();
        } elseif ($action == 'hapus') {
            $id_kategori = $_GET['id'] ?? '';
            if (!empty($id_kategori)) {
                $hasil = $this->modelKategori->hapusKategori($id_kategori);

                if ($hasil === "digunakan") {
                    $_SESSION['flash_message'] = "Gagal menghapus: Kategori ini masih digunakan oleh pengaduan aktif yang belum selesai/ditolak.";
                } elseif ($hasil === "riwayat") {
                    $_SESSION['flash_message'] = "Gagal menghapus: Kategori ini masih tercatat pada riwayat pengaduan (selesai/ditolak).";
                } elseif ($hasil === "sukses") {
                    $_SESSION['flash_message'] = "Kategori berhasil dihapus.";
                } else {
                    $_SESSION['flash_message'] = "Terjadi kesalahan saat menghapus kategori.";
                }
            }
            header("Location: ../View/v_kelola_kategori.php");
            exit();
        } else {
            header("Location: ../View/v_admin.php");
            exit();
        }
    }
}

// Eksekusi Otomatis
$controller = new c_kategori();
$controller->kelolaKategori();
