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
// PAGINATION
// =========================

$page = max(1, (int) ($_GET["page"] ?? 1));
$limit = min(50, max(1, (int) ($_GET["limit"] ?? 10)));

$offset = ($page - 1) * $limit;

// =========================
// FILTER STATUS
// =========================

$status = trim($_GET["status"] ?? "");

$allowedStatus = [
    "belum_bayar",
    "lunas"
];

if ($status !== "" && !in_array($status, $allowedStatus, true)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Status tidak valid."
    ]);

    exit;
}

// =========================
// QUERY DATA
// =========================

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

        b.id_book,
        b.isbn,
        b.barcode,
        b.judul,
        b.pengarang,
        b.harga

    FROM fines f

    INNER JOIN loans l
        ON f.id_loan = l.id_loan

    INNER JOIN books b
        ON l.id_book = b.id_book

    WHERE l.id_user = ?
";

$params = [$id_user];
$types = "i";

if ($status !== "") {
    $sql .= " AND f.status_pembayaran = ?";
    $params[] = $status;
    $types .= "s";
}

$sql .= "
    ORDER BY f.id_fine DESC
    LIMIT ? OFFSET ?
";

$params[] = $limit;
$params[] = $offset;
$types .= "ii";

// =========================
// EXECUTE
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

$stmt->bind_param($types, ...$params);

$stmt->execute();

$result = $stmt->get_result();

// =========================
// DATA
// =========================

$data = [];

while ($row = $result->fetch_assoc()) {

    // =========================
    // PAYMENT TERAKHIR
    // =========================

    $payment = null;

    $paymentStmt = $conn->prepare("
        SELECT
            id_payment,
            tanggal_bayar,
            nominal,
            metode,
            status,
            bukti_pembayaran
        FROM payments
        WHERE id_fine = ?
        ORDER BY id_payment DESC
        LIMIT 1
    ");

    $paymentStmt->bind_param(
        "i",
        $row["id_fine"]
    );

    $paymentStmt->execute();

    $paymentResult = $paymentStmt->get_result();

    if ($paymentResult->num_rows > 0) {
        $payment = $paymentResult->fetch_assoc();

        $payment["id_payment"] = (int) $payment["id_payment"];
        $payment["nominal"] = (float) $payment["nominal"];
    }

    $paymentStmt->close();

    $data[] = [
        "id_fine" => (int) $row["id_fine"],
        "id_loan" => (int) $row["id_loan"],

        "denda" => [
            "nominal" => (float) $row["nominal_denda"],
            "status_pembayaran" => $row["status_pembayaran"],
            "created_at" => $row["created_at"]
        ],

        "buku" => [
            "id_book" => (int) $row["id_book"],
            "isbn" => $row["isbn"],
            "barcode" => $row["barcode"],
            "judul" => $row["judul"],
            "pengarang" => $row["pengarang"],
            "harga" => (float) $row["harga"]
        ],

        "peminjaman" => [
            "tanggal_pinjam" => $row["tanggal_pinjam"],
            "batas_kembali" => $row["batas_kembali"],
            "tanggal_kembali" => $row["tanggal_kembali"],
            "status" => $row["status_peminjaman"]
        ],

        "payment_terakhir" => $payment
    ];
}

$stmt->close();

// =========================
// TOTAL DATA
// =========================

$countSql = "
    SELECT COUNT(*) AS total
    FROM fines f
    INNER JOIN loans l
        ON f.id_loan = l.id_loan
    WHERE l.id_user = ?
";

$countParams = [$id_user];
$countTypes = "i";

if ($status !== "") {
    $countSql .= " AND f.status_pembayaran = ?";
    $countParams[] = $status;
    $countTypes .= "s";
}

$countStmt = $conn->prepare($countSql);

$countStmt->bind_param(
    $countTypes,
    ...$countParams
);

$countStmt->execute();

$countResult = $countStmt->get_result();
$countData = $countResult->fetch_assoc();

$total = (int) $countData["total"];

$countStmt->close();

$conn->close();

// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Daftar denda berhasil diambil.",
    "data" => $data,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total" => $total,
        "total_page" => $limit > 0
            ? (int) ceil($total / $limit)
            : 0
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);