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

if ($_SERVER["REQUEST_METHOD"] !== "PUT") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode PUT."
    ]);

    exit;
}


// =====================================
// AMBIL INPUT JSON
// =====================================

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


// =====================================
// AMBIL ID ADMIN DARI SESSION
// =====================================

$id_admin = (int) $_SESSION["id_admin"];


// =====================================
// AMBIL PASSWORD
// =====================================

$password_lama = $input["password_lama"] ?? "";
$password_baru = $input["password_baru"] ?? "";
$konfirmasi_password = $input["konfirmasi_password"] ?? "";


// =====================================
// VALIDASI PASSWORD LAMA
// =====================================

if ($password_lama === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password lama wajib diisi."
    ]);

    exit;
}


// =====================================
// VALIDASI PASSWORD BARU
// =====================================

if ($password_baru === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru wajib diisi."
    ]);

    exit;
}

if (
    strlen($password_baru) < 8 ||
    strlen($password_baru) > 72
) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru harus 8-72 karakter."
    ]);

    exit;
}


// =====================================
// VALIDASI KONFIRMASI PASSWORD
// =====================================

if ($konfirmasi_password === "") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Konfirmasi password wajib diisi."
    ]);

    exit;
}

if ($password_baru !== $konfirmasi_password) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Konfirmasi password tidak cocok."
    ]);

    exit;
}


// =====================================
// PASSWORD BARU TIDAK BOLEH SAMA
// DENGAN PASSWORD LAMA
// =====================================

if ($password_lama === $password_baru) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru harus berbeda dari password lama."
    ]);

    exit;
}


// =====================================
// AMBIL PASSWORD ADMIN DARI DATABASE
// =====================================

$stmt = $conn->prepare("
    SELECT password
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

$stmt->bind_param(
    "i",
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

if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data admin tidak ditemukan."
    ]);

    exit;
}

$admin = $result->fetch_assoc();

$passwordHash = $admin["password"];

$stmt->close();


// =====================================
// VERIFIKASI PASSWORD LAMA
// =====================================

if (!password_verify($password_lama, $passwordHash)) {

    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Password lama salah."
    ]);

    exit;
}


// =====================================
// HASH PASSWORD BARU
// =====================================

$passwordBaruHash = password_hash(
    $password_baru,
    PASSWORD_DEFAULT
);

if ($passwordBaruHash === false) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memproses password baru."
    ]);

    exit;
}


// =====================================
// UPDATE PASSWORD
// =====================================

$stmt = $conn->prepare("
    UPDATE admins
    SET password = ?
    WHERE id_admin = ?
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
    $passwordBaruHash,
    $id_admin
);


// =====================================
// EXECUTE
// =====================================

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengubah password."
    ]);

    exit;
}


$stmt->close();
$conn->close();


// =====================================
// RESPONSE
// =====================================

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Password admin berhasil diubah."
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>