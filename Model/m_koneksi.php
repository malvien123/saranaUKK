<?php
class m_koneksi
{
    private $host     = "localhost";
    private $username = "root";
    private $pass     = "";
    private $db       = "sarpras";

    public $koneksi;

    public function __construct()
    {
        // Melakukan koneksi ke database
        $this->koneksi = new mysqli($this->host, $this->username, $this->pass, $this->db);

        // Cek apakah ada error saat koneksi
        if ($this->koneksi->connect_error) {
            die("Koneksi database gagal: " . $this->koneksi->connect_error);
        }
    }

    // TAMBAHKAN FUNGSI INI agar bisa dipanggil oleh Controller/View
    public function hubungkan()
    {
        return $this->koneksi;
    }

    // Destruct sebaiknya tidak menutup koneksi terlalu cepat dalam pola MVC
    // Tapi jika ingin tetap ada, pastikan dicek dulu
    public function __destruct()
    {
        // Biarkan kosong atau hapus jika sering menyebabkan error "MySQL server has gone away"
    }
}