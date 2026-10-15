<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// ===============================
// HANYA POST
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method harus POST."
    ]);

    exit;
}


// ===============================
// AMBIL INPUT JSON
// ===============================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}


// ===============================
// AMBIL DATA
// ===============================
$token = trim($input["token"] ?? "");
$password_baru = $input["password_baru"] ?? "";
$konfirmasi_password = $input["konfirmasi_password"] ?? "";


// ===============================
// VALIDASI TOKEN
// ===============================
if ($token === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Token wajib diisi."
    ]);

    exit;
}


// Token harus 64 karakter hexadecimal
if (!preg_match('/^[a-fA-F0-9]{64}$/', $token)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format token tidak valid."
    ]);

    exit;
}


// ===============================
// VALIDASI PASSWORD
// ===============================
if (!is_string($password_baru)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru tidak valid."
    ]);

    exit;
}

if (strlen($password_baru) < 8 || strlen($password_baru) > 72) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password baru harus 8-72 karakter."
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


// ===============================
// HASH TOKEN
// ===============================
$token_hash = hash("sha256", $token);


// ===============================
// CARI TOKEN
// ===============================
$stmt = $conn->prepare("
    SELECT
        id_reset,
        id_user,
        expires_at,
        used_at
    FROM user_password_resets
    WHERE token_hash = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan server."
    ]);

    exit;
}

$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$reset = $result->fetch_assoc();

$stmt->close();


// ===============================
// TOKEN TIDAK DITEMUKAN
// ===============================
if (!$reset) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Token reset password tidak valid."
    ]);

    exit;
}


// ===============================
// TOKEN SUDAH DIGUNAKAN
// ===============================
if ($reset["used_at"] !== null) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Token reset password sudah digunakan."
    ]);

    exit;
}


// ===============================
// CEK EXPIRED
// ===============================
if (strtotime($reset["expires_at"]) < time()) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Token reset password sudah expired."
    ]);

    exit;
}


// ===============================
// HASH PASSWORD BARU
// ===============================
$password_hash = password_hash(
    $password_baru,
    PASSWORD_DEFAULT
);

if ($password_hash === false) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengenkripsi password."
    ]);

    exit;
}


// ===============================
// TRANSACTION
// ===============================
$conn->begin_transaction();

try {

    // Update password user
    $stmtUser = $conn->prepare("
        UPDATE users
        SET
            password = ?,
            updated_at = NOW()
        WHERE id_user = ?
    ");

    if (!$stmtUser) {
        throw new Exception("Gagal menyiapkan update user.");
    }

    $id_user = (int) $reset["id_user"];

    $stmtUser->bind_param(
        "si",
        $password_hash,
        $id_user
    );

    if (!$stmtUser->execute()) {
        throw new Exception("Gagal memperbarui password.");
    }

    if ($stmtUser->affected_rows === 0) {
        $stmtUser->close();
        throw new Exception("User tidak ditemukan.");
    }

    $stmtUser->close();


    // Tandai token sudah digunakan
    $stmtToken = $conn->prepare("
        UPDATE user_password_resets
        SET used_at = NOW()
        WHERE id_reset = ?
          AND used_at IS NULL
    ");

    if (!$stmtToken) {
        throw new Exception("Gagal menyiapkan update token.");
    }

    $id_reset = (int) $reset["id_reset"];

    $stmtToken->bind_param(
        "i",
        $id_reset
    );

    if (!$stmtToken->execute()) {
        throw new Exception("Gagal menandai token.");
    }

    $stmtToken->close();


    // Commit
    $conn->commit();

    http_response_code(200);

    echo json_encode([
        "status" => true,
        "message" => "Password berhasil direset."
    ]);

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan server."
    ]);
}

?>