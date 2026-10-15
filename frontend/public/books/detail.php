<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

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
            color: #172033;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        /* =========================
           NAVBAR
        ========================= */

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e7ebf2;
            padding: 18px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            color: #164e8a;
            font-size: 22px;
            font-weight: 800;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .nav-links a {
            text-decoration: none;
            color: #536273;
            font-size: 14px;
            font-weight: 600;
            padding: 9px 13px;
            border-radius: 9px;
            transition: .2s;
        }

        .nav-links a:hover {
            background: #f1f6fc;
            color: #164e8a;
        }

        .login-btn {
            background: #164e8a;
            color: white !important;
        }

        .login-btn:hover {
            background: #123e6e !important;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #164e8a;
            font-weight: 700;
            border: 1px solid #d9e3f0;
            background: #fff;
            padding: 10px 15px;
            border-radius: 10px;
        }

        .back-btn:hover {
            background: #f1f6fc;
        }

        /* =========================
           MAIN
        ========================= */

        main {
            padding: 35px 0 55px;
        }

        .loading,
        .error-box {
            background: #fff;
            border-radius: 18px;
            padding: 45px;
            text-align: center;
            box-shadow: 0 8px 28px rgba(25, 48, 82, .07);
        }

        .loading {
            color: #65748b;
        }

        .error-box {
            color: #b42318;
            display: none;
        }

        /* =========================
           DETAIL
        ========================= */

        .detail-card {
            background: #ffffff;
            border-radius: 22px;
            box-shadow: 0 10px 35px rgba(25, 48, 82, .08);
            padding: 30px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 330px 1fr;
            gap: 38px;
        }

        .cover-wrap {
            background: #eef3f8;
            border-radius: 18px;
            padding: 16px;
            min-height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover {
            width: 100%;
            max-width: 300px;
            height: 420px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .12);
            background: #e9eef5;
        }

        .book-content h1 {
            color: #132f55;
            font-size: 34px;
            line-height: 1.25;
            margin-bottom: 12px;
        }

        .author {
            color: #65748b;
            font-size: 17px;
            margin-bottom: 18px;
        }

        .status {
            display: inline-flex;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 24px;
            background: #eef2f6;
            color: #536273;
        }

        .status.available {
            background: #e9f8ef;
            color: #16794c;
        }

        .status.borrowed {
            background: #fff1e8;
            color: #b54708;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .info-item {
            background: #f7f9fc;
            border: 1px solid #edf0f5;
            border-radius: 12px;
            padding: 13px 15px;
        }

        .label {
            display: block;
            color: #78869a;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            color: #26374e;
            font-size: 14px;
            font-weight: 600;
            word-break: break-word;
        }

        .rak-box {
            margin-top: 16px;
            background: #f1f6fc;
            border: 1px solid #dce9f7;
            border-radius: 14px;
            padding: 15px;
            color: #35516f;
            line-height: 1.6;
        }

        .sinopsis {
            border-top: 1px solid #e8edf3;
            padding-top: 24px;
            margin-top: 25px;
        }

        .sinopsis h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #173b68;
        }

        .sinopsis p {
            color: #5e6c7f;
            line-height: 1.75;
            white-space: pre-line;
        }

        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 11px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #164e8a;
            color: white;
        }

        .btn-primary:hover {
            background: #123e6e;
        }

        .btn-secondary {
            background: #edf3f9;
            color: #164e8a;
        }

        .btn-secondary:hover {
            background: #e2ebf5;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 850px) {

            .detail-grid {
                grid-template-columns: 1fr;
            }

            .cover-wrap {
                min-height: auto;
            }

            .cover {
                max-width: 260px;
                height: 360px;
            }
        }

        @media (max-width: 600px) {

            .container {
                width: min(100% - 20px, 1180px);
            }

            .topbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                overflow-x: auto;
            }

            main {
                padding-top: 20px;
            }

            .detail-card {
                padding: 18px;
                border-radius: 16px;
            }

            .book-content h1 {
                font-size: 26px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .cover {
                height: 330px;
            }
        }
    </style>
</head>

<body>

<header class="topbar">

    <div class="container topbar-inner">

        <a
            href="../../../index.php"
            class="brand"
        >
            PUSTANKA
        </a>

        <nav class="nav-links">

            <a href="../../../index.php">
                Beranda
            </a>

            <a href="./index.php">
                📚 Katalog
            </a>

            <a
                href="../../../auth/login.php"
                class="login-btn"
            >
                Login
            </a>

        </nav>

    </div>

</header>


<main>

    <div class="container">

        <a
            href="./index.php"
            class="back-btn"
            style="margin-bottom:20px;"
        >
            ← Kembali ke Katalog
        </a>


        <div
            id="loading"
            class="loading"
        >
            Memuat detail buku...
        </div>


        <div
            id="errorBox"
            class="error-box"
        ></div>


        <div
            id="detailCard"
            class="detail-card"
            style="display:none;"
        >

            <div class="detail-grid">

                <!-- COVER -->

                <div class="cover-wrap">

                    <img
                        id="bookCover"
                        class="cover"
                        alt="Cover buku"
                    >

                </div>


                <!-- CONTENT -->

                <div class="book-content">

                    <h1 id="bookTitle">
                        -
                    </h1>

                    <p
                        class="author"
                        id="bookAuthor"
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

                    </div>


                    <div class="rak-box">

                        <strong>
                            📍 Lokasi Buku
                        </strong>

                        <br>

                        <span id="bookShelf">
                            -
                        </span>

                    </div>


                    <div class="sinopsis">

                        <h2>
                            Sinopsis
                        </h2>

                        <p id="bookSynopsis">
                            Sinopsis belum tersedia.
                        </p>

                    </div>


                    <div class="actions">

                        <!--
                            PUBLIC:
                            tombol pinjam hanya menuju login.
                        -->

                        <a
                            id="borrowBtn"
                            href="../../auth/login.php"
                            class="btn btn-primary"
                        >
                            📚 Pinjam Buku
                        </a>


                        <a
                            href="./index.php"
                            class="btn btn-secondary"
                        >
                            Kembali ke Katalog
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>


