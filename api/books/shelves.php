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
// QUERY DATA RAK
// ==========================================
$sql = "
    SELECT
        id_shelf,
        no_rak,
        lantai,
        keterangan
    FROM shelves
    ORDER BY lantai ASC, no_rak ASC
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
$shelves = [];

while ($row = $result->fetch_assoc()) {
    $shelves[] = [
        "id_shelf" => (int) $row["id_shelf"],
        "no_rak" => $row["no_rak"],
        "lantai" => (int) $row["lantai"],
        "keterangan" => $row["keterangan"]
    ];
}

// ==========================================
// RESPONSE
// ==========================================
http_response_code(200);

echo json_encode([
    "status" => true,
    "message" => "Data rak berhasil diambil.",
    "data" => $shelves
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

$conn->close();

?>