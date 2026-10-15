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
// METHOD HARUS GET
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);
    exit;
}

// =========================
// CEK ADMIN
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
        "message" => "Admin belum login."
    ]);
    exit;
}

// =========================
// AMBIL DATA REFRESH DARI DATABASE
// =========================

$idAdmin = (int) $_SESSION["id_admin"];
$foto = $_SESSION["foto"] ?? null;

// Ambil data lengkap (termasuk no_telp dan alamat) dari tabel admins
$stmt = $conn->prepare("SELECT nama, username, email, no_telp, alamat, foto FROM admins WHERE id_admin = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param("i", $idAdmin);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res) {
        $nama     = $res["nama"];
        $username = $res["username"];
        $email    = $res["email"];
        $no_telp  = $res["no_telp"];
        $alamat   = $res["alamat"];
        $foto     = $res["foto"];
    }
    $stmt->close();
}

$role = $_SESSION["role"] ?? "admin";

// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Session admin aktif.",
    "data" => [
        "id_admin" => $idAdmin,
        "nama"     => $nama ?? ($_SESSION["nama"] ?? ""),
        "username" => $username ?? ($_SESSION["username"] ?? ""),
        "email"    => $email ?? ($_SESSION["email"] ?? ""),
        "no_telp"  => $no_telp ?? "",
        "alamat"   => $alamat ?? "",
        "foto"     => $foto,
        "role"     => $role
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>