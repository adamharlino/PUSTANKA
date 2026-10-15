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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Admin - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .container {
            max-width: 850px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 28px;
            color: #0f172a;
        }

        .back-btn {
            text-decoration: none;
            background: #0f172a;
            color: white;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        .photo-section {
            text-align: center;
            margin-bottom: 30px;
        }

        .profile-photo {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid #dbeafe;
            background: #e2e8f0;
            display: block;
            margin: 0 auto 15px;
        }

        .photo-label {
            display: inline-block;
            background: #2563eb;
            color: white;
            padding: 10px 16px;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: bold;
        }

        #foto {
            display: none;
        }

        .photo-info {
            margin-top: 8px;
            font-size: 12px;
            color: #64748b;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        input,
        textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 14px;
            outline: none;
            font-family: inherit;
            transition: 0.2s;
        }

        input:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        input:disabled {
            background: #f1f5f9;
            color: #64748b;
            cursor: not-allowed;
        }

        textarea {
            resize: vertical;
            min-height: 110px;
        }

        .hint {
            font-size: 12px;
            color: #64748b;
            margin-top: 6px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid #e5e7eb;
        }

        button,
        .cancel-btn {
            border: none;
            padding: 12px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
        }

        .cancel-btn {
            background: #e2e8f0;
            color: #334155;
        }

        .save-btn {
            background: #2563eb;
            color: white;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

        .save-btn:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .message {
            display: none;
            padding: 14px;
            border-radius: 10px;
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

        .loading {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }

        @media (max-width: 700px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .back-btn {
                text-align: center;
            }

            .actions {
                flex-direction: column-reverse;
            }

            button,
            .cancel-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topbar">
        <h1>Edit Profil Admin</h1>

        <a href="./index.php" class="back-btn">
            ← Kembali
        </a>
    </div>

    <div id="message" class="message"></div>

    <div class="card">

        <div id="loading" class="loading">
            Memuat data profil...
        </div>

        <form id="profileForm" style="display:none;" enctype="multipart/form-data">

            <div class="photo-section">

                <img
                    id="photoPreview"
                    class="profile-photo"
                    src=""
                    alt="Foto Admin"
                >

                <label for="foto" class="photo-label">
                    Pilih Foto
                </label>

                <input
                    type="file"
                    id="foto"
                    name="foto"
                    accept="image/jpeg,image/png,image/webp"
                >

                <div class="photo-info">
                    JPG, PNG, atau WEBP — maksimal 2 MB
                </div>

            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="id_admin">
                        ID Admin
                    </label>

                    <input
                        type="text"
                        id="id_admin"
                        disabled
                    >

                    <div class="hint">
                        ID Admin tidak dapat diubah.
                    </div>
                </div>

                <div class="form-group">
                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        maxlength="100"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="nama">
                        Nama
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

                <div class="form-group">
                    <label for="no_telp">
                        No. Telepon
                    </label>

                    <input
                        type="text"
                        id="no_telp"
                        name="no_telp"
                        maxlength="20"
                    >
                </div>

                <div class="form-group">
                    <label for="card_code">
                        Kode Kartu Admin
                    </label>

                    <input
                        type="text"
                        id="card_code"
                        disabled
                    >

                    <div class="hint">
                        Kode kartu dibuat otomatis dan tidak dapat diubah.
                    </div>
                </div>

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

            <div class="actions">

                <a
                    href="./index.php"
                    class="cancel-btn"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="save-btn"
                    id="saveButton"
                >
                    Simpan Perubahan
                </button>

            </div>

        </form>

    </div>

</div>

<script>

const DETAIL_API = "../../../api/admins/detail.php";
const UPDATE_API = "../../../api/admins/update.php";

let currentPhoto = "";

const loading = document.getElementById("loading");
const form = document.getElementById("profileForm");
const message = document.getElementById("message");
const photoInput = document.getElementById("foto");
const photoPreview = document.getElementById("photoPreview");
const saveButton = document.getElementById("saveButton");


function showMessage(text, type) {

    message.textContent = text;
    message.className = "message " + type;

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}


function avatarUrl(name) {

    return "https://ui-avatars.com/api/?name=" +
        encodeURIComponent(name || "Admin") +
        "&background=2563eb&color=fff";
}


function resolvePhoto(photo, name) {

    if (!photo) {
        return avatarUrl(name);
    }

    if (
        photo.startsWith("http://") ||
        photo.startsWith("https://")
    ) {
        return photo;
    }

    return "../../../" + photo;
}


async function loadProfile() {

    try {

        const response = await fetch(DETAIL_API, {
            method: "GET",
            credentials: "include"
        });

        const result = await response.json();

        if (!response.ok || !result.status) {
            throw new Error(
                result.message || "Gagal mengambil data profil."
            );
        }

        const data = result.data;

        document.getElementById("id_admin").value =
            data.id_admin ?? "";

        document.getElementById("username").value =
            data.username ?? "";

        document.getElementById("nama").value =
            data.nama ?? "";

        document.getElementById("email").value =
            data.email ?? "";

        document.getElementById("no_telp").value =
            data.no_telp ?? "";

        document.getElementById("alamat").value =
            data.alamat ?? "";

        document.getElementById("card_code").value =
            data.card_code ?? "";

        currentPhoto = data.foto ?? "";

        photoPreview.src =
            resolvePhoto(currentPhoto, data.nama);

        loading.style.display = "none";
        form.style.display = "block";

    } catch (error) {

        loading.style.display = "none";

        showMessage(
            error.message || "Gagal memuat profil.",
            "error"
        );
    }
}


photoInput.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) {
        return;
    }

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (!allowedTypes.includes(file.type)) {

        showMessage(
            "Format foto harus JPG, PNG, atau WEBP.",
            "error"
        );

        this.value = "";
        return;
    }

    if (file.size > 2 * 1024 * 1024) {

        showMessage(
            "Ukuran foto maksimal 2 MB.",
            "error"
        );

        this.value = "";
        return;
    }

    const reader = new FileReader();

    reader.onload = function (event) {
        photoPreview.src = event.target.result;
    };

    reader.readAsDataURL(file);
});


form.addEventListener("submit", async function (event) {

    event.preventDefault();

    message.className = "message";
    message.textContent = "";

    saveButton.disabled = true;
    saveButton.textContent = "Menyimpan...";

    try {

        const formData = new FormData();

        formData.append(
            "username",
            document.getElementById("username").value.trim()
        );

        formData.append(
            "nama",
            document.getElementById("nama").value.trim()
        );

        formData.append(
            "email",
            document.getElementById("email").value.trim()
        );

        formData.append(
            "no_telp",
            document.getElementById("no_telp").value.trim()
        );

        formData.append(
            "alamat",
            document.getElementById("alamat").value.trim()
        );

        if (photoInput.files.length > 0) {

            formData.append(
                "foto",
                photoInput.files[0]
            );
        }


        const response = await fetch(UPDATE_API, {
            method: "POST",
            credentials: "include",
            body: formData
        });

        const result = await response.json();

        if (!response.ok || !result.status) {

            throw new Error(
                result.message || "Gagal memperbarui profil."
            );
        }


        showMessage(
            result.message || "Profil berhasil diperbarui.",
            "success"
        );


        setTimeout(() => {

            window.location.href = "./index.php";

        }, 1000);


    } catch (error) {

        showMessage(
            error.message || "Terjadi kesalahan.",
            "error"
        );

        saveButton.disabled = false;
        saveButton.textContent = "💾 Simpan Perubahan";
    }

});


loadProfile();

</script>

</body>
</html>