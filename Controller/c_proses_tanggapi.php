<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Panggil model yang sesuai (m_tanggapan, bukan m_aspirasi)
require_once '../Model/m_tanggapan.php';

class c_tanggapan
{
    private $modelTanggapan;

    public function __construct()
    {
        // Gunakan class m_tanggapan
        $this->modelTanggapan = new m_tanggapan();
    }

    public function prosesTanggapan()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: ../View/v_login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_aspirasi = $_POST['id_aspirasi'] ?? '';
            $status      = $_POST['status'] ?? '';
            $tanggapan   = $_POST['tanggapan'] ?? '';
            $bukti_admin = null;

            // Proses upload file bukti admin jika ada
            if (isset($_FILES['bukti_admin']) && $_FILES['bukti_admin']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath   = $_FILES['bukti_admin']['tmp_name'];
                $fileName      = $_FILES['bukti_admin']['name'];
                $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

                $allowedExtensions = ['jpg', 'jpeg', 'png', 'mp4', 'mov', 'avi', 'mkv'];

                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName   = 'admin_' . time() . '_' . uniqid() . '.' . $fileExtension;
                    $uploadFileDir = '../uploads/';

                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $bukti_admin = $newFileName;
                    }
                }
            }

            if (!empty($id_aspirasi)) {
                $id_admin = $_SESSION['id_admin'] ?? 1;
                
                // Panggil method dari objek m_tanggapan
                $this->modelTanggapan->tanggapiAspirasi($id_aspirasi, $status, $tanggapan, $id_admin, $bukti_admin);
            }
        }

        header("Location: ../View/v_admin.php");
        exit();
    }
}

// Eksekusi Otomatis
$controller = new c_tanggapan();
$controller->prosesTanggapan();
?>