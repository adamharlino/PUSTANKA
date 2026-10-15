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

if ($_SERVER["REQUEST_METHOD"] !== "DELETE") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode DELETE."
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
// VALIDASI ID
// =====================================

$id_book = (int) ($input["id_book"] ?? 0);

if ($id_book <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID buku wajib diisi."
    ]);

    exit;
}


// =====================================
// CEK BUKU
// =====================================

$stmt = $conn->prepare("
    SELECT
        id_book,
        judul,
        status
    FROM books
    WHERE id_book = ?
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

$stmt->bind_param("i", $id_book);

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
        "message" => "Buku tidak ditemukan."
    ]);

    exit;
}

$book = $result->fetch_assoc();

$stmt->close();


// =====================================
// CEK STATUS BUKU
// =====================================

if ($book["status"] === "dipinjam") {

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Buku tidak dapat dihapus karena sedang dipinjam."
    ]);

    exit;
}


// =====================================
// CEK PEMINJAMAN AKTIF
// =====================================

$stmt = $conn->prepare("
    SELECT id_loan
    FROM loans
    WHERE id_book = ?
    AND status IN ('dipinjam', 'terlambat')
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

$stmt->bind_param("i", $id_book);

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
        "message" => "Buku tidak dapat dihapus karena masih memiliki peminjaman aktif."
    ]);

    exit;
}

$stmt->close();


// =====================================
// CEK RIWAYAT PEMINJAMAN
// =====================================

$stmt = $conn->prepare("
    SELECT id_loan
    FROM loans
    WHERE id_book = ?
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

$stmt->bind_param("i", $id_book);

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
        "message" => "Buku tidak dapat dihapus karena memiliki riwayat peminjaman."
    ]);

    exit;
}

$stmt->close();


// =====================================
// HAPUS BUKU
// =====================================

$stmt = $conn->prepare("
    DELETE FROM books
    WHERE id_book = ?
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param("i", $id_book);

if ($stmt->execute()) {

    echo json_encode([
        "status" => true,
        "message" => "Buku berhasil dihapus.",
        "data" => [
            "id_book" => $id_book,
            "judul" => $book["judul"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} else {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menghapus buku."
    ]);
}


$stmt->close();
$conn->close();

?>