<?php
require_once 'm_koneksi.php';

class m_admin {
    private $db;

    public function __construct() {
        $koneksi = new m_koneksi();
        $this->db = $koneksi->hubungkan();
    }

    // Verifikasi Login Admin dengan HASH PASSWORD
    public function loginAdmin($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM admin WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            return $admin;
        }
        return false;
    }
}