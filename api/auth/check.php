<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

// ===============================
// METHOD HARUS GET
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
// AMBIL ROLE SESSION
// ===============================

$role = $_SESSION["role"] ?? "";

// ===============================
// CEK SESSION USER
// ===============================

if (
    $role === "user" &&
    isset($_SESSION["id_user"]) &&
    is_numeric($_SESSION["id_user"]) &&
    (int) $_SESSION["id_user"] > 0
) {
    $idUser = (int) $_SESSION["id_user"];
    $nama = $_SESSION["nama"] ?? "";
    $email = $_SESSION["email"] ?? "";

    echo json_encode([
        "status" => true,
        "message" => "User sudah login.",
        "data" => [
            "id_user" => $idUser,
            "nama" => $nama,
            "email" => $email,
            "role" => "user"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

// ===============================
// CEK SESSION ADMIN
// ===============================

if (
    $role === "admin" &&
    isset($_SESSION["id_admin"]) &&
    is_numeric($_SESSION["id_admin"]) &&
    (int) $_SESSION["id_admin"] > 0
) {
    $idAdmin = (int) $_SESSION["id_admin"];
    $nama = $_SESSION["nama"] ?? "";
    $email = $_SESSION["email"] ?? "";

    echo json_encode([
        "status" => true,
        "message" => "Admin sudah login.",
        "data" => [
            "id_admin" => $idAdmin,
            "nama" => $nama,
            "email" => $email,
            "role" => "admin"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    exit;
}

// ===============================
// SESSION TIDAK DITEMUKAN
// ===============================

http_response_code(401);

echo json_encode([
    "status" => false,
    "message" => "Silakan login terlebih dahulu."
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>