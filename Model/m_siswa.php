<?php
require_once 'm_koneksi.php';

class m_siswa
{
    private $db;

    public function __construct()
    {
        // Mengambil objek koneksi dari class m_koneksi
        $koneksiObj = new m_koneksi();
        $this->db = $koneksiObj->hubungkan();
    }

    // 1. Ambil Semua Data Siswa
    public function getAllSiswa()
    {
        $query = "SELECT * FROM siswa ORDER BY nis ASC";
        $result = $this->db->query($query);
        $data = [];

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // 2. Ambil 1 Data Siswa Berdasarkan NIS (Untuk Form Edit)
    public function getSiswaByNis($nis)
    {
        $nis = $this->db->real_escape_string($nis);
        $query = "SELECT * FROM siswa WHERE nis = '$nis'";
        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc() : null;
    }

    // Hash password; pakai Argon2id bila tersedia di server, jika tidak pakai default (bcrypt)
    private function hashPassword($password)
    {
        $algo = defined('PASSWORD_ARGON2ID') ? PASSWORD_ARGON2ID : PASSWORD_DEFAULT;
        return password_hash($password, $algo);
    }

    // Validasi Nama: Hanya mengizinkan huruf besar (A-Z), huruf kecil (a-z), dan spasi (tanpa angka & tanda baca)
    public function isValidNama($nama)
    {
        return preg_match('/^[a-zA-Z ]+$/', $nama);
    }

    // 3. Tambah Siswa Baru (Dengan Validasi NIS 10 Digit, Validasi Nama, & Cek Duplikat)
    public function tambahSiswa($nis, $nama, $kelas, $password)
    {
        // Validasi ketat: NIS harus berupa angka dan panjangnya persis 10 karakter
        if (!preg_match('/^[0-9]{10}$/', $nis)) {
            return false;
        }

        // Validasi nama: hanya huruf dan spasi
        if (!$this->isValidNama($nama)) {
            return false;
        }

        $nis   = $this->db->real_escape_string($nis);
        $nama  = $this->db->real_escape_string($nama);
        $kelas = $this->db->real_escape_string($kelas);

        // Cek apakah NIS sudah terdaftar sebelumnya untuk menghindari duplikasi klik ganda
        $cek_nis = $this->db->query("SELECT * FROM siswa WHERE nis = '$nis'");
        if ($cek_nis && $cek_nis->num_rows > 0) {
            return false;
        }

        $hashed_pw = $this->hashPassword($password);

        $query = "INSERT INTO siswa (nis, nama, kelas, password) VALUES ('$nis', '$nama', '$kelas', '$hashed_pw')";
        return $this->db->query($query);
    }

    // 4. Update Data Siswa (Dengan Validasi NIS 10 Digit & Validasi Nama)
    public function updateSiswa($nis, $nama, $kelas, $password = null)
    {
        // Validasi ketat: NIS harus berupa angka dan panjangnya persis 10 karakter
        if (!preg_match('/^[0-9]{10}$/', $nis)) {
            return false;
        }

        // Validasi nama: hanya huruf dan spasi
        if (!$this->isValidNama($nama)) {
            return false;
        }

        $nis   = $this->db->real_escape_string($nis);
        $nama  = $this->db->real_escape_string($nama);
        $kelas = $this->db->real_escape_string($kelas);

        if (!empty($password)) {
            $hashed_pw = $this->hashPassword($password);
            $query = "UPDATE siswa SET nama = '$nama', kelas = '$kelas', password = '$hashed_pw' WHERE nis = '$nis'";
        } else {
            $query = "UPDATE siswa SET nama = '$nama', kelas = '$kelas' WHERE nis = '$nis'";
        }

        return $this->db->query($query);
    }

    // 5. Hapus Data Siswa (Dilindungi oleh Try-Catch Foreign Key Constraint)
    public function hapusSiswa($nis)
    {
        $nis = $this->db->real_escape_string($nis);

        // Tabel aspirasi berelasi ke siswa lewat id_siswa (bukan nis), jadi cari id_siswa dulu
        $resSiswa = $this->db->query("SELECT id_siswa FROM siswa WHERE nis = '$nis'");
        $rowSiswa = $resSiswa ? $resSiswa->fetch_assoc() : null;
        if (!$rowSiswa) {
            return false;
        }
        $idSiswa = intval($rowSiswa['id_siswa']);

        // Cek manual terlebih dahulu
        $cekQuery = "SELECT COUNT(*) as total FROM aspirasi WHERE id_siswa = '$idSiswa'";
        $resultCek = $this->db->query($cekQuery);
        if ($resultCek) {
            $rowCek = $resultCek->fetch_assoc();
            if ($rowCek['total'] > 0) {
                return false;
            }
        }

        // Jalankan hapus dengan try-catch untuk mengantisipasi aturan Foreign Key database
        try {
            $query = "DELETE FROM siswa WHERE nis = '$nis'";
            return $this->db->query($query);
        } catch (mysqli_sql_exception $e) {
            return false;
        }
    }
}
