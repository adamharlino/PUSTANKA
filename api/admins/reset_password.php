<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// =========================
// METHOD
// =========================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);
    exit;
}

// =========================
// INPUT
// =========================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$token = trim($input["token"] ?? "");
$passwordBaru = $input["password_baru"] ?? "";
$konfirmasi = $input["konfirmasi_password"] ?? "";

// =========================
// VALIDASI TOKEN
// =========================
if ($token === "") {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Token reset password wajib diisi."
    ]);
    exit;
}

if (!preg_match('/^[a-f0-9]{64}$/i', $token)) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Token reset password tidak valid."
    ]);
    exit;
}

// =========================
// VALIDASI PASSWORD
// =========================
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

// =========================
// HASH TOKEN
// =========================
$tokenHash = hash("sha256", $token);

// =========================
// CARI TOKEN
// =========================
$stmt = $conn->prepare("
    SELECT
        id_reset,
        id_admin,
        expires_at,
        used_at
    FROM admin_password_resets
    WHERE token_hash = ?
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

$stmt->bind_param("s", $tokenHash);

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
$reset = $result->fetch_assoc();

$stmt->close();

// =========================
// TOKEN TIDAK DITEMUKAN
// =========================
if (!$reset) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Token reset password tidak valid."
    ]);
    exit;
}

// =========================
// TOKEN SUDAH DIGUNAKAN
// =========================
if ($reset["used_at"] !== null) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Token reset password sudah digunakan."
    ]);
    exit;
}

// =========================
// CEK EXPIRED
// =========================
if (strtotime($reset["expires_at"]) < time()) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Token reset password sudah expired."
    ]);
    exit;
}

$idReset = (int) $reset["id_reset"];
$idAdmin = (int) $reset["id_admin"];

// =========================
// HASH PASSWORD BARU
// =========================
$passwordHash = password_hash(
    $passwordBaru,
    PASSWORD_DEFAULT
);

if ($passwordHash === false) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Gagal membuat password baru."
    ]);
    exit;
}

// =========================
// TRANSACTION
// =========================
$conn->begin_transaction();

try {

    // =========================
    // UPDATE PASSWORD ADMIN
    // =========================
    $updatePassword = $conn->prepare("
        UPDATE admins
        SET password = ?
        WHERE id_admin = ?
    ");

    if (!$updatePassword) {
        throw new Exception("Gagal prepare update password.");
    }

    $updatePassword->bind_param(
        "si",
        $passwordHash,
        $idAdmin
    );

    if (!$updatePassword->execute()) {
        $updatePassword->close();
        throw new Exception("Gagal update password.");
    }

    if ($updatePassword->affected_rows === 0) {
        $updatePassword->close();
        throw new Exception("Admin tidak ditemukan.");
    }

    $updatePassword->close();

    // =========================
    // TANDAI TOKEN SUDAH DIPAKAI
    // =========================
    $updateToken = $conn->prepare("
        UPDATE admin_password_resets
        SET used_at = NOW()
        WHERE id_reset = ?
          AND used_at IS NULL
    ");

    if (!$updateToken) {
        throw new Exception("Gagal prepare token.");
    }

    $updateToken->bind_param(
        "i",
        $idReset
    );

    if (!$updateToken->execute()) {
        $updateToken->close();
        throw new Exception("Gagal update token.");
    }

    if ($updateToken->affected_rows !== 1) {
        $updateToken->close();
        throw new Exception("Token sudah digunakan.");
    }

    $updateToken->close();

    // =========================
    // COMMIT
    // =========================
    $conn->commit();

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Gagal mereset password."
    ]);
    exit;
}

// =========================
// SUCCESS
// =========================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Password admin berhasil direset. Silakan login dengan password baru."
]);