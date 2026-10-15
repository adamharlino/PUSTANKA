<?php

mysqli_report(MYSQLI_REPORT_OFF);

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../../config/database.php";

try {
    // 1. Cek Autentikasi Admin
    if (
        !isset($_SESSION["id_admin"]) ||
        !is_numeric($_SESSION["id_admin"]) ||
        (int) $_SESSION["id_admin"] <= 0 ||
        ($_SESSION["role"] ?? "") !== "admin"
    ) {
        http_response_code(401);
        echo json_encode(["status" => false, "message" => "Akses ditolak. Silakan login sebagai admin."]);
        exit;
    }

    $idAdmin = (int) $_SESSION["id_admin"];

    // 2. Cek Method Request
    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        http_response_code(405);
        echo json_encode(["status" => false, "message" => "Gunakan metode POST."]);
        exit;
    }

    // 3. Ambil Input Data
    $input = json_decode(file_get_contents("php://input"), true);
    if (!is_array($input)) {
        $input = $_POST;
    }

    $nama   = trim($input["nama"] ?? "");
    $email  = strtolower(trim($input["email"] ?? ""));
    $noHp   = trim($input["no_hp"] ?? ($input["no_telp"] ?? ""));
    $alamat = trim($input["alamat"] ?? "");

    // 4. Validasi Input Wajib
    if ($nama === "" || $email === "") {
        http_response_code(400);
        echo json_encode(["status" => false, "message" => "Nama dan email tidak boleh kosong."]);
        exit;
    }

    // 5. Cek Duplikasi Email dengan Admin Lain
    $stmtCek = $conn->prepare("SELECT id_admin FROM admins WHERE email = ? AND id_admin != ? LIMIT 1");
    if ($stmtCek) {
        $stmtCek->bind_param("si", $email, $idAdmin);
        $stmtCek->execute();
        $resCek = $stmtCek->get_result();
        if ($resCek && $resCek->num_rows > 0) {
            $stmtCek->close();
            http_response_code(409);
            echo json_encode(["status" => false, "message" => "Email sudah digunakan oleh admin lain."]);
            exit;
        }
        $stmtCek->close();
    }

    // 6. Update Data Admin di Database
    $stmt = $conn->prepare("UPDATE admins SET nama = ?, email = ?, no_telp = ?, alamat = ? WHERE id_admin = ?");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(["status" => false, "message" => "Gagal Prepare Query: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("ssssi", $nama, $email, $noHp, $alamat, $idAdmin);

    if ($stmt->execute()) {
        // Update data nama & email di Session
        $_SESSION["nama"]  = $nama;
        $_SESSION["email"] = $email;

        echo json_encode([
            "status" => true,
            "message" => "Profil berhasil diperbarui!",
            "data" => [
                "nama" => $nama,
                "email" => $email,
                "no_telp" => $noHp,
                "alamat" => $alamat
            ]
        ]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => false, "message" => "Gagal memperbarui profil: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => false,
        "message" => "Server Error: " . $e->getMessage()
    ]);
}

?>