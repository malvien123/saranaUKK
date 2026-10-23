<?php
require_once 'm_koneksi.php';

class m_tanggapan
{
    private $db;

    public function __construct()
    {
        $koneksiObj = new m_koneksi();
        $this->db = $koneksiObj->hubungkan();
    }

    public function cekStatusAspirasi($id_aspirasi)
    {
        $id_aspirasi = intval($id_aspirasi);
        $query = "SELECT status FROM aspirasi WHERE id_aspirasi = '$id_aspirasi'";
        
        $result = $this->db->query($query);
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['status'];
        }
        return null;
    }

    public function tanggapiAspirasi($id_aspirasi, $status, $tanggapan, $id_admin, $bukti_admin = null)
    {
        $id_aspirasi = intval($id_aspirasi);
        $status      = $this->db->real_escape_string($status);
        $tanggapan   = $this->db->real_escape_string($tanggapan);
        $id_admin    = intval($id_admin);
        
        $updateBuktiSql = "";
        if ($bukti_admin !== null) {
            $bukti_admin = $this->db->real_escape_string($bukti_admin);
            $updateBuktiSql = ", bukti_admin = '$bukti_admin'";
        }

        $query = "UPDATE aspirasi 
                    SET status = '$status', feedback = '$tanggapan', id_admin = '$id_admin' $updateBuktiSql 
                    WHERE id_aspirasi = '$id_aspirasi'";
                
        $execute = $this->db->query($query);

        if ($execute && $status == 'Selesai') {
            $cekHistory = $this->db->query("SELECT id_aspirasi FROM tb_history_aspirasi WHERE id_aspirasi = '$id_aspirasi'");
            if ($cekHistory && $cekHistory->num_rows == 0) {
                $queryInsert = "INSERT INTO tb_history_aspirasi (id_aspirasi, status_akhir, feedback_admin) 
                                VALUES ('$id_aspirasi', 'Selesai', '$tanggapan')";
                $this->db->query($queryInsert);
            }
        }

        return $execute;
    }

    public function getAllHistory()
    {
        $query = "SELECT tb_history_aspirasi.*, aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori, admin.username AS nama_admin 
                  FROM tb_history_aspirasi 
                  JOIN aspirasi ON tb_history_aspirasi.id_aspirasi = aspirasi.id_aspirasi
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  LEFT JOIN admin ON aspirasi.id_admin = admin.id_admin
                  ORDER BY tb_history_aspirasi.id_history DESC";
                
        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getHistoryBySiswa($id_siswa)
    {
        $id_siswa = intval($id_siswa);
        $query = "SELECT tb_history_aspirasi.*, aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM tb_history_aspirasi 
                  JOIN aspirasi ON tb_history_aspirasi.id_aspirasi = aspirasi.id_aspirasi
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.id_siswa = '$id_siswa' 
                  ORDER BY tb_history_aspirasi.id_history DESC";
                
        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
}
?>