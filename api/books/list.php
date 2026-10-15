<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ===============================
// CEK METHOD
// ===============================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Method tidak diizinkan. Gunakan GET."
    ]);

    exit;
}

// ===============================
// PARAMETER
// ===============================
$search = trim($_GET["search"] ?? "");
$id_category = trim($_GET["id_category"] ?? "");
$id_shelf = trim($_GET["id_shelf"] ?? "");
$jenis_buku = trim($_GET["jenis_buku"] ?? "");
$tahun_terbit = trim($_GET["tahun_terbit"] ?? "");

$pageInput = $_GET["page"] ?? "1";
$limitInput = $_GET["limit"] ?? "10";

// ===============================
// VALIDASI PAGE
// ===============================
if (
    !ctype_digit((string) $pageInput) ||
    (int) $pageInput < 1
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter page tidak valid."
    ]);

    exit;
}

$page = (int) $pageInput;

// ===============================
// VALIDASI LIMIT
// ===============================
if (
    !ctype_digit((string) $limitInput) ||
    (int) $limitInput < 1 ||
    (int) $limitInput > 100
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter limit tidak valid. Gunakan angka 1 sampai 100."
    ]);

    exit;
}

$limit = (int) $limitInput;

$offset = ($page - 1) * $limit;

// ===============================
// VALIDASI ID CATEGORY
// ===============================
if (
    $id_category !== "" &&
    (
        !ctype_digit($id_category) ||
        (int) $id_category <= 0
    )
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID kategori tidak valid."
    ]);

    exit;
}

if ($id_category !== "") {
    $id_category = (int) $id_category;
}

// ===============================
// VALIDASI ID SHELF
// ===============================
if (
    $id_shelf !== "" &&
    (
        !ctype_digit($id_shelf) ||
        (int) $id_shelf <= 0
    )
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID rak tidak valid."
    ]);

    exit;
}

if ($id_shelf !== "") {
    $id_shelf = (int) $id_shelf;
}

// ===============================
// VALIDASI JENIS BUKU
// ===============================
if ($jenis_buku !== "" && strlen($jenis_buku) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Jenis buku maksimal 100 karakter."
    ]);

    exit;
}

// ===============================
// VALIDASI TAHUN TERBIT
// ===============================
if ($tahun_terbit !== "") {

    $currentYear = (int) date("Y");

    if (
        !ctype_digit($tahun_terbit) ||
        strlen($tahun_terbit) !== 4 ||
        (int) $tahun_terbit < 1000 ||
        (int) $tahun_terbit > $currentYear
    ) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Tahun terbit tidak valid."
        ]);

        exit;
    }

    $tahun_terbit = (int) $tahun_terbit;
}

// ===============================
// VALIDASI SEARCH
// ===============================
if (strlen($search) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Parameter search maksimal 255 karakter."
    ]);

    exit;
}

// ===============================
// QUERY DASAR
// ===============================
$where = [];
$params = [];
$types = "";

$sql = "
    SELECT
        b.id_book,
        b.isbn,
        b.judul,
        b.pengarang,
        b.penerbit,
        b.tahun_terbit,
        b.jenis_buku,
        b.harga,
        b.foto,
        b.barcode,
        b.status,

        c.id_category,
        c.nama_category,

        s.id_shelf,
        s.no_rak,
        s.lantai

    FROM books b

    LEFT JOIN categories c
        ON b.id_category = c.id_category

    LEFT JOIN shelves s
        ON b.id_shelf = s.id_shelf
";

// ===============================
// SEARCH JUDUL
// ===============================
if ($search !== "") {

    $where[] = "b.judul LIKE ?";

    $params[] = "%" . $search . "%";

    $types .= "s";
}

// ===============================
// FILTER KATEGORI
// ===============================
if ($id_category !== "") {

    $where[] = "b.id_category = ?";

    $params[] = $id_category;

    $types .= "i";
}

// ===============================
// FILTER RAK
// ===============================
if ($id_shelf !== "") {

    $where[] = "b.id_shelf = ?";

    $params[] = $id_shelf;

    $types .= "i";
}

// ===============================
// FILTER JENIS BUKU
// ===============================
if ($jenis_buku !== "") {

    $where[] = "b.jenis_buku = ?";

    $params[] = $jenis_buku;

    $types .= "s";
}

// ===============================
// FILTER TAHUN TERBIT
// ===============================
if ($tahun_terbit !== "") {

    $where[] = "b.tahun_terbit = ?";

    $params[] = $tahun_terbit;

    $types .= "i";
}

// ===============================
// WHERE
// ===============================
if (count($where) > 0) {

    $sql .= " WHERE " . implode(" AND ", $where);
}

// ===============================
// HITUNG TOTAL DATA
// ===============================
$countSql = "
    SELECT COUNT(*) AS total
    FROM books b
";

if (count($where) > 0) {

    $countSql .= " WHERE " . implode(" AND ", $where);
}

$countStmt = $conn->prepare($countSql);

if (!$countStmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}

if (!$countStmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menghitung data buku."
    ]);

    $countStmt->close();
    $conn->close();
    exit;
}

$countResult = $countStmt->get_result();

$countRow = $countResult->fetch_assoc();

$totalData = (int) ($countRow["total"] ?? 0);

$countStmt->close();

// ===============================
// DATA BUKU
// ===============================
$sql .= " ORDER BY b.id_book DESC LIMIT ? OFFSET ?";

$dataParams = $params;

$dataTypes = $types . "ii";

$dataParams[] = $limit;
$dataParams[] = $offset;

$stmt = $conn->prepare($sql);

if (!$stmt) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

$stmt->bind_param($dataTypes, ...$dataParams);

if (!$stmt->execute()) {

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengambil data buku."
    ]);

    $stmt->close();
    $conn->close();
    exit;
}

$result = $stmt->get_result();

$books = [];

while ($row = $result->fetch_assoc()) {

    $books[] = [
        "id_book" => (int) $row["id_book"],

        "isbn" => $row["isbn"],

        "judul" => $row["judul"],

        "pengarang" => $row["pengarang"],

        "penerbit" => $row["penerbit"],

        "tahun_terbit" => $row["tahun_terbit"],

        "jenis_buku" => $row["jenis_buku"],

        "kategori" => [
            "id_category" => $row["id_category"] !== null
                ? (int) $row["id_category"]
                : null,

            "nama_category" => $row["nama_category"]
        ],

        "rak" => [
            "id_shelf" => $row["id_shelf"] !== null
                ? (int) $row["id_shelf"]
                : null,

            "no_rak" => $row["no_rak"],

            "lantai" => $row["lantai"]
        ],

        "harga" => (float) $row["harga"],

        "foto" => $row["foto"],

        "barcode" => $row["barcode"],

        "status" => $row["status"]
    ];
}

$stmt->close();

// ===============================
// PAGINATION
// ===============================
$totalPage = $totalData > 0
    ? (int) ceil($totalData / $limit)
    : 0;

// ===============================
// RESPONSE
// ===============================
echo json_encode([
    "status" => true,

    "message" => "Data buku berhasil diambil.",

    "data" => $books,

    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total_data" => $totalData,
        "total_page" => $totalPage
    ]

], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>