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
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}

// =========================
// TANGGAL & JAM
// =========================
$tanggal = date("Y-m-d");
$jam_kedatangan = date("H:i:s");

// =========================
// CEK SUDAH ABSEN HARI INI
// =========================
$stmt = $conn->prepare("
    SELECT
        id_attendance,
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

if ($result->num_rows > 0) {

    $attendance = $result->fetch_assoc();

    $stmt->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Anda sudah melakukan check-in hari ini.",
        "data" => [
            "id_attendance" => (int) $attendance["id_attendance"],
            "tanggal" => $tanggal,
            "jam_kedatangan" => $attendance["jam_kedatangan"],
            "jam_kepulangan" => $attendance["jam_kepulangan"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$stmt->close();

// =========================
// SIMPAN ABSENSI
// =========================
$stmt = $conn->prepare("
    INSERT INTO attendance
    (
        id_user,
        tanggal,
        jam_kedatangan
    )
    VALUES (?, ?, ?)
");

$stmt->bind_param(
    "iss",
    $id_user,
    $tanggal,
    $jam_kedatangan
);

// =========================
// EKSEKUSI
// =========================
if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mencatat check-in."
    ]);

    exit;
}

$id_attendance = $conn->insert_id;

$stmt->close();

// =========================
// RESPONSE
// =========================
echo json_encode([
    "status" => true,
    "message" => "Check-in berhasil dicatat.",
    "data" => [
        "id_attendance" => $id_attendance,
        "id_user" => $id_user,
        "tanggal" => $tanggal,
        "jam_kedatangan" => $jam_kedatangan,
        "jam_kepulangan" => null
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>