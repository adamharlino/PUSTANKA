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
// PAGINATION
// =========================
$page = max(
    1,
    (int) ($_GET["page"] ?? 1)
);

$limit = max(
    1,
    min(
        100,
        (int) ($_GET["limit"] ?? 10)
    )
);

$offset = ($page - 1) * $limit;

// =========================
// TOTAL DATA
// =========================
$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM attendance
    WHERE id_user = ?
");

$stmt->bind_param(
    "i",
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

$total = (int) $result->fetch_assoc()["total"];

$stmt->close();

// =========================
// AMBIL DATA ATTENDANCE
// =========================
$stmt = $conn->prepare("
    SELECT
        id_attendance,
        id_user,
        tanggal,
        jam_kedatangan,
        jam_kepulangan,
        created_at
    FROM attendance
    WHERE id_user = ?
    ORDER BY tanggal DESC, id_attendance DESC
    LIMIT ? OFFSET ?
");

$stmt->bind_param(
    "iii",
    $id_user,
    $limit,
    $offset
);

$stmt->execute();

$result = $stmt->get_result();

$attendance = [];

while ($row = $result->fetch_assoc()) {

    $attendance[] = [
        "id_attendance" => (int) $row["id_attendance"],
        "id_user" => (int) $row["id_user"],
        "tanggal" => $row["tanggal"],
        "jam_kedatangan" => $row["jam_kedatangan"],
        "jam_kepulangan" => $row["jam_kepulangan"],
        "created_at" => $row["created_at"]
    ];
}

$stmt->close();

// =========================
// PAGINATION
// =========================
$totalPages = $limit > 0
    ? (int) ceil($total / $limit)
    : 0;

// =========================
// RESPONSE
// =========================
echo json_encode([
    "status" => true,
    "message" => "Riwayat attendance berhasil diambil.",
    "data" => $attendance,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total_data" => $total,
        "total_pages" => $totalPages
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>