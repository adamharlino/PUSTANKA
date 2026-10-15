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

$search = trim($_GET["search"] ?? "");
$tanggal = trim($_GET["tanggal"] ?? "");

/*
|--------------------------------------------------------------------------
| VALIDASI TANGGAL
|--------------------------------------------------------------------------
*/

if ($tanggal !== "") {

    $dateObject = DateTime::createFromFormat("Y-m-d", $tanggal);

    if (
        !$dateObject ||
        $dateObject->format("Y-m-d") !== $tanggal
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format tanggal harus YYYY-MM-DD."
        ]);

        exit;
    }
}

/*
|--------------------------------------------------------------------------
| WHERE
|--------------------------------------------------------------------------
*/

$where = [];
$params = [];
$types = "";

/*
|--------------------------------------------------------------------------
| FILTER TANGGAL
|--------------------------------------------------------------------------
*/

if ($tanggal !== "") {

    $where[] = "a.tanggal = ?";
    $params[] = $tanggal;
    $types .= "s";
}

/*
|--------------------------------------------------------------------------
| SEARCH USER
|--------------------------------------------------------------------------
*/

if ($search !== "") {

    $where[] = "(
        u.nama LIKE ?
        OR u.nik LIKE ?
    )";

    $searchValue = "%" . $search . "%";

    $params[] = $searchValue;
    $params[] = $searchValue;

    $types .= "ss";
}

$whereSql = "";

if (!empty($where)) {
    $whereSql = "WHERE " . implode(" AND ", $where);
}

/*
|--------------------------------------------------------------------------
| HITUNG TOTAL DATA
|--------------------------------------------------------------------------
*/

$countSql = "
    SELECT COUNT(*) AS total
    FROM attendance a

    INNER JOIN users u
        ON a.id_user = u.id_user

    $whereSql
";

$countStmt = $conn->prepare($countSql);

if (!$countStmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total attendance.",
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
| QUERY ATTENDANCE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        a.id_attendance,
        a.id_user,
        a.tanggal,
        a.jam_kedatangan,
        a.jam_kepulangan,
        a.created_at,

        u.nama,
        u.nik

    FROM attendance a

    INNER JOIN users u
        ON a.id_user = u.id_user

    $whereSql

    ORDER BY a.tanggal DESC, a.jam_kedatangan DESC

    LIMIT ? OFFSET ?
";

/*
|--------------------------------------------------------------------------
| PARAMETER PAGINATION
|--------------------------------------------------------------------------
*/

$dataParams = $params;
$dataTypes = $types . "ii";

$dataParams[] = $limit;
$dataParams[] = $offset;

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query attendance.",
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
        "id_attendance" => (int) $row["id_attendance"],

        "user" => [
            "id_user" => (int) $row["id_user"],
            "nama" => $row["nama"],
            "nik" => $row["nik"]
        ],

        "tanggal" => $row["tanggal"],
        "jam_kedatangan" => $row["jam_kedatangan"],
        "jam_kepulangan" => $row["jam_kepulangan"],
        "created_at" => $row["created_at"]
    ];
}

$stmt->close();

/*
|--------------------------------------------------------------------------
| PAGINATION INFO
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
    "message" => "Data attendance berhasil diambil.",
    "data" => $data,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total" => $totalData,
        "total_pages" => $totalPages
    ]
]);