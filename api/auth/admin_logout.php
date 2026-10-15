<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// VALIDASI ADMIN
// ===============================

if (
    !isset($_SESSION["id_admin"]) ||
    !is_numeric($_SESSION["id_admin"]) ||
    (int) $_SESSION["id_admin"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. Admin belum login."
    ]);

    exit;
}

// ===============================
// VALIDASI METHOD
// ===============================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diperbolehkan. Gunakan POST."
    ]);

    exit;
}

// ===============================
// HAPUS SESSION
// ===============================

$_SESSION = [];

// ===============================
// HAPUS COOKIE SESSION
// ===============================

if (ini_get("session.use_cookies")) {

    $params = session_get_cookie_params();

    setcookie(
        session_name(),
        "",
        [
            "expires" => time() - 42000,
            "path" => $params["path"],
            "domain" => $params["domain"],
            "secure" => $params["secure"],
            "httponly" => $params["httponly"],
            "samesite" => $params["samesite"] ?? "Lax"
        ]
    );
}

// ===============================
// HANCURKAN SESSION
// ===============================

session_destroy();

// ===============================
// RESPONSE
// ===============================

echo json_encode([
    "status" => true,
    "message" => "Admin berhasil logout."
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>