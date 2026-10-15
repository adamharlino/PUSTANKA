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
| CEK ADMIN LOGIN
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
| CEK METHOD
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| AMBIL INPUT
|--------------------------------------------------------------------------
*/

$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    $input = $_POST;
}

$id_loan = (int) ($input["id_loan"] ?? 0);


/*
|--------------------------------------------------------------------------
| VALIDASI ID PEMINJAMAN
|--------------------------------------------------------------------------
*/

if ($id_loan <= 0) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID peminjaman wajib diisi."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| CEK DATA PEMINJAMAN
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        l.id_loan,
        l.id_user,
        l.id_book,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status,
        u.nama AS nama_user,
        b.judul,
        b.harga,
        b.status AS status_buku
    FROM loans l
    INNER JOIN users u
        ON l.id_user = u.id_user
    INNER JOIN books b
        ON l.id_book = b.id_book
    WHERE l.id_loan = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal memproses data."
    ]);

    exit;
}

$stmt->bind_param("i", $id_loan);
$stmt->execute();

$result = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| PEMINJAMAN TIDAK DITEMUKAN
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| CEK STATUS PEMINJAMAN
|--------------------------------------------------------------------------
*/

if (
    $loan["status"] !== "dipinjam" &&
    $loan["status"] !== "terlambat"
) {
    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Buku sudah dikembalikan atau status peminjaman tidak valid."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| TANGGAL PENGEMBALIAN
|--------------------------------------------------------------------------
*/

$tanggal_kembali = date("Y-m-d");


/*
|--------------------------------------------------------------------------
| CEK KETERLAMBATAN
|--------------------------------------------------------------------------
|
| Aturan:
| - Sampai 7 hari setelah batas kembali = tidak kena denda
| - Lebih dari 7 hari = denda 10% harga buku
|
*/

$batas_denda = date(
    "Y-m-d",
    strtotime($loan["batas_kembali"] . " +7 days")
);

$terlambat = $tanggal_kembali > $batas_denda;

$nominal_denda = 0;

if ($terlambat) {
    $nominal_denda = (int) round($loan["harga"] * 0.10);
}


/*
|--------------------------------------------------------------------------
| MULAI TRANSACTION
|--------------------------------------------------------------------------
*/

$conn->begin_transaction();

try {

    /*
    |--------------------------------------------------------------------------
    | UPDATE PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        UPDATE loans
        SET
            tanggal_kembali = ?,
            status = 'dikembalikan'
        WHERE id_loan = ?
        AND status IN ('dipinjam', 'terlambat')
    ");

    if (!$stmt) {
        throw new Exception("Gagal memproses pengembalian.");
    }

    $stmt->bind_param(
        "si",
        $tanggal_kembali,
        $id_loan
    );

    if (!$stmt->execute()) {
        throw new Exception("Gagal memperbarui data peminjaman.");
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();

        throw new Exception(
            "Peminjaman sudah diproses atau status tidak valid."
        );
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS BUKU
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        UPDATE books
        SET status = 'tersedia'
        WHERE id_book = ?
    ");

    if (!$stmt) {
        throw new Exception("Gagal memproses status buku.");
    }

    $stmt->bind_param(
        "i",
        $loan["id_book"]
    );

    if (!$stmt->execute()) {
        throw new Exception("Gagal memperbarui status buku.");
    }

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | CEK / BUAT DENDA
    |--------------------------------------------------------------------------
    */

    if ($terlambat && $nominal_denda > 0) {

        // Cegah denda ganda untuk peminjaman yang sama
        $stmt = $conn->prepare("
            SELECT id_fine
            FROM fines
            WHERE id_loan = ?
            LIMIT 1
        ");

        if (!$stmt) {
            throw new Exception("Gagal mengecek data denda.");
        }

        $stmt->bind_param(
            "i",
            $id_loan
        );

        $stmt->execute();

        $fine_result = $stmt->get_result();

        $stmt->close();


        /*
        |--------------------------------------------------------------------------
        | INSERT DENDA JIKA BELUM ADA
        |--------------------------------------------------------------------------
        */

        if ($fine_result->num_rows === 0) {

            $status_pembayaran = "belum_bayar";

            $stmt = $conn->prepare("
                INSERT INTO fines (
                    id_loan,
                    nominal_denda,
                    status_pembayaran
                )
                VALUES (?, ?, ?)
            ");

            if (!$stmt) {
                throw new Exception("Gagal membuat data denda.");
            }

            $stmt->bind_param(
                "ids",
                $id_loan,
                $nominal_denda,
                $status_pembayaran
            );

            if (!$stmt->execute()) {
                throw new Exception("Gagal menyimpan data denda.");
            }

            $stmt->close();
        }
    }


    /*
    |--------------------------------------------------------------------------
    | COMMIT
    |--------------------------------------------------------------------------
    */

    $conn->commit();

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | ROLLBACK
    |--------------------------------------------------------------------------
    */

    $conn->rollback();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);

    $conn->close();

    exit;
}


/*
|--------------------------------------------------------------------------
| RESPONSE
|--------------------------------------------------------------------------
*/

http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Buku berhasil dikembalikan.",
    "data" => [
        "id_loan" => (int) $loan["id_loan"],
        "id_user" => (int) $loan["id_user"],
        "id_book" => (int) $loan["id_book"],
        "nama_user" => $loan["nama_user"],
        "judul_buku" => $loan["judul"],
        "tanggal_pinjam" => $loan["tanggal_pinjam"],
        "batas_kembali" => $loan["batas_kembali"],
        "tanggal_kembali" => $tanggal_kembali,
        "status" => "dikembalikan",
        "terlambat" => $terlambat,
        "nominal_denda" => $nominal_denda
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>