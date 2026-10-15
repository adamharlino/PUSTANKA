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
// PAGINATION
// =========================

$page = max(1, (int) ($_GET["page"] ?? 1));

$limit = max(
    1,
    min(100, (int) ($_GET["limit"] ?? 10))
);

$offset = ($page - 1) * $limit;


// =========================
// FILTER STATUS
// =========================

$status = trim($_GET["status"] ?? "");

$allowedStatus = [
    "dipinjam",
    "dikembalikan",
    "terlambat"
];

$whereStatus = "";

if ($status !== "") {

    if (!in_array($status, $allowedStatus, true)) {

        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Status peminjaman tidak valid."
        ]);

        exit;
    }

    $whereStatus = "AND l.status = ?";
}


// =========================
// TOTAL DATA
// =========================

if ($status !== "") {

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM loans l
        WHERE l.id_user = ?
        AND l.status = ?
    ");

    $stmt->bind_param(
        "is",
        $id_user,
        $status
    );

} else {

    $stmt = $conn->prepare("
        SELECT COUNT(*) AS total
        FROM loans l
        WHERE l.id_user = ?
    ");

    $stmt->bind_param(
        "i",
        $id_user
    );
}

$stmt->execute();

$result = $stmt->get_result();

$total = (int) $result->fetch_assoc()["total"];

$stmt->close();


// =========================
// AMBIL DATA PEMINJAMAN
// =========================

$sql = "
    SELECT
        l.id_loan,
        l.id_book,
        b.judul,
        b.pengarang,
        b.harga,
        b.foto,
        l.tanggal_pinjam,
        l.batas_kembali,
        l.tanggal_kembali,
        l.status
    FROM loans l
    INNER JOIN books b
        ON l.id_book = b.id_book
    WHERE l.id_user = ?
    $whereStatus
    ORDER BY l.id_loan DESC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($sql);

if ($status !== "") {

    $stmt->bind_param(
        "isii",
        $id_user,
        $status,
        $limit,
        $offset
    );

} else {

    $stmt->bind_param(
        "iii",
        $id_user,
        $limit,
        $offset
    );
}

$stmt->execute();

$result = $stmt->get_result();

$loans = [];

while ($row = $result->fetch_assoc()) {

    $loans[] = [
        "id_loan" => (int) $row["id_loan"],
        "id_book" => (int) $row["id_book"],
        "judul" => $row["judul"],
        "pengarang" => $row["pengarang"],
        "harga_buku" => (float) $row["harga"],
        "foto" => $row["foto"],
        "tanggal_pinjam" => $row["tanggal_pinjam"],
        "batas_kembali" => $row["batas_kembali"],
        "tanggal_kembali" => $row["tanggal_kembali"],
        "status" => $row["status"]
    ];
}

$stmt->close();


// =========================
// PAGINATION
// =========================

$totalPages = $limit > 0
    ? ceil($total / $limit)
    : 0;


// =========================
// RESPONSE
// =========================

echo json_encode([
    "status" => true,
    "message" => "Riwayat peminjaman berhasil diambil.",
    "data" => $loans,
    "pagination" => [
        "page" => $page,
        "limit" => $limit,
        "total_data" => $total,
        "total_pages" => $totalPages
    ]
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>