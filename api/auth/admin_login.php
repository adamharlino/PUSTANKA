<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // Set true jika menggunakan HTTPS pada Server Production
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK METHOD REQUEST
// ===============================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}

// ===============================
// AMBIL INPUT
// ===============================

$input = json_decode(
    file_get_contents("php://input"),
    true
);

// Jika bukan JSON, coba form-data / x-www-form-urlencoded
if (!is_array($input)) {
    $input = $_POST;
}

// ===============================
// AMBIL EMAIL & PASSWORD
// ===============================

$email = strtolower(trim($input["email"] ?? ""));
$password = $input["password"] ?? "";

// ===============================
// VALIDASI INPUT KOSONG
// ===============================

if ($email === "" || $password === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email dan password wajib diisi."
    ]);

    exit;
}

// ===============================
// VALIDASI PANJANG EMAIL
// ===============================

if (strlen($email) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email maksimal 100 karakter."
    ]);

    exit;
}

// ===============================
// VALIDASI FORMAT EMAIL
// ===============================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);

    exit;
}

// ===============================
// VALIDASI PANJANG PASSWORD
// ===============================

if (strlen($password) > 72) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password maksimal 72 karakter."
    ]);

    exit;
}

// ===============================
// CARI ADMIN BERDASARKAN EMAIL
// ===============================

$stmt = $conn->prepare(
    "SELECT
        id_admin,
        nama,
        username,
        email,
        password,
        alamat,
        no_telp,
        foto
     FROM admins
     WHERE email = ?
     LIMIT 1"
);

// ===============================
// CEK PREPARE
// ===============================

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

// ===============================
// BIND EMAIL
// ===============================

$stmt->bind_param("s", $email);

// ===============================
// EKSEKUSI QUERY
// ===============================

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

// ===============================
// AMBIL DATA ADMIN
// ===============================

$result = $stmt->get_result();

if (!$result) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$admin = $result->fetch_assoc();

$stmt->close();

// ===============================
// CEK EMAIL & PASSWORD
// ===============================

if (
    !$admin ||
    !password_verify($password, $admin["password"])
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Email atau password salah."
    ]);

    exit;
}

// ===============================
// REGENERATE SESSION ID
// Mencegah session fixation
// ===============================

if (!session_regenerate_id(true)) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal membuat session login."
    ]);

    exit;
}

// ===============================
// SIMPAN IDENTITAS ADMIN
// KE SESSION
// ===============================

$_SESSION["id_admin"] = (int) $admin["id_admin"];
$_SESSION["nama"] = $admin["nama"];
$_SESSION["username"] = $admin["username"];
$_SESSION["email"] = $admin["email"];
$_SESSION["role"] = "admin";

// ===============================
// JANGAN KIRIM PASSWORD
// ===============================

unset($admin["password"]);

// Pastikan ID berupa integer
$admin["id_admin"] = (int) $admin["id_admin"];

// ===============================
// RESPONSE BERHASIL
// ===============================

echo json_encode([
    "status" => true,
    "message" => "Login admin berhasil.",
    "data" => $admin
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>