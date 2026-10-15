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
// VALIDASI USER
// ==========================================
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

$id_user = (int) $_SESSION["id_user"];

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
// DATA USER
// ==========================================
$userQuery = $conn->prepare("
    SELECT
        id_user,
        nama,
        email,
        foto,
        status
    FROM users
    WHERE id_user = ?
    LIMIT 1
");

if (!$userQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data user."
    ]);

    $conn->close();
    exit;
}

$userQuery->bind_param("i", $id_user);

if (!$userQuery->execute()) {
    $userQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data user."
    ]);

    $conn->close();
    exit;
}

$userResult = $userQuery->get_result();

if ($userResult->num_rows === 0) {
    $userQuery->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data user tidak ditemukan."
    ]);

    $conn->close();
    exit;
}

$user = $userResult->fetch_assoc();

$userQuery->close();

// ==========================================
// PEMINJAMAN AKTIF
// ==========================================
$loanQuery = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM loans
    WHERE id_user = ?
      AND status = 'dipinjam'
");

if (!$loanQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data peminjaman."
    ]);

    $conn->close();
    exit;
}

$loanQuery->bind_param("i", $id_user);

if (!$loanQuery->execute()) {
    $loanQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data peminjaman."
    ]);

    $conn->close();
    exit;
}

$loanData = $loanQuery->get_result()->fetch_assoc();

$jumlahDipinjam = (int) ($loanData["total"] ?? 0);

$loanQuery->close();

// ==========================================
// PEMINJAMAN TERLAMBAT
// ==========================================
// Terlambat jika:
// tanggal sekarang > batas_kembali + 7 hari
// dan status masih dipinjam
// ==========================================
$lateQuery = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM loans
    WHERE id_user = ?
      AND status = 'dipinjam'
      AND CURDATE() > DATE_ADD(batas_kembali, INTERVAL 7 DAY)
");

if (!$lateQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data keterlambatan."
    ]);

    $conn->close();
    exit;
}

$lateQuery->bind_param("i", $id_user);

if (!$lateQuery->execute()) {
    $lateQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data keterlambatan."
    ]);

    $conn->close();
    exit;
}

$lateData = $lateQuery->get_result()->fetch_assoc();

$jumlahTerlambat = (int) ($lateData["total"] ?? 0);

$lateQuery->close();

// ==========================================
// DENDA BELUM BAYAR
// ==========================================
$fineQuery = $conn->prepare("
    SELECT
        COUNT(*) AS jumlah,
        COALESCE(SUM(f.nominal_denda), 0) AS total
    FROM fines f
    INNER JOIN loans l
        ON f.id_loan = l.id_loan
    WHERE l.id_user = ?
      AND f.status_pembayaran = 'belum_bayar'
");

if (!$fineQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data denda."
    ]);

    $conn->close();
    exit;
}

$fineQuery->bind_param("i", $id_user);

if (!$fineQuery->execute()) {
    $fineQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data denda."
    ]);

    $conn->close();
    exit;
}

$fineData = $fineQuery->get_result()->fetch_assoc();

$jumlahDendaBelumBayar = (int) ($fineData["jumlah"] ?? 0);
$totalDendaBelumBayar = (float) ($fineData["total"] ?? 0);

$fineQuery->close();

// ==========================================
// TOTAL PEMBAYARAN BERHASIL USER
// ==========================================
$paymentQuery = $conn->prepare("
    SELECT
        COALESCE(SUM(p.nominal), 0) AS total
    FROM payments p
    INNER JOIN fines f
        ON p.id_fine = f.id_fine
    INNER JOIN loans l
        ON f.id_loan = l.id_loan
    WHERE l.id_user = ?
      AND p.status = 'berhasil'
");

if (!$paymentQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data pembayaran."
    ]);

    $conn->close();
    exit;
}

$paymentQuery->bind_param("i", $id_user);

if (!$paymentQuery->execute()) {
    $paymentQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data pembayaran."
    ]);

    $conn->close();
    exit;
}

$paymentData = $paymentQuery->get_result()->fetch_assoc();

$totalPayment = (float) ($paymentData["total"] ?? 0);

$paymentQuery->close();

// ==========================================
// ATTENDANCE HARI INI
// ==========================================
$attendanceQuery = $conn->prepare("
    SELECT
        id_attendance,
        tanggal,
        jam_kedatangan,
        jam_kepulangan
    FROM attendance
    WHERE id_user = ?
      AND tanggal = CURDATE()
    ORDER BY id_attendance DESC
    LIMIT 1
");

if (!$attendanceQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan data attendance."
    ]);

    $conn->close();
    exit;
}

$attendanceQuery->bind_param("i", $id_user);

if (!$attendanceQuery->execute()) {
    $attendanceQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data attendance."
    ]);

    $conn->close();
    exit;
}

$attendanceResult = $attendanceQuery->get_result();

$attendanceHariIni = null;

if ($attendanceResult->num_rows > 0) {
    $attendance = $attendanceResult->fetch_assoc();

    $attendanceHariIni = [
        "id_attendance" => (int) $attendance["id_attendance"],
        "tanggal" => $attendance["tanggal"],
        "jam_kedatangan" => $attendance["jam_kedatangan"],
        "jam_kepulangan" => $attendance["jam_kepulangan"]
    ];
}

$attendanceQuery->close();

// ==========================================
// DAFTAR PEMINJAMAN AKTIF
// ==========================================
$loansDetailQuery = $conn->prepare("
    SELECT
        l.id_loan,
        l.id_book,
        b.judul,
        b.pengarang,
        b.foto,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.status
    FROM loans l
    INNER JOIN books b
        ON l.id_book = b.id_book
    WHERE l.id_user = ?
      AND l.status = 'dipinjam'
    ORDER BY l.batas_kembali ASC
");

if (!$loansDetailQuery) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan daftar peminjaman."
    ]);

    $conn->close();
    exit;
}

$loansDetailQuery->bind_param("i", $id_user);

if (!$loansDetailQuery->execute()) {
    $loansDetailQuery->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil daftar peminjaman."
    ]);

    $conn->close();
    exit;
}

$loansResult = $loansDetailQuery->get_result();

$loans = [];

while ($row = $loansResult->fetch_assoc()) {
    $loans[] = [
        "id_loan" => (int) $row["id_loan"],
        "id_book" => (int) $row["id_book"],
        "judul" => $row["judul"],
        "pengarang" => $row["pengarang"],
        "foto" => $row["foto"],
        "tanggal_pinjam" => $row["tanggal_pinjam"],
        "batas_kembali" => $row["batas_kembali"],
        "status" => $row["status"]
    ];
}

$loansDetailQuery->close();

// ==========================================
// RESPONSE
// ==========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Dashboard user berhasil diambil.",
    "data" => [
        "user" => [
            "id_user" => (int) $user["id_user"],
            "nama" => $user["nama"],
            "email" => $user["email"],
            "foto" => $user["foto"],
            "status" => $user["status"]
        ],

        "loans" => [
            "jumlah_dipinjam" => $jumlahDipinjam,
            "jumlah_terlambat" => $jumlahTerlambat,
            "data" => $loans
        ],

        "fines" => [
            "jumlah_belum_bayar" => $jumlahDendaBelumBayar,
            "total_belum_bayar" => $totalDendaBelumBayar
        ],

        "payments" => [
            "total_berhasil" => $totalPayment
        ],

        "attendance" => [
            "hari_ini" => $attendanceHariIni
        ]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>