<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// =====================================
// CEK LOGIN ADMIN
// =====================================

if (
    !isset($_SESSION["id_admin"]) ||
    !is_numeric($_SESSION["id_admin"]) ||
    (int) $_SESSION["id_admin"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. Admin harus login."
    ]);

    exit;
}


// =====================================
// CEK METHOD
// =====================================
//
// POST = update profil + optional upload foto
// PUT  = update profil tanpa upload foto
//

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST" && $method !== "PUT") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST atau PUT."
    ]);

    exit;
}


// =====================================
// AMBIL ID ADMIN DARI SESSION
// =====================================

$id_admin = (int) $_SESSION["id_admin"];


// =====================================
// AMBIL INPUT
// =====================================

if ($method === "POST") {

    // Untuk multipart/form-data
    $input = $_POST;

} else {

    // Untuk PUT JSON
    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!is_array($input)) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Data JSON tidak valid."
        ]);

        exit;
    }
}


// =====================================
// AMBIL DATA ADMIN LAMA
// =====================================

$stmt = $conn->prepare("
    SELECT
        id_admin,
        nama,
        username,
        email,
        alamat,
        no_telp,
        foto
    FROM admins
    WHERE id_admin = ?
    LIMIT 1
");

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param("i", $id_admin);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data admin tidak ditemukan."
    ]);

    exit;
}

$adminLama = $result->fetch_assoc();

$fotoLama = $adminLama["foto"] ?? "";

$stmt->close();


// =====================================
// AMBIL INPUT PROFIL
// =====================================

$nama = trim($input["nama"] ?? "");
$username = trim($input["username"] ?? "");
$email = strtolower(trim($input["email"] ?? ""));
$alamat = trim($input["alamat"] ?? "");
$no_telp = trim($input["no_telp"] ?? "");


// =====================================
// VALIDASI NAMA
// =====================================

if ($nama === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama wajib diisi."
    ]);

    exit;
}

if (strlen($nama) < 3 || strlen($nama) > 100) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama harus 3-100 karakter."
    ]);

    exit;
}


// =====================================
// VALIDASI USERNAME
// =====================================

if ($username === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Username wajib diisi."
    ]);

    exit;
}

if (strlen($username) < 3 || strlen($username) > 100) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Username harus 3-100 karakter."
    ]);

    exit;
}


// =====================================
// VALIDASI EMAIL
// =====================================

if ($email === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email wajib diisi."
    ]);

    exit;
}

if (strlen($email) > 100 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);

    exit;
}


// =====================================
// VALIDASI ALAMAT
// =====================================

if (strlen($alamat) > 255) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Alamat maksimal 255 karakter."
    ]);

    exit;
}


// =====================================
// VALIDASI NO TELEPON
// =====================================

if ($no_telp !== "") {

    if (!preg_match('/^\+?[0-9]{10,15}$/', $no_telp)) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Nomor telepon tidak valid."
        ]);

        exit;
    }
}


// =====================================
// CEK USERNAME DUPLIKAT
// =====================================

$stmt = $conn->prepare("
    SELECT id_admin
    FROM admins
    WHERE username = ?
    AND id_admin != ?
    LIMIT 1
");

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "si",
    $username,
    $id_admin
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Username sudah digunakan admin lain."
    ]);

    exit;
}

$stmt->close();


// =====================================
// CEK EMAIL DUPLIKAT
// =====================================

$stmt = $conn->prepare("
    SELECT id_admin
    FROM admins
    WHERE email = ?
    AND id_admin != ?
    LIMIT 1
");

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "si",
    $email,
    $id_admin
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Email sudah digunakan admin lain."
    ]);

    exit;
}

$stmt->close();


// =====================================
// FOTO
// =====================================

$fotoBaru = $fotoLama;
$fileFotoBaru = null;


// =====================================
// UPLOAD FOTO BARU
// =====================================

if (
    $method === "POST" &&
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $file = $_FILES["foto"];


    // =================================
    // CEK ERROR UPLOAD
    // =================================

    if ($file["error"] !== UPLOAD_ERR_OK) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Gagal mengupload foto."
        ]);

        exit;
    }


    // =================================
    // MAKSIMAL 2 MB
    // =================================

    if ($file["size"] > 2 * 1024 * 1024) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }


    // =================================
    // VALIDASI MIME
    // =================================

    $allowedMime = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    $finfo = finfo_open(FILEINFO_MIME_TYPE);

    if (!$finfo) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Terjadi kesalahan pada server."
        ]);

        exit;
    }

    $mime = finfo_file(
        $finfo,
        $file["tmp_name"]
    );

    finfo_close($finfo);


    if (!isset($allowedMime[$mime])) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format foto harus JPG, PNG, atau WEBP."
        ]);

        exit;
    }


    // =================================
    // VALIDASI GAMBAR
    // =================================

    if (@getimagesize($file["tmp_name"]) === false) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "File foto tidak valid."
        ]);

        exit;
    }


    // =================================
    // FOLDER UPLOAD
    // =================================

    $uploadDir = __DIR__ . "/../../uploads/admins/";

    if (!is_dir($uploadDir)) {

        if (!mkdir($uploadDir, 0755, true)) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Folder upload tidak dapat dibuat."
            ]);

            exit;
        }
    }


    // =================================
    // NAMA FILE RANDOM
    // =================================

    $extension = $allowedMime[$mime];

    $fileName =
        "admin_" .
        bin2hex(random_bytes(16)) .
        "." .
        $extension;

    $fileFotoBaru = $uploadDir . $fileName;

    $fotoBaru = "uploads/admins/" . $fileName;


    // =================================
    // PINDAHKAN FILE
    // =================================

    if (!move_uploaded_file(
        $file["tmp_name"],
        $fileFotoBaru
    )) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Foto gagal disimpan."
        ]);

        exit;
    }
}


// =====================================
// UPDATE DATABASE
// =====================================

$stmt = $conn->prepare("
    UPDATE admins
    SET
        nama = ?,
        username = ?,
        email = ?,
        alamat = ?,
        no_telp = ?,
        foto = ?
    WHERE id_admin = ?
");

if (!$stmt) {

    if ($fileFotoBaru && file_exists($fileFotoBaru)) {
        unlink($fileFotoBaru);
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}


$stmt->bind_param(
    "ssssssi",
    $nama,
    $username,
    $email,
    $alamat,
    $no_telp,
    $fotoBaru,
    $id_admin
);


// =====================================
// EXECUTE
// =====================================

if ($stmt->execute()) {

    // =================================
    // HAPUS FOTO LAMA
    // Hanya file lokal uploads/admins/
    // =================================

    if (
        $fileFotoBaru &&
        $fotoLama !== "" &&
        strpos($fotoLama, "uploads/admins/") === 0
    ) {

        $oldFile = __DIR__ . "/../../" . $fotoLama;

        if (
            file_exists($oldFile) &&
            is_file($oldFile)
        ) {
            unlink($oldFile);
        }
    }


    http_response_code(200);

    echo json_encode([
        "status" => true,
        "message" => "Profil admin berhasil diperbarui.",
        "data" => [
            "id_admin" => $id_admin,
            "nama" => $nama,
            "username" => $username,
            "email" => $email,
            "alamat" => $alamat,
            "no_telp" => $no_telp,
            "foto" => $fotoBaru
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} else {

    // =================================
    // JIKA DATABASE GAGAL
    // HAPUS FOTO BARU
    // =================================

    if ($fileFotoBaru && file_exists($fileFotoBaru)) {
        unlink($fileFotoBaru);
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memperbarui profil admin."
    ]);
}


$stmt->close();
$conn->close();

?>