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

$adminName =$_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Edit User - Pustanka</title>

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
            max-width: 1200px;
            margin: 0 auto;
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
            max-width: 900px;
            margin: 0 auto;
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

        .readonly {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
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
           LOADING
        ========================= */

        .loading {
            text-align: center;
            padding: 40px;
            color: #64748b;
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
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }

        .alert.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
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
            <h1>Edit User</h1>

            <div class="admin-info">
                Perbarui data anggota perpustakaan
            </div>
        </div>

        <div class="admin-info">
            Admin:
            <strong>
                <?= htmlspecialchars($adminName) ?>
            </strong>
        </div>

    </div>


    <div id="alertBox" class="alert"></div>


    <div id="loading" class="loading">
        Memuat data user...
    </div>


    <div id="formCard" class="card" style="display:none;">

        <form id="userForm">

            <!-- FOTO -->

            <div class="photo-section">

                <label>Foto User</label>

                <div class="photo-wrapper">

                    <div id="photoContainer">
                        <div class="photo-placeholder">
                            Belum ada foto
                        </div>
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


            <!-- FORM -->

            <div class="form-grid">

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

                </div>


                <div class="form-group">

                    <label for="status">
                        Status <span class="required">*</span>
                    </label>

                    <select id="status" name="status">

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


                <div class="form-group">

                    <label for="nama">
                        Nama <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        maxlength="100"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="nik">
                        NIK <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        maxlength="16"
                        inputmode="numeric"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        maxlength="100"
                        required
                    >

                </div>


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

                </div>


                <div class="form-group full">

                    <label for="alamat">
                        Alamat
                    </label>

                    <textarea
                        id="alamat"
                        name="alamat"
                        maxlength="255"
                        placeholder="Masukkan alamat user"
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
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</main>


<script>

const API_BASE = "../../../api";
const DETAIL_API = API_BASE + "/users/detail.php";
const UPDATE_API = API_BASE + "/users/update.php";

const params = new URLSearchParams(window.location.search);
const idUser = Number(params.get("id"));

const loading = document.getElementById("loading");
const formCard = document.getElementById("formCard");
const form = document.getElementById("userForm");
const alertBox = document.getElementById("alertBox");
const saveButton = document.getElementById("saveButton");

const fotoInput = document.getElementById("foto");
const photoContainer = document.getElementById("photoContainer");


/* =========================
   VALIDATE ID
========================= */

if (!idUser || idUser <= 0) {

    showError("ID user tidak valid.");

    loading.style.display = "none";

} else {

    loadUser();

}


/* =========================
   GET JSON
========================= */

async function getJson(url) {

    const response = await fetch(url, {
        method: "GET",
        credentials: "include"
    });

    if (response.status === 401) {

        window.location.href = "../../auth/login.php";

        return null;
    }

    const result = await response.json();

    if (!response.ok || !result.status) {

        throw new Error(
            result.message ||
            "Gagal mengambil data user."
        );

    }

    return result;

}


/* =========================
   LOAD USER
========================= */

async function loadUser() {

    try {

        const result = await getJson(
            DETAIL_API + "?id=" + encodeURIComponent(idUser)
        );

        if (!result) return;

        const data = result.data;

        if (!data) {
            throw new Error("Data user tidak tersedia.");
        }


        document.getElementById("member_code").value =
            data.member_code || "-";

        document.getElementById("nama").value =
            data.nama || "";

        document.getElementById("nik").value =
            data.nik || "";

        document.getElementById("email").value =
            data.email || "";

        document.getElementById("no_telp").value =
            data.no_telp || "";

        document.getElementById("alamat").value =
            data.alamat || "";

        document.getElementById("status").value =
            data.status || "aktif";


        /* FOTO */

        if (data.foto) {

            const image = document.createElement("img");

            image.className = "photo-preview";

            image.alt = "Foto User";

            image.src = "../../../" + data.foto;

            image.onerror = function () {

                photoContainer.innerHTML = `
                    <div class="photo-placeholder">
                        Foto tidak ditemukan
                    </div>
                `;

            };

            photoContainer.innerHTML = "";

            photoContainer.appendChild(image);

        }


        loading.style.display = "none";

        formCard.style.display = "block";

    } catch (error) {

        console.error(error);

        loading.style.display = "none";

        showError(
            error.message ||
            "Gagal memuat data user."
        );

    }

}


/* =========================
   PHOTO PREVIEW
========================= */

fotoInput.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) return;


    if (file.size > 2 * 1024 * 1024) {

        alert("Ukuran foto maksimal 2 MB.");

        this.value = "";

        return;

    }


    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (!allowedTypes.includes(file.type)) {

        alert(
            "Format foto hanya boleh JPG, PNG, atau WEBP."
        );

        this.value = "";

        return;

    }


    const image = document.createElement("img");

    image.className = "photo-preview";

    image.alt = "Preview Foto";


    const reader = new FileReader();

    reader.onload = function (event) {

        image.src = event.target.result;

        photoContainer.innerHTML = "";

        photoContainer.appendChild(image);

    };

    reader.readAsDataURL(file);

});


