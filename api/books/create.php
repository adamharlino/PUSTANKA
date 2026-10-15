<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// ==========================================
// CEK LOGIN ADMIN
// ==========================================

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

// Bisa menerima:
// 1. multipart/form-data
// 2. application/json

$input = $_POST;

if (empty($input)) {

    $input = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!is_array($input)) {
        $input = [];
    }
}


// ==========================================
// AMBIL DATA
// ==========================================

$isbn = trim($input["isbn"] ?? "");
$judul = trim($input["judul"] ?? "");
$pengarang = trim($input["pengarang"] ?? "");
$penerbit = trim($input["penerbit"] ?? "");
$tahun_terbit = trim($input["tahun_terbit"] ?? "");
$id_category = trim($input["id_category"] ?? "");
$id_shelf = trim($input["id_shelf"] ?? "");
$jenis_buku = trim($input["jenis_buku"] ?? "");
$sinopsis = trim($input["sinopsis"] ?? "");
$harga = trim($input["harga"] ?? "");
$barcode = trim($input["barcode"] ?? "");


// ==========================================
// VALIDASI WAJIB
// ==========================================

if (
    $judul === "" ||
    $pengarang === "" ||
    $harga === ""
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Judul, pengarang, dan harga wajib diisi."
    ]);

    exit;
}


// ==========================================
// VALIDASI JUDUL
// ==========================================

if (strlen($judul) < 1 || strlen($judul) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Judul buku harus 1-255 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI PENGARANG
// ==========================================

if (strlen($pengarang) < 1 || strlen($pengarang) > 150) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama pengarang harus 1-150 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI PENERBIT
// ==========================================

if (strlen($penerbit) > 150) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama penerbit maksimal 150 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI ISBN / KODE BUKU
// ==========================================

if ($isbn !== "") {

    if (strlen($isbn) > 50) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "ISBN maksimal 50 karakter."
        ]);

        exit;
    }
}


// ==========================================
// VALIDASI TAHUN TERBIT
// ==========================================

if ($tahun_terbit !== "") {

    if (!preg_match('/^[0-9]{4}$/', $tahun_terbit)) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Tahun terbit harus berupa 4 digit angka."
        ]);

        exit;
    }

    $currentYear = (int) date("Y");
    $year = (int) $tahun_terbit;

    if ($year < 1000 || $year > $currentYear) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Tahun terbit tidak valid."
        ]);

        exit;
    }

} else {

    $year = null;
}


// ==========================================
// VALIDASI CATEGORY
// ==========================================

if ($id_category !== "") {

    if (!ctype_digit($id_category) || (int) $id_category <= 0) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "ID kategori tidak valid."
        ]);

        exit;
    }

    $category = (int) $id_category;

} else {

    $category = null;
}


// ==========================================
// VALIDASI SHELF
// ==========================================

if ($id_shelf !== "") {

    if (!ctype_digit($id_shelf) || (int) $id_shelf <= 0) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "ID rak tidak valid."
        ]);

        exit;
    }

    $shelf = (int) $id_shelf;

} else {

    $shelf = null;
}


// ==========================================
// VALIDASI JENIS BUKU
// ==========================================

if (strlen($jenis_buku) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Jenis buku maksimal 100 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI SINOPSIS
// ==========================================

if (strlen($sinopsis) > 5000) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Sinopsis maksimal 5000 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI HARGA
// ==========================================

if (
    !is_numeric($harga) ||
    (float) $harga < 0
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Harga buku tidak valid."
    ]);

    exit;
}

$harga = (float) $harga;


// ==========================================
// VALIDASI BARCODE
// ==========================================

if ($barcode !== "") {

    if (!preg_match('/^[A-Za-z0-9-]+$/', $barcode)) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Barcode hanya boleh berisi huruf, angka, dan tanda hubung."
        ]);

        exit;
    }

    if (strlen($barcode) > 50) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Barcode maksimal 50 karakter."
        ]);

        exit;
    }
}


// ==========================================
// CEK ISBN DUPLIKAT
// ==========================================

if ($isbn !== "") {

    $stmt = $conn->prepare("
        SELECT id_book
        FROM books
        WHERE isbn = ?
        LIMIT 1
    ");

    if (!$stmt) {
        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Terjadi kesalahan pada server."
        ]);

        exit;
    }

    $stmt->bind_param("s", $isbn);

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

    if ($result->num_rows > 0) {

        $stmt->close();

        http_response_code(409);

        echo json_encode([
            "status" => false,
            "message" => "ISBN sudah digunakan."
        ]);

        exit;
    }

    $stmt->close();
}


// ==========================================
// CEK BARCODE DUPLIKAT
// ==========================================

