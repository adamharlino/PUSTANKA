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
| ADMIN AUTH
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
| METHOD
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

/*
|--------------------------------------------------------------------------
| INPUT
|--------------------------------------------------------------------------
*/
$id_payment = $_POST["id_payment"] ?? null;
$aksi = strtolower(trim($_POST["aksi"] ?? ""));

if (
    !$id_payment ||
    !is_numeric($id_payment) ||
    (int) $id_payment <= 0
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "ID payment tidak valid."
    ]);

    exit;
}

$id_payment = (int) $id_payment;

$allowedAksi = ["setujui", "tolak"];

if (!in_array($aksi, $allowedAksi, true)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Aksi harus setujui atau tolak."
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| GET PAYMENT
|--------------------------------------------------------------------------
*/
$sql = "
    SELECT
        p.id_payment,
        p.id_fine,
        p.nominal,
        p.metode,
        p.status AS status_payment,
        f.nominal_denda,
        f.status_pembayaran
    FROM payments p
    INNER JOIN fines f
        ON p.id_fine = f.id_fine
    WHERE p.id_payment = ?
    LIMIT 1
";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Query payment gagal dipersiapkan."
    ]);

    exit;
}

$stmt->bind_param("i", $id_payment);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();

    http_response_code(404);

    echo json_encode([
        "status" => false,
        "message" => "Payment tidak ditemukan."
    ]);

    exit;
}

$payment = $result->fetch_assoc();
$stmt->close();

/*
|--------------------------------------------------------------------------
| ONLY PENDING CAN BE VERIFIED
|--------------------------------------------------------------------------
*/
if ($payment["status_payment"] !== "pending") {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Payment ini sudah diverifikasi sebelumnya.",
        "data" => [
            "id_payment" => (int) $payment["id_payment"],
            "status" => $payment["status_payment"]
        ]
    ]);

    exit;
}

/*
|--------------------------------------------------------------------------
| APPROVE / REJECT
|--------------------------------------------------------------------------
*/
$conn->begin_transaction();

try {

    if ($aksi === "setujui") {

        /*
        | Payment → disetujui
        */
        $sqlPayment = "
            UPDATE payments
            SET status = 'disetujui'
            WHERE id_payment = ?
              AND status = 'pending'
        ";

        $stmtPayment = $conn->prepare($sqlPayment);

        if (!$stmtPayment) {
            throw new Exception(
                "Query persetujuan payment gagal dipersiapkan."
            );
        }

        $stmtPayment->bind_param("i", $id_payment);

        if (!$stmtPayment->execute()) {
            throw new Exception(
                "Payment gagal disetujui."
            );
        }

        if ($stmtPayment->affected_rows !== 1) {
            throw new Exception(
                "Payment sudah tidak berstatus pending."
            );
        }

        $stmtPayment->close();

        /*
        | Fine → lunas
        */
        $sqlFine = "
            UPDATE fines
            SET status_pembayaran = 'lunas'
            WHERE id_fine = ?
              AND status_pembayaran = 'belum_bayar'
        ";

        $stmtFine = $conn->prepare($sqlFine);

        if (!$stmtFine) {
            throw new Exception(
                "Query update denda gagal dipersiapkan."
            );
        }

        $stmtFine->bind_param(
            "i",
            $payment["id_fine"]
        );

        if (!$stmtFine->execute()) {
            throw new Exception(
                "Status denda gagal diperbarui."
            );
        }

        if ($stmtFine->affected_rows !== 1) {
            throw new Exception(
                "Denda sudah lunas atau status denda tidak sesuai."
            );
        }

        $stmtFine->close();

        $conn->commit();

        echo json_encode([
            "status" => true,
            "message" => "Pembayaran berhasil disetujui dan denda telah lunas.",
            "data" => [
                "id_payment" => (int) $id_payment,
                "id_fine" => (int) $payment["id_fine"],
                "status_payment" => "disetujui",
                "status_denda" => "lunas"
            ]
        ]);

        exit;
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */

    $sqlReject = "
        UPDATE payments
        SET status = 'ditolak'
        WHERE id_payment = ?
          AND status = 'pending'
    ";

    $stmtReject = $conn->prepare($sqlReject);

    if (!$stmtReject) {
        throw new Exception(
            "Query penolakan payment gagal dipersiapkan."
        );
    }

    $stmtReject->bind_param("i", $id_payment);

    if (!$stmtReject->execute()) {
        throw new Exception(
            "Payment gagal ditolak."
        );
    }

    if ($stmtReject->affected_rows !== 1) {
        throw new Exception(
            "Payment sudah tidak berstatus pending."
        );
    }

    $stmtReject->close();

    /*
    | Fine tetap belum_bayar
    */

    $conn->commit();

    echo json_encode([
        "status" => true,
        "message" => "Pembayaran berhasil ditolak. Denda tetap belum dibayar.",
        "data" => [
            "id_payment" => (int) $id_payment,
            "id_fine" => (int) $payment["id_fine"],
            "status_payment" => "ditolak",
            "status_denda" => "belum_bayar"
        ]
    ]);

} catch (Exception $e) {

    $conn->rollback();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => $e->getMessage()
    ]);
}

?>