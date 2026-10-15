<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

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
// CEK ID BUKU
// ===============================
$id_book = $_GET["id"] ?? "";

// Harus berupa angka bulat positif
if (
    $id_book === "" ||
    !ctype_digit((string) $id_book) ||
    (int) $id_book <= 0
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID buku tidak valid."
    ]);

    exit;
}

$id_book = (int) $id_book;

// ===============================
// QUERY DETAIL BUKU
// ===============================
$sql = "
    SELECT
        b.id_book,
        b.isbn,
        b.judul,
        b.pengarang,
        b.penerbit,
        b.tahun_terbit,
        b.jenis_buku,
        b.sinopsis,
        b.harga,
        b.foto,
        b.barcode,
        b.status,

        c.id_category,
        c.nama_category,

        s.id_shelf,
        s.no_rak,
        s.lantai,
        s.keterangan

    FROM books b

    LEFT JOIN categories c
        ON b.id_category = c.id_category

    LEFT JOIN shelves s
        ON b.id_shelf = s.id_shelf

    WHERE b.id_book = ?

    LIMIT 1
";

// ===============================
// PREPARE
// ===============================
$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

// ===============================
// BIND PARAMETER
// ===============================
$stmt->bind_param("i", $id_book);

// ===============================
// EXECUTE
// ===============================
if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil detail buku."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

// ===============================
// AMBIL HASIL
// ===============================
$result = $stmt->get_result();

$book = $result->fetch_assoc();

$stmt->close();

// ===============================
// JIKA BUKU TIDAK DITEMUKAN
// ===============================
if (!$book) {
    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Buku tidak ditemukan."
    ]);

    $conn->close();
    exit;
}

// ===============================
// FORMAT DATA
// ===============================
$data = [
    "id_book" => (int) $book["id_book"],

    "isbn" => $book["isbn"],
    "barcode" => $book["barcode"],

    "judul" => $book["judul"],
    "pengarang" => $book["pengarang"],
    "penerbit" => $book["penerbit"],

    "tahun_terbit" => $book["tahun_terbit"],
    "jenis_buku" => $book["jenis_buku"],

    "sinopsis" => $book["sinopsis"],

    "harga" => (float) $book["harga"],

    "foto" => $book["foto"],

    "status" => $book["status"],

    "kategori" => [
        "id_category" => $book["id_category"] !== null
            ? (int) $book["id_category"]
            : null,

        "nama_category" => $book["nama_category"]
    ],

    "rak" => [
        "id_shelf" => $book["id_shelf"] !== null
            ? (int) $book["id_shelf"]
            : null,

        "no_rak" => $book["no_rak"],
        "lantai" => $book["lantai"],
        "keterangan" => $book["keterangan"]
    ]
];

// ===============================
// RESPONSE
// ===============================
echo json_encode([
    "status" => true,
    "message" => "Detail buku berhasil diambil.",
    "data" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>