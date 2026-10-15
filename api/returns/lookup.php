<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


/*
|--------------------------------------------------------------------------
| ADMIN AUTHORIZATION
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| METHOD CHECK
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| VALIDASI BARCODE
|--------------------------------------------------------------------------
*/

$barcode = trim($_GET["barcode"] ?? "");

if ($barcode === "") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Barcode wajib diisi."
    ]);

    exit;
}

if (strlen($barcode) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Barcode maksimal 100 karakter."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| CARI PEMINJAMAN AKTIF BERDASARKAN BARCODE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        l.id_loan,
        l.id_user,
        l.id_book,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status AS status_peminjaman,

        u.nama AS nama_user,
        u.email AS email_user,
        u.no_telp AS no_telp_user,

        b.barcode,
        b.judul,
        b.pengarang,
        b.foto,
        b.status AS status_buku

    FROM loans l

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE b.barcode = ?
      AND l.status = 'dipinjam'

    ORDER BY l.tanggal_pinjam DESC

    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memproses permintaan."
    ]);

    exit;
}

$stmt->bind_param("s", $barcode);

if (!$stmt->execute()) {
    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mencari data peminjaman."
    ]);

    exit;
}

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| DATA TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

if ($result->num_rows === 0) {
    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Tidak ditemukan peminjaman aktif untuk buku tersebut."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| DATA DITEMUKAN
|--------------------------------------------------------------------------
*/

$data = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Data peminjaman aktif ditemukan.",
    "data" => $data
]);