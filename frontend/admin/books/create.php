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

    <title>Tambah Buku - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #1e293b;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 0;
            min-height: 100vh;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar-left h1 {
            font-size: 28px;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .topbar-left p {
            color: #64748b;
            font-size: 14px;
        }

        .admin-info {
            background: white;
            padding: 10px 15px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.06);
            font-size: 14px;
        }

        /* =========================
           CARD
        ========================= */

        .form-card {
            background: white;
            border-radius: 14px;
            padding: 30px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, 0.07);
            max-width: 100%;
        }

        .section-title {
            font-size: 18px;
            color: #0f172a;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #e2e8f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 7px;
            color: #334155;
        }

        .required {
            color: #dc2626;
        }

        input,
        select,
        textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 11px 12px;
            font-size: 14px;
            color: #1e293b;
            background: white;
            outline: none;
            transition: 0.2s;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        .help-text {
            font-size: 11px;
            color: #64748b;
            margin-top: 5px;
        }

        /* =========================
           PHOTO
        ========================= */

        .photo-box {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            flex-wrap: wrap;
        }

        .photo-preview {
            width: 150px;
            height: 200px;
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #f8fafc;
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
            padding: 10px;
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-input {
            flex: 1;
            min-width: 250px;
        }

        /* =========================
           BUTTON
        ========================= */

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 11px 18px;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            transition: 0.2s;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
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

        /* =========================
           ALERT
        ========================= */

        .alert {
            display: none;
            padding: 13px 15px;
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

        .alert.success {
            display: block;
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            color: #64748b;
            font-size: 13px;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 900px) {
            .main {
                margin-left: 0;
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
            .main {
                margin-left: 0;
                padding: 15px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-card {
                padding: 20px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .btn {
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

        <div class="topbar-left">
            <h1>Tambah Buku</h1>
            <p>Tambahkan data buku baru ke dalam sistem perpustakaan.</p>
        </div>

        <div class="admin-info">
            👤 <?= htmlspecialchars($adminName) ?>
        </div>

    </div>


    <div class="form-card">

        <div id="alertBox" class="alert"></div>

        <form id="bookForm" enctype="multipart/form-data">

            <!-- =========================
                 INFORMASI BUKU
            ========================= -->

            <div class="section-title">
                Informasi Buku
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label for="isbn">
                        ISBN
                    </label>

                    <input
                        type="text"
                        id="isbn"
                        name="isbn"
                        maxlength="50"
                        placeholder="Contoh: ISBN-01001"
                    >
                </div>


                <div class="form-group">
                    <label for="barcode">
                        Barcode
                    </label>

                    <input
                        type="text"
                        id="barcode"
                        name="barcode"
                        maxlength="50"
                        placeholder="Contoh: BK01001"
                    >

                    <div class="help-text">
                        Gunakan huruf, angka, dan tanda strip (-).
                    </div>
                </div>


                <div class="form-group full">
                    <label for="judul">
                        Judul Buku <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="judul"
                        name="judul"
                        maxlength="255"
                        required
                        placeholder="Masukkan judul buku"
                    >
                </div>


                <div class="form-group">
                    <label for="pengarang">
                        Pengarang <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="pengarang"
                        name="pengarang"
                        maxlength="150"
                        required
                        placeholder="Masukkan nama pengarang"
                    >
                </div>


                <div class="form-group">
                    <label for="penerbit">
                        Penerbit
                    </label>

                    <input
                        type="text"
                        id="penerbit"
                        name="penerbit"
                        maxlength="150"
                        placeholder="Masukkan nama penerbit"
                    >
                </div>


                <div class="form-group">
                    <label for="tahun_terbit">
                        Tahun Terbit
                    </label>

                    <input
                        type="number"
                        id="tahun_terbit"
                        name="tahun_terbit"
                        min="1000"
                        max="<?= date("Y") ?>"
                        placeholder="Contoh: 2026"
                    >
                </div>


                <div class="form-group">
                    <label for="jenis_buku">
                        Jenis Buku
                    </label>

                    <input
                        type="text"
                        id="jenis_buku"
                        name="jenis_buku"
                        maxlength="100"
                        placeholder="Contoh: Novel"
                    >
                </div>


                <!-- KATEGORI -->

                <div class="form-group">
                    <label for="id_category">
                        Kategori
                    </label>

                    <select id="id_category" name="id_category">

                        <option value="">
                            Memuat kategori...
                        </option>

                    </select>
                </div>


                <!-- RAK -->

                <div class="form-group">
                    <label for="id_shelf">
                        Rak
                    </label>

                    <select id="id_shelf" name="id_shelf">

                        <option value="">
                            Memuat rak...
                        </option>

                    </select>
                </div>


                <!-- HARGA -->

                <div class="form-group">
                    <label for="harga">
                        Harga <span class="required">*</span>
                    </label>

                    <input
                        type="number"
                        id="harga"
                        name="harga"
                        min="0"
                        step="0.01"
                        required
                        placeholder="Contoh: 75000"
                    >
                </div>


                <!-- SINOPSIS -->

                <div class="form-group full">

                    <label for="sinopsis">
                        Sinopsis
                    </label>

                    <textarea
                        id="sinopsis"
                        name="sinopsis"
                        maxlength="5000"
                        placeholder="Masukkan sinopsis buku..."
                    ></textarea>

                    <div class="help-text">
                        Maksimal 5000 karakter.
                    </div>

                </div>

            </div>


            <!-- =========================
                 FOTO
            ========================= -->

            <div class="section-title" style="margin-top: 30px;">
                Foto Buku
            </div>

            <div class="photo-box">

                <div class="photo-preview" id="photoPreview">
                    Preview foto
                </div>

                <div class="photo-input">

                    <div class="form-group">

                        <label for="foto">
                            Foto Buku
                        </label>

                        <input
                            type="file"
                            id="foto"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                        >

                        <div class="help-text">
                            Format JPG, PNG, atau WEBP. Maksimal 2 MB.
                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================
                 BUTTON
            ========================= -->

            <div class="form-actions">

                <a
                    href="index.php"
                    class="btn btn-secondary"
                >
                    ← Batal
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                    id="submitBtn"
                >
                    Simpan Buku
                </button>

            </div>

        </form>

    </div>

</main>


<script>

const API_BASE = "../../../api";

const CATEGORY_API = API_BASE + "/books/categories.php";
const SHELF_API = API_BASE + "/books/shelves.php";
const CREATE_API = API_BASE + "/books/create.php";

const form = document.getElementById("bookForm");
const alertBox = document.getElementById("alertBox");
const submitBtn = document.getElementById("submitBtn");

const categorySelect = document.getElementById("id_category");
const shelfSelect = document.getElementById("id_shelf");

const fotoInput = document.getElementById("foto");
const photoPreview = document.getElementById("photoPreview");


/* =========================
   ALERT
========================= */

function showAlert(message, type = "error") {

    alertBox.textContent = message;

    alertBox.className = "alert " + type;

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}


function hideAlert() {

    alertBox.textContent = "";

    alertBox.className = "alert";
}


/* =========================
   JSON HELPER
========================= */

async function getJson(url) {

    const response = await fetch(url, {
        method: "GET",
        credentials: "include"
    });

    let result;

    try {
        result = await response.json();
    } catch (error) {
        throw new Error("Response server tidak valid.");
    }

    if (response.status === 401) {
        window.location.href = "../../auth/login.php";
        return null;
    }

    if (!response.ok || result.status === false) {

        throw new Error(
            result.message || "Gagal mengambil data."
        );
    }

    return result;
}


/* =========================
   LOAD CATEGORIES
========================= */

async function loadCategories() {

    try {

        categorySelect.innerHTML = `
            <option value="">Memuat kategori...</option>
        `;

        const result = await getJson(CATEGORY_API);

        if (!result) return;

        categorySelect.innerHTML = `
            <option value="">Pilih Kategori</option>
        `;

        const data = Array.isArray(result.data)
            ? result.data
            : [];

        data.forEach(category => {

            const option = document.createElement("option");

            option.value =
                category.id_category ??
                category.id ??
                "";

            option.textContent =
                category.nama_category ??
                category.nama_kategori ??
                category.name ??
                category.nama ??
                "Kategori";

            if (option.value) {
                categorySelect.appendChild(option);
            }

        });

    } catch (error) {

        categorySelect.innerHTML = `
            <option value="">
                Gagal memuat kategori
            </option>
        `;

        showAlert(
            "Kategori gagal dimuat: " + error.message
        );
    }
}


/* =========================
   LOAD SHELVES
========================= */

async function loadShelves() {

    try {

        shelfSelect.innerHTML = `
            <option value="">Memuat rak...</option>
        `;

        const result = await getJson(SHELF_API);

        if (!result) return;

        shelfSelect.innerHTML = `
            <option value="">Pilih Rak</option>
        `;

        (result.data || []).forEach(shelf => {

            const option = document.createElement("option");

            option.value = shelf.id_shelf;

            let label = "Rak " + (shelf.no_rak || "-");

            if (
                shelf.lantai !== null &&
                shelf.lantai !== undefined &&
                shelf.lantai !== ""
            ) {
                label += " • Lantai " + shelf.lantai;
            }

            option.textContent = label;

            shelfSelect.appendChild(option);
        });

    } catch (error) {

        shelfSelect.innerHTML = `
            <option value="">
                Gagal memuat rak
            </option>
        `;

        showAlert(
            "Rak gagal dimuat: " + error.message
        );
    }
}


/* =========================
   PHOTO PREVIEW
========================= */

fotoInput.addEventListener("change", function () {

    const file = this.files[0];

    if (!file) {

        photoPreview.innerHTML = "Preview foto";

        return;
    }


    /* cek ukuran */

    if (file.size > 2 * 1024 * 1024) {

        showAlert(
            "Ukuran foto maksimal 2 MB."
        );

        this.value = "";

        photoPreview.innerHTML = "Preview foto";

        return;
    }


    /* cek tipe */

    const allowedTypes = [
        "image/jpeg",
        "image/png",
        "image/webp"
    ];

    if (!allowedTypes.includes(file.type)) {

        showAlert(
            "Format foto harus JPG, PNG, atau WEBP."
        );

        this.value = "";

        photoPreview.innerHTML = "Preview foto";

        return;
    }


    const reader = new FileReader();

    reader.onload = function (event) {

        photoPreview.innerHTML = `
            <img
                src="${event.target.result}"
                alt="Preview Foto Buku"
            >
        `;
    };

    reader.readAsDataURL(file);

});


/* =========================
   FORM SUBMIT
========================= */

form.addEventListener("submit", async function (event) {

    event.preventDefault();

    hideAlert();


    const judul =
        document.getElementById("judul").value.trim();

    const pengarang =
        document.getElementById("pengarang").value.trim();

    const harga =
        document.getElementById("harga").value.trim();

    const tahun =
        document.getElementById("tahun_terbit").value.trim();

    const barcode =
        document.getElementById("barcode").value.trim();


    /* =========================
       FRONTEND VALIDATION
    ========================= */

    if (!judul) {

        showAlert("Judul buku wajib diisi.");

        document.getElementById("judul").focus();

        return;
    }


    if (judul.length > 255) {

        showAlert(
            "Judul buku maksimal 255 karakter."
        );

        return;
    }


    if (!pengarang) {

        showAlert("Pengarang wajib diisi.");

        document.getElementById("pengarang").focus();

        return;
    }


    if (pengarang.length > 150) {

        showAlert(
            "Nama pengarang maksimal 150 karakter."
        );

        return;
    }


    if (!harga) {

        showAlert("Harga buku wajib diisi.");

        document.getElementById("harga").focus();

        return;
    }


    if (Number(harga) < 0) {

        showAlert(
            "Harga buku tidak boleh negatif."
        );

        return;
    }


    if (tahun) {

        const currentYear =
            new Date().getFullYear();

        if (!/^\d{4}$/.test(tahun)) {

            showAlert(
                "Tahun terbit harus terdiri dari 4 digit."
            );

            return;
        }

        if (
            Number(tahun) < 1000 ||
            Number(tahun) > currentYear
        ) {

            showAlert(
                "Tahun terbit tidak valid."
            );

            return;
        }
    }


    if (barcode) {

        if (!/^[A-Za-z0-9-]+$/.test(barcode)) {

            showAlert(
                "Barcode hanya boleh berisi huruf, angka, dan tanda strip (-)."
            );

            return;
        }

        if (barcode.length > 50) {

            showAlert(
                "Barcode maksimal 50 karakter."
            );

            return;
        }
    }


    /* =========================
       FORM DATA
    ========================= */

    const formData = new FormData(form);


    /* =========================
       LOADING
    ========================= */

    submitBtn.disabled = true;

    submitBtn.textContent = "Menyimpan...";


    try {

        const response = await fetch(
            CREATE_API,
            {
                method: "POST",
                credentials: "include",
                body: formData
            }
        );


        let result;

        try {

            result = await response.json();

        } catch (error) {

            throw new Error(
                "Response server tidak valid."
            );
        }


        /* =========================
           SESSION
        ========================= */

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        /* =========================
           ERROR
        ========================= */

        if (!response.ok || result.status === false) {

            throw new Error(
                result.message ||
                "Buku gagal ditambahkan."
            );
        }


        /* =========================
           SUCCESS
        ========================= */

        showAlert(
            result.message ||
            "Buku berhasil ditambahkan.",
            "success"
        );


        submitBtn.textContent = "Berhasil";


        /*
         * Beri sedikit waktu agar
         * pesan berhasil terlihat
         */

        setTimeout(() => {

            window.location.href = "index.php";

        }, 1000);


    } catch (error) {

        showAlert(
            error.message ||
            "Terjadi kesalahan saat menyimpan buku."
        );

        submitBtn.disabled = false;

        submitBtn.textContent = "Simpan Buku";
    }

});


/* =========================
   LOGOUT
========================= */

async function logout() {

    try {

        await fetch(
            "../../api/auth/logout.php",
            {
                method: "POST",
                credentials: "include"
            }
        );

    } catch (error) {

        console.error(error);

    }

    window.location.href =
        "../../auth/login.php";
}


/* =========================
   INIT
========================= */

async function init() {

    await Promise.all([
        loadCategories(),
        loadShelves()
    ]);

}


init();

</script>

</body>
</html>