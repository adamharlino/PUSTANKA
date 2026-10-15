<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// =====================================
// CEK LOGIN ADMIN
// =====================================

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


// =====================================
// CEK METHOD
// =====================================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode GET."
    ]);

    exit;
}


// =====================================
// AMBIL ID ADMIN DARI SESSION
// =====================================

$id_admin = (int) $_SESSION["id_admin"];


// =====================================
// AMBIL DATA ADMIN
// =====================================

$stmt = $conn->prepare("
    SELECT
        id_admin,
        nama,
        username,
        email,
        alamat,
        no_telp,
        foto
    FROM admins
    WHERE id_admin = ?
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

$stmt->bind_param("i", $id_admin);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}


// =====================================
// AMBIL HASIL
// =====================================

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data admin tidak ditemukan."
    ]);

    exit;
}

$data = $result->fetch_assoc();


// =====================================
// FORMAT DATA
// =====================================
$data["id_admin"] = (int) $data["id_admin"];

$data["card_code"] = "ADMIN-" . str_pad(
    $data["id_admin"],
    6,
    "0",
    STR_PAD_LEFT
);


// =====================================
// RESPONSE
// =====================================

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Profil admin berhasil diambil.",
    "data" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);


$stmt->close();
$conn->close();

?>