<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ==========================================
// SESSION
// ==========================================
session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // ubah true jika production HTTPS
    "samesite" => "Lax"
]);

// ==========================================
// CEK METHOD
// ==========================================
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);
    exit;
}

// ==========================================
// AMBIL INPUT
// ==========================================
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

// ==========================================
// AMBIL DATA
// ==========================================
$nama = trim($input["nama"] ?? "");
$nik = trim($input["nik"] ?? "");
$alamat = trim($input["alamat"] ?? "");
$no_telp = trim($input["no_telp"] ?? "");
$email = strtolower(trim($input["email"] ?? ""));
$password = $input["password"] ?? "";
$konfirmasi_password = $input["konfirmasi_password"] ?? "";

// ==========================================
// VALIDASI DATA WAJIB
// ==========================================
if (
    $nama === "" ||
    $nik === "" ||
    $email === "" ||
    $password === "" ||
    $konfirmasi_password === ""
) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Data wajib belum lengkap."
    ]);
    exit;
}

// ==========================================
// VALIDASI NAMA
// ==========================================
if (strlen($nama) < 3) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Nama minimal 3 karakter."
    ]);
    exit;
}

if (strlen($nama) > 100) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Nama maksimal 100 karakter."
    ]);
    exit;
}

// ==========================================
// VALIDASI NIK
// ==========================================
if (!preg_match('/^[0-9]{16}$/', $nik)) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "NIK harus terdiri dari 16 digit angka."
    ]);
    exit;
}

// ==========================================
// VALIDASI ALAMAT
// ==========================================
if (strlen($alamat) > 255) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Alamat maksimal 255 karakter."
    ]);
    exit;
}

// ==========================================
// VALIDASI NOMOR TELEPON
// ==========================================
if ($no_telp !== "") {
    if (!preg_match('/^\+?[0-9]{10,15}$/', $no_telp)) {
        http_response_code(400);
        echo json_encode([
            "status" => false,
            "message" => "Nomor telepon harus 10-15 digit dan hanya boleh berisi angka."
        ]);
        exit;
    }
}

// ==========================================
// VALIDASI EMAIL
// ==========================================
if (strlen($email) > 100) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Email maksimal 100 karakter."
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);
    exit;
}

// ==========================================
// VALIDASI PASSWORD
// ==========================================
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Password minimal 8 karakter."
    ]);
    exit;
}

if (strlen($password) > 72) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Password maksimal 72 karakter."
    ]);
    exit;
}

// ==========================================
// KONFIRMASI PASSWORD
// ==========================================
if ($password !== $konfirmasi_password) {
    http_response_code(400);
    echo json_encode([
        "status" => false,
        "message" => "Konfirmasi password tidak cocok."
    ]);
    exit;
}

// ==========================================
// CEK EMAIL / NIK DUPLIKAT
// ==========================================
$cek = $conn->prepare(
    "SELECT id_user
     FROM users
     WHERE email = ? OR nik = ?
     LIMIT 1"
);

if (!$cek) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);
    exit;
}

$cek->bind_param("ss", $email, $nik);

if (!$cek->execute()) {
    $cek->close();

    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);
    exit;
}

$cek->store_result();

if ($cek->num_rows > 0) {
    $cek->close();

    http_response_code(409);
    echo json_encode([
        "status" => false,
        "message" => "Email atau NIK sudah terdaftar."
    ]);
    exit;
}

$cek->close();

// ==========================================
// HASH PASSWORD
// ==========================================
$password_hash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

if ($password_hash === false) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Gagal mengamankan password."
    ]);
    exit;
}

/// ==========================================
// INSERT USER
// ==========================================

$stmt = $conn->prepare(
    "INSERT INTO users
    (nama, nik, alamat, no_telp, email, password, member_code)
    VALUES (?, ?, ?, ?, ?, ?, NULL)"
);

if (!$stmt) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);
    exit;
}

$stmt->bind_param(
    "ssssss",
    $nama,
    $nik,
    $alamat,
    $no_telp,
    $email,
    $password_hash
);

// ==========================================
// EXECUTE
// ==========================================
if ($stmt->execute()) {

    $newId = (int) $conn->insert_id;

// ==========================================
// GENERATE MEMBER CODE
// ==========================================

    $memberCode = "PSTK-" . str_pad(
        $newId,
        6,
        "0",
        STR_PAD_LEFT
    );

    $updateMember = $conn->prepare(
        "UPDATE users
         SET member_code = ?
         WHERE id_user = ?"
    );

    if (!$updateMember) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal membuat kode anggota."
        ]);

        $stmt->close();
        $conn->close();
        exit;
    }

    $updateMember->bind_param(
        "si",
        $memberCode,
        $newId
    );

    if (!$updateMember->execute()) {

        $updateMember->close();

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan kode anggota."
        ]);

        $stmt->close();
        $conn->close();
        exit;
    }

    $updateMember->close();

    http_response_code(201);

    echo json_encode([
        "status" => true,
        "message" => "Registrasi berhasil.",
        "data" => [
            "id_user" => $newId,
            "member_code" => $memberCode,
            "nama" => $nama,
            "email" => $email
        ]
    ]);

} else {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Registrasi gagal. Silakan coba lagi."
    ]);
}

?>