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
    !is_numeric($_SESSION["id_user"]) ||
    (int) $_SESSION["id_user"] <= 0 ||
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
// CEK METHOD
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {

    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode GET."
    ]);

    exit;
}

// =========================
// VALIDASI ID PAYMENT
// =========================

$id_payment = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id_payment <= 0) {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID payment tidak valid."
    ]);

    exit;
}

// =========================
// QUERY PAYMENT
// =========================

$sql = "
    SELECT

        p.id_payment,
        p.id_fine,
        p.tanggal_bayar,
        p.nominal,
        p.metode,
        p.status,
        p.bukti_pembayaran,

        f.nominal_denda,
        f.status_pembayaran AS status_denda,
        f.created_at AS fine_created_at,

        l.id_loan,
        l.id_user,
        l.id_book,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status AS status_peminjaman,

        b.isbn,
        b.barcode,
        b.judul,
        b.pengarang,
        b.penerbit,
        b.tahun_terbit,
        b.jenis_buku,
        b.sinopsis,
        b.harga,
        b.foto

    FROM payments p

    INNER JOIN fines f
        ON p.id_fine = f.id_fine

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE
        p.id_payment = ?
        AND l.id_user = ?

    LIMIT 1
";

// =========================
// PREPARE
// =========================

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query."
    ]);

    exit;
}

$stmt->bind_param(
    "ii",
    $id_payment,
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// CEK DATA
// =========================

if ($result->num_rows === 0) {

    $stmt->close();
    $conn->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Payment tidak ditemukan atau bukan milik user."
    ]);

    exit;
}

$row = $result->fetch_assoc();

$stmt->close();
$conn->close();

// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Detail payment berhasil diambil.",

    "data" => [

        "id_payment" => (int) $row["id_payment"],

        "pembayaran" => [

            "id_payment" => (int) $row["id_payment"],

            "tanggal_bayar" =>
                $row["tanggal_bayar"],

            "nominal" =>
                (float) $row["nominal"],

            "metode" =>
                $row["metode"],

            "status" =>
                $row["status"],

            "bukti_pembayaran" =>
                $row["bukti_pembayaran"]
        ],

        "denda" => [

            "id_fine" =>
                (int) $row["id_fine"],

            "nominal_denda" =>
                (float) $row["nominal_denda"],

            "status_pembayaran" =>
                $row["status_denda"],

            "created_at" =>
                $row["fine_created_at"]
        ],

        "buku" => [

            "id_book" =>
                (int) $row["id_book"],

            "isbn" =>
                $row["isbn"],

            "barcode" =>
                $row["barcode"],

            "judul" =>
                $row["judul"],

            "pengarang" =>
                $row["pengarang"],

            "penerbit" =>
                $row["penerbit"],

            "tahun_terbit" =>
                $row["tahun_terbit"],

            "jenis_buku" =>
                $row["jenis_buku"],

            "sinopsis" =>
                $row["sinopsis"],

            "harga" =>
                (float) $row["harga"],

            "foto" =>
                $row["foto"]
        ],

        "peminjaman" => [

            "id_loan" =>
                (int) $row["id_loan"],

            "tanggal_pinjam" =>
                $row["tanggal_pinjam"],

            "batas_kembali" =>
                $row["batas_kembali"],

            "tanggal_kembali" =>
                $row["tanggal_kembali"],

            "status" =>
                $row["status_peminjaman"]
        ]
    ]

], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);