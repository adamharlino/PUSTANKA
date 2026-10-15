<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

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

$idBook = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($idBook <= 0) {
    header("Location: ./index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Edit Buku - Pustanka</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 0;
            width: 100%;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
        }

        .page-header p {
            margin: 7px 0 0;
            color: #64748b;
            font-size: 14px;
        }

        .back-btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 9px;
            background: white;
            border: 1px solid #dbe2ea;
            color: #334155;
            font-size: 14px;
            font-weight: 600;
        }

        .back-btn:hover {
            background: #f8fafc;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.04);
        }

        .loading {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }

        /* =========================
           FORM
        ========================= */

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
            font-weight: 700;
            margin-bottom: 7px;
            color: #334155;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: white;
            color: #1f2937;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        textarea {
            min-height: 140px;
            resize: vertical;
        }

        .hint {
            margin-top: 5px;
            font-size: 11px;
            color: #64748b;
        }

        /* =========================
           PHOTO
        ========================= */

        .photo-section {
            display: flex;
            gap: 25px;
            align-items: flex-start;
        }

        .photo-preview {
            width: 150px;
            height: 200px;
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            background: #f8fafc;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
        }

        .photo-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .photo-empty {
            color: #94a3b8;
            font-size: 12px;
            text-align: center;
            padding: 15px;
        }

        .photo-input {
            flex: 1;
        }

        /* =========================
           BUTTON
        ========================= */

        .form-actions {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            border: 0;
            border-radius: 9px;
            padding: 11px 18px;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
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
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* =========================
           ALERT
        ========================= */

        .alert {
            display: none;
            padding: 12px 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }

        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .main {
                margin-left: 0;
                width: 100%;
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

            .layout {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 18px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card {
                padding: 18px;
            }

            .photo-section {
                flex-direction: column;
            }

            .photo-preview {
                width: 130px;
                height: 175px;
            }

            .form-actions {
                flex-direction: column-reverse;
            }

            .form-actions .btn {
                width: 100%;
            }

        }

    </style>

</head>

<body>

<div class="layout">

    <!-- =========================
         MAIN
    ========================== -->

    <main class="main">

        <div class="page-header">

            <div>

                <h1>
                    Edit Buku
                </h1>

                <p>
                    Perbarui informasi buku perpustakaan.
                </p>

            </div>

            <a
                href="./index.php"
                class="back-btn"
            >
                ← Kembali
            </a>

        </div>


        <div
            id="errorBox"
            class="alert error"
        ></div>


        <div
            id="successBox"
            class="alert success"
        ></div>


        <section class="card">

            <div
                id="loading"
                class="loading"
            >
                Memuat data buku...
            </div>


            <form
                id="bookForm"
                style="display:none;"
            >

                <div class="form-grid">

                    <!-- ISBN -->

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


                    <!-- BARCODE -->

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

                        <div class="hint">
                            Hanya huruf, angka, dan tanda hubung (-).
                        </div>

                    </div>


                    <!-- JUDUL -->

                    <div class="form-group full">

                        <label for="judul">
                            Judul Buku <span style="color:red;">*</span>
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


                    <!-- PENGARANG -->

                    <div class="form-group">

                        <label for="pengarang">
                            Pengarang <span style="color:red;">*</span>
                        </label>

                        <input
                            type="text"
                            id="pengarang"
                            name="pengarang"
                            maxlength="150"
                            required
                            placeholder="Nama pengarang"
                        >

                    </div>


                    <!-- PENERBIT -->

                    <div class="form-group">

                        <label for="penerbit">
                            Penerbit
                        </label>

                        <input
                            type="text"
                            id="penerbit"
                            name="penerbit"
                            maxlength="150"
                            placeholder="Nama penerbit"
                        >

                    </div>


                    <!-- TAHUN -->

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
                            placeholder="Contoh: 2024"
                        >

                    </div>


                    <!-- JENIS -->

                    <div class="form-group">

                        <label for="jenis_buku">
                            Jenis Buku
                        </label>

                        <input
                            type="text"
                            id="jenis_buku"
                            name="jenis_buku"
                            maxlength="100"
                            placeholder="Contoh: Fiksi"
                        >

                    </div>


                    <!-- CATEGORY -->

                    <div class="form-group">

                        <label for="id_category">
                            Kategori
                        </label>

                        <select
                            id="id_category"
                            name="id_category"
                        >

                            <option value="">
                                Memuat kategori...
                            </option>

                        </select>

                    </div>


                    <!-- SHELF -->

                    <div class="form-group">

                        <label for="id_shelf">
                            Rak
                        </label>

                        <select
                            id="id_shelf"
                            name="id_shelf"
                        >

                            <option value="">
                                Memuat rak...
                            </option>

                        </select>

                    </div>


                    <!-- HARGA -->

                    <div class="form-group">

                        <label for="harga">
                            Harga <span style="color:red;">*</span>
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


                    <!-- STATUS -->

                    <div class="form-group">

                        <label for="status">
                            Status
                        </label>

                        <select
                            id="status"
                            name="status"
                        >

                            <option value="tersedia">
                                Tersedia
                            </option>

                            <option value="dipinjam">
                                Dipinjam
                            </option>

                        </select>

                        <div class="hint">
                            Status mengikuti nilai yang diterima endpoint buku.
                        </div>

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

                    </div>


                    <!-- FOTO -->

                    <div class="form-group full">

                        <label>
                            Foto Buku
                        </label>

                        <div class="photo-section">

                            <div
                                class="photo-preview"
                                id="photoPreview"
                            >

                                <div class="photo-empty">
                                    Belum ada foto
                                </div>

                            </div>

                            <div class="photo-input">

                                <input
                                    type="file"
                                    id="foto"
                                    name="foto"
                                    accept="image/jpeg,image/png,image/webp"
                                >

                                <div class="hint">
                                    Kosongkan jika tidak ingin mengganti foto.
                                    Maksimal 2 MB. Format JPG, PNG, atau WEBP.
                                </div>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ACTION -->

                <div class="form-actions">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="window.location.href='./index.php'"
                    >
                        Batal
                    </button>

                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="submitButton"
                    >
                        Simpan Perubahan
                    </button>

                </div>

            </form>

        </section>

    </main>

</div>


<script>

const API_BASE = "../../../api";

const BOOK_DETAIL_API =
    API_BASE + "/books/detail.php";

const UPDATE_API =
    API_BASE + "/books/update.php";

const CATEGORY_API =
    API_BASE + "/books/categories.php";

const SHELF_API =
    API_BASE + "/books/shelves.php";

const BOOK_ID =
    <?= $idBook ?>;


const form =
    document.getElementById("bookForm");

const loading =
    document.getElementById("loading");

const errorBox =
    document.getElementById("errorBox");

const successBox =
    document.getElementById("successBox");

const submitButton =
    document.getElementById("submitButton");

const fotoInput =
    document.getElementById("foto");

const photoPreview =
    document.getElementById("photoPreview");


/* =========================
   HELPERS
========================= */

function showError(message) {

    errorBox.textContent = message;

    errorBox.style.display = "block";

    successBox.style.display = "none";

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}


function showSuccess(message) {

    successBox.textContent = message;

    successBox.style.display = "block";

    errorBox.style.display = "none";
}


function escapeHtml(value) {

    const div =
        document.createElement("div");

    div.textContent =
        value === null ||
        value === undefined
            ? ""
            : String(value);

    return div.innerHTML;
}


/* =========================
   FETCH JSON
========================= */

async function getJson(url) {

    const response =
        await fetch(
            url,
            {
                method: "GET",
                credentials: "include"
            }
        );

    const result =
        await response.json();

    if (!response.ok || !result.status) {

        throw new Error(
            result.message ||
            "Gagal mengambil data."
        );

    }

    return result;
}


/* =========================
   LOAD CATEGORIES
========================= */

async function loadCategories() {

    const select =
        document.getElementById(
            "id_category"
        );

    try {

        const result =
            await getJson(
                CATEGORY_API
            );

        const categories =
            result.data || [];

        select.innerHTML = `
            <option value="">
                -- Pilih Kategori --
            </option>
        `;

        categories.forEach(
            category => {

                const option =
                    document.createElement(
                        "option"
                    );

                option.value =
                    category.id_category;

                option.textContent =
                    category.nama_category ||
                    category.nama ||
                    category.name ||
                    "-";

                select.appendChild(option);

            }
        );

    } catch (error) {

        console.error(
            "Kategori:",
            error
        );

        select.innerHTML = `
            <option value="">
                Gagal memuat kategori
            </option>
        `;

    }
}


/* =========================
   LOAD SHELVES
========================= */

async function loadShelves() {

    const select =
        document.getElementById(
            "id_shelf"
        );

    try {

        const result =
            await getJson(
                SHELF_API
            );

        const shelves =
            result.data || [];

        select.innerHTML = `
            <option value="">
                -- Pilih Rak --
            </option>
        `;

        shelves.forEach(
            shelf => {

                const option =
                    document.createElement(
                        "option"
                    );

                option.value =
                    shelf.id_shelf;

                let label =
                    "Rak " +
                    (shelf.no_rak || "-");

                if (
                    shelf.lantai !== null &&
                    shelf.lantai !== undefined &&
                    shelf.lantai !== ""
                ) {

                    label +=
                        " • Lantai " +
                        shelf.lantai;

                }

                option.textContent =
                    label;

                select.appendChild(option);

            }
        );

    } catch (error) {

        console.error(
            "Rak:",
            error
        );

        select.innerHTML = `
            <option value="">
                Gagal memuat rak
            </option>
        `;

    }
}


/* =========================
   PHOTO
========================= */

function showPhoto(path) {

    if (!path) {

        photoPreview.innerHTML = `
            <div class="photo-empty">
                Belum ada foto
            </div>
        `;

        return;
    }

    let imageUrl = path;

    /*
     * Database menyimpan:
     * uploads/books/nama_file.jpg
     *
     * Karena halaman berada di:
     * frontend/admin/books/
     *
     * maka perlu ../../../
     */

    if (
        !path.startsWith("http://") &&
        !path.startsWith("https://")
    ) {

        imageUrl =
            "../../../" +
            path.replace(/^\/+/, "");

    }

    photoPreview.innerHTML = `
        <img
            src="${escapeHtml(imageUrl)}"
            alt="Foto Buku"
            onerror="this.parentElement.innerHTML='<div class=\\'photo-empty\\'>Foto tidak dapat dimuat</div>'"
        >
    `;
}


/* =========================
   LOAD BOOK
========================= */

async function loadBook() {

    try {

        const result =
            await getJson(
                BOOK_DETAIL_API +
                "?id=" +
                encodeURIComponent(BOOK_ID)
            );

        let data =
            result.data || {};

        /*
         * Antisipasi jika API detail
         * membungkus data buku.
         */

        const book =
            data.buku ||
            data.book ||
            data;

        document.getElementById(
            "isbn"
        ).value =
            book.isbn || "";

        document.getElementById(
            "judul"
        ).value =
            book.judul || "";

        document.getElementById(
            "pengarang"
        ).value =
            book.pengarang || "";

        document.getElementById(
            "penerbit"
        ).value =
            book.penerbit || "";

        document.getElementById(
            "tahun_terbit"
        ).value =
            book.tahun_terbit || "";

        document.getElementById(
            "jenis_buku"
        ).value =
            book.jenis_buku || "";

        document.getElementById(
            "harga"
        ).value =
            book.harga ?? "";

        document.getElementById(
            "barcode"
        ).value =
            book.barcode || "";

        document.getElementById(
            "sinopsis"
        ).value =
            book.sinopsis || "";

        document.getElementById(
            "status"
        ).value =
            book.status || "tersedia";


        /*
         * ID category.
         */

        const categoryId =
            book.id_category ??
            book.category?.id_category ??
            "";

        document.getElementById(
            "id_category"
        ).value =
            categoryId;


        /*
         * ID shelf.
         */

        const shelfId =
            book.id_shelf ??
            book.shelf?.id_shelf ??
            "";

        document.getElementById(
            "id_shelf"
        ).value =
            shelfId;


        /*
         * Foto.
         */

        showPhoto(
            book.foto || ""
        );


        loading.style.display =
            "none";

        form.style.display =
            "block";

    } catch (error) {

        console.error(error);

        loading.innerHTML = `
            <div style="color:#b91c1c;">
                Gagal memuat data buku.
                <br><br>
                ${escapeHtml(error.message)}
                <br><br>
                <a href="./index.php">
                    Kembali ke daftar buku
                </a>
            </div>
        `;

    }

}


/* =========================
   PREVIEW FOTO BARU
========================= */

fotoInput.addEventListener(
    "change",
    function () {

        const file =
            this.files[0];

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

        const allowed =
            [
                "image/jpeg",
                "image/png",
                "image/webp"
            ];

        if (
            !allowed.includes(
                file.type
            )
        ) {

            showError(
                "Format foto harus JPG, PNG, atau WEBP."
            );

            this.value = "";

            return;

        }

        const reader =
            new FileReader();

        reader.onload =
            function (event) {

                photoPreview.innerHTML = `
                    <img
                        src="${event.target.result}"
                        alt="Preview Foto Buku"
                    >
                `;

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

        errorBox.style.display =
            "none";

        successBox.style.display =
            "none";


        const judul =
            document.getElementById(
                "judul"
            ).value.trim();

        const pengarang =
            document.getElementById(
                "pengarang"
            ).value.trim();

        const harga =
            document.getElementById(
                "harga"
            ).value.trim();

        const tahun =
            document.getElementById(
                "tahun_terbit"
            ).value.trim();

        const barcode =
            document.getElementById(
                "barcode"
            ).value.trim();


        /* =====================
           FRONTEND VALIDATION
        ===================== */

        if (!judul) {

            showError(
                "Judul buku wajib diisi."
            );

            return;

        }

        if (!pengarang) {

            showError(
                "Pengarang wajib diisi."
            );

            return;

        }

        if (!harga) {

            showError(
                "Harga buku wajib diisi."
            );

            return;

        }

        if (
            tahun &&
            !/^\d{4}$/.test(tahun)
        ) {

            showError(
                "Tahun terbit harus berupa 4 digit angka."
            );

            return;

        }

        if (
            barcode &&
            !/^[A-Za-z0-9-]+$/.test(
                barcode
            )
        ) {

            showError(
                "Barcode hanya boleh berisi huruf, angka, dan tanda hubung."
            );

            return;

        }


        /* =====================
           FORM DATA
        ===================== */

        const formData =
            new FormData();

        formData.append(
            "id_book",
            BOOK_ID
        );

        formData.append(
            "isbn",
            document.getElementById(
                "isbn"
            ).value.trim()
        );

        formData.append(
            "judul",
            judul
        );

        formData.append(
            "pengarang",
            pengarang
        );

        formData.append(
            "penerbit",
            document.getElementById(
                "penerbit"
            ).value.trim()
        );

        formData.append(
            "tahun_terbit",
            tahun
        );

        formData.append(
            "id_category",
            document.getElementById(
                "id_category"
            ).value
        );

        formData.append(
            "id_shelf",
            document.getElementById(
                "id_shelf"
            ).value
        );

        formData.append(
            "jenis_buku",
            document.getElementById(
                "jenis_buku"
            ).value.trim()
        );

        formData.append(
            "sinopsis",
            document.getElementById(
                "sinopsis"
            ).value.trim()
        );

        formData.append(
            "harga",
            harga
        );

        formData.append(
            "barcode",
            barcode
        );

        formData.append(
            "status",
            document.getElementById(
                "status"
            ).value
        );


        /*
         * Foto hanya dikirim jika
         * admin memilih foto baru.
         */

        if (
            fotoInput.files.length > 0
        ) {

            formData.append(
                "foto",
                fotoInput.files[0]
            );

        }


        /* =====================
           SEND
        ===================== */

        submitButton.disabled =
            true;

        submitButton.textContent =
            "Menyimpan...";


        try {

            const response =
                await fetch(
                    UPDATE_API,
                    {
                        method: "POST",
                        credentials: "include",
                        body: formData
                    }
                );

            const result =
                await response.json();


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal memperbarui buku."
                );

            }


            showSuccess(
                result.message ||
                "Buku berhasil diperbarui."
            );


            /*
             * Tunggu sebentar lalu
             * kembali ke daftar buku.
             */

            setTimeout(
                function () {

                    window.location.href =
                        "./index.php";

                },
                800
            );


        } catch (error) {

            console.error(error);

            showError(
                error.message ||
                "Terjadi kesalahan saat memperbarui buku."
            );

            submitButton.disabled =
                false;

            submitButton.textContent =
                "Simpan Perubahan";

        }

    }
);


/* =========================
   INITIAL LOAD
========================= */

async function init() {

    try {

        /*
         * Load dropdown dahulu.
         */

        await Promise.all([
            loadCategories(),
            loadShelves()
        ]);


        /*
         * Setelah dropdown tersedia,
         * baru load data buku.
         */

        await loadBook();

    } catch (error) {

        console.error(error);

        showError(
            "Gagal menyiapkan halaman edit buku."
        );

    }

}

init();

</script>

</body>
</html>