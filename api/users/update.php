<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// ==========================================
// CEK ADMIN
// ==========================================

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


// ==========================================
// CEK METHOD
// ==========================================

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST" && $method !== "PUT") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST untuk upload foto atau PUT untuk update JSON."
    ]);

    exit;
}


// ==========================================
// AMBIL INPUT
// ==========================================

if ($method === "POST") {

    // POST digunakan untuk multipart/form-data
    $input = $_POST;

} else {

    // PUT digunakan untuk JSON
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


// ==========================================
// AMBIL ID USER
// ==========================================

$id_user = (int) ($input["id_user"] ?? 0);

if ($id_user <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID user wajib diisi."
    ]);

    exit;
}


// ==========================================
// CEK USER + AMBIL FOTO LAMA
// ==========================================

$stmt = $conn->prepare("
    SELECT
        id_user,
        foto
    FROM users
    WHERE id_user = ?
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

$stmt->bind_param("i", $id_user);

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
        "message" => "Anggota tidak ditemukan."
    ]);

    exit;
}

$userData = $result->fetch_assoc();

$fotoLama = $userData["foto"] ?? "";

$stmt->close();


// ==========================================
// AMBIL DATA
// ==========================================

$nama = trim($input["nama"] ?? "");
$nik = trim($input["nik"] ?? "");
$alamat = trim($input["alamat"] ?? "");
$no_telp = trim($input["no_telp"] ?? "");
$email = strtolower(trim($input["email"] ?? ""));
$status = trim($input["status"] ?? "aktif");


// ==========================================
// VALIDASI DATA WAJIB
// ==========================================

if (
    $nama === "" ||
    $nik === "" ||
    $email === ""
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama, NIK, dan email wajib diisi."
    ]);

    exit;
}


// ==========================================
// VALIDASI NAMA
// ==========================================

if (strlen($nama) < 3) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama minimal 3 karakter."
    ]);

    exit;
}

if (strlen($nama) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama maksimal 100 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI NIK
// ==========================================

if (!preg_match('/^[0-9]{16}$/', $nik)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "NIK harus terdiri dari 16 digit angka."
    ]);

    exit;
}


// ==========================================
// VALIDASI ALAMAT
// ==========================================

if (strlen($alamat) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Alamat maksimal 255 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI NOMOR TELEPON
// ==========================================

if ($no_telp !== "") {

    if (!preg_match('/^\+?[0-9]{10,15}$/', $no_telp)) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Nomor telepon harus 10-15 digit dan hanya boleh berisi angka."
        ]);

        exit;
    }
}


// ==========================================
// VALIDASI EMAIL
// ==========================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);

    exit;
}

if (strlen($email) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email maksimal 100 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI STATUS
// ==========================================

$allowedStatus = [
    "aktif",
    "nonaktif",
    "diblokir"
];

if (!in_array($status, $allowedStatus, true)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Status user tidak valid."
    ]);

    exit;
}


// ==========================================
// CEK NIK DUPLIKAT
// ==========================================

$stmt = $conn->prepare("
    SELECT id_user
    FROM users
    WHERE nik = ?
      AND id_user != ?
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

$stmt->bind_param("si", $nik, $id_user);

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
        "message" => "NIK sudah digunakan oleh anggota lain."
    ]);

    exit;
}

$stmt->close();


// ==========================================
// CEK EMAIL DUPLIKAT
// ==========================================

$stmt = $conn->prepare("
    SELECT id_user
    FROM users
    WHERE email = ?
      AND id_user != ?
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

$stmt->bind_param("si", $email, $id_user);

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
        "message" => "Email sudah digunakan oleh anggota lain."
    ]);

    exit;
}

$stmt->close();


// ==========================================
// FOTO
// ==========================================

$fotoBaru = $fotoLama;
$fotoPathBaru = "";


// ==========================================
// PROSES UPLOAD FOTO
// HANYA UNTUK POST MULTIPART/FORM-DATA
// ==========================================

