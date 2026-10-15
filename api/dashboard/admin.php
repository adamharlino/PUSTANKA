<?php

// ==========================================
// SESSION
// ==========================================
session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // ubah true jika production HTTPS
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ==========================================
// VALIDASI ADMIN
// ==========================================
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

// ==========================================
// CEK METHOD
// ==========================================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode GET."
    ]);

    exit;
}

// ==========================================
// TOTAL USER
// ==========================================
$userQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM users
");

if (!$userQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil total user."
    ]);

    $conn->close();
    exit;
}

$totalUser = (int) $userQuery->fetch_assoc()["total"];

// ==========================================
// DATA BUKU
// ==========================================
$bookQuery = $conn->query("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status = 'tersedia' THEN 1 ELSE 0 END) AS tersedia,
        SUM(CASE WHEN status = 'dipinjam' THEN 1 ELSE 0 END) AS dipinjam
    FROM books
");

if (!$bookQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data buku."
    ]);

    $conn->close();
    exit;
}

$bookData = $bookQuery->fetch_assoc();

$totalBook = (int) $bookData["total"];
$bookTersedia = (int) ($bookData["tersedia"] ?? 0);
$bookDipinjam = (int) ($bookData["dipinjam"] ?? 0);

// ==========================================
// LOAN AKTIF
// ==========================================
$loanAktifQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM loans
    WHERE status = 'dipinjam'
");

if (!$loanAktifQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil loan aktif."
    ]);

    $conn->close();
    exit;
}

$loanAktif = (int) $loanAktifQuery->fetch_assoc()["total"];

// ==========================================
// LOAN TERLAMBAT
// ==========================================
// Terlambat jika:
// tanggal sekarang > batas_kembali + 7 hari
// dan status masih dipinjam
// ==========================================
$loanTerlambatQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM loans
    WHERE status = 'dipinjam'
      AND CURDATE() > DATE_ADD(batas_kembali, INTERVAL 7 DAY)
");

if (!$loanTerlambatQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil loan terlambat."
    ]);

    $conn->close();
    exit;
}

$loanTerlambat = (int) $loanTerlambatQuery->fetch_assoc()["total"];

// ==========================================
// FINE BELUM BAYAR
// ==========================================
$fineQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM fines
    WHERE status_pembayaran = 'belum_bayar'
");

if (!$fineQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil fine belum bayar."
    ]);

    $conn->close();
    exit;
}

$fineBelumBayar = (int) $fineQuery->fetch_assoc()["total"];

// ==========================================
// TOTAL PAYMENT
// ==========================================
// Hanya payment dengan status berhasil
// ==========================================
$paymentQuery = $conn->query("
    SELECT COALESCE(SUM(nominal), 0) AS total
    FROM payments
    WHERE status = 'berhasil'
");

if (!$paymentQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil total payment."
    ]);

    $conn->close();
    exit;
}

$totalPayment = $paymentQuery->fetch_assoc()["total"];

// ==========================================
// ATTENDANCE HARI INI
// ==========================================
$attendanceQuery = $conn->query("
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE tanggal = CURDATE()
");

if (!$attendanceQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil attendance hari ini."
    ]);

    $conn->close();
    exit;
}

$attendanceHariIni = (int) $attendanceQuery->fetch_assoc()["total"];

// ==========================================
// RESPONSE
// ==========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Dashboard admin berhasil diambil.",
    "data" => [
        "users" => [
            "total" => $totalUser
        ],
        "books" => [
            "total" => $totalBook,
            "tersedia" => $bookTersedia,
            "dipinjam" => $bookDipinjam
        ],
        "loans" => [
            "aktif" => $loanAktif,
            "terlambat" => $loanTerlambat
        ],
        "fines" => [
            "belum_bayar" => $fineBelumBayar
        ],
        "payments" => [
            "total" => (float) $totalPayment
        ],
        "attendance" => [
            "hari_ini" => $attendanceHariIni
        ]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>