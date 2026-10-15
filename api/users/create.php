<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // localhost HTTP
    "samesite" => "Lax"
]);

session_start();

header("Content-Type: application/json");

require_once __DIR__ . "/../../config/database.php";


// ==========================================
// CEK ADMIN
// ==========================================

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


// ==========================================
// CEK METHOD
// ==========================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);

    echo json_encode([
        "status" => false,
        "message" => "Gunakan metode POST."
    ]);

    exit;
}


// ==========================================
// AMBIL DATA FORM
// ==========================================

$nama = trim($_POST["nama"] ?? "");
$nik = trim($_POST["nik"] ?? "");
$alamat = trim($_POST["alamat"] ?? "");
$no_telp = trim($_POST["no_telp"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$password = $_POST["password"] ?? "";
$status = trim($_POST["status"] ?? "aktif");


// ==========================================
// VALIDASI DATA WAJIB
// ==========================================

if (
    $nama === "" ||
    $nik === "" ||
    $email === "" ||
    $password === ""
) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama, NIK, email, dan password wajib diisi."
    ]);

    exit;
}


// ==========================================
// VALIDASI NAMA
// ==========================================

if (strlen($nama) < 3) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama minimal 3 karakter."
    ]);

    exit;
}

if (strlen($nama) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Nama maksimal 100 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI NIK
// ==========================================

if (!preg_match('/^[0-9]{16}$/', $nik)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "NIK harus terdiri dari 16 digit angka."
    ]);

    exit;
}


// ==========================================
// VALIDASI ALAMAT
// ==========================================

if (strlen($alamat) > 255) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Alamat maksimal 255 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI NOMOR TELEPON
// ==========================================

if ($no_telp !== "") {

    if (!preg_match('/^\+?[0-9]{10,15}$/', $no_telp)) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Nomor telepon harus 10-15 digit dan hanya boleh berisi angka."
        ]);

        exit;
    }
}


// ==========================================
// VALIDASI EMAIL
// ==========================================

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Format email tidak valid."
    ]);

    exit;
}

if (strlen($email) > 100) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Email maksimal 100 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI PASSWORD
// ==========================================

if (strlen($password) < 8) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password minimal 8 karakter."
    ]);

    exit;
}

if (strlen($password) > 72) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Password maksimal 72 karakter."
    ]);

    exit;
}


// ==========================================
// VALIDASI STATUS
// ==========================================

$allowedStatus = [
    "aktif",
    "nonaktif",
    "diblokir"
];

if (!in_array($status, $allowedStatus, true)) {
    http_response_code(400);

    echo json_encode([
        "status" => false,
        "message" => "Status user tidak valid."
    ]);

    exit;
}


// ==========================================
// VALIDASI FOTO
// ==========================================

$fotoPath = "";

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {

    // Cek error upload
    if ($_FILES["foto"]["error"] !== UPLOAD_ERR_OK) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Foto gagal diupload."
        ]);

        exit;
    }

    // Maksimal 2 MB
    $maxSize = 2 * 1024 * 1024;

    if ($_FILES["foto"]["size"] > $maxSize) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Ukuran foto maksimal 2 MB."
        ]);

        exit;
    }

    // Cek MIME type berdasarkan isi file
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($_FILES["foto"]["tmp_name"]);

    $allowedMime = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowedMime[$mimeType])) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "Format foto hanya boleh JPG, PNG, atau WEBP."
        ]);

        exit;
    }

    // Pastikan benar-benar gambar
    if (@getimagesize($_FILES["foto"]["tmp_name"]) === false) {
        http_response_code(400);

        echo json_encode([
            "status" => false,
            "message" => "File yang diupload bukan gambar yang valid."
        ]);

        exit;
    }

    // Folder upload
    $uploadDir = __DIR__ . "/../../uploads/users/";

    if (!is_dir($uploadDir)) {
        if (!mkdir($uploadDir, 0755, true)) {
            http_response_code(500);

            echo json_encode([
                "status" => false,
                "message" => "Folder upload tidak dapat dibuat."
            ]);

            exit;
        }
    }

    // Nama file random
    $randomName = bin2hex(random_bytes(16));
    $extension = $allowedMime[$mimeType];

    $fileName = "user_" . $randomName . "." . $extension;

    $targetPath = $uploadDir . $fileName;

    // Pindahkan file
    if (!move_uploaded_file($_FILES["foto"]["tmp_name"], $targetPath)) {
        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan foto."
        ]);

        exit;
    }

    // Path yang disimpan ke database
    $fotoPath = "uploads/users/" . $fileName;
}


