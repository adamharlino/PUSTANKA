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
// CEK METHOD
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan PUT."
    ]);

    exit;
}

$id_user = (int) $_SESSION["id_user"];

// ===============================
// AMBIL INPUT JSON
// ===============================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format JSON tidak valid."
    ]);

    exit;
}

$passwordLama = $input["password_lama"] ?? "";
$passwordBaru = $input["password_baru"] ?? "";
$konfirmasi = $input["konfirmasi_password"] ?? "";

// ===============================
// VALIDASI PASSWORD LAMA
// ===============================
if ($passwordLama === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password lama wajib diisi."
    ]);

    exit;
}

// ===============================
// VALIDASI PASSWORD BARU
// ===============================
if ($passwordBaru === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru wajib diisi."
    ]);

    exit;
}

if (strlen($passwordBaru) < 8 || strlen($passwordBaru) > 72) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru harus 8-72 karakter."
    ]);

    exit;
}

// ===============================
// VALIDASI KONFIRMASI
// ===============================
if ($konfirmasi === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Konfirmasi password wajib diisi."
    ]);

    exit;
}

if ($passwordBaru !== $konfirmasi) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Konfirmasi password tidak cocok."
    ]);

    exit;
}

// ===============================
// PASSWORD BARU TIDAK BOLEH SAMA
// ===============================
if ($passwordLama === $passwordBaru) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru harus berbeda dari password lama."
    ]);

    exit;
}

// ===============================
// AMBIL PASSWORD DARI DATABASE
// ===============================
$stmt = $conn->prepare("
    SELECT password
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
$user = $result->fetch_assoc();

$stmt->close();

// ===============================
// USER TIDAK DITEMUKAN
// ===============================
if (!$user) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "User tidak ditemukan."
    ]);

    exit;
}

$passwordHashLama = $user["password"];

// ===============================
// VERIFIKASI PASSWORD LAMA
// ===============================
if (!password_verify($passwordLama, $passwordHashLama)) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Password lama salah."
    ]);

    exit;
}

// ===============================
// HASH PASSWORD BARU
// ===============================
$passwordHashBaru = password_hash(
    $passwordBaru,
    PASSWORD_DEFAULT
);

if ($passwordHashBaru === false) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal membuat password baru."
    ]);

    exit;
}

// ===============================
// UPDATE PASSWORD
// ===============================
$stmt = $conn->prepare("
    UPDATE users
    SET password = ?,
        updated_at = NOW()
    WHERE id_user = ?
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
    $passwordHashBaru,
    $id_user
);

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

// ===============================
// RESPONSE
// ===============================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Password user berhasil diubah."
], JSON_UNESCAPED_UNICODE);

$conn->close();
?>