if ($barcode !== "") {

    $stmt = $conn->prepare("
        SELECT id_book
        FROM books
        WHERE barcode = ?
        LIMIT 1
    ");

    if (!$stmt) {
        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Terjadi kesalahan pada server."
        ]);

        exit;
    }

    $stmt->bind_param("s", $barcode);

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

    if ($result->num_rows > 0) {

        $stmt->close();

        http_response_code(409);

        echo json_encode([
            "status" => false,
            "message" => "Barcode sudah digunakan."
        ]);

        exit;
    }

    $stmt->close();
}


// ==========================================
// UPLOAD FOTO BUKU
// ==========================================

$foto = "";
$fotoPathBaru = "";

if (
    isset($_FILES["foto"]) &&
    $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE
) {

    // --------------------------------------
    // CEK ERROR UPLOAD
    // --------------------------------------

    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Foto buku gagal diupload."
        ]);

        exit;
    }


    // --------------------------------------
    // BATAS UKURAN
    // Maksimal 2 MB
    // --------------------------------------

    $maxSize = 2 * 1024 * 1024;

    if ($_FILES["foto"]["size"] > $maxSize) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }


    // --------------------------------------
    // CEK MIME TYPE
    // --------------------------------------

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mimeType = $finfo->file(
        $_FILES["foto"]["tmp_name"]
    );

    $allowedMime = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowedMime[$mimeType])) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format foto hanya boleh JPG, PNG, atau WEBP."
        ]);

        exit;
    }


    // --------------------------------------
    // CEK FILE BENAR-BENAR GAMBAR
    // --------------------------------------

    if (
        @getimagesize(
            $_FILES["foto"]["tmp_name"]
        ) === false
    ) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "File yang diupload bukan gambar yang valid."
        ]);

        exit;
    }


    // --------------------------------------
    // FOLDER UPLOAD
    // --------------------------------------

    $uploadDir = __DIR__ . "/../../uploads/books/";

    if (!is_dir($uploadDir)) {

        if (!mkdir($uploadDir, 0755, true)) {

            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Folder upload buku tidak dapat dibuat."
            ]);

            exit;
        }
    }


    // --------------------------------------
    // NAMA FILE RANDOM
    // --------------------------------------

    try {

        $randomName = bin2hex(
            random_bytes(16)
        );

    } catch (Exception $e) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal membuat nama file."
        ]);

        exit;
    }


    $extension = $allowedMime[$mimeType];

    $fileName = "book_" . $randomName . "." . $extension;

    $targetPath = $uploadDir . $fileName;


    // --------------------------------------
    // PINDAHKAN FILE
    // --------------------------------------

    if (
        !move_uploaded_file(
            $_FILES["foto"]["tmp_name"],
            $targetPath
        )
    ) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan foto buku."
        ]);

        exit;
    }


    // --------------------------------------
    // PATH UNTUK DATABASE
    // --------------------------------------

    $fotoPathBaru = "uploads/books/" . $fileName;

    $foto = $fotoPathBaru;
}


// ==========================================
// INSERT BUKU
// ==========================================

$stmt = $conn->prepare("
    INSERT INTO books
    (
        isbn,
        judul,
        pengarang,
        penerbit,
        tahun_terbit,
        id_category,
        id_shelf,
        jenis_buku,
        sinopsis,
        harga,
        foto,
        barcode,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'tersedia')
");

if (!$stmt) {

    // Hapus foto jika query gagal dibuat
    if ($fotoPathBaru !== "") {

        $uploadedFile = __DIR__ . "/../../" . $fotoPathBaru;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}


// ==========================================
// BIND PARAMETER
// ==========================================
//
// s = string
// i = integer
// d = decimal/double
//
// Urutan:
// isbn        = s
// judul       = s
// pengarang   = s
// penerbit    = s
// year        = i
// category    = i
// shelf       = i
// jenis_buku  = s
// sinopsis    = s
// harga       = d
// foto        = s
// barcode     = s

$stmt->bind_param(
    "ssssiiissdss",
    $isbn,
    $judul,
    $pengarang,
    $penerbit,
    $year,
    $category,
    $shelf,
    $jenis_buku,
    $sinopsis,
    $harga,
    $foto,
    $barcode
);


// ==========================================
// EXECUTE
// ==========================================

if ($stmt->execute()) {

    $newId = $conn->insert_id;

    http_response_code(201);

    echo json_encode([
        "status" => true,
        "message" => "Buku berhasil ditambahkan.",
        "data" => [
            "id_book" => $newId,
            "judul" => $judul,
            "foto" => $foto !== "" ? $foto : null,
            "status" => "tersedia"
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} else {

    // --------------------------------------
    // JIKA DATABASE GAGAL
    // HAPUS FOTO YANG SUDAH DIUPLOAD
    // --------------------------------------

    if ($fotoPathBaru !== "") {

        $uploadedFile = __DIR__ . "/../../" . $fotoPathBaru;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menambahkan buku."
    ]);
}


$stmt->close();
$conn->close();

?>