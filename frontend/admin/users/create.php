<?php
session_start();

if (
    !isset($_SESSION["id_admin"]) ||
    !is_numeric($_SESSION["id_admin"]) ||
    (int) $_SESSION["id_admin"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../../auth/login.php");
    exit;
}

$adminName = $_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Tambah User - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
            min-height: 100vh;
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .admin-info {
            color: #64748b;
            font-size: 14px;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: white;
            border-radius: 14px;
            padding: 28px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.06);
            width: 100%;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
            color: #0f172a;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .required {
            color: #dc2626;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: white;
            color: #1e293b;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        textarea {
            min-height: 110px;
            resize: vertical;
        }

        .help-text {
            color: #64748b;
            font-size: 12px;
            line-height: 1.5;
        }

        /* =========================
           PASSWORD
        ========================= */

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 75px;
        }

        .toggle-password {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #2563eb;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           PHOTO
        ========================= */

        .photo-section {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e2e8f0;
        }

        .photo-wrapper {
            display: flex;
            align-items: center;
            gap: 20px;
            margin-top: 12px;
        }

        .photo-preview {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e2e8f0;
            background: #f1f5f9;
            display: none;
        }

        .photo-placeholder {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 13px;
            text-align: center;
            border: 4px solid #cbd5e1;
        }

        .photo-info {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        .photo-info strong {
            color: #334155;
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            display: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* =========================
           BUTTON
        ========================= */

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            border: none;
            padding: 11px 18px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
        }

        .btn-save:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .main {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }
        }

        @media (max-width: 700px) {
            .topbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }

        @media (max-width: 500px) {
            .main {
                padding: 15px;
            }

            .card {
                padding: 18px;
            }

            .photo-wrapper {
                flex-direction: column;
                align-items: flex-start;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<!-- =========================
     MAIN
========================= -->

<main class="main">

    <div class="topbar">

        <div>

            <h1>Tambah User</h1>

            <div class="admin-info">
                Tambahkan anggota baru ke sistem perpustakaan
            </div>

        </div>

        <div class="admin-info">

            Admin:
            <strong>
                <?= htmlspecialchars($adminName) ?>
            </strong>

        </div>

    </div>


    <div
        id="alertBox"
        class="alert"
    ></div>


    <div class="card">

        <div class="section-title">
            Data Anggota
        </div>


        <form id="userForm">

            <!-- FOTO -->

            <div class="photo-section">

                <label>
                    Foto User
                </label>

                <div class="photo-wrapper">

                    <div>

                        <div
                            id="photoPlaceholder"
                            class="photo-placeholder"
                        >
                            Belum ada foto
                        </div>

                        <img
                            id="photoPreview"
                            class="photo-preview"
                            alt="Preview foto"
                        >

                    </div>


                    <div class="photo-info">

                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <br>

                        <strong>Ketentuan:</strong><br>

                        Maksimal 2 MB<br>
                        Format JPG, PNG, atau WEBP

                    </div>

                </div>

            </div>


            <!-- FORM GRID -->

            <div class="form-grid">

                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama">
                        Nama
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        maxlength="100"
                        required
                        placeholder="Masukkan nama lengkap"
                    >

                </div>


                <!-- NIK -->

                <div class="form-group">

                    <label for="nik">
                        NIK
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        maxlength="16"
                        inputmode="numeric"
                        required
                        placeholder="16 digit NIK"
                    >

                    <div class="help-text">
                        NIK harus terdiri dari 16 digit angka.
                    </div>

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                        <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        required
                        placeholder="contoh@email.com"
                    >

                </div>


                <!-- TELEPON -->

                <div class="form-group">

                    <label for="no_telp">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        id="no_telp"
                        name="no_telp"
                        maxlength="16"
                        inputmode="tel"
                        placeholder="Contoh: 081234567890"
                    >

                    <div class="help-text">
                        10–15 digit. Tanda + di awal diperbolehkan.
                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        Password
                        <span class="required">*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            maxlength="72"
                            required
                            placeholder="Minimal 8 karakter"
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            onclick="togglePassword()"
                        >
                            Lihat
                        </button>

                    </div>

                    <div class="help-text">
                        Password minimal 8 dan maksimal 72 karakter.
                    </div>

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label for="status">
                        Status
                        <span class="required">*</span>
                    </label>

                    <select
                        id="status"
                        name="status"
                    >

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="nonaktif">
                            Nonaktif
                        </option>

                        <option value="diblokir">
                            Diblokir
                        </option>

                    </select>

                </div>


                <!-- ALAMAT -->

                <div class="form-group full">

                    <label for="alamat">
                        Alamat
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        maxlength="255"
                        placeholder="Masukkan alamat anggota"
                    ></textarea>

                </div>

            </div>


            <!-- ACTION -->

            <div class="actions">

                <a
                    href="index.php"
                    class="btn btn-back"
                >
                    ← Kembali
                </a>

                <button
                    type="submit"
                    class="btn btn-save"
                    id="saveButton"
                >
                    💾 Simpan User
                </button>

            </div>

        </form>

    </div>

</main>


<script>

const API_BASE = "../../../api";

const CREATE_API =
    API_BASE + "/users/create.php";


const form =
    document.getElementById("userForm");

const alertBox =
    document.getElementById("alertBox");

const saveButton =
    document.getElementById("saveButton");

const fotoInput =
    document.getElementById("foto");

const photoPreview =
    document.getElementById("photoPreview");

const photoPlaceholder =
    document.getElementById("photoPlaceholder");


/* =========================
   FOTO PREVIEW
========================= */

fotoInput.addEventListener(
    "change",
    function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        if (
            file.size >
            2 * 1024 * 1024
        ) {

            showError(
                "Ukuran foto maksimal 2 MB."
            );

            this.value = "";

            return;
        }


        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];


        if (
            !allowedTypes.includes(
                file.type
            )
        ) {

            showError(
                "Format foto hanya boleh JPG, PNG, atau WEBP."
            );

            this.value = "";

            return;
        }


        const reader =
            new FileReader();


        reader.onload = function (event) {

            photoPreview.src =
                event.target.result;

            photoPreview.style.display =
                "block";

            photoPlaceholder.style.display =
                "none";

        };


        reader.readAsDataURL(file);

    }
);


/* =========================
   SUBMIT
========================= */

form.addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();

        hideError();


        const nama =
            document
                .getElementById("nama")
                .value
                .trim();


        const nik =
            document
                .getElementById("nik")
                .value
                .trim();


        const email =
            document
                .getElementById("email")
                .value
                .trim()
                .toLowerCase();


        const noTelp =
            document
                .getElementById("no_telp")
                .value
                .trim();


        const password =
            document
                .getElementById("password")
                .value;


        const alamat =
            document
                .getElementById("alamat")
                .value
                .trim();


        const status =
            document
                .getElementById("status")
                .value;


        /* =========================
           VALIDASI NAMA
        ========================= */

        if (nama.length < 3) {

            showError(
                "Nama minimal 3 karakter."
            );

            return;
        }


        if (nama.length > 100) {

            showError(
                "Nama maksimal 100 karakter."
            );

            return;
        }


        /* =========================
           VALIDASI NIK
        ========================= */

        if (
            !/^[0-9]{16}$/.test(nik)
        ) {

            showError(
                "NIK harus terdiri dari 16 digit angka."
            );

            return;
        }


        /* =========================
           VALIDASI TELEPON
        ========================= */

        if (
            noTelp !== "" &&
            !/^\+?[0-9]{10,15}$/.test(noTelp)
        ) {

            showError(
                "Nomor telepon harus 10-15 digit dan hanya boleh berisi angka."
            );

            return;
        }


        /* =========================
           VALIDASI EMAIL
        ========================= */

        if (!email) {

            showError(
                "Email wajib diisi."
            );

            return;
        }


        if (email.length > 100) {

            showError(
                "Email maksimal 100 karakter."
            );

            return;
        }


        /* =========================
           VALIDASI PASSWORD
        ========================= */

        if (password.length < 8) {

            showError(
                "Password minimal 8 karakter."
            );

            return;
        }


        if (password.length > 72) {

            showError(
                "Password maksimal 72 karakter."
            );

            return;
        }


        /* =========================
           VALIDASI ALAMAT
        ========================= */

        if (alamat.length > 255) {

            showError(
                "Alamat maksimal 255 karakter."
            );

            return;
        }


        /* =========================
           VALIDASI STATUS
        ========================= */

        const allowedStatus = [
            "aktif",
            "nonaktif",
            "diblokir"
        ];


        if (
            !allowedStatus.includes(status)
        ) {

            showError(
                "Status user tidak valid."
            );

            return;
        }


        /* =========================
           FORM DATA
        ========================= */

        const formData =
            new FormData();


        formData.append(
            "nama",
            nama
        );

        formData.append(
            "nik",
            nik
        );

        formData.append(
            "alamat",
            alamat
        );

        formData.append(
            "no_telp",
            noTelp
        );

        formData.append(
            "email",
            email
        );

        formData.append(
            "password",
            password
        );

        formData.append(
            "status",
            status
        );


        if (
            fotoInput.files.length > 0
        ) {

            formData.append(
                "foto",
                fotoInput.files[0]
            );

        }


        /* =========================
           SUBMIT API
        ========================= */

        saveButton.disabled = true;

        saveButton.textContent =
            "⏳ Menyimpan...";


        try {

            const response =
                await fetch(
                    CREATE_API,
                    {
                        method: "POST",
                        credentials: "include",
                        body: formData
                    }
                );


            if (
                response.status === 401
            ) {

                window.location.href =
                    "../../auth/login.php";

                return;
            }


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal menambahkan user."
                );
            }


            /* =========================
               BERHASIL
            ========================= */

            const data =
                result.data || {};


            const memberCode =
                data.member_code || "-";


            alert(
                (result.message ||
                    "Anggota berhasil ditambahkan.") +
                "\n\nMember Code: " +
                memberCode
            );


            if (data.id_user) {

                window.location.href =
                    "detail.php?id=" +
                    encodeURIComponent(
                        data.id_user
                    );

            } else {

                window.location.href =
                    "index.php";

            }


        } catch (error) {

            console.error(error);

            showError(
                error.message ||
                "Terjadi kesalahan saat menambahkan user."
            );

        } finally {

            saveButton.disabled = false;

            saveButton.textContent =
                "💾 Simpan User";

        }

    }
);


/* =========================
   TOGGLE PASSWORD
========================= */

function togglePassword() {

    const input =
        document.getElementById("password");

    const button =
        document.querySelector(
            ".toggle-password"
        );


    if (
        input.type === "password"
    ) {

        input.type = "text";

        button.textContent =
            "Sembunyikan";

    } else {

        input.type = "password";

        button.textContent =
            "Lihat";

    }

}


/* =========================
   ERROR
========================= */

function showError(message) {

    alertBox.textContent =
        "❌ " + message;

    alertBox.className =
        "alert error";

    alertBox.style.display =
        "block";


    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

}


function hideError() {

    alertBox.style.display =
        "none";

}

</script>

</body>
</html>