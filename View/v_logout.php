<?php
// 1. Jalankan session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Kosongkan seluruh variabel session
$_SESSION = array();

// 3. Hapus cookie session jika ada
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Hancurkan session sepenuhnya
session_destroy();

// 5. Redirect pengguna kembali ke halaman login (sejajar di dalam folder View)
header("Location: v_login.php");
exit();
?>