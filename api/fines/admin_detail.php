<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

/*
|--------------------------------------------------------------------------
| VALIDASI ADMIN
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
| METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diperbolehkan."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| VALIDASI ID FINE
|--------------------------------------------------------------------------
*/

$id_fine = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id_fine <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID denda tidak valid."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| QUERY DETAIL FINE
|--------------------------------------------------------------------------
*/

$sql = "
    SELECT
        f.id_fine,
        f.id_loan,
        f.nominal_denda,
        f.status_pembayaran,
        f.created_at,

        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status AS status_peminjaman,

        u.id_user,
        u.member_code,
        u.nama AS nama_peminjam,
        u.nik,
        u.email,
        u.no_telp,
        u.alamat,
        u.foto AS foto_user,

        b.id_book,
        b.isbn,
        b.barcode,
        b.judul,
        b.pengarang,
        b.penerbit,
        b.tahun_terbit,
        b.jenis_buku,
        b.sinopsis,
        b.harga,
        b.foto AS foto_buku

    FROM fines f

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE f.id_fine = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query detail denda.",
        "error" => $conn->error
    ]);

    exit;
}

$stmt->bind_param("i", $id_fine);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data denda tidak ditemukan."
    ]);

    exit;
}

$row = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

echo json_encode([
    "status" => true,
    "message" => "Detail denda berhasil diambil.",
    "data" => [
        "id_fine" => (int) $row["id_fine"],
        "nominal_denda" => $row["nominal_denda"],
        "status_pembayaran" => $row["status_pembayaran"],
        "created_at" => $row["created_at"],

        "peminjam" => [
            "id_user" => (int) $row["id_user"],
            "member_code" => $row["member_code"],
            "nama" => $row["nama_peminjam"],
            "nik" => $row["nik"],
            "email" => $row["email"],
            "no_telp" => $row["no_telp"],
            "alamat" => $row["alamat"],
            "foto" => $row["foto_user"]
        ],

        "buku" => [
            "id_book" => (int) $row["id_book"],
            "isbn" => $row["isbn"],
            "barcode" => $row["barcode"],
            "judul" => $row["judul"],
            "pengarang" => $row["pengarang"],
            "penerbit" => $row["penerbit"],
            "tahun_terbit" => $row["tahun_terbit"],
            "jenis_buku" => $row["jenis_buku"],
            "sinopsis" => $row["sinopsis"],
            "harga" => $row["harga"],
            "foto" => $row["foto_buku"]
        ],

        "peminjaman" => [
            "id_loan" => (int) $row["id_loan"],
            "tanggal_pinjam" => $row["tanggal_pinjam"],
            "batas_kembali" => $row["batas_kembali"],
            "tanggal_kembali" => $row["tanggal_kembali"],
            "status" => $row["status_peminjaman"]
        ]
    ]
]);