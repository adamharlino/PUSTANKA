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
// TIMEZONE INDONESIA
// =========================
date_default_timezone_set("Asia/Jakarta");

// =========================
// CEK USER LOGIN
// =========================
if (
    !isset($_SESSION["id_user"]) ||
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

// =========================
// CEK METHOD
// =========================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode GET."
    ]);

    exit;
}

// =========================
// AMBIL ID ATTENDANCE
// =========================
$id_attendance = (int) ($_GET["id_attendance"] ?? 0);

// =========================
// VALIDASI ID
// =========================
if ($id_attendance <= 0) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID attendance wajib diisi."
    ]);

    exit;
}

// =========================
// AMBIL DETAIL ATTENDANCE
// =========================
$stmt = $conn->prepare("
    SELECT
        a.id_attendance,
        a.id_user,
        u.nama,
        u.nik,
        a.tanggal,
        a.jam_kedatangan,
        a.jam_kepulangan,
        a.created_at
    FROM attendance a
    INNER JOIN users u
        ON a.id_user = u.id_user
    WHERE a.id_attendance = ?
    AND a.id_user = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $id_attendance,
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// DATA TIDAK DITEMUKAN
// =========================
if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data attendance tidak ditemukan."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$row = $result->fetch_assoc();

$stmt->close();

// =========================
// RESPONSE
// =========================
echo json_encode([
    "status" => true,
    "message" => "Detail attendance berhasil diambil.",
    "data" => [
        "id_attendance" => (int) $row["id_attendance"],
        "id_user" => (int) $row["id_user"],
        "nama" => $row["nama"],
        "nik" => $row["nik"],
        "tanggal" => $row["tanggal"],
        "jam_kedatangan" => $row["jam_kedatangan"],
        "jam_kepulangan" => $row["jam_kepulangan"],
        "created_at" => $row["created_at"]
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>