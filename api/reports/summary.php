<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// =====================================================
// 1. CEK METHOD
// =====================================================

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);

    exit;
}


// =====================================================
// 2. CEK SESSION ADMIN
// =====================================================

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


// =====================================================
// 3. AMBIL FILTER TANGGAL
// =====================================================

$tanggal_mulai = trim($_GET["tanggal_mulai"] ?? "");
$tanggal_selesai = trim($_GET["tanggal_selesai"] ?? "");


// =====================================================
// 4. VALIDASI FORMAT TANGGAL
// =====================================================

function validTanggal($tanggal)
{
    if ($tanggal === "") {
        return true;
    }

    $date = DateTime::createFromFormat("Y-m-d", $tanggal);

    return $date && $date->format("Y-m-d") === $tanggal;
}

if (!validTanggal($tanggal_mulai)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format tanggal_mulai harus YYYY-MM-DD."
    ]);

    exit;
}

if (!validTanggal($tanggal_selesai)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format tanggal_selesai harus YYYY-MM-DD."
    ]);

    exit;
}


// =====================================================
// 5. VALIDASI RENTANG TANGGAL
// =====================================================

if (
    $tanggal_mulai !== "" &&
    $tanggal_selesai !== "" &&
    $tanggal_mulai > $tanggal_selesai
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "tanggal_mulai tidak boleh lebih besar dari tanggal_selesai."
    ]);

    exit;
}


// =====================================================
// 6. SIAPKAN FILTER PERIODE
// =====================================================

$filter_loan = "";
$filter_return = "";
$filter_fine = "";
$filter_payment = "";
$filter_attendance = "";

$params_loan = [];
$params_return = [];
$params_fine = [];
$params_payment = [];
$params_attendance = [];

$types_loan = "";
$types_return = "";
$types_fine = "";
$types_payment = "";
$types_attendance = "";


// Jika tanggal mulai diisi
if ($tanggal_mulai !== "") {

    $filter_loan .= " AND DATE(l.tanggal_pinjam) >= ?";
    $params_loan[] = $tanggal_mulai;
    $types_loan .= "s";

    $filter_return .= " AND DATE(l.tanggal_kembali) >= ?";
    $params_return[] = $tanggal_mulai;
    $types_return .= "s";

    $filter_fine .= " AND DATE(f.created_at) >= ?";
    $params_fine[] = $tanggal_mulai;
    $types_fine .= "s";

    $filter_payment .= " AND DATE(p.tanggal_bayar) >= ?";
    $params_payment[] = $tanggal_mulai;
    $types_payment .= "s";

    $filter_attendance .= " AND DATE(a.tanggal) >= ?";
    $params_attendance[] = $tanggal_mulai;
    $types_attendance .= "s";
}


// Jika tanggal selesai diisi
if ($tanggal_selesai !== "") {

    $filter_loan .= " AND DATE(l.tanggal_pinjam) <= ?";
    $params_loan[] = $tanggal_selesai;
    $types_loan .= "s";

    $filter_return .= " AND DATE(l.tanggal_kembali) <= ?";
    $params_return[] = $tanggal_selesai;
    $types_return .= "s";

    $filter_fine .= " AND DATE(f.created_at) <= ?";
    $params_fine[] = $tanggal_selesai;
    $types_fine .= "s";

    $filter_payment .= " AND DATE(p.tanggal_bayar) <= ?";
    $params_payment[] = $tanggal_selesai;
    $types_payment .= "s";

    $filter_attendance .= " AND DATE(a.tanggal) <= ?";
    $params_attendance[] = $tanggal_selesai;
    $types_attendance .= "s";
}


// =====================================================
// 7. TOTAL PEMINJAMAN
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM loans l
    WHERE 1=1
    $filter_loan
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total peminjaman."
    ]);

    exit;
}

if (!empty($params_loan)) {
    $stmt->bind_param($types_loan, ...$params_loan);
}

$stmt->execute();

$result = $stmt->get_result();
$total_peminjaman = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 8. TOTAL PENGEMBALIAN
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM loans l
    WHERE l.tanggal_kembali IS NOT NULL
    $filter_return
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total pengembalian."
    ]);

    exit;
}

if (!empty($params_return)) {
    $stmt->bind_param($types_return, ...$params_return);
}

$stmt->execute();

$result = $stmt->get_result();
$total_pengembalian = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 9. TOTAL DENDA
// =====================================================

