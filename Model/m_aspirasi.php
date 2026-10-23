<?php
require_once 'm_koneksi.php';

class m_aspirasi
{
    private $db;

    public function __construct()
    {
        $koneksiObj = new m_koneksi();
        $this->db = $koneksiObj->hubungkan();
    }

    public function tambahAspirasi($id_siswa, $id_kategori, $lokasi, $keterangan, $bukti_siswa = null)
    {
        $id_siswa    = intval($id_siswa);
        $id_kategori = intval($id_kategori);
        $lokasi      = $this->db->real_escape_string(trim($lokasi));
        $keterangan  = $this->db->real_escape_string(trim($keterangan));

        $val_bukti = $bukti_siswa ? "'" . $this->db->real_escape_string($bukti_siswa) . "'" : "NULL";

        $query = "INSERT INTO aspirasi (id_siswa, id_kategori, lokasi, ket, bukti_siswa, status) 
                  VALUES ('$id_siswa', '$id_kategori', '$lokasi', '$keterangan', $val_bukti, 'Menunggu')";

        return $this->db->query($query);
    }

    public function getAspirasiBySiswa($id_siswa)
    {
        $id_siswa = intval($id_siswa);
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.id_siswa = '$id_siswa' 
                  ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAspirasiAktifBySiswa($id_siswa)
    {
        $id_siswa = intval($id_siswa);
        // Status Selesai atau Ditolak tidak masuk ke daftar aktif siswa
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.id_siswa = '$id_siswa' AND aspirasi.status NOT IN ('Selesai', 'Ditolak')
                  ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAllAspirasi()
    {
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  GROUP BY aspirasi.id_aspirasi
                  ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAspirasiSelesai()
    {
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.status IN ('Selesai', 'Ditolak')
                  GROUP BY aspirasi.id_aspirasi
                  ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAspirasiAktif()
    {
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.status NOT IN ('Selesai', 'Ditolak')
                  GROUP BY aspirasi.id_aspirasi
                  ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    // --- FUNGSI PAGINATION & FILTER ADMIN ---

    public function getAspirasiAktifLimit($limit, $offset, $id_kategori = '', $bulan = '', $tanggal = '')
    {
        $limit  = intval($limit);
        $offset = intval($offset);

        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE aspirasi.status NOT IN ('Selesai', 'Ditolak')";

        if (!empty($id_kategori)) {
            $id_kategori = intval($id_kategori);
            $query .= " AND aspirasi.id_kategori = '$id_kategori'";
        }

        if (!empty($bulan)) {
            $bulan = $this->db->real_escape_string($bulan);
            $query .= " AND DATE_FORMAT(aspirasi.created_at, '%m') = '$bulan'";
        }

        if (!empty($tanggal)) {
            $tanggal = $this->db->real_escape_string($tanggal);
            $query .= " AND DATE(aspirasi.created_at) = '$tanggal'";
        }

        $query .= " GROUP BY aspirasi.id_aspirasi ORDER BY aspirasi.id_aspirasi DESC LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getTotalAspirasiAktif($id_kategori = '', $bulan = '', $tanggal = '')
    {
        $query = "SELECT COUNT(*) as total FROM aspirasi WHERE status NOT IN ('Selesai', 'Ditolak')";

        if (!empty($id_kategori)) {
            $id_kategori = intval($id_kategori);
            $query .= " AND id_kategori = '$id_kategori'";
        }

        if (!empty($bulan)) {
            $bulan = $this->db->real_escape_string($bulan);
            $query .= " AND DATE_FORMAT(created_at, '%m') = '$bulan'";
        }

        if (!empty($tanggal)) {
            $tanggal = $this->db->real_escape_string($tanggal);
            $query .= " AND DATE(created_at) = '$tanggal'";
        }

        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['total']);
        }
        return 0;
    }

    public function getFilteredAspirasi($id_siswa = '', $id_kategori = '', $bulan = '', $tanggal = '', $status = '')
    {
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE 1=1";

        if (!empty($id_siswa)) {
            $id_siswa = intval($id_siswa);
            $query .= " AND aspirasi.id_siswa = '$id_siswa'";
        }

        if (!empty($id_kategori)) {
            $id_kategori = intval($id_kategori);
            $query .= " AND aspirasi.id_kategori = '$id_kategori'";
        }

        if (!empty($bulan)) {
            $bulan = $this->db->real_escape_string($bulan);
            $query .= " AND DATE_FORMAT(aspirasi.created_at, '%m') = '$bulan'";
        }

        if (!empty($tanggal)) {
            $tanggal = $this->db->real_escape_string($tanggal);
            $query .= " AND DATE(aspirasi.created_at) = '$tanggal'";
        }

        if (!empty($status)) {
            $status = $this->db->real_escape_string($status);
            $query .= " AND aspirasi.status = '$status'";
        }

        $query .= " GROUP BY aspirasi.id_aspirasi ORDER BY aspirasi.id_aspirasi DESC";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getAspirasiById($id_aspirasi)
    {
        $id_aspirasi = intval($id_aspirasi);
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori 
                  FROM aspirasi 
                  LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                  LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                  WHERE aspirasi.id_aspirasi = '$id_aspirasi'";

        $result = $this->db->query($query);
        return ($result && $result->num_rows > 0) ? $result->fetch_assoc() : null;
    }

    public function hapusAspirasi($id_aspirasi)
    {
        $id_aspirasi = intval($id_aspirasi);
        $query = "DELETE FROM aspirasi WHERE id_aspirasi = '$id_aspirasi'";
        return $this->db->query($query);
    }

    public function isVideoFile($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return in_array($ext, ['mp4', 'mov', 'avi', 'mkv']);
    }

    // --- FUNGSI CEK STATUS ASPIRASI ---
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

    // --- FUNGSI TANGGAPI ASPIRASI ---
    public function tanggapiAspirasi($id_aspirasi, $status_baru, $tanggapan, $id_admin, $bukti_admin = null)
    {
        $id_aspirasi  = intval($id_aspirasi);
        $status_baru  = $this->db->real_escape_string(trim($status_baru));
        $tanggapan    = $this->db->real_escape_string(trim($tanggapan));
        $id_admin     = intval($id_admin);

        // Kolom bukti_admin hanya diubah bila ada file baru,
        // supaya foto dari tanggapan sebelumnya tidak terhapus (jadi NULL).
        $updateBuktiSql = "";
        if (!empty($bukti_admin)) {
            $updateBuktiSql = ", bukti_admin = '" . $this->db->real_escape_string($bukti_admin) . "'";
        }

        $query = "UPDATE aspirasi
                  SET status = '$status_baru',
                      feedback = '$tanggapan',
                      id_admin = '$id_admin'
                      $updateBuktiSql
                  WHERE id_aspirasi = '$id_aspirasi'";

        $execute = $this->db->query($query);

        // Catat ke tb_history_aspirasi saat status Selesai
        // (dipakai halaman riwayat selesai milik siswa: m_tanggapan::getHistoryBySiswa)
        if ($execute && $status_baru === 'Selesai') {
            $cekHistory = $this->db->query("SELECT id_aspirasi FROM tb_history_aspirasi WHERE id_aspirasi = '$id_aspirasi'");
            if ($cekHistory && $cekHistory->num_rows == 0) {
                $this->db->query("INSERT INTO tb_history_aspirasi (id_aspirasi, status_akhir, feedback_admin)
                                  VALUES ('$id_aspirasi', 'Selesai', '$tanggapan')");
            }
        }

        return $execute;
    }

    // --- FUNGSI TOLAK ASPIRASI ---
    public function tolakAspirasi($id_aspirasi, $id_admin, $alasan = 'Pengaduan ditolak karena tidak sesuai dengan ketentuan sarana prasarana.')
    {
        $id_aspirasi = intval($id_aspirasi);
        $id_admin    = intval($id_admin);
        $alasan      = $this->db->real_escape_string(trim($alasan));

        $query = "UPDATE aspirasi 
                  SET status = 'Ditolak', 
                      feedback = '$alasan', 
                      id_admin = '$id_admin'
                  WHERE id_aspirasi = '$id_aspirasi'";

        return $this->db->query($query);
    }

    // --- FUNGSI PAGINASI HISTORI SELESAI / DITOLAK ---

    public function getAspirasiSelesaiLimit($limit, $offset)
    {
        $limit  = intval($limit);
        $offset = intval($offset);

        // Mengambil status Selesai atau Ditolak untuk masuk ke histori arsip
        $query = "SELECT aspirasi.*, siswa.nama AS nama_siswa, siswa.kelas, kategori.nama_kategori AS nama_kategori, aspirasi.feedback AS feedback_admin 
                 FROM aspirasi 
                 LEFT JOIN siswa ON aspirasi.id_siswa = siswa.id_siswa 
                 LEFT JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                 WHERE aspirasi.status IN ('Selesai', 'Ditolak')
                 GROUP BY aspirasi.id_aspirasi
                 ORDER BY aspirasi.id_aspirasi DESC 
                 LIMIT $limit OFFSET $offset";

        $result = $this->db->query($query);
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getTotalAspirasiSelesai()
    {
        $query = "SELECT COUNT(*) as total FROM aspirasi WHERE status IN ('Selesai', 'Ditolak')";
        $result = $this->db->query($query);
        if ($result && $row = $result->fetch_assoc()) {
            return intval($row['total']);
        }
        return 0;
    }
}