<script>

    /*
     * Lokasi file:
     *
     * frontend/public/books/detail.php
     *
     * API:
     *
     * ../../../api
     */

    const API_BASE = "../../../api";


    const params =
        new URLSearchParams(
            window.location.search
        );


    const idBook =
        params.get("id_book");


    const loading =
        document.getElementById("loading");

    const errorBox =
        document.getElementById("errorBox");

    const detailCard =
        document.getElementById("detailCard");

    const borrowBtn =
        document.getElementById("borrowBtn");


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


    function photo(value) {

        if (value) {
            return value;
        }

        return "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";
    }


    function showError(text) {

        loading.style.display = "none";

        detailCard.style.display = "none";

        errorBox.textContent = text;

        errorBox.style.display = "block";
    }


    async function loadDetail() {

        if (
            !idBook ||
            !/^\d+$/.test(idBook) ||
            Number(idBook) <= 0
        ) {

            showError(
                "ID buku tidak valid."
            );

            return;
        }


        try {

            const response =
                await fetch(
                    API_BASE +
                    "/books/detail.php?id=" +
                    encodeURIComponent(idBook),
                    {
                        method: "GET",
                        credentials: "include"
                    }
                );


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.status ||
                !result.data
            ) {

                showError(
                    result.message ||
                    "Detail buku gagal dimuat."
                );

                return;
            }


            const book =
                result.data;


            /*
             * COVER
             */

            const bookCover =
                document.getElementById(
                    "bookCover"
                );


            bookCover.src =
                photo(book.foto);


            bookCover.onerror =
                function () {

                    this.src =
                        "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";
                };


            /*
             * INFORMASI UTAMA
             */

            document.getElementById(
                "bookTitle"
            ).textContent =
                escapeText(book.judul);


            document.getElementById(
                "bookAuthor"
            ).textContent =
                "Oleh " +
                escapeText(book.pengarang);


            document.getElementById(
                "bookIsbn"
            ).textContent =
                escapeText(book.isbn);


            document.getElementById(
                "bookBarcode"
            ).textContent =
                escapeText(book.barcode);


            document.getElementById(
                "bookPublisher"
            ).textContent =
                escapeText(book.penerbit);


            document.getElementById(
                "bookYear"
            ).textContent =
                escapeText(book.tahun_terbit);


            document.getElementById(
                "bookType"
            ).textContent =
                escapeText(book.jenis_buku);


            document.getElementById(
                "bookCategory"
            ).textContent =
                book.kategori?.nama_category ||
                "-";


            document.getElementById(
                "bookPrice"
            ).textContent =
                formatRupiah(book.harga);


            /*
             * LOKASI RAK
             */

            const rak =
                book.rak || {};


            const shelfText = [];


            if (rak.no_rak) {

                shelfText.push(
                    "Rak " +
                    rak.no_rak
                );

            }


            if (rak.lantai) {

                shelfText.push(
                    "Lantai " +
                    rak.lantai
                );

            }


            if (rak.keterangan) {

                shelfText.push(
                    rak.keterangan
                );

            }


            document.getElementById(
                "bookShelf"
            ).textContent =
                shelfText.length
                    ? shelfText.join(" • ")
                    : "Lokasi rak belum tersedia.";


            /*
             * SINOPSIS
             */

            document.getElementById(
                "bookSynopsis"
            ).textContent =
                book.sinopsis ||
                "Sinopsis belum tersedia.";


            /*
             * STATUS
             */

            const status =
                String(
                    book.status || ""
                ).toLowerCase();


            const statusElement =
                document.getElementById(
                    "bookStatus"
                );


            statusElement.textContent =
                book.status ||
                "Tidak diketahui";


            statusElement.className =
                "status";


            if (
                status === "tersedia"
            ) {

                statusElement.classList.add(
                    "available"
                );

                /*
                 * Buku tersedia:
                 * tombol pinjam menuju login.
                 */

                borrowBtn.style.display =
                    "inline-flex";

                borrowBtn.textContent =
                    "📚 Pinjam Buku";

            } else {

                statusElement.classList.add(
                    "borrowed"
                );

                /*
                 * Buku sedang dipinjam:
                 * tetap tidak bisa dipinjam.
                 */

                borrowBtn.style.display =
                    "none";
            }


            /*
             * Tampilkan halaman
             */

            loading.style.display =
                "none";

            errorBox.style.display =
                "none";

            detailCard.style.display =
                "block";

        } catch (error) {

            console.error(error);

            showError(
                "Tidak dapat terhubung ke server."
            );
        }

    }


    loadDetail();

</script>

</body>
</html>