$sql = "
    SELECT
        COUNT(*) AS jumlah_denda,
        COALESCE(SUM(nominal_denda), 0) AS total_denda
    FROM fines f
    WHERE 1=1
    $filter_fine
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query denda."
    ]);

    exit;
}

if (!empty($params_fine)) {
    $stmt->bind_param($types_fine, ...$params_fine);
}

$stmt->execute();

$result = $stmt->get_result();
$fine_data = $result->fetch_assoc();

$jumlah_denda = (int) $fine_data["jumlah_denda"];
$total_denda = (float) $fine_data["total_denda"];

$stmt->close();

// =====================================================
// 10. TOTAL PEMBAYARAN DISETUJUI
// =====================================================

$sql = "
    SELECT
        COUNT(*) AS jumlah_pembayaran,
        COALESCE(SUM(nominal), 0) AS total_pembayaran
    FROM payments p
    WHERE p.status = 'disetujui'
    $filter_payment
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query pembayaran."
    ]);

    exit;
}

if (!empty($params_payment)) {
    $stmt->bind_param($types_payment, ...$params_payment);
}

$stmt->execute();

$result = $stmt->get_result();
$payment_data = $result->fetch_assoc();

$jumlah_pembayaran = (int) $payment_data["jumlah_pembayaran"];
$total_pembayaran = (float) $payment_data["total_pembayaran"];

$stmt->close();


// =====================================================
// 11. TOTAL KEHADIRAN
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM attendance a
    WHERE 1=1
    $filter_attendance
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query attendance."
    ]);

    exit;
}

if (!empty($params_attendance)) {
    $stmt->bind_param($types_attendance, ...$params_attendance);
}

$stmt->execute();

$result = $stmt->get_result();
$total_kehadiran = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 12. TOTAL USER
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM users
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total user."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$total_user = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 13. TOTAL BUKU
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM books
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query total buku."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$total_buku = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 14. BUKU TERSEDIA
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM books
    WHERE status = 'tersedia'
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query buku tersedia."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$buku_tersedia = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 15. BUKU DIPINJAM
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM books
    WHERE status = 'dipinjam'
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query buku dipinjam."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$buku_dipinjam = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 16. PEMINJAMAN AKTIF
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM loans
    WHERE status = 'dipinjam'
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query peminjaman aktif."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$peminjaman_aktif = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 17. PEMINJAMAN TERLAMBAT
// Rule: lewat 7 hari dari batas_kembali
// =====================================================

$sql = "
    SELECT COUNT(*) AS total
    FROM loans
    WHERE status = 'dipinjam'
    AND CURDATE() > DATE_ADD(batas_kembali, INTERVAL 7 DAY)
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query keterlambatan."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$peminjaman_terlambat = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =====================================================
// 18. DENDA BELUM BAYAR
// =====================================================

$sql = "
    SELECT
        COUNT(*) AS jumlah,
        COALESCE(SUM(nominal_denda), 0) AS total
    FROM fines
    WHERE status_pembayaran = 'belum_bayar'
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menyiapkan query denda belum lunas."
    ]);

    exit;
}

$stmt->execute();

$result = $stmt->get_result();
$unpaid_fine_data = $result->fetch_assoc();

$denda_belum_lunas = (int) $unpaid_fine_data["jumlah"];
$total_denda_belum_lunas = (float) $unpaid_fine_data["total"];

$stmt->close();


// =====================================================
// 19. RESPONSE
// =====================================================

echo json_encode([
    "status" => true,
    "message" => "Summary laporan berhasil diambil.",

    "periode" => [
        "tanggal_mulai" => $tanggal_mulai !== "" ? $tanggal_mulai : null,
        "tanggal_selesai" => $tanggal_selesai !== "" ? $tanggal_selesai : null
    ],

    "summary" => [
        "total_user" => $total_user,

        "total_buku" => $total_buku,
        "buku_tersedia" => $buku_tersedia,
        "buku_dipinjam" => $buku_dipinjam,

        "total_peminjaman" => $total_peminjaman,
        "peminjaman_aktif" => $peminjaman_aktif,
        "peminjaman_terlambat" => $peminjaman_terlambat,

        "total_pengembalian" => $total_pengembalian,

        "jumlah_denda" => $jumlah_denda,
        "total_denda" => $total_denda,

        "denda_belum_bayar" => $denda_belum_lunas,
        "total_denda_belum_bayar" => $total_denda_belum_lunas,

        "jumlah_pembayaran_disetujui" => $jumlah_pembayaran,
        "total_pembayaran_disetujui" => $total_pembayaran,

        "total_kehadiran" => $total_kehadiran
    ]
]);

?>