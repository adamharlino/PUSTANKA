<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // Untuk localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

// ===============================
// VALIDASI SESSION
// ===============================

$role = $_SESSION["role"] ?? "";

$isUser = (
    isset($_SESSION["id_user"]) &&
    is_numeric($_SESSION["id_user"]) &&
    (int) $_SESSION["id_user"] > 0 &&
    $role === "user"
);

$isAdmin = (
    isset($_SESSION["id_admin"]) &&
    is_numeric($_SESSION["id_admin"]) &&
    (int) $_SESSION["id_admin"] > 0 &&
    $role === "admin"
);

// ===============================
// CEK LOGIN
// ===============================

if (!$isUser && !$isAdmin) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. User atau admin belum login."
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
// SIMPAN ROLE UNTUK RESPONSE
// ===============================

$logoutRole = $role;

// ===============================
// HAPUS DATA SESSION
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
    "message" => "Logout berhasil.",
    "role" => $logoutRole
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

?>