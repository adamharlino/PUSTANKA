<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK ADMIN
// ===============================
if (
    !isset($_SESSION["id_admin"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. Admin harus login."
    ]);

    exit;
}

// ===============================
// CEK METHOD
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan DELETE."
    ]);

    exit;
}

// ===============================
// AMBIL JSON
// ===============================
$rawInput = file_get_contents("php://input");

$input = json_decode($rawInput, true);

// JSON harus valid
if (!is_array($input)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format JSON tidak valid."
    ]);

    exit;
}

// ===============================
// VALIDASI ID USER
// ===============================
$id_user = $input["id_user"] ?? null;

if (
    $id_user === null ||
    !is_int($id_user) && !ctype_digit((string) $id_user)
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID user tidak valid."
    ]);

    exit;
}

$id_user = (int) $id_user;

if ($id_user <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID user harus lebih besar dari 0."
    ]);

    exit;
}

// ===============================
// CEK USER
// ===============================
$stmt = $conn->prepare("
    SELECT
        id_user,
        nama
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

    $conn->close();
    exit;
}

$stmt->bind_param("i", $id_user);

if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memeriksa data anggota."
    ]);

    $stmt->close();
    $conn->close();
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
        "message" => "Anggota tidak ditemukan."
    ]);

    $conn->close();
    exit;
}

// ===============================
// CEK PEMINJAMAN AKTIF
// ===============================
$stmt = $conn->prepare("
    SELECT id_loan
    FROM loans
    WHERE id_user = ?
    AND status IN ('dipinjam', 'terlambat')
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param("i", $id_user);

if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memeriksa peminjaman anggota."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

$hasActiveLoan = $result->num_rows > 0;

$stmt->close();

if ($hasActiveLoan) {
    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Anggota tidak dapat dihapus karena masih memiliki buku yang sedang dipinjam."
    ]);

    $conn->close();
    exit;
}

// ===============================
// CEK RIWAYAT PEMINJAMAN
// ===============================
$stmt = $conn->prepare("
    SELECT id_loan
    FROM loans
    WHERE id_user = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param("i", $id_user);

if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memeriksa riwayat peminjaman anggota."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

$hasLoanHistory = $result->num_rows > 0;

$stmt->close();

// ===============================
// JIKA PUNYA RIWAYAT
// ===============================
if ($hasLoanHistory) {
    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Anggota tidak dapat dihapus karena memiliki riwayat peminjaman."
    ]);

    $conn->close();
    exit;
}

// ===============================
// HAPUS ANGGOTA
// ===============================
$stmt = $conn->prepare("
    DELETE FROM users
    WHERE id_user = ?
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param("i", $id_user);

if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menghapus anggota."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// ===============================
// CEK HASIL DELETE
// ===============================
if ($stmt->affected_rows === 0) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Anggota tidak ditemukan atau sudah dihapus."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// ===============================
// RESPONSE BERHASIL
// ===============================
echo json_encode([
    "status" => true,
    "message" => "Anggota berhasil dihapus.",
    "data" => [
        "id_user" => $id_user,
        "nama" => $user["nama"]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

?>