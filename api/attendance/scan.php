<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
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
// CEK ADMIN LOGIN
// =========================
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
// AMBIL MEMBER CODE
// =========================
$member_code = strtoupper(trim($_POST["member_code"] ?? ""));

if ($member_code === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Member code wajib diisi."
    ]);

    exit;
}

// =========================
// VALIDASI MEMBER CODE
// =========================
if (strlen($member_code) > 30) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Member code tidak valid."
    ]);

    exit;
}

// =========================
// CARI USER
// =========================
$stmt = $conn->prepare("
    SELECT
        id_user,
        member_code,
        nama,
        foto,
        status
    FROM users
    WHERE member_code = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param("s", $member_code);

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Member code tidak ditemukan."
    ]);

    exit;
}

$user = $result->fetch_assoc();

$stmt->close();

// =========================
// CEK STATUS USER
// =========================
if ($user["status"] !== "aktif") {
    http_response_code(403);

    echo json_encode([
        "status" => false,
        "message" => "User tidak dapat melakukan absensi karena status tidak aktif.",
        "data" => [
            "id_user" => (int) $user["id_user"],
            "member_code" => $user["member_code"],
            "nama" => $user["nama"],
            "status" => $user["status"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

$id_user = (int) $user["id_user"];

// =========================
// TANGGAL & JAM
// =========================
$tanggal = date("Y-m-d");
$jam_sekarang = date("H:i:s");

// =========================
// CEK ATTENDANCE HARI INI
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

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "is",
    $id_user,
    $tanggal
);

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

// ==========================================
// BELUM ABSEN → CHECK-IN
// ==========================================
if ($result->num_rows === 0) {

    $stmt->close();

    $stmt = $conn->prepare("
        INSERT INTO attendance
        (
            id_user,
            tanggal,
            jam_kedatangan
        )
        VALUES (?, ?, ?)
    ");

    if (!$stmt) {
        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyiapkan check-in."
        ]);

        exit;
    }

    $stmt->bind_param(
        "iss",
        $id_user,
        $tanggal,
        $jam_sekarang
    );

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

    echo json_encode([
        "status" => true,
        "message" => "Check-in berhasil dicatat melalui scan.",
        "data" => [
            "aksi" => "check_in",
            "id_attendance" => (int) $id_attendance,
            "id_user" => $id_user,
            "member_code" => $user["member_code"],
            "nama" => $user["nama"],
            "foto" => $user["foto"],
            "tanggal" => $tanggal,
            "jam_kedatangan" => $jam_sekarang,
            "jam_kepulangan" => null
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();

    exit;
}

// ==========================================
// SUDAH ADA ATTENDANCE
// ==========================================
$attendance = $result->fetch_assoc();

$stmt->close();

// ==========================================
// SUDAH CHECK-OUT
// ==========================================
if ($attendance["jam_kepulangan"] !== null) {

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "User sudah melakukan check-in dan check-out hari ini.",
        "data" => [
            "aksi" => "selesai",
            "id_attendance" => (int) $attendance["id_attendance"],
            "id_user" => $id_user,
            "member_code" => $user["member_code"],
            "nama" => $user["nama"],
            "tanggal" => $attendance["tanggal"],
            "jam_kedatangan" => $attendance["jam_kedatangan"],
            "jam_kepulangan" => $attendance["jam_kepulangan"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();

    exit;
}

// ==========================================
// SUDAH CHECK-IN → CHECK-OUT
// ==========================================
$stmt = $conn->prepare("
    UPDATE attendance
    SET jam_kepulangan = ?
    WHERE id_attendance = ?
    AND id_user = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan check-out."
    ]);

    exit;
}

$id_attendance = (int) $attendance["id_attendance"];

$stmt->bind_param(
    "sii",
    $jam_sekarang,
    $id_attendance,
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
// RESPONSE CHECK-OUT
// =========================
echo json_encode([
    "status" => true,
    "message" => "Check-out berhasil dicatat melalui scan.",
    "data" => [
        "aksi" => "check_out",
        "id_attendance" => $id_attendance,
        "id_user" => $id_user,
        "member_code" => $user["member_code"],
        "nama" => $user["nama"],
        "foto" => $user["foto"],
        "tanggal" => $attendance["tanggal"],
        "jam_kedatangan" => $attendance["jam_kedatangan"],
        "jam_kepulangan" => $jam_sekarang
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>