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
| Cek Login User
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Hanya POST
|--------------------------------------------------------------------------
*/
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan POST."
    ]);

    exit;
}

$id_user = (int) $_SESSION["id_user"];

/*
|--------------------------------------------------------------------------
| Ambil ID Fine
|--------------------------------------------------------------------------
*/
$id_fine = $_POST["id_fine"] ?? null;

if (
    !$id_fine ||
    !is_numeric($id_fine) ||
    (int) $id_fine <= 0
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID denda tidak valid."
    ]);

    exit;
}

$id_fine = (int) $id_fine;

/*
|--------------------------------------------------------------------------
| Metode Pembayaran
|--------------------------------------------------------------------------
*/
$metode = strtolower(trim($_POST["metode"] ?? ""));

$allowedMetode = [
    "qris",
    "transfer_bank"
];

if (!in_array($metode, $allowedMetode, true)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Metode pembayaran harus qris atau transfer_bank."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Cek File Bukti
|--------------------------------------------------------------------------
*/
if (
    !isset($_FILES["bukti_pembayaran"]) ||
    $_FILES["bukti_pembayaran"]["error"] !== UPLOAD_ERR_OK
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Bukti pembayaran wajib diupload."
    ]);

    exit;
}

$file = $_FILES["bukti_pembayaran"];

/*
|--------------------------------------------------------------------------
| Maksimal 2 MB
|--------------------------------------------------------------------------
*/
$maxSize = 2 * 1024 * 1024;

if ($file["size"] > $maxSize) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Ukuran bukti pembayaran maksimal 2 MB."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Validasi MIME Type
|--------------------------------------------------------------------------
*/
$finfo = finfo_open(FILEINFO_MIME_TYPE);

$mimeType = finfo_file(
    $finfo,
    $file["tmp_name"]
);

finfo_close($finfo);

$allowedMime = [
    "image/jpeg" => "jpg",
    "image/png" => "png",
    "image/webp" => "webp",
    "application/pdf" => "pdf"
];

if (!isset($allowedMime[$mimeType])) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format bukti pembayaran harus JPG, PNG, WEBP, atau PDF."
    ]);

    exit;
}

$extension = $allowedMime[$mimeType];

/*
|--------------------------------------------------------------------------
| Ambil Data Denda + Validasi Kepemilikan
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        f.id_fine,
        f.id_loan,
        f.nominal_denda,
        f.status_pembayaran,
        l.id_user,
        u.nama
    FROM fines f
    INNER JOIN loans l
        ON f.id_loan = l.id_loan
    INNER JOIN users u
        ON l.id_user = u.id_user
    WHERE
        f.id_fine = ?
        AND l.id_user = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Query denda gagal dipersiapkan."
    ]);

    exit;
}

$stmt->bind_param(
    "ii",
    $id_fine,
    $id_user
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Denda tidak ditemukan atau bukan milik user."
    ]);

    exit;
}

$fine = $result->fetch_assoc();

$stmt->close();

/*
|--------------------------------------------------------------------------
| Cek Status Denda
|--------------------------------------------------------------------------
*/
if ($fine["status_pembayaran"] === "lunas") {

    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Denda ini sudah lunas."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Cek Payment Pending
|--------------------------------------------------------------------------
*/
$sqlPending = "
    SELECT id_payment
    FROM payments
    WHERE
        id_fine = ?
        AND status = 'pending'
    LIMIT 1
";

$stmtPending = $conn->prepare($sqlPending);

if (!$stmtPending) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Query payment gagal dipersiapkan."
    ]);

    exit;
}

$stmtPending->bind_param(
    "i",
    $id_fine
);

$stmtPending->execute();

$resultPending = $stmtPending->get_result();

if ($resultPending->num_rows > 0) {

    $stmtPending->close();

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Masih ada pembayaran yang sedang menunggu verifikasi admin."
    ]);

    exit;
}

$stmtPending->close();

/*
|--------------------------------------------------------------------------
| Siapkan Folder Upload
|--------------------------------------------------------------------------
*/
$uploadDir = __DIR__ . "/../../uploads/payments/";

if (!is_dir($uploadDir)) {

    if (!mkdir($uploadDir, 0755, true)) {

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Folder upload pembayaran gagal dibuat."
        ]);

        exit;
    }
}

/*
|--------------------------------------------------------------------------
| Nama File Unik
|--------------------------------------------------------------------------
*/
$randomName =
    "payment_" .
    bin2hex(random_bytes(16)) .
    "." .
    $extension;

$filePath = $uploadDir . $randomName;

/*
|--------------------------------------------------------------------------
| Pindahkan File
|--------------------------------------------------------------------------
*/
if (!move_uploaded_file(
    $file["tmp_name"],
    $filePath
)) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Bukti pembayaran gagal disimpan."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| Path yang disimpan ke database
|--------------------------------------------------------------------------
*/
$dbFilePath =
    "uploads/payments/" . $randomName;

/*
|--------------------------------------------------------------------------
| Simpan Payment
|--------------------------------------------------------------------------
*/
$nominal = $fine["nominal_denda"];

$conn->begin_transaction();

try {

    $sqlInsert = "
        INSERT INTO payments (
            id_fine,
            nominal,
            metode,
            status,
            bukti_pembayaran
        )
        VALUES (?, ?, ?, 'pending', ?)
    ";

    $stmtInsert = $conn->prepare($sqlInsert);

    if (!$stmtInsert) {
        throw new Exception(
            "Query insert payment gagal dipersiapkan."
        );
    }

    $stmtInsert->bind_param(
        "idss",
        $id_fine,
        $nominal,
        $metode,
        $dbFilePath
    );

    if (!$stmtInsert->execute()) {
        throw new Exception(
            "Payment gagal disimpan."
        );
    }

    $id_payment =
        $stmtInsert->insert_id;

    $stmtInsert->close();

    /*
    |--------------------------------------------------------------------------
    | Commit
    |--------------------------------------------------------------------------
    */
    $conn->commit();

    echo json_encode([
        "status" => true,
        "message" => "Pembayaran berhasil diajukan dan menunggu verifikasi admin.",
        "data" => [
            "id_payment" => (int) $id_payment,
            "id_fine" => (int) $id_fine,
            "nominal" => $nominal,
            "metode" => $metode,
            "status" => "pending",
            "bukti_pembayaran" => $dbFilePath
        ]
    ]);

} catch (Exception $e) {

    /*
    |--------------------------------------------------------------------------
    | Rollback
    |--------------------------------------------------------------------------
    */
    $conn->rollback();

    /*
    |--------------------------------------------------------------------------
    | Hapus file jika database gagal
    |--------------------------------------------------------------------------
    */
    if (file_exists($filePath)) {
        unlink($filePath);
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);

}

?>