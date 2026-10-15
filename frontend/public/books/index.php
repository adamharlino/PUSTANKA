<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog Buku - Pustanka</title>

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
            gap: 20px;
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
            gap: 10px;
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
            background: #164e8a !important;
            color: white !important;
        }

        .login-btn:hover {
            background: #123e6e !important;
        }

        /* =========================
           HERO
        ========================= */

        .hero {
            padding: 45px 0 30px;
        }

        .hero h1 {
            color: #132f55;
            font-size: 34px;
            margin-bottom: 10px;
        }

        .hero p {
            color: #65748b;
            font-size: 16px;
            line-height: 1.6;
            max-width: 720px;
        }

        /* =========================
           SEARCH
        ========================= */

        .filter-card {
            background: #ffffff;
            border-radius: 18px;
            padding: 20px;
            border: 1px solid #e7ebf2;
            box-shadow: 0 8px 25px rgba(25, 48, 82, .05);
            margin-bottom: 28px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr repeat(4, minmax(0, 1fr)) auto;
            gap: 12px;
            align-items: end;
        }

        .field label {
            display: block;
            font-size: 12px;
            font-weight: 700;
            color: #65748b;
            margin-bottom: 7px;
        }

        .field .form-control {
            width: 100%;
            min-width: 0;
        }

        .form-control {
            width: 100%;
            height: 44px;
            border: 1px solid #d9e3f0;
            border-radius: 10px;
            padding: 0 13px;
            font-size: 14px;
            outline: none;
            background: white;
            color: #26374e;
        }

        .form-control:focus {
            border-color: #164e8a;
            box-shadow: 0 0 0 3px rgba(22, 78, 138, .08);
        }

        .search-btn {
            height: 44px;
            border: 0;
            border-radius: 10px;
            background: #164e8a;
            color: white;
            padding: 0 20px;
            font-weight: 700;
            cursor: pointer;
        }

        .search-btn:hover {
            background: #123e6e;
        }

        /* =========================
           RESULT HEADER
        ========================= */

        .result-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .result-title {
            color: #173b68;
            font-size: 20px;
            font-weight: 700;
        }

        .result-count {
            color: #78869a;
            font-size: 14px;
        }

        /* =========================
           BOOK GRID
        ========================= */

        .book-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #ffffff;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e7ebf2;
            box-shadow: 0 8px 25px rgba(25, 48, 82, .05);
            transition: .2s;
        }

        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(25, 48, 82, .10);
        }

        .book-cover-wrap {
            height: 300px;
            background: #eef3f8;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 12px;
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 10px;
        }

        .book-content {
            padding: 17px;
        }

        .book-title {
            color: #173b68;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.4;
            margin-bottom: 7px;

            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .book-author {
            color: #65748b;
            font-size: 13px;
            margin-bottom: 12px;

            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .book-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 14px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 8px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            background: #f1f5f9;
            color: #536273;
        }

        .badge.available {
            background: #e9f8ef;
            color: #16794c;
        }

        .badge.borrowed {
            background: #fff1e8;
            color: #b54708;
        }

        .book-button {
            display: block;
            width: 100%;
            text-align: center;
            text-decoration: none;
            border: 0;
            border-radius: 10px;
            padding: 11px 14px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            background: #164e8a;
            color: white;
            transition: .2s;
        }

        .book-button:hover {
            background: #123e6e;
        }

        .book-button.disabled {
            background: #e9eef5;
            color: #78869a;
            cursor: not-allowed;
        }

        /* =========================
           LOADING / EMPTY / ERROR
        ========================= */

        .loading,
        .empty,
        .error-box {
            background: #ffffff;
            border-radius: 18px;
            padding: 45px 20px;
            text-align: center;
            border: 1px solid #e7ebf2;
        }

        .loading {
            color: #65748b;
        }

        .empty {
            color: #65748b;
        }

        .error-box {
            color: #b42318;
            display: none;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin: 35px 0 50px;
            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 38px;
            height: 38px;
            border: 1px solid #d9e3f0;
            background: white;
            color: #536273;
            border-radius: 9px;
            cursor: pointer;
            font-weight: 600;
        }

        .page-btn:hover {
            background: #f1f6fc;
            color: #164e8a;
        }

        .page-btn.active {
            background: #164e8a;
            color: white;
            border-color: #164e8a;
        }

        .page-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {
            .book-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .filter-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .search-field {
                grid-column: span 3;
            }
        }

        @media (max-width: 700px) {
            .topbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                overflow-x: auto;
            }

            .hero h1 {
                font-size: 28px;
            }

            .book-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .book-cover-wrap {
                height: 250px;
            }
        }

        @media (max-width: 500px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }

            .search-field {
                grid-column: span 1;
            }
        }

        @media (max-width: 480px) {
            .book-grid {
                grid-template-columns: 1fr;
            }

            .book-cover-wrap {
                height: 330px;
            }

            .filter-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .search-field {
                grid-column: span 2;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="container topbar-inner">

        <a href="../../../index.php" class="brand">
            PUSTANKA
        </a>

        <nav class="nav-links">
            <a href="../../../index.php">
                Beranda
            </a>

            <a href="../../../index.php #fitur">
                Fitur
            </a>

            <a href="../../../index.php #tentang">
                Tentang
            </a>

            <a href="./index.php">
                Katalog Buku
            </a>

            <a href="../../auth/login.php" class="login-btn">
                Login
            </a>

            <a href="../../auth/register.php" class="register-btn">
                Register
            </a>
        </nav>

    </div>
</header>


<main>

    <section class="hero">
        <div class="container">

            <h1>Katalog Buku</h1>

            <p>
                Jelajahi koleksi buku Pustanka. Cari buku berdasarkan
                judul, pengarang, kategori, atau jenis buku.
            </p>

        </div>
    </section>


    <section>
        <div class="container">

            <div class="filter-card">

                <div class="filter-grid">

                    <div class="field search-field">
                        <label for="searchInput">Cari Judul Buku</label>
                        <input
                            type="text"
                            id="searchInput"
                            class="form-control"
                            placeholder="Ketik judul buku..."
                            autocomplete="off"
                        >
                    </div>

                    <div class="field">
                        <label for="categorySelect">Kategori</label>
                        <select id="categorySelect" class="form-control">
                            <option value="">Semua Kategori</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="shelfSelect">Rak</label>
                        <select id="shelfSelect" class="form-control">
                            <option value="">Semua Rak</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="typeSelect">Jenis Buku</label>
                        <select id="typeSelect" class="form-control">
                            <option value="">Semua Jenis</option>
                        </select>
                    </div>

                    <div class="field">
                        <label for="yearSelect">Tahun Terbit</label>
                        <select id="yearSelect" class="form-control">
                            <option value="">Semua Tahun</option>
                        </select>
                    </div>

                    <button
                        type="button"
                        id="resetButton"
                        class="search-btn"
                        style="background:#edf3f9;color:#164e8a;"
                    >
                        Reset
                    </button>

                </div>

            </div>


            <div class="result-header">

                <div class="result-title">
                    Koleksi Buku
                </div>

                <div
                    id="resultCount"
                    class="result-count"
                >
                    Memuat...
                </div>

            </div>


            <div
                id="errorBox"
                class="error-box"
            ></div>


            <div
                id="loading"
                class="loading"
            >
                Memuat katalog buku...
            </div>


            <div
                id="emptyBox"
                class="empty"
                style="display:none;"
            >
                Buku tidak ditemukan.
            </div>


            <div
                id="bookGrid"
                class="book-grid"
                style="display:none;"
            ></div>


            <div
                id="pagination"
                class="pagination"
            ></div>

        </div>
    </section>

</main>


<script>

    /*
     * Karena file ini berada di:
     *
     * frontend/public/books/index.php
     *
     * maka untuk menuju api:
     *
     * ../../../api
     */

    const API_BASE = "../../../api";

    const searchInput = document.getElementById("searchInput");
    const categorySelect = document.getElementById("categorySelect");
    const shelfSelect = document.getElementById("shelfSelect");
    const typeSelect = document.getElementById("typeSelect");
    const yearSelect = document.getElementById("yearSelect");

    const bookGrid = document.getElementById("bookGrid");
    const loading = document.getElementById("loading");
    const emptyBox = document.getElementById("emptyBox");
    const errorBox = document.getElementById("errorBox");
    const resultCount = document.getElementById("resultCount");
    const pagination = document.getElementById("pagination");

    let currentPage = 1;
    const limit = 12;


    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return "-";
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function photo(value) {

        if (value) {
            return value;
        }

        return "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";
    }


    async function getJson(url) {

        const response = await fetch(url, {
            method: "GET",
            credentials: "include"
        });

        const result = await response.json();

        if (!response.ok || !result.status) {
            throw new Error(
                result.message ||
                "Gagal mengambil data."
            );
        }

        return result;
    }


    function showError(message) {

        loading.style.display = "none";
        bookGrid.style.display = "none";
        emptyBox.style.display = "none";

        errorBox.textContent = message;
        errorBox.style.display = "block";
    }


    function getBookData(result) {

        if (
            result.data &&
            Array.isArray(result.data)
        ) {
            return result.data;
        }

        if (
            result.data &&
            Array.isArray(result.data.books)
        ) {
            return result.data.books;
        }

        if (
            result.data &&
            Array.isArray(result.data.buku)
        ) {
            return result.data.buku;
        }

        if (
            Array.isArray(result.books)
        ) {
            return result.books;
        }

        if (
            Array.isArray(result.buku)
        ) {
            return result.buku;
        }

        return [];
    }


    function getPagination(result) {

        if (
            result.pagination
        ) {
            return result.pagination;
        }

        if (
            result.data &&
            result.data.pagination
        ) {
            return result.data.pagination;
        }

        return {};
    }


    function renderBooks(result) {

        const books = getBookData(result);
        const paginationData = getPagination(result);

        loading.style.display = "none";
        errorBox.style.display = "none";

        if (books.length === 0) {

            bookGrid.style.display = "none";
            emptyBox.style.display = "block";
            pagination.innerHTML = "";

            resultCount.textContent = "0 buku";

            return;
        }

        emptyBox.style.display = "none";
        bookGrid.style.display = "grid";

        const totalData = Number(
            paginationData.total_data ??
            paginationData.total ??
            books.length
        );

        resultCount.textContent = `${totalData} buku`;


        bookGrid.innerHTML = books.map(book => {

            const idBook = book.id_book;

            const status = String(
                book.status || ""
            ).toLowerCase();

            const available =
                status === "tersedia";

            const statusClass =
                available
                    ? "available"
                    : "borrowed";

            const statusText =
                book.status || "Tidak diketahui";


            /*
             * Detail publik.
             *
             * Nanti kita buat:
             *
             * frontend/public/books/detail.php
             */

            const detailUrl =
                "./detail.php?id_book=" +
                encodeURIComponent(idBook);


            /*
             * Pinjam tidak langsung melakukan
             * proses peminjaman.
             *
             * Guest diarahkan ke login.
             */

            const borrowUrl =
                "../../auth/login.php";


            return `
                <article class="book-card">

                    <div class="book-cover-wrap">

                        <img
                            class="book-cover"
                            src="${escapeHtml(photo(book.foto))}"
                            alt="Cover ${escapeHtml(book.judul || "Buku")}"
                            onerror="
                                this.src='https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600'
                            "
                        >

                    </div>


                    <div class="book-content">

                        <div class="book-title">
                            ${escapeHtml(book.judul)}
                        </div>


                        <div class="book-author">
                            Oleh ${escapeHtml(book.pengarang)}
                        </div>


                        <div class="book-meta">

                            <span class="badge ${statusClass}">
                                ${escapeHtml(statusText)}
                            </span>

                            ${
                                book.jenis_buku
                                    ? `
                                        <span class="badge">
                                            ${escapeHtml(book.jenis_buku)}
                                        </span>
                                    `
                                    : ""
                            }

                        </div>


                        <a
                            href="${detailUrl}"
                            class="book-button"
                        >
                            Lihat Detail →
                        </a>


                        ${
                            available
                                ? `
                                    <a
                                        href="${borrowUrl}"
                                        class="book-button"
                                        style="margin-top:8px;"
                                    >
                                        Pinjam Buku
                                    </a>
                                `
                                : `
                                    <span
                                        class="book-button disabled"
                                        style="margin-top:8px;"
                                    >
                                        Buku Tidak Tersedia
                                    </span>
                                `
                        }

                    </div>

                </article>
            `;

        }).join("");


        renderPagination(paginationData);
    }


    function renderPagination(data) {

        // API books/list.php menggunakan nama total_page (singular).
        // Tetap dukung beberapa format lama sebagai fallback.
        const totalPages = Number(
            data.total_page ||
            data.total_pages ||
            data.totalPages ||
            data.last_page ||
            1
        );

        const page = Number(
            data.page ||
            currentPage
        );

        if (totalPages <= 1) {
            pagination.innerHTML = "";
            return;
        }


        let html = "";


        html += `
            <button
                class="page-btn"
                ${page <= 1 ? "disabled" : ""}
                onclick="changePage(${page - 1})"
            >
                ‹
            </button>
        `;


        const start = Math.max(
            1,
            page - 2
        );

        const end = Math.min(
            totalPages,
            page + 2
        );


        for (
            let i = start;
            i <= end;
            i++
        ) {

            html += `
                <button
                    class="page-btn ${i === page ? "active" : ""}"
                    onclick="changePage(${i})"
                >
                    ${i}
                </button>
            `;
        }


        html += `
            <button
                class="page-btn"
                ${page >= totalPages ? "disabled" : ""}
                onclick="changePage(${page + 1})"
            >
                ›
            </button>
        `;


        pagination.innerHTML = html;
    }


    async function loadBooks(page = 1) {

        currentPage = page;

        loading.style.display = "block";
        bookGrid.style.display = "none";
        emptyBox.style.display = "none";
        errorBox.style.display = "none";

        resultCount.textContent = "Memuat...";


        const params =
            new URLSearchParams();


        const search =
            searchInput.value.trim();

        const category =
            categorySelect.value;

        const shelf =
            shelfSelect.value;

        const type =
            typeSelect.value;

        const year =
            yearSelect.value;


        if (search) {
            params.set(
                "search",
                search
            );
        }

        if (category) {
            params.set(
                "id_category",
                category
            );
        }

        if (shelf) {
            params.set(
                "id_shelf",
                shelf
            );
        }

        if (type) {
            params.set(
                "jenis_buku",
                type
            );
        }

        if (year) {
            params.set(
                "tahun_terbit",
                year
            );
        }


        params.set(
            "page",
            page
        );

        params.set(
            "limit",
            limit
        );


        try {

            const result =
                await getJson(
                    API_BASE +
                    "/books/list.php?" +
                    params.toString()
                );


            renderBooks(result);

        } catch (error) {

            console.error(error);

            showError(
                error.message ||
                "Katalog buku gagal dimuat."
            );

        }
    }


    async function loadCategories() {

        try {

            /*
             * Kita coba menggunakan endpoint
             * kategori yang sudah ada.
             */

            const result =
                await getJson(
                    API_BASE +
                    "/books/categories.php"
                );


            const items =
                Array.isArray(result.data)
                    ? result.data
                    : (
                        result.data &&
                        Array.isArray(result.data.categories)
                            ? result.data.categories
                            : []
                    );


            items.forEach(item => {

                const option =
                    document.createElement("option");

                option.value =
                    item.id_category ||
                    item.id ||
                    "";

                option.textContent =
                    item.nama_category ||
                    item.nama ||
                    item.name ||
                    "Kategori";

                categorySelect.appendChild(
                    option
                );

            });

        } catch (error) {

            /*
             * Kalau endpoint kategori
             * berbeda/tidak tersedia,
             * katalog tetap bisa digunakan
             * berdasarkan pencarian.
             */

            console.warn(
                "Kategori tidak dapat dimuat:",
                error
            );
        }
    }


    async function loadShelves() {
    const result = await getJson(
        API_BASE + "/books/shelves.php"
    );

    shelfSelect.innerHTML =
        '<option value="">Semua Rak</option>';

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
}


    async function loadYears() {
        try {
            const params = new URLSearchParams({ page: 1, limit: 100 });
            const firstResult = await getJson(
                API_BASE + "/books/list.php?" + params.toString()
            );

            let allBooks = [...getBookData(firstResult)];
            const paginationData = getPagination(firstResult);
            const totalPages = Number(
                paginationData.total_page || paginationData.total_pages ||
                paginationData.totalPages || paginationData.last_page || 1
            );

            for (let page = 2; page <= totalPages; page++) {
                const pageParams = new URLSearchParams({ page, limit: 100 });
                const result = await getJson(
                    API_BASE + "/books/list.php?" + pageParams.toString()
                );
                allBooks = allBooks.concat(getBookData(result));
            }

            const years = [...new Set(allBooks
                .map(book => String(book.tahun_terbit ?? "").trim())
                .filter(Boolean))]
                .sort((a, b) => Number(b) - Number(a));

            years.forEach(year => {
                const option = document.createElement("option");
                option.value = year;
                option.textContent = year;
                yearSelect.appendChild(option);
            });
        } catch (error) {
            console.warn("Tahun terbit tidak dapat dimuat:", error);
        }
    }


    async function loadTypes() {

        /*
         * Ambil semua halaman data buku, lalu kumpulkan
         * jenis_buku yang unik. Mengambil satu halaman saja
         * dapat membuat dropdown hanya berisi satu jenis.
         */

        try {

            const firstParams = new URLSearchParams({
                page: 1,
                limit: 100
            });

            const firstResult = await getJson(
                API_BASE + "/books/list.php?" + firstParams.toString()
            );

            let allBooks = [...getBookData(firstResult)];
            const firstPagination = getPagination(firstResult);
            const totalPages = Number(
                firstPagination.total_page ||
                firstPagination.total_pages ||
                firstPagination.totalPages ||
                firstPagination.last_page ||
                1
            );

            for (let page = 2; page <= totalPages; page++) {
                const params = new URLSearchParams({
                    page: page,
                    limit: 100
                });

                const result = await getJson(
                    API_BASE + "/books/list.php?" + params.toString()
                );

                allBooks = allBooks.concat(getBookData(result));
            }

            const types = [...new Set(
                allBooks
                    .map(book => String(book.jenis_buku || "").trim())
                    .filter(Boolean)
            )].sort((a, b) => a.localeCompare(b, "id"));

            types.forEach(type => {
                const option = document.createElement("option");
                option.value = type;
                option.textContent = type;
                typeSelect.appendChild(option);
            });

        } catch (error) {
            console.warn("Jenis buku tidak dapat dimuat:", error);
        }
    }


    function changePage(page) {

        if (page < 1) {
            return;
        }

        loadBooks(page);

        window.scrollTo({
            top: 0,
            behavior: "smooth"
        });
    }


    let searchTimer = null;
    searchInput.addEventListener("input", function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadBooks(1), 350);
    });

    searchInput.addEventListener(
        "keydown",
        function(event) {

            if (event.key === "Enter") {
                loadBooks(1);
            }

        }
    );


    categorySelect.addEventListener(
        "change",
        () => loadBooks(1)
    );


    shelfSelect.addEventListener(
        "change",
        () => loadBooks(1)
    );

    typeSelect.addEventListener(
        "change",
        () => loadBooks(1)
    );

    yearSelect.addEventListener(
        "change",
        () => loadBooks(1)
    );

    // Reset semua filter katalog publik.
    const resetButton = document.getElementById("resetButton");
    if (resetButton) {
        resetButton.addEventListener("click", () => {
            searchInput.value = "";
            categorySelect.value = "";
            shelfSelect.value = "";
            typeSelect.value = "";
            yearSelect.value = "";
            loadBooks(1);
        });
    }

    /* Jalankan katalog. */
    loadCategories();
    loadShelves();
    loadTypes();
    loadYears();
    loadBooks();

</script>

</body>
</html>