if (
    $method === "POST" &&
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    // --------------------------------------
    // CEK ERROR UPLOAD
    // --------------------------------------

    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Foto gagal diupload."
        ]);

        exit;
    }


    // --------------------------------------
    // BATAS UKURAN
    // Maksimal 2 MB
    // --------------------------------------

    $maxSize = 2 * 1024 * 1024;

    if ($_FILES["foto"]["size"] > $maxSize) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }


    // --------------------------------------
    // CEK MIME TYPE
    // --------------------------------------

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mimeType = $finfo->file(
        $_FILES["foto"]["tmp_name"]
    );

    $allowedMime = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowedMime[$mimeType])) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format foto hanya boleh JPG, PNG, atau WEBP."
        ]);

        exit;
    }


    // --------------------------------------
    // CEK FILE BENAR-BENAR GAMBAR
    // --------------------------------------

    if (
        @getimagesize(
            $_FILES["foto"]["tmp_name"]
        ) === false
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "File yang diupload bukan gambar yang valid."
        ]);

        exit;
    }


    // --------------------------------------
    // FOLDER UPLOAD
    // --------------------------------------

    $uploadDir = __DIR__ . "/../../uploads/users/";

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


    // --------------------------------------
    // BUAT NAMA FILE RANDOM
    // --------------------------------------

    try {
        $randomName = bin2hex(random_bytes(16));
    } catch (Exception $e) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal membuat nama file."
        ]);

        exit;
    }

    $extension = $allowedMime[$mimeType];

    $fileName = "user_" . $randomName . "." . $extension;

    $targetPath = $uploadDir . $fileName;


    // --------------------------------------
    // PINDAHKAN FILE
    // --------------------------------------

    if (
        !move_uploaded_file(
            $_FILES["foto"]["tmp_name"],
            $targetPath
        )
    ) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan foto."
        ]);

        exit;
    }


    // --------------------------------------
    // PATH DATABASE
    // --------------------------------------

    $fotoPathBaru = "uploads/users/" . $fileName;

    $fotoBaru = $fotoPathBaru;
}


// ==========================================
// UPDATE USER
// ==========================================

$stmt = $conn->prepare("
    UPDATE users
    SET
        nama = ?,
        nik = ?,
        alamat = ?,
        no_telp = ?,
        email = ?,
        foto = ?,
        status = ?
    WHERE id_user = ?
");

if (!$stmt) {

    // Jika foto baru sudah tersimpan, hapus
    if ($fotoPathBaru !== "") {

        $uploadedFile = __DIR__ . "/../../" . $fotoPathBaru;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "sssssssi",
    $nama,
    $nik,
    $alamat,
    $no_telp,
    $email,
    $fotoBaru,
    $status,
    $id_user
);


// ==========================================
// EXECUTE UPDATE
// ==========================================

if ($stmt->execute()) {

    // --------------------------------------
    // HAPUS FOTO LAMA
    // --------------------------------------

    if (
        $fotoPathBaru !== "" &&
        $fotoLama !== "" &&
        $fotoLama !== $fotoPathBaru
    ) {

        // Pastikan file lama berasal dari folder
        // uploads/users/
        if (
            strpos(
                $fotoLama,
                "uploads/users/"
            ) === 0
        ) {

            $oldFile = __DIR__ . "/../../" . $fotoLama;

            if (is_file($oldFile)) {
                unlink($oldFile);
            }
        }
    }


    // --------------------------------------
    // RESPONSE
    // --------------------------------------

    http_response_code(200);

    echo json_encode([
        "status" => true,
        "message" => "Data anggota berhasil diperbarui.",
        "data" => [
            "id_user" => $id_user,
            "nama" => $nama,
            "nik" => $nik,
            "email" => $email,
            "foto" => $fotoBaru !== "" ? $fotoBaru : null,
            "status" => $status
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} else {

    // --------------------------------------
    // JIKA UPDATE GAGAL
    // HAPUS FOTO BARU
    // --------------------------------------

    if ($fotoPathBaru !== "") {

        $uploadedFile = __DIR__ . "/../../" . $fotoPathBaru;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memperbarui data anggota."
    ]);
}


$stmt->close();
$conn->close();

?>