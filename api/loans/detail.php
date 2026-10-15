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
// CEK USER LOGIN
// =========================

if (
    !isset($_SESSION["id_user"]) ||
    ($_SESSION["role"] ?? "") !== "user"
) {
    http_response_code(401);

    echo json_encode([
        "status" => false,
        "message" => "Akses ditolak. User harus login."
    ]);

    exit;
}

$id_user = (int) $_SESSION["id_user"];

// =========================
// CEK ID PEMINJAMAN
// =========================

$id_loan = (int) ($_GET["id_loan"] ?? 0);

if ($id_loan <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID peminjaman tidak valid."
    ]);

    exit;
}

// =========================
// AMBIL DETAIL PEMINJAMAN
// =========================

$sql = "
    SELECT
        l.id_loan,
        l.id_user,
        u.nama AS nama_peminjam,
        l.id_book,

        b.isbn,
        b.judul,
        b.pengarang,
        b.penerbit,
        b.tahun_terbit,
        b.jenis_buku,
        b.sinopsis,
        b.harga,
        b.foto,

        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status,
        l.created_at

    FROM loans l

    INNER JOIN books b
        ON l.id_book = b.id_book

    INNER JOIN users u
        ON l.id_user = u.id_user

    WHERE l.id_loan = ?
    AND l.id_user = ?

    LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ii",
    $id_loan,
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// CEK DATA
// =========================

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

// =========================
// AMBIL DATA
// =========================

$row = $result->fetch_assoc();

// =========================
// FORMAT DATA
// =========================

$data = [
    "id_loan" => (int) $row["id_loan"],

    "peminjam" => [
        "id_user" => (int) $row["id_user"],
        "nama" => $row["nama_peminjam"]
    ],

    "buku" => [
        "id_book" => (int) $row["id_book"],
        "isbn" => $row["isbn"],
        "judul" => $row["judul"],
        "pengarang" => $row["pengarang"],
        "penerbit" => $row["penerbit"],
        "tahun_terbit" => $row["tahun_terbit"],
        "jenis_buku" => $row["jenis_buku"],
        "sinopsis" => $row["sinopsis"],
        "harga_buku" => (float) $row["harga"],
        "foto" => $row["foto"]
    ],

    "peminjaman" => [
        "tanggal_pinjam" => $row["tanggal_pinjam"],
        "batas_kembali" => $row["batas_kembali"],
        "tanggal_kembali" => $row["tanggal_kembali"],
        "status" => $row["status"],
        "created_at" => $row["created_at"]
    ]
];
// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Detail peminjaman berhasil diambil.",
    "data" => $data
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$stmt->close();
$conn->close();

?>