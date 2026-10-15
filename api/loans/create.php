<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// =========================
// CEK USER LOGIN
// =========================

if (
    !isset($_SESSION["id_user"]) ||
    ($_SESSION["role"] ?? "") !== "user"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. User harus login."
    ]);

    exit;
}


// =========================
// CEK METHOD
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
// AMBIL JSON
// =========================

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$id_book = (int) ($input["id_book"] ?? 0);


// =========================
// VALIDASI ID BUKU
// =========================

if ($id_book <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID buku wajib diisi."
    ]);

    exit;
}


$id_user = (int) $_SESSION["id_user"];


// =========================
// CEK BUKU
// =========================

$stmt = $conn->prepare("
    SELECT
        id_book,
        judul,
        harga,
        status
    FROM books
    WHERE id_book = ?
    LIMIT 1
");

$stmt->bind_param("i", $id_book);
$stmt->execute();

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


// =========================
// CEK STATUS BUKU
// =========================

if ($book["status"] !== "tersedia") {

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Buku sedang dipinjam."
    ]);

    exit;
}


// =========================
// CEK USER SUDAH MEMINJAM
// BUKU YANG SAMA
// =========================

$stmt = $conn->prepare("
    SELECT id_loan
    FROM loans
    WHERE id_user = ?
    AND id_book = ?
    AND status IN ('dipinjam', 'terlambat')
    LIMIT 1
");

$stmt->bind_param("ii", $id_user, $id_book);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Anda masih memiliki pinjaman aktif untuk buku ini."
    ]);

    exit;
}

$stmt->close();


// =========================
// TANGGAL PEMINJAMAN
// =========================

$tanggal_pinjam = date("Y-m-d");


// =========================
// BATAS PENGEMBALIAN
// 1 BULAN
// =========================

$batas_kembali = date(
    "Y-m-d",
    strtotime("+1 month", strtotime($tanggal_pinjam))
);


// =========================
// SIMPAN PEMINJAMAN
// =========================

$stmt = $conn->prepare("
    INSERT INTO loans
    (
        id_user,
        id_book,
        tanggal_pinjam,
        batas_kembali,
        status
    )
    VALUES (?, ?, ?, ?, 'dipinjam')
");

$stmt->bind_param(
    "iiss",
    $id_user,
    $id_book,
    $tanggal_pinjam,
    $batas_kembali
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal membuat peminjaman."
    ]);

    exit;
}

$id_loan = $conn->insert_id;

$stmt->close();


// =========================
// UPDATE STATUS BUKU
// =========================

$stmt = $conn->prepare("
    UPDATE books
    SET status = 'dipinjam'
    WHERE id_book = ?
");

$stmt->bind_param("i", $id_book);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Peminjaman berhasil dibuat, tetapi status buku gagal diperbarui."
    ]);

    exit;
}

$stmt->close();


// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Buku berhasil dipinjam.",
    "data" => [
        "id_loan" => $id_loan,
        "id_user" => $id_user,
        "id_book" => $id_book,
        "judul_buku" => $book["judul"],
        "tanggal_pinjam" => $tanggal_pinjam,
        "batas_kembali" => $batas_kembali,
        "status" => "dipinjam"
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


$conn->close();

?>