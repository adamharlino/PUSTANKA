<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // production HTTPS => true
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK USER
// ===============================
if (
    !isset($_SESSION["id_user"]) ||
    !is_numeric($_SESSION["id_user"]) ||
    (int) $_SESSION["id_user"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "user"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. User harus login."
    ]);

    exit;
}

// ===============================
// CEK METHOD
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);

    exit;
}

// ===============================
// AMBIL ID DARI SESSION
// ===============================
$id_user = (int) $_SESSION["id_user"];

// ===============================
// AMBIL DATA USER
// ===============================
$stmt = $conn->prepare("
    SELECT
        id_user,
        member_code,
        nama,
        nik,
        alamat,
        no_telp,
        email,
        foto,
        status,
        created_at,
        updated_at
    FROM users
    WHERE id_user = ?
    LIMIT 1
");

// ===============================
// CEK PREPARE
// ===============================
if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param("i", $id_user);

// ===============================
// EXECUTE
// ===============================
if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil profile user."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// ===============================
// AMBIL HASIL
// ===============================
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$stmt->close();

// ===============================
// USER TIDAK DITEMUKAN
// ===============================
if (!$user) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Profile user tidak ditemukan."
    ]);

    $conn->close();
    exit;
}

// ===============================
// FORMAT DATA
// ===============================
$data = [
    "id_user" => (int) $user["id_user"],
    "member_code" => $user["member_code"],
    "nama" => $user["nama"],
    "nik" => $user["nik"],
    "alamat" => $user["alamat"],
    "no_telp" => $user["no_telp"],
    "email" => $user["email"],
    "foto" => $user["foto"],
    "status" => $user["status"],
    "created_at" => $user["created_at"],
    "updated_at" => $user["updated_at"]
];

// ===============================
// RESPONSE
// ===============================
echo json_encode([
    "status" => true,
    "message" => "Profile user berhasil diambil.",
    "data" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();
?>