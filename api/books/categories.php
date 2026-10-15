<?php

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";

// ==========================================
// CEK METHOD
// ==========================================
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode GET."
    ]);

    exit;
}

// ==========================================
// QUERY DATA KATEGORI
// ==========================================
$sql = "
    SELECT
        id_category,
        nama_category,
        deskripsi
    FROM categories
    ORDER BY nama_category ASC
";

$result = $conn->query($sql);

// ==========================================
// CEK QUERY
// ==========================================
if (!$result) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    $conn->close();
    exit;
}

// ==========================================
// AMBIL DATA
// ==========================================
$categories = [];

while ($row = $result->fetch_assoc()) {
    $categories[] = [
        "id_category" => (int) $row["id_category"],
        "nama_category" => $row["nama_category"],
        "deskripsi" => $row["deskripsi"]
    ];
}

// ==========================================
// RESPONSE
// ==========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Data kategori berhasil diambil.",
    "data" => $categories
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>