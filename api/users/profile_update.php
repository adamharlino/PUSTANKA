<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // production HTTPS => true
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK SESSION USER
// ===============================
if (
    !isset($_SESSION["id_user"]) ||
    !is_numeric($_SESSION["id_user"]) ||
    (int) $_SESSION["id_user"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "user"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. User harus login."
    ]);

    exit;
}

// ===============================
// METHOD
// ===============================
// POST = bisa edit + upload foto
// PUT  = edit data tanpa foto
// ===============================

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST" && $method !== "PUT") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST atau PUT."
    ]);

    exit;
}

$id_user = (int) $_SESSION["id_user"];

// ===============================
// AMBIL DATA USER LAMA
// ===============================

$stmt = $conn->prepare("
    SELECT
        id_user,
        nama,
        alamat,
        no_telp,
        email,
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
        "message" => "Gagal mengambil data user."
    ]);

    exit;
}

$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

if (!$user) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data user tidak ditemukan."
    ]);

    exit;
}

$oldFoto = $user["foto"];

// ===============================
// AMBIL INPUT
// ===============================

if ($method === "POST") {

    $nama = trim($_POST["nama"] ?? "");
    $alamat = trim($_POST["alamat"] ?? "");
    $no_telp = trim($_POST["no_telp"] ?? "");
    $email = strtolower(trim($_POST["email"] ?? ""));

} else {

    $input = json_decode(file_get_contents("php://input"), true);

    if (!is_array($input)) {
        $input = [];
    }

    $nama = trim($input["nama"] ?? "");
    $alamat = trim($input["alamat"] ?? "");
    $no_telp = trim($input["no_telp"] ?? "");
    $email = strtolower(trim($input["email"] ?? ""));
}

// ===============================
// VALIDASI NAMA
// ===============================

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

// ===============================
// VALIDASI ALAMAT
// ===============================

if (strlen($alamat) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Alamat maksimal 255 karakter."
    ]);

    exit;
}

// ===============================
// VALIDASI NO TELEPON
// ===============================

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

// ===============================
// VALIDASI EMAIL
// ===============================

if ($email === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email wajib diisi."
    ]);

    exit;
}

if (
    strlen($email) > 100 ||
    !filter_var($email, FILTER_VALIDATE_EMAIL)
) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);

    exit;
}

// ===============================
// CEK EMAIL DUPLIKAT
// ===============================

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

if ($result->fetch_assoc()) {

    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Email sudah digunakan user lain."
    ]);

    exit;
}

$stmt->close();

// ===============================
// FOTO
// ===============================

$newFoto = $oldFoto;
$newUploadedPath = null;

if (
    $method === "POST" &&
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    $foto = $_FILES["foto"];

    // ===========================
    // CEK ERROR UPLOAD
    // ===========================

    if ($foto["error"] !== UPLOAD_ERR_OK) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Upload foto gagal."
        ]);

        exit;
    }

    // ===========================
    // MAX 2 MB
    // ===========================

    if ($foto["size"] > 2 * 1024 * 1024) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }

    // ===========================
    // CEK MIME
    // ===========================

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($foto["tmp_name"]);

    $allowedMime = [
        "image/jpeg" => "jpg",
        "image/png"  => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowedMime[$mime])) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format foto harus JPG, PNG, atau WEBP."
        ]);

        exit;
    }

    // ===========================
    // CEK FILE GAMBAR
    // ===========================

    if (@getimagesize($foto["tmp_name"]) === false) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "File yang diupload bukan gambar yang valid."
        ]);

        exit;
    }

    // ===========================
    // FOLDER UPLOAD
    // ===========================

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

    // ===========================
    // NAMA FILE RANDOM
    // ===========================

    $extension = $allowedMime[$mime];

    $fileName = "user_" . bin2hex(random_bytes(16)) . "." . $extension;

    $destination = $uploadDir . $fileName;

    // ===========================
    // PINDAHKAN FILE
    // ===========================

    if (!move_uploaded_file($foto["tmp_name"], $destination)) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan foto."
        ]);

        exit;
    }

    /*
     * PENTING:
     * Database hanya menyimpan nama file.
     *
     * Contoh:
     * user_a1b2c3d4.jpg
     *
     * BUKAN:
     * uploads/users/user_a1b2c3d4.jpg
     */

    $newFoto = $fileName;

    $newUploadedPath = $destination;
}

// ===============================
// UPDATE DATABASE
// ===============================

$stmt = $conn->prepare("
    UPDATE users
    SET
        nama = ?,
        alamat = ?,
        no_telp = ?,
        email = ?,
        foto = ?,
        updated_at = NOW()
    WHERE id_user = ?
");

if (!$stmt) {

    if ($newUploadedPath !== null && file_exists($newUploadedPath)) {
        unlink($newUploadedPath);
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "sssssi",
    $nama,
    $alamat,
    $no_telp,
    $email,
    $newFoto,
    $id_user
);

if (!$stmt->execute()) {

    $stmt->close();

    if ($newUploadedPath !== null && file_exists($newUploadedPath)) {
        unlink($newUploadedPath);
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memperbarui profile user."
    ]);

    exit;
}

$stmt->close();

// ===============================
// HAPUS FOTO LAMA
// ===============================

if (
    $newUploadedPath !== null &&
    !empty($oldFoto)
) {

    /*
     * Karena database sekarang hanya menyimpan
     * nama file, langsung arahkan ke folder users.
     */

    $oldFotoPath = __DIR__ . "/../../uploads/users/" . basename($oldFoto);

    if (
        file_exists($oldFotoPath) &&
        is_file($oldFotoPath)
    ) {
        unlink($oldFotoPath);
    }
}

// ===============================
// RESPONSE
// ===============================

echo json_encode([
    "status" => true,
    "message" => "Profile user berhasil diperbarui.",
    "data" => [
        "id_user" => $id_user,
        "nama" => $nama,
        "alamat" => $alamat,
        "no_telp" => $no_telp,
        "email" => $email,
        "foto" => $newFoto
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>