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
$id_user = trim($_GET["id_user"] ?? "");


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
// VALIDASI ID USER
// ========================================
if ($id_user !== "") {

    if (
        !ctype_digit($id_user) ||
        (int) $id_user <= 0
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "id_user harus berupa angka positif."
        ]);

        exit;
    }
}


// ========================================
// QUERY DASAR
// ========================================
$sql = "
    SELECT
        a.id_attendance,
        a.id_user,

        u.member_code,
        u.nama AS nama_user,
        u.email AS email_user,

        a.tanggal,
        a.jam_kedatangan,
        a.jam_kepulangan,
        a.created_at

    FROM attendance a

    INNER JOIN users u
        ON a.id_user = u.id_user

    WHERE 1=1
";

$types = "";
$params = [];


// ========================================
// FILTER TANGGAL MULAI
// ========================================
if ($tanggal_mulai !== "") {

    $sql .= "
        AND a.tanggal >= ?
    ";

    $types .= "s";
    $params[] = $tanggal_mulai;
}


// ========================================
// FILTER TANGGAL SELESAI
// ========================================
if ($tanggal_selesai !== "") {

    $sql .= "
        AND a.tanggal <= ?
    ";

    $types .= "s";
    $params[] = $tanggal_selesai;
}


// ========================================
// FILTER USER
// ========================================
if ($id_user !== "") {

    $sql .= "
        AND a.id_user = ?
    ";

    $types .= "i";
    $params[] = (int) $id_user;
}


// ========================================
// URUTKAN DATA
// ========================================
$sql .= "
    ORDER BY a.tanggal DESC,
             a.jam_kedatangan DESC,
             a.id_attendance DESC
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
        "message" => "Gagal mengambil laporan attendance."
    ]);

    exit;
}


$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {

    $data[] = [
        "id_attendance" => (int) $row["id_attendance"],

        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama_user" => $row["nama_user"],
        "email_user" => $row["email_user"],

        "tanggal" => $row["tanggal"],
        "jam_kedatangan" => $row["jam_kedatangan"],
        "jam_kepulangan" => $row["jam_kepulangan"],

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
    "message" => "Laporan attendance berhasil diambil.",
    "total_data" => count($data),
    "data" => $data
]);

?>