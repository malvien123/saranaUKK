<?php
require_once 'm_koneksi.php';

class m_login
{
    private $db;

    public function __construct()
    {
        // Mengambil objek koneksi dari class m_koneksi
        $koneksiObj = new m_koneksi();
        $this->db = $koneksiObj->hubungkan();
    }

    public function cekLogin($username, $password)
    {
        $username = $this->db->real_escape_string($username);

        // 1. Cek di tabel 'admin'
        $queryAdmin = "SELECT * FROM admin WHERE username = '$username'";
        $resultAdmin = $this->db->query($queryAdmin);

        if ($resultAdmin && $resultAdmin->num_rows > 0) {
            $data = $resultAdmin->fetch_assoc();
            // Verifikasi password hash
            if (password_verify($password, $data['password'])) {
                return [
                    'status'   => true,
                    'role'     => 'admin',
                    'id_admin' => $data['id_admin'] ?? null,
                    'username' => $data['username'],
                    'nama'     => $data['username']
                ];
            }
        }

        // 2. Cek di tabel 'siswa' (menggunakan NIS)
        $querySiswa = "SELECT * FROM siswa WHERE nis = '$username'";
        $resultSiswa = $this->db->query($querySiswa);

        if ($resultSiswa && $resultSiswa->num_rows > 0) {
            $data = $resultSiswa->fetch_assoc();
            // Verifikasi password hash
            if (password_verify($password, $data['password'])) {
                return [
                    'status'   => true,
                    'role'     => 'siswa',
                    'id_siswa' => $data['id_siswa'],
                    'username' => $data['nis'],
                    'nama'     => $data['nama'] ?? $data['nis']
                ];
            }
        }

        return false;
    }
}
?>