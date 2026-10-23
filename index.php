<?php
session_start();

// Cek apakah user sudah login atau belum (admin maupun siswa memakai $_SESSION['role'])
if (!isset($_SESSION['role'])) {
    // Jika belum login, arahkan ke halaman login
    header("location:View/v_login.php");
    exit();
} else {
    // Jika sudah login, arahkan sesuai role-nya
    if ($_SESSION['role'] == 'admin') {
        header("location:View/v_admin.php");
    } else if ($_SESSION['role'] == 'siswa') {
        header("location:View/v_dashboard_siswa.php");
    } else {
        header("location:View/v_login.php");
    }
    exit();
}
