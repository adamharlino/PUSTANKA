<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// ========================================
// CEK METHOD
// ========================================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method harus GET."
    ]);

    exit;
}


// ========================================
// CEK LOGIN ADMIN
// ========================================
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


// ========================================
// AMBIL FILTER
// ========================================
$tanggal_mulai = trim($_GET["tanggal_mulai"] ?? "");
$tanggal_selesai = trim($_GET["tanggal_selesai"] ?? "");
$status = trim($_GET["status"] ?? "");
$metode = trim($_GET["metode"] ?? "");


// ========================================
// VALIDASI TANGGAL MULAI
// ========================================
if ($tanggal_mulai !== "") {

    $date = DateTime::createFromFormat(
        "Y-m-d",
        $tanggal_mulai
    );

    if (
        !$date ||
        $date->format("Y-m-d") !== $tanggal_mulai
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format tanggal_mulai harus YYYY-MM-DD."
        ]);

        exit;
    }
}


// ========================================
// VALIDASI TANGGAL SELESAI
// ========================================
if ($tanggal_selesai !== "") {

    $date = DateTime::createFromFormat(
        "Y-m-d",
        $tanggal_selesai
    );

    if (
        !$date ||
        $date->format("Y-m-d") !== $tanggal_selesai
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format tanggal_selesai harus YYYY-MM-DD."
        ]);

        exit;
    }
}


// ========================================
// VALIDASI RANGE
// ========================================
if (
    $tanggal_mulai !== "" &&
    $tanggal_selesai !== "" &&
    $tanggal_mulai > $tanggal_selesai
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "tanggal_mulai tidak boleh lebih besar dari tanggal_selesai."
    ]);

    exit;
}


// ========================================
// QUERY
// ========================================
$sql = "
    SELECT
        p.id_payment,
        p.id_fine,

        f.nominal_denda,
        f.status_pembayaran AS status_denda,

        l.id_loan,

        u.id_user,
        u.member_code,
        u.nama AS nama_user,
        u.email AS email_user,

        b.id_book,
        b.judul AS judul_buku,

        p.tanggal_bayar,
        p.nominal,
        p.metode,
        p.status,
        p.bukti_pembayaran

    FROM payments p

    INNER JOIN fines f
        ON p.id_fine = f.id_fine

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE 1=1
";

$types = "";
$params = [];


// ========================================
// FILTER TANGGAL MULAI
// Berdasarkan tanggal_bayar
// ========================================
if ($tanggal_mulai !== "") {

    $sql .= "
        AND DATE(p.tanggal_bayar) >= ?
    ";

    $types .= "s";
    $params[] = $tanggal_mulai;
}


// ========================================
// FILTER TANGGAL SELESAI
// ========================================
if ($tanggal_selesai !== "") {

    $sql .= "
        AND DATE(p.tanggal_bayar) <= ?
    ";

    $types .= "s";
    $params[] = $tanggal_selesai;
}


// ========================================
// FILTER STATUS PAYMENT
// ========================================
if ($status !== "") {

    $sql .= "
        AND p.status = ?
    ";

    $types .= "s";
    $params[] = $status;
}


// ========================================
// FILTER METODE
// ========================================
if ($metode !== "") {

    $sql .= "
        AND p.metode = ?
    ";

    $types .= "s";
    $params[] = $metode;
}


// ========================================
// URUTKAN
// ========================================
$sql .= "
    ORDER BY p.tanggal_bayar DESC,
             p.id_payment DESC
";


// ========================================
// PREPARE
// ========================================
$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan server."
    ]);

    exit;
}


// ========================================
// BIND PARAMETER
// ========================================
if (!empty($params)) {

    $stmt->bind_param(
        $types,
        ...$params
    );
}


// ========================================
// EXECUTE
// ========================================
if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil laporan pembayaran."
    ]);

    exit;
}


$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "id_payment" => (int) $row["id_payment"],
        "id_fine" => (int) $row["id_fine"],
        "id_loan" => (int) $row["id_loan"],

        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama_user" => $row["nama_user"],
        "email_user" => $row["email_user"],

        "id_book" => (int) $row["id_book"],
        "judul_buku" => $row["judul_buku"],

        "nominal_denda" => (float) $row["nominal_denda"],

        "tanggal_bayar" => $row["tanggal_bayar"],
        "nominal" => (float) $row["nominal"],
        "metode" => $row["metode"],
        "status" => $row["status"],

        "status_denda" => $row["status_denda"],
        "bukti_pembayaran" => $row["bukti_pembayaran"]
    ];
}


$stmt->close();


// ========================================
// RESPONSE
// ========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Laporan pembayaran berhasil diambil.",
    "total_data" => count($data),
    "data" => $data
]);

?>