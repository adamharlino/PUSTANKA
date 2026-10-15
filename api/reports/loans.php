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


// ========================================
// VALIDASI TANGGAL
// ========================================
if ($tanggal_mulai !== "") {

    $date = DateTime::createFromFormat("Y-m-d", $tanggal_mulai);

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


if ($tanggal_selesai !== "") {

    $date = DateTime::createFromFormat("Y-m-d", $tanggal_selesai);

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
// VALIDASI RANGE TANGGAL
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
// QUERY DASAR
// ========================================
$sql = "
    SELECT
        l.id_loan,
        l.id_user,
        u.member_code,
        u.nama AS nama_user,
        u.email AS email_user,

        l.id_book,
        b.judul AS judul_buku,

        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status,
        l.created_at

    FROM loans l

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
// ========================================
if ($tanggal_mulai !== "") {

    $sql .= " AND DATE(l.tanggal_pinjam) >= ? ";

    $types .= "s";
    $params[] = $tanggal_mulai;
}


// ========================================
// FILTER TANGGAL SELESAI
// ========================================
if ($tanggal_selesai !== "") {

    $sql .= " AND DATE(l.tanggal_pinjam) <= ? ";

    $types .= "s";
    $params[] = $tanggal_selesai;
}


// ========================================
// FILTER STATUS
// ========================================
if ($status !== "") {

    $sql .= " AND l.status = ? ";

    $types .= "s";
    $params[] = $status;
}


// ========================================
// URUTKAN DATA
// ========================================
$sql .= "
    ORDER BY l.tanggal_pinjam DESC, l.id_loan DESC
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
        "message" => "Gagal mengambil laporan peminjaman."
    ]);

    exit;
}


$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "id_loan" => (int) $row["id_loan"],
        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama_user" => $row["nama_user"],
        "email_user" => $row["email_user"],

        "id_book" => (int) $row["id_book"],
        "judul_buku" => $row["judul_buku"],

        "tanggal_pinjam" => $row["tanggal_pinjam"],
        "batas_kembali" => $row["batas_kembali"],
        "tanggal_kembali" => $row["tanggal_kembali"],

        "status" => $row["status"],
        "created_at" => $row["created_at"]
    ];
}


$stmt->close();


// ========================================
// RESPONSE
// ========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Laporan peminjaman berhasil diambil.",
    "total_data" => count($data),
    "data" => $data
]);

?>