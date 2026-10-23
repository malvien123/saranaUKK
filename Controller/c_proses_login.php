<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika file diakses langsung tanpa tombol submit POST, tendang keluar
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../View/v_login");
    exit();
}

require_once '../Model/m_login.php';

class c_login
{
    private $m_login;

    public function __construct()
    {
        $this->m_login = new m_login();
    }

    public function prosesLogin()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $user = $this->m_login->cekLogin($username, $password);

            if ($user) {
                // Simpan data session
                $_SESSION['role']     = $user['role'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nama']     = $user['nama'] ?? $user['username'];

                // Simpan id_siswa atau id_admin berdasarkan role
                if ($user['role'] === 'siswa') {
                    $_SESSION['id_siswa'] = $user['id_siswa'];
                } else if ($user['role'] === 'admin') {
                    $_SESSION['id_admin'] = $user['id_admin'] ?? '';
                }

                // Pesan sukses
                $_SESSION['flash_message'] = "Berhasil login! Selamat datang, " . htmlspecialchars($_SESSION['nama']);

                // Arahkan ke halaman dashboard masing-masing (tanpa ekstensi .php)
                if ($user['role'] === 'admin') {
                    header("Location: ../View/v_admin");
                } else {
                    header("Location: ../View/v_dashboard_siswa");
                }
                exit();
            } else {
                // Pesan gagal
                $_SESSION['error_login'] = "Username atau Password yang Anda masukkan salah!";
                header("Location: ../View/v_login");
                exit();
            }
        } else {
            // Jika diakses langsung tanpa POST, kembalikan ke halaman login
            header("Location: ../View/v_login");
            exit();
        }
    }
}

// EKSEKUSI OTOMATIS METHOD LOGIN
$controller = new c_login();
$controller->prosesLogin();
