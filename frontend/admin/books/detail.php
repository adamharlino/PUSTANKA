<?php
session_start();

if (
    !isset($_SESSION["id_admin"]) ||
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
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Detail Buku - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1e293b;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 0;
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 27px;
            color: #0f172a;
        }

        .topbar p {
            color: #64748b;
            margin-top: 5px;
            font-size: 14px;
        }

        .admin-name {
            font-weight: bold;
            color: #1e40af;
        }

        /* =========================
           DETAIL CARD
        ========================= */

        .detail-card {
            background: white;
            border-radius: 18px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .07);
            overflow: hidden;
        }

        .detail-header {
            padding: 22px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .detail-header h2 {
            font-size: 19px;
            color: #0f172a;
        }

        .detail-body {
            padding: 30px;
        }

        /* =========================
           BOOK TOP
        ========================= */

        .book-top {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 35px;
            margin-bottom: 30px;
        }

        .book-photo {
            width: 100%;
            height: 330px;
            border-radius: 14px;
            background: #e2e8f0;
            object-fit: cover;
            border: 1px solid #e2e8f0;
        }

        .book-main h2 {
            font-size: 27px;
            color: #0f172a;
            line-height: 1.3;
            margin-bottom: 10px;
        }

        .author {
            color: #64748b;
            font-size: 15px;
            margin-bottom: 15px;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .status-available {
            background: #dcfce7;
            color: #166534;
        }

        .status-borrowed {
            background: #fef3c7;
            color: #92400e;
        }

        .status-other {
            background: #e2e8f0;
            color: #475569;
        }

        /* =========================
           INFO GRID
        ========================= */

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .info-item {
            padding: 15px;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item:nth-child(odd) {
            border-right: 1px solid #e2e8f0;
        }

        .label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            color: #94a3b8;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .value {
            font-size: 14px;
            color: #334155;
            font-weight: 600;
        }

        /* =========================
           SINOPSIS
        ========================= */

        .sinopsis {
            margin-top: 25px;
            padding: 20px;
            border-radius: 12px;
            background: #f8fafc;
        }

        .sinopsis h3 {
            margin-bottom: 10px;
            color: #0f172a;
            font-size: 16px;
        }

        .sinopsis p {
            color: #64748b;
            line-height: 1.7;
            font-size: 14px;
        }

        /* =========================
           ACTION
        ========================= */

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 25px;
        }

        .btn {
            padding: 11px 18px;
            border-radius: 9px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            text-align: center;
            padding: 60px;
            color: #64748b;
        }

        .error-box {
            display: none;
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
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

            .book-top {
                grid-template-columns: 180px 1fr;
            }

            .book-photo {
                height: 250px;
            }
        }

        @media (max-width: 650px) {

            .main {
                margin-left: 0;
                padding: 15px;
            }

            .topbar {
                display: block;
            }

            .admin-name {
                display: block;
                margin-top: 10px;
            }

            .book-top {
                grid-template-columns: 1fr;
            }

            .book-photo {
                height: 300px;
                max-width: 250px;
                margin: auto;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .info-item:nth-child(odd) {
                border-right: none;
            }

            .actions {
                flex-direction: column;
            }

            .actions .btn {
                width: 100%;
                text-align: center;
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
            <h1>Detail Buku</h1>
            <p>
                Informasi lengkap data buku
            </p>
        </div>

        <div>
            Admin:
            <span class="admin-name">
                <?= htmlspecialchars($_SESSION["nama"] ?? "Admin") ?>
            </span>
        </div>

    </div>


    <div
        id="errorBox"
        class="error-box"
    ></div>


    <section class="detail-card">

        <div class="detail-header">

            <h2>
                Informasi Buku
            </h2>

        </div>


        <div
            id="loading"
            class="loading"
        >
            Memuat detail buku...
        </div>


        <div
            id="detailBody"
            class="detail-body"
            style="display:none;"
        >

            <div class="book-top">

                <img
                    id="bookPhoto"
                    class="book-photo"
                    src=""
                    alt="Foto Buku"
                >

                <div class="book-main">

                    <h2 id="bookTitle">
                        -
                    </h2>

                    <p
                        id="bookAuthor"
                        class="author"
                    >
                        -
                    </p>

                    <span
                        id="bookStatus"
                        class="status"
                    >
                        -
                    </span>


                    <div class="info-grid">

                        <div class="info-item">
                            <span class="label">
                                ID Buku
                            </span>

                            <span
                                class="value"
                                id="bookId"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                ISBN
                            </span>

                            <span
                                class="value"
                                id="bookIsbn"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Barcode
                            </span>

                            <span
                                class="value"
                                id="bookBarcode"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Penerbit
                            </span>

                            <span
                                class="value"
                                id="bookPublisher"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Tahun Terbit
                            </span>

                            <span
                                class="value"
                                id="bookYear"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Jenis Buku
                            </span>

                            <span
                                class="value"
                                id="bookType"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Kategori
                            </span>

                            <span
                                class="value"
                                id="bookCategory"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Harga
                            </span>

                            <span
                                class="value"
                                id="bookPrice"
                            >
                                -
                            </span>
                        </div>


                        <div class="info-item">
                            <span class="label">
                                Lokasi Rak
                            </span>

                            <span
                                class="value"
                                id="bookShelf"
                            >
                                -
                            </span>
                        </div>

                    </div>

                </div>

            </div>


            <div class="sinopsis">

                <h3>
                    Sinopsis
                </h3>

                <p id="bookSynopsis">
                    Sinopsis belum tersedia.
                </p>

            </div>


            <div class="actions">

                <a
                    href="./"
                    class="btn btn-secondary"
                >
                    ← Kembali
                </a>

                <button
                    id="editBtn"
                    class="btn btn-primary"
                >
                    Edit Buku
                </button>

            </div>

        </div>

    </section>

</main>


<script>

const API_URL =
    "../../../api/books/detail.php";

const params =
    new URLSearchParams(window.location.search);

const idBook =
    params.get("id");


const loading =
    document.getElementById("loading");

const detailBody =
    document.getElementById("detailBody");

const errorBox =
    document.getElementById("errorBox");


/* =========================
   ESCAPE
========================= */

function escapeText(value) {

    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return "-";
    }

    return String(value);
}


/* =========================
   RUPIAH
========================= */

function formatRupiah(value) {

    const number =
        Number(value);

    if (Number.isNaN(number)) {
        return "-";
    }

    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }
    ).format(number);
}


/* =========================
   PHOTO
========================= */

function getPhoto(value) {

    if (!value) {

        return "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";

    }

    /*
     * Backend menyimpan path foto.
     * Jika sudah berupa URL, gunakan langsung.
     * Jika berupa path relatif, tambahkan root project.
     */

    if (
        value.startsWith("http://") ||
        value.startsWith("https://")
    ) {
        return value;
    }

    return "../../../" + value;
}


/* =========================
   LOAD DETAIL
========================= */

async function loadDetail() {

    if (!idBook) {

        showError(
            "ID buku tidak ditemukan."
        );

        return;
    }


    try {

        const response =
            await fetch(
                `${API_URL}?id=${encodeURIComponent(idBook)}`,
                {
                    method: "GET",
                    credentials: "include"
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
                "Gagal mengambil detail buku."
            );

        }


        renderDetail(
            result.data
        );


    } catch (error) {

        console.error(error);

        showError(
            error.message
        );

    }

}


/* =========================
   RENDER DETAIL
========================= */

function renderDetail(book) {

    if (!book) {

        showError(
            "Data buku tidak ditemukan."
        );

        return;
    }


    document.getElementById("bookId").textContent =
        escapeText(book.id_book);

    document.getElementById("bookTitle").textContent =
        escapeText(book.judul);

    document.getElementById("bookAuthor").textContent =
        escapeText(book.pengarang);

    document.getElementById("bookIsbn").textContent =
        escapeText(book.isbn);

    document.getElementById("bookBarcode").textContent =
        escapeText(book.barcode);

    document.getElementById("bookPublisher").textContent =
        escapeText(book.penerbit);

    document.getElementById("bookYear").textContent =
        escapeText(book.tahun_terbit);

    document.getElementById("bookType").textContent =
        escapeText(book.jenis_buku);


    /* KATEGORI */

    document.getElementById("bookCategory").textContent =
        escapeText(
            book.kategori?.nama_category
        );


    /* RAK */

    let shelf = "-";

    if (book.rak) {

        const noRak =
            book.rak.no_rak || "";

        const lantai =
            book.rak.lantai || "";

        if (noRak && lantai) {

            shelf =
                `Rak ${noRak} • Lantai ${lantai}`;

        } else if (noRak) {

            shelf =
                `Rak ${noRak}`;

        } else if (lantai) {

            shelf =
                `Lantai ${lantai}`;

        }

    }

    document.getElementById("bookShelf").textContent =
        shelf;


    /* HARGA */

    document.getElementById("bookPrice").textContent =
        formatRupiah(book.harga);


    /* SINOPSIS */

    document.getElementById("bookSynopsis").textContent =
        escapeText(book.sinopsis);


    /* STATUS */

    const statusElement =
        document.getElementById("bookStatus");

    const status =
        book.status || "-";

    statusElement.textContent =
        status;

    statusElement.className =
        "status";


    if (
        status.toLowerCase() ===
        "tersedia"
    ) {

        statusElement.classList.add(
            "status-available"
        );

    } else if (
        status.toLowerCase() ===
        "dipinjam"
    ) {

        statusElement.classList.add(
            "status-borrowed"
        );

    } else {

        statusElement.classList.add(
            "status-other"
        );

    }


    /* PHOTO */

    const photo =
        document.getElementById("bookPhoto");

    photo.src =
        getPhoto(book.foto);

    photo.onerror = function () {

        this.src =
            "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";

    };


    /* EDIT */

    document.getElementById("editBtn").onclick =
        function () {

            window.location.href =
                `./edit.php?id=${encodeURIComponent(book.id_book)}`;

        };


    loading.style.display =
        "none";

    detailBody.style.display =
        "block";
}


/* =========================
   ERROR
========================= */

function showError(message) {

    loading.style.display =
        "none";

    detailBody.style.display =
        "none";

    errorBox.textContent =
        message;

    errorBox.style.display =
        "block";
}


/* =========================
   LOGOUT
========================= */

async function logout() {

    try {

        await fetch(
            "../../../api/auth/logout.php",
            {
                method: "POST",
                credentials: "include"
            }
        );

    } finally {

        window.location.href =
            "../../auth/login.php";

    }

}


/* =========================
   START
========================= */

loadDetail();

</script>

</body>
</html>