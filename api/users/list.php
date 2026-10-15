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
// CEK ADMIN
// =========================

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

// =========================
// METHOD HARUS GET
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);

    exit;
}

// =========================
// VALIDASI SEARCH
// =========================

$search = trim($_GET["search"] ?? "");

if (strlen($search) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter search maksimal 255 karakter."
    ]);

    exit;
}

// =========================
// VALIDASI PAGE
// =========================

$pageRaw = $_GET["page"] ?? "1";

if (
    !ctype_digit((string) $pageRaw) ||
    (int) $pageRaw < 1
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter page harus berupa angka minimal 1."
    ]);

    exit;
}

$page = (int) $pageRaw;

// =========================
// VALIDASI LIMIT
// =========================

$limitRaw = $_GET["limit"] ?? "10";

if (
    !ctype_digit((string) $limitRaw) ||
    (int) $limitRaw < 1 ||
    (int) $limitRaw > 100
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter limit harus berupa angka 1 sampai 100."
    ]);

    exit;
}

$limit = (int) $limitRaw;

$offset = ($page - 1) * $limit;

// =========================
// FILTER SEARCH
// =========================

$where = "";
$params = [];
$types = "";

if ($search !== "") {

    $where = "
        WHERE nama LIKE ?
        OR nik LIKE ?
        OR email LIKE ?
        OR no_telp LIKE ?
    ";

    $keyword = "%" . $search . "%";

    $params = [
        $keyword,
        $keyword,
        $keyword,
        $keyword
    ];

    $types = "ssss";
}

// =========================
// TOTAL DATA
// =========================

$countSql = "
    SELECT COUNT(*) AS total
    FROM users
    $where
";

$stmt = $conn->prepare($countSql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query."
    ]);

    exit;
}

if ($search !== "") {
    $stmt->bind_param($types, ...$params);
}

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil jumlah data."
    ]);

    exit;
}

$result = $stmt->get_result();

if (!$result) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal membaca hasil query."
    ]);

    exit;
}

$total = (int) $result->fetch_assoc()["total"];

$stmt->close();

// =========================
// AMBIL DATA USER
// =========================

$sql = "
    SELECT
        id_user,
        member_code,
        nama,
        nik,
        alamat,
        email,
        no_telp,
        status,
        created_at
    FROM users
    $where
    ORDER BY id_user DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query data user."
    ]);

    exit;
}

if ($search !== "") {

    $typesWithPagination = $types . "ii";

    $paramsWithPagination = [
        ...$params,
        $limit,
        $offset
    ];

    $stmt->bind_param(
        $typesWithPagination,
        ...$paramsWithPagination
    );

} else {

    $stmt->bind_param(
        "ii",
        $limit,
        $offset
    );
}

// =========================
// EXECUTE QUERY
// =========================

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data user."
    ]);

    exit;
}

$result = $stmt->get_result();

if (!$result) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal membaca data user."
    ]);

    exit;
}

// =========================
// FORMAT DATA
// =========================

$users = [];

while ($row = $result->fetch_assoc()) {

    $users[] = [
        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama" => $row["nama"],
        "nik" => $row["nik"],
        "alamat" => $row["alamat"],
        "email" => $row["email"],
        "no_telp" => $row["no_telp"],
        "status" => $row["status"],
        "created_at" => $row["created_at"]
    ];
}

$stmt->close();

// =========================
// PAGINATION
// =========================

$totalPages = $total > 0
    ? (int) ceil($total / $limit)
    : 0;

// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Data anggota berhasil diambil.",
    "data" => $users,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total_data" => $total,
        "total_pages" => $totalPages
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>