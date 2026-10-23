<?php
require_once 'm_koneksi.php';

class m_kategori
{
    private $db;

    public function __construct()
    {
        $koneksiObj = new m_koneksi();
        $this->db = $koneksiObj->hubungkan();
    }

    public function getAllKategori()
    {
        $query = "SELECT * FROM kategori ORDER BY id_kategori ASC";
        $result = $this->db->query($query);
        $data = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getKategoriById($id)
    {
        $id = intval($id);
        $query = "SELECT * FROM kategori WHERE id_kategori = '$id'";
        $result = $this->db->query($query);
        return $result ? $result->fetch_assoc() : null;
    }

    // Cek apakah nama kategori sudah ada (tidak peduli huruf besar/kecil).
    // $kecualiId dipakai saat edit supaya kategori itu sendiri tidak dianggap duplikat.
    public function namaSudahAda($nama, $kecualiId = 0)
    {
        $nama = $this->db->real_escape_string($nama);
        $kecualiId = intval($kecualiId);
        $query = "SELECT COUNT(*) AS total FROM kategori
                  WHERE LOWER(nama_kategori) = LOWER('$nama')
                  AND id_kategori <> '$kecualiId'";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['total']) > 0;
        }
        return false;
    }

    // Return: "sukses" | "duplikat" | "gagal"
    public function tambahKategori($nama)
    {
        if ($this->namaSudahAda($nama)) {
            return "duplikat";
        }
        try {
            $nama = $this->db->real_escape_string($nama);
            $query = "INSERT INTO kategori (nama_kategori) VALUES ('$nama')";
            return $this->db->query($query) ? "sukses" : "gagal";
        } catch (mysqli_sql_exception $e) {
            return "gagal";
        }
    }

    // Return: "sukses" | "duplikat" | "gagal"
    public function updateKategori($id, $nama)
    {
        $id = intval($id);
        if ($this->namaSudahAda($nama, $id)) {
            return "duplikat";
        }
        try {
            $nama = $this->db->real_escape_string($nama);
            $query = "UPDATE kategori SET nama_kategori = '$nama' WHERE id_kategori = '$id'";
            return $this->db->query($query) ? "sukses" : "gagal";
        } catch (mysqli_sql_exception $e) {
            return "gagal";
        }
    }

    // Cek apakah kategori masih dipakai oleh pengaduan yang statusnya aktif (Belum Selesai & Belum Ditolak)
    public function cekKategoriDigunakan($id_kategori)
    {
        $id_kategori = intval($id_kategori);
        $query = "SELECT COUNT(*) as total FROM aspirasi
                  WHERE id_kategori = '$id_kategori'
                  AND status NOT IN ('Selesai', 'Ditolak')";

        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['total']) > 0;
        }
        return false;
    }

    // Return: "sukses" | "digunakan" | "riwayat" | "gagal"
    public function hapusKategori($id)
    {
        $id = intval($id);

        // Jika kategori masih dipakai di pengaduan aktif, gagalkan penghapusan
        if ($this->cekKategoriDigunakan($id)) {
            return "digunakan";
        }

        try {
            $query = "DELETE FROM kategori WHERE id_kategori = '$id'";
            return $this->db->query($query) ? "sukses" : "gagal";
        } catch (mysqli_sql_exception $e) {
            // 1451 = masih direferensikan foreign key (mis. laporan Selesai/Ditolak)
            return ($e->getCode() == 1451) ? "riwayat" : "gagal";
        }
    }
}
