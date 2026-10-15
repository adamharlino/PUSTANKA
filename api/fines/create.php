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
// CEK METHOD
// =========================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}

// =========================
// AMBIL JSON
// =========================

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$id_loan = (int) ($input["id_loan"] ?? 0);

// =========================
// VALIDASI ID LOAN
// =========================

if ($id_loan <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID peminjaman wajib diisi."
    ]);

    exit;
}

// =========================
// AMBIL DATA PEMINJAMAN
// =========================

$stmt = $conn->prepare("
    SELECT
        l.id_loan,
        l.id_user,
        l.id_book,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status,

        b.judul,
        b.harga

    FROM loans l

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE l.id_loan = ?
    AND l.id_user = ?

    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $id_loan,
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// CEK DATA PEMINJAMAN
// =========================

if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Data peminjaman tidak ditemukan."
    ]);

    exit;
}

$loan = $result->fetch_assoc();

$stmt->close();

// =========================
// TENTUKAN TANGGAL ACUAN
// =========================

if (!empty($loan["tanggal_kembali"])) {

    // Buku sudah dikembalikan
    $tanggal_acuan = $loan["tanggal_kembali"];

} else {

    // Buku belum dikembalikan
    $tanggal_acuan = date("Y-m-d");
}

// =========================
// HITUNG BATAS TOLERANSI
// =========================

$batas_toleransi = date(
    "Y-m-d",
    strtotime("+7 days", strtotime($loan["batas_kembali"]))
);

// =========================
// CEK APAKAH SUDAH TERKENA DENDA
// =========================

if ($tanggal_acuan <= $batas_toleransi) {

    echo json_encode([
        "status" => true,
        "message" => "Peminjaman belum terkena denda.",
        "data" => [
            "id_loan" => (int) $loan["id_loan"],
            "judul_buku" => $loan["judul"],
            "harga_buku" => (float) $loan["harga"],
            "tanggal_pinjam" => $loan["tanggal_pinjam"],
            "batas_kembali" => $loan["batas_kembali"],
            "batas_toleransi" => $batas_toleransi,
            "tanggal_acuan" => $tanggal_acuan,
            "nominal_denda" => 0,
            "status_pembayaran" => null
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();

    exit;
}

// =========================
// HITUNG DENDA
// =========================

$nominal_denda = (float) $loan["harga"] * 0.10;

// =========================
// CEK APAKAH DENDA SUDAH ADA
// =========================

$stmt = $conn->prepare("
    SELECT
        id_fine,
        nominal_denda,
        status_pembayaran,
        created_at

    FROM fines

    WHERE id_loan = ?

    ORDER BY id_fine DESC

    LIMIT 1
");

$stmt->bind_param(
    "i",
    $id_loan
);

$stmt->execute();

$result = $stmt->get_result();

$existingFine = $result->fetch_assoc();

$stmt->close();

// =========================
// JIKA DENDA SUDAH ADA
// =========================

if ($existingFine) {

    echo json_encode([
        "status" => true,
        "message" => "Denda untuk peminjaman ini sudah tersedia.",
        "data" => [
            "id_fine" => (int) $existingFine["id_fine"],
            "id_loan" => (int) $loan["id_loan"],
            "judul_buku" => $loan["judul"],
            "harga_buku" => (float) $loan["harga"],
            "nominal_denda" => (float) $existingFine["nominal_denda"],
            "status_pembayaran" => $existingFine["status_pembayaran"],
            "created_at" => $existingFine["created_at"]
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    $conn->close();

    exit;
}

// =========================
// SIMPAN DENDA
// =========================

$stmt = $conn->prepare("
    INSERT INTO fines
    (
        id_loan,
        nominal_denda,
        status_pembayaran
    )
    VALUES (?, ?, 'belum_bayar')
");

$stmt->bind_param(
    "id",
    $id_loan,
    $nominal_denda
);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyimpan data denda."
    ]);

    $conn->close();

    exit;
}

$id_fine = $conn->insert_id;

$stmt->close();

// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Denda berhasil dibuat.",
    "data" => [
        "id_fine" => $id_fine,
        "id_loan" => (int) $loan["id_loan"],
        "judul_buku" => $loan["judul"],
        "harga_buku" => (float) $loan["harga"],
        "tanggal_pinjam" => $loan["tanggal_pinjam"],
        "batas_kembali" => $loan["batas_kembali"],
        "batas_toleransi" => $batas_toleransi,
        "tanggal_acuan" => $tanggal_acuan,
        "nominal_denda" => $nominal_denda,
        "status_pembayaran" => "belum_bayar"
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>