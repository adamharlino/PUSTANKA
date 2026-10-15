<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit Profil - Pustanka</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1f2937;
        }

        /* =========================
           NAVBAR
        ========================= */

        .navbar {
            height: 70px;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 35px;

            position: sticky;
            top: 0;
            z-index: 100;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }

        .logo span {
            color: #111827;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-btn {
            text-decoration: none;
            color: #374151;
            font-weight: 600;
            padding: 10px 15px;
            border-radius: 8px;
        }

        .back-btn:hover {
            background: #f3f4f6;
        }

        .logout-btn {
            border: none;
            background: #ef4444;
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
        }

        .logout-btn:hover {
            background: #dc2626;
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            max-width: 850px;
            margin: auto;
            padding: 35px 20px;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 28px;
            margin-bottom: 7px;
        }

        .page-title p {
            color: #6b7280;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }

        /* =========================
           PHOTO
        ========================= */

        .photo-section {
            display: flex;
            align-items: center;
            gap: 20px;

            padding-bottom: 25px;
            margin-bottom: 25px;

            border-bottom: 1px solid #e5e7eb;
        }

        .profile-photo {
            width: 110px;
            height: 110px;

            border-radius: 50%;

            object-fit: cover;

            border: 4px solid #eff6ff;

            background: #e5e7eb;
        }

        .photo-info h3 {
            margin-bottom: 7px;
        }

        .photo-info p {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .file-input {
            font-size: 13px;
        }

        /* =========================
           FORM
        ========================= */

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        input,
        textarea {
            width: 100%;

            padding: 12px 14px;

            border: 1px solid #d1d5db;

            border-radius: 9px;

            font-size: 14px;

            outline: none;

            transition: 0.2s;
        }

        input:focus,
        textarea:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .readonly {
            background: #f3f4f6;
            color: #6b7280;
        }

        .help-text {
            font-size: 12px;
            color: #6b7280;
            margin-top: 6px;
        }

        /* =========================
           BUTTON
        ========================= */

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;

            margin-top: 30px;

            padding-top: 25px;

            border-top: 1px solid #e5e7eb;
        }

        .btn {
            border: none;

            padding: 12px 22px;

            border-radius: 9px;

            cursor: pointer;

            font-weight: bold;

            text-decoration: none;

            text-align: center;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-primary:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e5e7eb;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;

            padding: 13px 15px;

            border-radius: 9px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        .message.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        .message.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            text-align: center;
            padding: 40px;
            color: #6b7280;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .navbar {
                padding: 0 18px;
            }

            .back-btn {
                display: none;
            }

            .container {
                padding: 25px 15px;
            }

            .card {
                padding: 22px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .photo-section {
                align-items: flex-start;
            }

            .actions {
                flex-direction: column-reverse;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">

    <div class="logo">
        Pus<span>tanka</span>
    </div>

    <div class="nav-right">

        <a
            href="./"
            class="back-btn"
        >
            ← Profil
        </a>

        <button
            class="logout-btn"
            onclick="logout()"
        >
            Logout
        </button>

    </div>

</nav>


<div class="container">

    <div class="page-title">

        <h1>Edit Profil</h1>

        <p>
            Perbarui informasi akun kamu.
        </p>

    </div>


    <div id="loading" class="loading">
        Memuat data profil...
    </div>


    <div
        id="formCard"
        class="card"
        style="display:none;"
    >

        <div
            id="message"
            class="message"
        ></div>


        <!-- =========================
             FOTO
        ========================= -->

        <div class="photo-section">

            <img
                id="profilePhoto"
                class="profile-photo"
                src=""
                alt="Foto Profil"
            >

            <div class="photo-info">

                <h3>
                    Foto Profil
                </h3>

                <p>
                    JPG, PNG, atau WEBP. Maksimal 2 MB.
                </p>

                <input
                    type="file"
                    id="foto"
                    class="file-input"
                    accept="image/jpeg,image/png,image/webp"
                >

            </div>

        </div>


        <!-- =========================
             FORM
        ========================= -->

        <form id="profileForm">

            <div class="form-grid">

                <!-- NAMA -->

                <div class="form-group">

                    <label for="nama">
                        Nama Lengkap
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        Email
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        required
                    >

                </div>


                <!-- NIK -->

                <div class="form-group">

                    <label for="nik">
                        NIK
                    </label>

                    <input
                        type="text"
                        id="nik"
                        class="readonly"
                        readonly
                    >

                    <div class="help-text">
                        NIK tidak dapat diubah.
                    </div>

                </div>


                <!-- NO TELEPON -->

                <div class="form-group">

                    <label for="no_telp">
                        Nomor Telepon
                    </label>

                    <input
                        type="text"
                        id="no_telp"
                        name="no_telp"
                        maxlength="15"
                    >

                </div>


                <!-- MEMBER CODE -->

                <div class="form-group">

                    <label for="member_code">
                        Member Code
                    </label>

                    <input
                        type="text"
                        id="member_code"
                        class="readonly"
                        readonly
                    >

                    <div class="help-text">
                        Member code dibuat otomatis oleh sistem.
                    </div>

                </div>


                <!-- STATUS -->

                <div class="form-group">

                    <label for="status">
                        Status Akun
                    </label>

                    <input
                        type="text"
                        id="status"
                        class="readonly"
                        readonly
                    >

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
                    ></textarea>

                </div>

            </div>


            <!-- ACTIONS -->

            <div class="actions">

                <a
                    href="./"
                    class="btn btn-secondary"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="saveButton"
                    class="btn btn-primary"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>


<script>

const API_BASE = "../../../api";


/*
|--------------------------------------------------------------------------
| LOAD PROFILE
|--------------------------------------------------------------------------
*/

async function loadProfile() {

    try {

        const response = await fetch(
            API_BASE + "/users/profile.php",
            {
                method: "GET",
                credentials: "include"
            }
        );


        const result = await response.json();

        console.log(
            "Profile:",
            result
        );


        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Gagal mengambil profil"
            );
        }


        const user = result.data;


        /*
        |--------------------------------------------------------------------------
        | FOTO
        |--------------------------------------------------------------------------
        */

        const photo =
            document.getElementById(
                "profilePhoto"
            );


        if (user.foto) {

            photo.src =
                "../../../uploads/users/" +
                user.foto;

        } else {

            photo.src =
                "https://ui-avatars.com/api/?name=" +
                encodeURIComponent(user.nama) +
                "&background=2563eb&color=fff&size=256";
        }


        /*
        |--------------------------------------------------------------------------
        | FORM DATA
        |--------------------------------------------------------------------------
        */

        document.getElementById("nama").value =
            user.nama || "";


        document.getElementById("email").value =
            user.email || "";


        document.getElementById("nik").value =
            user.nik || "";


        document.getElementById("no_telp").value =
            user.no_telp || "";


        document.getElementById("alamat").value =
            user.alamat || "";


        document.getElementById("member_code").value =
            user.member_code || "";


        document.getElementById("status").value =
            user.status || "";


        /*
        |--------------------------------------------------------------------------
        | SHOW FORM
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "loading"
        ).style.display = "none";


        document.getElementById(
            "formCard"
        ).style.display = "block";


    } catch (error) {

        console.error(
            "Load profile error:",
            error
        );


        document.getElementById(
            "loading"
        ).textContent =
            "Gagal mengambil data profil.";

    }

}


/*
|--------------------------------------------------------------------------
| FOTO PREVIEW
|--------------------------------------------------------------------------
*/

document.getElementById(
    "foto"
).addEventListener(
    "change",
    function () {

        const file = this.files[0];

        if (!file) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | VALIDASI UKURAN
        |--------------------------------------------------------------------------
        */

        if (
            file.size >
            2 * 1024 * 1024
        ) {

            showMessage(
                "Ukuran foto maksimal 2 MB.",
                "error"
            );

            this.value = "";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | PREVIEW
        |--------------------------------------------------------------------------
        */

        const reader =
            new FileReader();


        reader.onload = function (event) {

            document.getElementById(
                "profilePhoto"
            ).src =
                event.target.result;

        };


        reader.readAsDataURL(file);

    }
);


/*
|--------------------------------------------------------------------------
| SUBMIT FORM
|--------------------------------------------------------------------------
*/

document.getElementById(
    "profileForm"
).addEventListener(
    "submit",
    async function (event) {

        event.preventDefault();


        const button =
            document.getElementById(
                "saveButton"
            );


        button.disabled = true;

        button.textContent =
            "Menyimpan...";


        hideMessage();


        try {

            const formData =
                new FormData();


            /*
            |--------------------------------------------------------------------------
            | DATA YANG BOLEH DIUBAH
            |--------------------------------------------------------------------------
            */

            formData.append(
                "nama",
                document.getElementById("nama").value.trim()
            );


            formData.append(
                "alamat",
                document.getElementById("alamat").value.trim()
            );


            formData.append(
                "no_telp",
                document.getElementById("no_telp").value.trim()
            );


            formData.append(
                "email",
                document.getElementById("email").value.trim()
            );


            /*
            |--------------------------------------------------------------------------
            | FOTO
            |--------------------------------------------------------------------------
            */

            const file =
                document.getElementById(
                    "foto"
                ).files[0];


            if (file) {

                formData.append(
                    "foto",
                    file
                );

            }


            /*
            |--------------------------------------------------------------------------
            | REQUEST
            |--------------------------------------------------------------------------
            */

            const response =
                await fetch(
                    API_BASE +
                    "/users/profile_update.php",
                    {
                        method: "POST",
                        credentials: "include",
                        body: formData
                    }
                );


            const result =
                await response.json();


            console.log(
                "Update profile:",
                result
            );


            /*
            |--------------------------------------------------------------------------
            | ERROR
            |--------------------------------------------------------------------------
            */

            if (
                response.status === 401
            ) {

                window.location.href =
                    "../../auth/login.php";

                return;
            }


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal memperbarui profil."
                );
            }


            /*
            |--------------------------------------------------------------------------
            | SUCCESS
            |--------------------------------------------------------------------------
            */

            showMessage(
                result.message ||
                "Profil berhasil diperbarui.",
                "success"
            );

            setTimeout(() => {
                window.location.href = "./";
            }, 800);


            /*
            |--------------------------------------------------------------------------
            | REFRESH PHOTO
            |--------------------------------------------------------------------------
            */

            if (
                result.data &&
                result.data.foto
            ) {

                document.getElementById(
                    "profilePhoto"
                ).src =
                    "../../../uploads/users/" +
                    result.data.foto +
                    "?t=" +
                    Date.now();

            }


            /*
            |--------------------------------------------------------------------------
            | RESET FILE
            |--------------------------------------------------------------------------
            */

            document.getElementById(
                "foto"
            ).value = "";


            /*
            |--------------------------------------------------------------------------
            | RELOAD PROFILE
            |--------------------------------------------------------------------------
            */

            await loadProfile();


        } catch (error) {

            console.error(
                "Update error:",
                error
            );


            showMessage(
                error.message ||
                "Terjadi kesalahan saat memperbarui profil.",
                "error"
            );

        } finally {

            button.disabled = false;

            button.textContent =
                "Simpan Perubahan";

        }

    }
);


/*
|--------------------------------------------------------------------------
| MESSAGE
|--------------------------------------------------------------------------
*/

function showMessage(
    message,
    type
) {

    const element =
        document.getElementById(
            "message"
        );


    element.textContent =
        message;


    element.className =
        "message " + type;


    element.style.display =
        "block";

}


function hideMessage() {

    const element =
        document.getElementById(
            "message"
        );


    element.style.display =
        "none";

}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

async function logout() {

    try {

        await fetch(
            API_BASE +
            "/auth/logout.php",
            {
                method: "POST",
                credentials: "include"
            }
        );

    } catch (error) {

        console.error(
            "Logout error:",
            error
        );

    }


    window.location.href =
        "../../auth/login.php";

}


/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

loadProfile();

</script>

</body>
</html>