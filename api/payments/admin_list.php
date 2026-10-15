<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// =====================================================
// VALIDASI ADMIN
// =====================================================

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


// =====================================================
// METHOD
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diperbolehkan."
    ]);

    exit;
}


// =====================================================
// PAGINATION
// =====================================================

$page = isset($_GET["page"])
    ? (int) $_GET["page"]
    : 1;

$limit = isset($_GET["limit"])
    ? (int) $_GET["limit"]
    : 10;

if ($page < 1) {
    $page = 1;
}

if ($limit < 1) {
    $limit = 10;
}

if ($limit > 100) {
    $limit = 100;
}

$offset = ($page - 1) * $limit;


// =====================================================
// FILTER
// =====================================================

$status = strtolower(
    trim($_GET["status"] ?? "")
);

$metode = strtolower(
    trim($_GET["metode"] ?? "")
);

$search = trim(
    $_GET["search"] ?? ""
);


// =====================================================
// VALIDASI STATUS
// =====================================================

$allowedStatus = [
    "pending",
    "disetujui",
    "ditolak"
];

if (
    $status !== "" &&
    !in_array($status, $allowedStatus, true)
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Status payment tidak valid."
    ]);

    exit;
}


// =====================================================
// VALIDASI METODE
// =====================================================

$allowedMetode = [
    "qris",
    "transfer_bank"
];

if (
    $metode !== "" &&
    !in_array($metode, $allowedMetode, true)
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Metode pembayaran tidak valid."
    ]);

    exit;
}


// =====================================================
// WHERE
// =====================================================

$where = [];

$params = [];

$types = "";


// =====================================================
// FILTER STATUS
// =====================================================

if ($status !== "") {

    $where[] = "p.status = ?";

    $params[] = $status;

    $types .= "s";
}


// =====================================================
// FILTER METODE
// =====================================================

if ($metode !== "") {

    $where[] = "p.metode = ?";

    $params[] = $metode;

    $types .= "s";
}


// =====================================================
// SEARCH
// =====================================================

if ($search !== "") {

    $where[] = "(
        u.nama LIKE ?
        OR u.nik LIKE ?
        OR u.member_code LIKE ?
        OR b.judul LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ssss";
}


$whereSql = "";

if (!empty($where)) {

    $whereSql = "WHERE " . implode(
        " AND ",
        $where
    );
}


// =====================================================
// HITUNG TOTAL DATA
// =====================================================

$countSql = "
    SELECT COUNT(*) AS total

    FROM payments p

    INNER JOIN fines f
        ON p.id_fine = f.id_fine

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    $whereSql
";


$countStmt = $conn->prepare($countSql);

if (!$countStmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total payment.",
        "error" => $conn->error
    ]);

    exit;
}


if (!empty($params)) {

    $countStmt->bind_param(
        $types,
        ...$params
    );
}


$countStmt->execute();

$countResult = $countStmt->get_result();

$totalData = (int) $countResult
    ->fetch_assoc()["total"];

$countStmt->close();


// =====================================================
// QUERY PAYMENT
// =====================================================

$sql = "
    SELECT

        p.id_payment,
        p.id_fine,
        p.tanggal_bayar,
        p.nominal,
        p.metode,
        p.status,
        p.bukti_pembayaran,

        f.nominal_denda,
        f.status_pembayaran,

        l.id_loan,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,

        u.id_user,
        u.member_code,
        u.nama AS nama_peminjam,
        u.nik,
        u.email,
        u.no_telp,

        b.id_book,
        b.judul,
        b.pengarang

    FROM payments p

    INNER JOIN fines f
        ON p.id_fine = f.id_fine

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    $whereSql

    ORDER BY
        p.tanggal_bayar DESC,
        p.id_payment DESC

    LIMIT ? OFFSET ?
";


// =====================================================
// PARAMETER PAGINATION
// =====================================================

$dataParams = $params;

$dataTypes = $types . "ii";

$dataParams[] = $limit;

$dataParams[] = $offset;


// =====================================================
// EXECUTE
// =====================================================

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query payment.",
        "error" => $conn->error
    ]);

    exit;
}


$stmt->bind_param(
    $dataTypes,
    ...$dataParams
);

$stmt->execute();

$result = $stmt->get_result();


// =====================================================
// FORMAT DATA
// =====================================================

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [

        "id_payment" => (int) $row["id_payment"],

        "fine" => [
            "id_fine" => (int) $row["id_fine"],
            "nominal_denda" => $row["nominal_denda"],
            "status_pembayaran" => $row["status_pembayaran"]
        ],

        "loan" => [
            "id_loan" => (int) $row["id_loan"],
            "tanggal_pinjam" => $row["tanggal_pinjam"],
            "batas_kembali" => $row["batas_kembali"],
            "tanggal_kembali" => $row["tanggal_kembali"]
        ],

        "peminjam" => [
            "id_user" => (int) $row["id_user"],
            "member_code" => $row["member_code"],
            "nama" => $row["nama_peminjam"],
            "nik" => $row["nik"],
            "email" => $row["email"],
            "no_telp" => $row["no_telp"]
        ],

        "buku" => [
            "id_book" => (int) $row["id_book"],
            "judul" => $row["judul"],
            "pengarang" => $row["pengarang"]
        ],

        "tanggal_bayar" => $row["tanggal_bayar"],

        "nominal" => $row["nominal"],

        "metode" => $row["metode"],

        "status" => $row["status"],

        "bukti_pembayaran" => $row["bukti_pembayaran"]
    ];
}


$stmt->close();


// =====================================================
// PAGINATION INFO
// =====================================================

$totalPages = $limit > 0
    ? (int) ceil($totalData / $limit)
    : 0;


// =====================================================
// RESPONSE
// =====================================================

echo json_encode([

    "status" => true,

    "message" => "Data payment berhasil diambil.",

    "data" => $data,

    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total" => $totalData,
        "total_pages" => $totalPages
    ]

], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


$conn->close();

?>