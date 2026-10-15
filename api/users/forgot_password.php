<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK METHOD
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}

// ===============================
// AMBIL INPUT
// ===============================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$email = strtolower(trim($input["email"] ?? ""));

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
// CARI USER
// ===============================
$stmt = $conn->prepare("
    SELECT
        id_user,
        email,
        status
    FROM users
    WHERE email = ?
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

$stmt->bind_param("s", $email);

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
// EMAIL TIDAK DITEMUKAN
// ===============================
if (!$user) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Email user tidak ditemukan."
    ]);

    exit;
}

// ===============================
// CEK STATUS USER
// ===============================
if ($user["status"] !== "aktif") {
    http_response_code(403);

    echo json_encode([
        "status" => false,
        "message" => "Akun user tidak aktif."
    ]);

    exit;
}

$idUser = (int) $user["id_user"];

// ===============================
// GENERATE TOKEN
// ===============================
$token = bin2hex(random_bytes(32));

$tokenHash = hash("sha256", $token);

// Token berlaku 30 menit
$expiresAt = date(
    "Y-m-d H:i:s",
    time() + (30 * 60)
);

// ===============================
// HAPUS TOKEN LAMA
// ===============================
$deleteOld = $conn->prepare("
    DELETE FROM user_password_resets
    WHERE id_user = ?
      AND used_at IS NULL
");

if (!$deleteOld) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$deleteOld->bind_param("i", $idUser);

if (!$deleteOld->execute()) {
    $deleteOld->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$deleteOld->close();

// ===============================
// SIMPAN TOKEN
// ===============================
$insert = $conn->prepare("
    INSERT INTO user_password_resets
    (
        id_user,
        token_hash,
        expires_at
    )
    VALUES (?, ?, ?)
");

if (!$insert) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$insert->bind_param(
    "iss",
    $idUser,
    $tokenHash,
    $expiresAt
);

if (!$insert->execute()) {
    $insert->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$insert->close();

// ===============================
// RESPONSE DEVELOPMENT
// ===============================
//
// Sementara token dikembalikan untuk
// testing Postman.
//
// Production:
// token dikirim melalui email.
// ===============================

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Token reset password berhasil dibuat.",
    "data" => [
        "expires_at" => $expiresAt,
        "reset_token" => $token
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>