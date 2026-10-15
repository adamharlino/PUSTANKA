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
| CARI BUKU
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        id_book,
        barcode,
        judul,
        pengarang,
        penerbit,
        tahun_terbit,
        jenis_buku,
        harga,
        foto,
        status,
        sinopsis
    FROM books
    WHERE barcode = ?
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
        "message" => "Gagal mencari buku."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Buku dengan barcode tersebut tidak ditemukan."
    ]);

    exit;
}

$book = $result->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Buku ditemukan.",
    "data" => $book
]);