/* =========================
   SUBMIT
========================= */

form.addEventListener("submit", async function (event) {

    event.preventDefault();

    hideError();


    const nama =
        document.getElementById("nama").value.trim();

    const nik =
        document.getElementById("nik").value.trim();

    const email =
        document.getElementById("email").value.trim();

    const noTelp =
        document.getElementById("no_telp").value.trim();

    const alamat =
        document.getElementById("alamat").value.trim();

    const status =
        document.getElementById("status").value;


    /* NAMA */

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


    /* NIK */

    if (!/^[0-9]{16}$/.test(nik)) {

        showError(
            "NIK harus terdiri dari 16 digit angka."
        );

        return;

    }


    /* TELEPON */

    if (
        noTelp !== "" &&
        !/^\+?[0-9]{10,15}$/.test(noTelp)
    ) {

        showError(
            "Nomor telepon harus 10-15 digit dan hanya boleh berisi angka."
        );

        return;

    }


    /* EMAIL */

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


    /* ALAMAT */

    if (alamat.length > 255) {

        showError(
            "Alamat maksimal 255 karakter."
        );

        return;

    }


    /* STATUS */

    const allowedStatus = [
        "aktif",
        "nonaktif",
        "diblokir"
    ];

    if (!allowedStatus.includes(status)) {

        showError(
            "Status user tidak valid."
        );

        return;

    }


    /* =========================
       FORM DATA
    ========================= */

    const formData = new FormData();

    formData.append(
        "id_user",
        idUser
    );

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
        "status",
        status
    );


    if (fotoInput.files.length > 0) {

        formData.append(
            "foto",
            fotoInput.files[0]
        );

    }


    /* =========================
       SAVE
    ========================= */

    saveButton.disabled = true;

    saveButton.textContent =
        "Menyimpan...";


    try {

        const response = await fetch(
            UPDATE_API,
            {
                method: "POST",
                credentials: "include",
                body: formData
            }
        );


        if (response.status === 401) {

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
                "Gagal memperbarui data user."
            );

        }


        alert(
            result.message ||
            "Data anggota berhasil diperbarui."
        );


        window.location.href =
            "detail.php?id=" +
            encodeURIComponent(idUser);


    } catch (error) {

        console.error(error);

        showError(
            error.message ||
            "Terjadi kesalahan saat memperbarui user."
        );

    } finally {

        saveButton.disabled = false;

        saveButton.textContent =
            "Simpan Perubahan";

    }

});


/* =========================
   ERROR
========================= */

function showError(message) {

    alertBox.textContent = "❌ " + message;

    alertBox.className =
        "alert error";

    alertBox.style.display = "block";

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });

}


function hideError() {

    alertBox.style.display = "none";

}

</script>

</body>
</html>