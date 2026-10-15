<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

/*
|--------------------------------------------------------------------------
| VALIDASI ADMIN
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diperbolehkan."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
$limit = isset($_GET["limit"]) ? (int) $_GET["limit"] : 10;

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

/*
|--------------------------------------------------------------------------
| FILTER
|--------------------------------------------------------------------------
*/

$status = $_GET["status_pembayaran"] ?? "";
$search = trim($_GET["search"] ?? "");

/*
|--------------------------------------------------------------------------
| VALIDASI STATUS
|--------------------------------------------------------------------------
*/

$allowedStatus = [
    "belum_bayar",
    "lunas"
];

if ($status !== "" && !in_array($status, $allowedStatus)) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Status pembayaran tidak valid."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| WHERE
|--------------------------------------------------------------------------
*/

$where = [];
$params = [];
$types = "";

if ($status !== "") {

    $where[] = "f.status_pembayaran = ?";
    $params[] = $status;
    $types .= "s";
}

if ($search !== "") {

    $where[] = "(
        u.nama LIKE ?
        OR u.nik LIKE ?
        OR b.judul LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "sss";
}

$whereSql = "";

if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/*
|--------------------------------------------------------------------------
| HITUNG TOTAL
|--------------------------------------------------------------------------
*/

$countSql = "
    SELECT COUNT(*) AS total
    FROM fines f

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
        "message" => "Gagal menyiapkan query total fine.",
        "error" => $conn->error
    ]);

    exit;
}

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

$countStmt->execute();

$countResult = $countStmt->get_result();
$totalData = (int) $countResult->fetch_assoc()["total"];

$countStmt->close();

/*
|--------------------------------------------------------------------------
| AMBIL DATA FINE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        f.id_fine,
        f.id_loan,
        f.nominal_denda,
        f.status_pembayaran,
        f.created_at,

        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,

        u.id_user,
        u.nama AS nama_peminjam,
        u.nik,

        b.id_book,
        b.judul,
        b.pengarang

    FROM fines f

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    $whereSql

    ORDER BY f.created_at DESC

    LIMIT ? OFFSET ?
";

$dataParams = $params;
$dataTypes = $types . "ii";

$dataParams[] = $limit;
$dataParams[] = $offset;

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query fine.",
        "error" => $conn->error
    ]);

    exit;
}

$stmt->bind_param($dataTypes, ...$dataParams);

$stmt->execute();

$result = $stmt->get_result();

/*
|--------------------------------------------------------------------------
| FORMAT DATA
|--------------------------------------------------------------------------
*/

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "id_fine" => (int) $row["id_fine"],

        "loan" => [
            "id_loan" => (int) $row["id_loan"],
            "tanggal_pinjam" => $row["tanggal_pinjam"],
            "batas_kembali" => $row["batas_kembali"],
            "tanggal_kembali" => $row["tanggal_kembali"]
        ],

        "peminjam" => [
            "id_user" => (int) $row["id_user"],
            "nama" => $row["nama_peminjam"],
            "nik" => $row["nik"]
        ],

        "buku" => [
            "id_book" => (int) $row["id_book"],
            "judul" => $row["judul"],
            "pengarang" => $row["pengarang"]
        ],

        "nominal_denda" => $row["nominal_denda"],
        "status_pembayaran" => $row["status_pembayaran"],
        "created_at" => $row["created_at"]
    ];
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| PAGINATION
|--------------------------------------------------------------------------
*/

$totalPages = $limit > 0
    ? (int) ceil($totalData / $limit)
    : 0;

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status" => true,
    "message" => "Data fine berhasil diambil.",
    "data" => $data,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total" => $totalData,
        "total_pages" => $totalPages
    ]
]);