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
$status = trim($_GET["status"] ?? "");
$search = trim($_GET["search"] ?? "");
$tanggal_mulai = trim($_GET["tanggal_mulai"] ?? "");
$tanggal_selesai = trim($_GET["tanggal_selesai"] ?? "");


// ========================================
// VALIDASI SEARCH
// ========================================
if (strlen($search) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter search maksimal 100 karakter."
    ]);

    exit;
}


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
        id_user,
        member_code,
        nama,
        nik,
        alamat,
        email,
        no_telp,
        status,
        created_at,
        updated_at
    FROM users
    WHERE 1=1
";

$types = "";
$params = [];


// ========================================
// FILTER STATUS
// ========================================
if ($status !== "") {

    $sql .= "
        AND status = ?
    ";

    $types .= "s";
    $params[] = $status;
}


// ========================================
// FILTER SEARCH
// ========================================
if ($search !== "") {

    $sql .= "
        AND (
            nama LIKE ?
            OR email LIKE ?
            OR nik LIKE ?
            OR no_telp LIKE ?
        )
    ";

    $searchValue = "%" . $search . "%";

    $types .= "ssss";

    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
    $params[] = $searchValue;
}


// ========================================
// FILTER TANGGAL MULAI
// Berdasarkan created_at
// ========================================
if ($tanggal_mulai !== "") {

    $sql .= "
        AND DATE(created_at) >= ?
    ";

    $types .= "s";
    $params[] = $tanggal_mulai;
}


// ========================================
// FILTER TANGGAL SELESAI
// ========================================
if ($tanggal_selesai !== "") {

    $sql .= "
        AND DATE(created_at) <= ?
    ";

    $types .= "s";
    $params[] = $tanggal_selesai;
}


// ========================================
// URUTKAN
// ========================================
$sql .= "
    ORDER BY created_at DESC,
             id_user DESC
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
        "message" => "Gagal mengambil laporan users."
    ]);

    exit;
}


$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama" => $row["nama"],
        "nik" => $row["nik"],
        "alamat" => $row["alamat"],
        "email" => $row["email"],
        "no_telp" => $row["no_telp"],
        "status" => $row["status"],
        "created_at" => $row["created_at"],
        "updated_at" => $row["updated_at"]
    ];
}


$stmt->close();


// ========================================
// RESPONSE
// ========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Laporan users berhasil diambil.",
    "total_data" => count($data),
    "data" => $data
]);

?>