// ==========================================
// CEK NIK
// ==========================================

$stmt = $conn->prepare("
    SELECT id_user
    FROM users
    WHERE nik = ?
    LIMIT 1
");

if (!$stmt) {
    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param("s", $nik);

if (!$stmt->execute()) {

    $stmt->close();

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    // Hapus foto jika sebelumnya sudah terupload
    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "NIK sudah terdaftar."
    ]);

    exit;
}

$stmt->close();


// ==========================================
// CEK EMAIL
// ==========================================

$stmt = $conn->prepare("
    SELECT id_user
    FROM users
    WHERE email = ?
    LIMIT 1
");

if (!$stmt) {

    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param("s", $email);

if (!$stmt->execute()) {

    $stmt->close();

    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $stmt->close();

    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(409);

    echo json_encode([
        "status" => false,
        "message" => "Email sudah terdaftar."
    ]);

    exit;
}

$stmt->close();


// ==========================================
// HASH PASSWORD
// ==========================================

$hashedPassword = password_hash(
    $password,
    PASSWORD_DEFAULT
);

if ($hashedPassword === false) {

    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal mengamankan password."
    ]);

    exit;
}


// ==========================================
// INSERT USER
// ==========================================

$stmt = $conn->prepare("
    INSERT INTO users
    (
        nama,
        nik,
        alamat,
        no_telp,
        email,
        password,
        foto,
        status,
        member_code
    )
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NULL)
");

if (!$stmt) {

    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Terjadi kesalahan pada server."
    ]);

    exit;
}

$stmt->bind_param(
    "ssssssss",
    $nama,
    $nik,
    $alamat,
    $no_telp,
    $email,
    $hashedPassword,
    $fotoPath,
    $status
);


// ==========================================
// EXECUTE
// ==========================================

if ($stmt->execute()) {

    $newId = (int) $conn->insert_id;

    // ==========================================
    // GENERATE MEMBER CODE
    // ==========================================

    $memberCode = "PSTK-" . str_pad(
        $newId,
        6,
        "0",
        STR_PAD_LEFT
    );

    $updateMember = $conn->prepare("
        UPDATE users
        SET member_code = ?
        WHERE id_user = ?
    ");

    if (!$updateMember) {

        if ($fotoPath !== "") {
            $uploadedFile = __DIR__ . "/../../" . $fotoPath;

            if (is_file($uploadedFile)) {
                unlink($uploadedFile);
            }
        }

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal membuat kode anggota."
        ]);

        $stmt->close();
        $conn->close();
        exit;
    }

    $updateMember->bind_param(
        "si",
        $memberCode,
        $newId
    );

    if (!$updateMember->execute()) {

        $updateMember->close();

        if ($fotoPath !== "") {
            $uploadedFile = __DIR__ . "/../../" . $fotoPath;

            if (is_file($uploadedFile)) {
                unlink($uploadedFile);
            }
        }

        http_response_code(500);

        echo json_encode([
            "status" => false,
            "message" => "Gagal menyimpan kode anggota."
        ]);

        $stmt->close();
        $conn->close();
        exit;
    }

    $updateMember->close();

    http_response_code(201);

    echo json_encode([
        "status" => true,
        "message" => "Anggota berhasil ditambahkan.",
        "data" => [
            "id_user" => $newId,
            "member_code" => $memberCode,
            "nama" => $nama,
            "nik" => $nik,
            "email" => $email,
            "foto" => $fotoPath !== "" ? $fotoPath : null,
            "status" => $status
        ]
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} else {

    // Jika insert gagal, hapus file yang sudah diupload
    if ($fotoPath !== "") {
        $uploadedFile = __DIR__ . "/../../" . $fotoPath;

        if (is_file($uploadedFile)) {
            unlink($uploadedFile);
        }
    }

    http_response_code(500);

    echo json_encode([
        "status" => false,
        "message" => "Gagal menambahkan anggota."
    ]);
}


$stmt->close();
$conn->close();

?>