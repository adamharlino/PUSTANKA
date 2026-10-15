<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // Untuk localhost HTTP
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

// ==================================================
// 1. CEK LOGIN USER
// ==================================================

$stmt = $conn->prepare(
    "SELECT
        id_user,
        nama,
        nik,
        alamat,
        no_telp,
        email,
        password,
        status,
        member_code
     FROM users
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

$user = $result->fetch_assoc();

$stmt->close();

// ==================================================
// JIKA USER DITEMUKAN
// ==================================================

if ($user) {

    // ===============================
    // CEK PASSWORD USER
    // ===============================

    if (!password_verify($password, $user["password"])) {
        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Email atau password salah."
        ]);

        exit;
    }

    // ===============================
    // CEK STATUS USER
    // ===============================

    if ($user["status"] !== "aktif") {
        http_response_code(403);

        echo json_encode([
            "status" => false,
            "message" => "Akun tidak aktif atau diblokir."
        ]);

        exit;
    }

    // ===============================
    // REGENERATE SESSION ID
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
    // SIMPAN SESSION USER
    // ===============================

    $_SESSION["id_user"] = (int) $user["id_user"];
    $_SESSION["nama"] = $user["nama"];
    $_SESSION["email"] = $user["email"];
    $_SESSION["role"] = "user";

    // ===============================
    // HAPUS DATA SENSITIF
    // ===============================

    unset(
        $user["password"],
        $user["status"]
    );

    $user["id_user"] = (int) $user["id_user"];

    // ===============================
    // RESPONSE USER
    // ===============================

    echo json_encode([
        "status" => true,
        "message" => "Login berhasil.",
        "role" => "user",
        "redirect" => "/frontend/user/dashboard/",
        "data" => $user
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();
    exit;
}

// ==================================================
// 2. CEK LOGIN ADMIN
// ==================================================

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

// ==================================================
// JIKA ADMIN DITEMUKAN
// ==================================================

if ($admin) {

    // ===============================
    // CEK PASSWORD ADMIN
    // ===============================

    if (!password_verify($password, $admin["password"])) {
        http_response_code(401);

        echo json_encode([
            "status" => false,
            "message" => "Email atau password salah."
        ]);

        exit;
    }

    // ===============================
    // REGENERATE SESSION ID
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
    // SIMPAN SESSION ADMIN
    // ===============================

    $_SESSION["id_admin"] = (int) $admin["id_admin"];
    $_SESSION["nama"] = $admin["nama"];
    $_SESSION["email"] = $admin["email"];
    $_SESSION["role"] = "admin";

    // ===============================
    // CARD CODE ADMIN
    // ===============================

    $admin["id_admin"] = (int) $admin["id_admin"];

    $admin["card_code"] = "ADMIN-" . str_pad(
        $admin["id_admin"],
        6,
        "0",
        STR_PAD_LEFT
    );

    // ===============================
    // HAPUS PASSWORD
    // ===============================

    unset($admin["password"]);

    // ===============================
    // RESPONSE ADMIN
    // ===============================

    echo json_encode([
        "status" => true,
        "message" => "Login berhasil.",
        "role" => "admin",
        "redirect" => "/frontend/admin/dashboard/",
        "data" => $admin
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();
    exit;
}

// ==================================================
// EMAIL TIDAK DITEMUKAN
// ==================================================

http_response_code(401);

echo json_encode([
    "status" => false,
    "message" => "Email atau password salah."
]);

$conn->close();

?>