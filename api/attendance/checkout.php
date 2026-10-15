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
if ($_SERVER["REQUEST_METHOD"] !== "PUT") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode PUT."
    ]);

    exit;
}

// =========================
// TANGGAL & JAM
// =========================
$tanggal = date("Y-m-d");
$jam_kepulangan = date("H:i:s");

// =========================
// CARI ABSENSI HARI INI
// =========================
$stmt = $conn->prepare("
    SELECT
        id_attendance,
        tanggal,
        jam_kedatangan,
        jam_kepulangan
    FROM attendance
    WHERE id_user = ?
    AND tanggal = ?
    LIMIT 1
");

$stmt->bind_param(
    "is",
    $id_user,
    $tanggal
);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// BELUM CHECK-IN
// =========================
if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Anda belum melakukan check-in hari ini."
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$attendance = $result->fetch_assoc();

$stmt->close();

// =========================
// CEK SUDAH CHECK-OUT
// =========================
if ($attendance["jam_kepulangan"] !== null) {

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Anda sudah melakukan check-out hari ini.",
        "data" => [
            "id_attendance" => (int) $attendance["id_attendance"],
            "tanggal" => $attendance["tanggal"],
            "jam_kedatangan" => $attendance["jam_kedatangan"],
            "jam_kepulangan" => $attendance["jam_kepulangan"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

// =========================
// UPDATE JAM KEPULANGAN
// =========================
$stmt = $conn->prepare("
    UPDATE attendance
    SET jam_kepulangan = ?
    WHERE id_attendance = ?
    AND id_user = ?
");

$stmt->bind_param(
    "sii",
    $jam_kepulangan,
    $attendance["id_attendance"],
    $id_user
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mencatat check-out."
    ]);

    exit;
}

$stmt->close();

// =========================
// RESPONSE
// =========================
echo json_encode([
    "status" => true,
    "message" => "Check-out berhasil dicatat.",
    "data" => [
        "id_attendance" => (int) $attendance["id_attendance"],
        "id_user" => $id_user,
        "tanggal" => $tanggal,
        "jam_kedatangan" => $attendance["jam_kedatangan"],
        "jam_kepulangan" => $jam_kepulangan
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>