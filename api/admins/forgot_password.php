<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);
    exit;
}

// =========================
// AMBIL INPUT
// =========================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$email = strtolower(trim($input["email"] ?? ""));

// =========================
// VALIDASI EMAIL
// =========================
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

// =========================
// CARI ADMIN
// =========================
$stmt = $conn->prepare("
    SELECT id_admin, email
    FROM admins
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
$admin = $result->fetch_assoc();

$stmt->close();

// =========================
// EMAIL TIDAK DITEMUKAN
// =========================
if (!$admin) {
    http_response_code(404);
    echo json_encode([
        "status" => false,
        "message" => "Email admin tidak ditemukan."
    ]);
    exit;
}

$idAdmin = (int) $admin["id_admin"];

// =========================
// TOKEN RANDOM
// =========================
$token = bin2hex(random_bytes(32));

// Simpan hash token ke database
$tokenHash = hash("sha256", $token);

// Token berlaku 30 menit
$expiresAt = date("Y-m-d H:i:s", time() + (30 * 60));

// =========================
// HAPUS TOKEN LAMA YANG
// BELUM TERPAKAI
// =========================
$deleteOld = $conn->prepare("
    DELETE FROM admin_password_resets
    WHERE id_admin = ?
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

$deleteOld->bind_param("i", $idAdmin);
$deleteOld->execute();
$deleteOld->close();

// =========================
// SIMPAN TOKEN BARU
// =========================
$insert = $conn->prepare("
    INSERT INTO admin_password_resets
    (
        id_admin,
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
    $idAdmin,
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

// =========================
// RESPONSE DEVELOPMENT
// =========================
//
// Token dikembalikan sementara untuk
// testing Postman.
//
// Nanti production:
// token TIDAK dikembalikan,
// tetapi dikirim melalui email.
// =========================

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Token reset password berhasil dibuat.",
    "data" => [
        "expires_at" => $expiresAt,
        "reset_token" => $token
    ]
]);