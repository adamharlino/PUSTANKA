<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

/* =========================
   CEK ADMIN LOGIN
========================= */

if (
    !isset($_SESSION["id_admin"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. Admin harus login."
    ]);

    exit;
}

/* =========================
   CEK METHOD
========================= */

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diperbolehkan. Gunakan GET."
    ]);

    exit;
}

/* =========================
   CEK ID LOAN
========================= */

$id_loan = (int) ($_GET["id_loan"] ?? 0);

if ($id_loan <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID peminjaman tidak valid."
    ]);

    exit;
}

/* =========================
   QUERY DETAIL
========================= */

$sql = "
    SELECT
        l.id_loan,

        u.id_user,
        u.member_code,
        u.nama AS nama_peminjam,
        u.nik,
        u.email,
        u.no_telp,
        u.foto AS foto_peminjam,

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
        b.foto AS foto_buku,

        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status,
        l.created_at

    FROM loans l

    INNER JOIN users u
        ON l.id_user = u.id_user

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE l.id_loan = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query detail loan."
    ]);

    exit;
}

$stmt->bind_param("i", $id_loan);

if (!$stmt->execute()) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil detail peminjaman."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

$result = $stmt->get_result();

/* =========================
   CEK DATA
========================= */

if ($result->num_rows === 0) {

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data peminjaman tidak ditemukan."
    ]);

    $stmt->close();
    $conn->close();

    exit;
}

$row = $result->fetch_assoc();

/* =========================
   FORMAT DATA
========================= */

$data = [

    "id_loan" => (int) $row["id_loan"],

    "peminjam" => [
        "id_user" => (int) $row["id_user"],
        "member_code" => $row["member_code"],
        "nama" => $row["nama_peminjam"],
        "nik" => $row["nik"],
        "email" => $row["email"],
        "no_telp" => $row["no_telp"],
        "foto" => $row["foto_peminjam"]
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
        "harga_buku" => (float) $row["harga"],
        "foto" => $row["foto_buku"]
    ],

    "peminjaman" => [
        "tanggal_pinjam" => $row["tanggal_pinjam"],
        "batas_kembali" => $row["batas_kembali"],
        "tanggal_kembali" => $row["tanggal_kembali"],
        "status" => $row["status"],
        "created_at" => $row["created_at"]
    ]

];

/* =========================
   RESPONSE
========================= */

echo json_encode([
    "status" => true,
    "message" => "Detail peminjaman berhasil diambil.",
    "data" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();
?>