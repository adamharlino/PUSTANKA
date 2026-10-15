<?php
session_start();

if (
    !isset($_SESSION["id_user"]) ||
    !is_numeric($_SESSION["id_user"]) ||
    (int) $_SESSION["id_user"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "user"
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
    <title>Buku - Pustanka</title>

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f7fc;
            color: #172033;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* =========================
           SIDEBAR STYLING
        ========================== */
        .sidebar {
            width: 260px;
            background-color: #0d3b66;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 0;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 0 20px 30px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 25px;
            color: #c0d1e3;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu li.active a, 
        .sidebar-menu li a:hover {
            background-color: #092c4e;
            color: #ffffff;
            border-left: 4px solid #3182ce;
        }

        /* =========================
           MAIN CONTENT & NAVBAR
        ========================== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .user-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d3b66;
        }

        .logout-btn {
            background-color: #e53e3e;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background-color: #c53030;
        }

        .container {
            width: min(1240px, calc(100% - 48px));
            margin: 0 auto;
        }

        main { padding: 30px 0 50px; }

        .page-title {
            margin-bottom: 20px;
        }

        .page-title h1 {
            color: #0d3b66;
            font-size: 26px;
            margin-bottom: 6px;
            font-weight: 700;
        }

        .page-title p {
            color: #718096;
            font-size: 14px;
        }

        .filter-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            margin-bottom: 24px;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 2fr repeat(4, 1fr) auto;
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

        .field input,
        .field select {
            width: 100%;
            height: 43px;
            border: 1px solid #dce3ec;
            border-radius: 10px;
            padding: 0 12px;
            color: #25364d;
            background: #fff;
            outline: none;
        }

        .field input:focus,
        .field select:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, .1);
        }

        .reset-btn {
            height: 43px;
            border: 0;
            border-radius: 10px;
            padding: 0 15px;
            background: #edf3f9;
            color: #0d3b66;
            font-weight: 800;
            cursor: pointer;
            white-space: nowrap;
        }

        .reset-btn:hover { background: #dfeaf5; }

        .result-info {
            color: #65748b;
            font-size: 14px;
            margin-bottom: 15px;
        }

        .loading,
        .empty,
        .error {
            background: #fff;
            border-radius: 16px;
            padding: 38px;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            border: 1px solid #e2e8f0;
        }

        .error { color: #e53e3e; }
        .empty { color: #718096; }

        .books-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
        }

        .book-card {
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform .18s ease, box-shadow .18s ease;
            display: flex;
            flex-direction: column;
        }

        .book-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(13, 59, 102, 0.08);
        }

        .cover-wrap {
            height: 285px;
            background: #edf2f7;
            position: relative;
            overflow: hidden;
        }

        .cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .status {
            position: absolute;
            top: 12px;
            right: 12px;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 11px;
            font-weight: 800;
            background: rgba(255,255,255,.94);
            color: #536273;
        }

        .status.available {
            color: #16794c;
            background: #e9f8ef;
        }

        .status.borrowed {
            color: #b54708;
            background: #fff1e8;
        }

        .book-body {
            padding: 16px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }

        .book-title {
            color: #0d3b66;
            font-size: 17px;
            line-height: 1.35;
            font-weight: 800;
            margin-bottom: 7px;
        }

        .book-author {
            color: #66758a;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .book-meta {
            color: #8591a1;
            font-size: 12px;
            line-height: 1.55;
            min-height: 38px;
        }

        .detail-btn {
            margin-top: 15px;
            text-decoration: none;
            background: #0d3b66;
            color: #fff;
            text-align: center;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 13px;
            font-weight: 800;
        }

        .detail-btn:hover { background: #092c4e; }

        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 1px solid #dce3ec;
            border-radius: 9px;
            background: #fff;
            color: #36506f;
            cursor: pointer;
            font-weight: 700;
        }

        .page-btn:hover:not(:disabled) { background: #edf3f9; }

        .page-btn.active {
            background: #0d3b66;
            color: #fff;
            border-color: #0d3b66;
        }

        .page-btn:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        @media (max-width: 1100px) {
            .filter-grid {
                grid-template-columns: repeat(3, 1fr);
            }

            .search-field { grid-column: span 3; }

            .books-grid {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        @media (max-width: 750px) {
            body {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                overflow: visible;
            }

            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .search-field { grid-column: span 2; }

            .books-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 500px) {
            .container {
                width: min(100% - 20px, 1240px);
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .search-field { grid-column: span 1; }

            .books-grid {
                grid-template-columns: 1fr;
            }

            .page-title h1 { font-size: 22px; }

            .cover-wrap { height: 330px; }
        }
    </style>
</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <h2>PUSTANKA</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="../dashboard/index.php"> Dashboard</a></li>
                <li class="active"><a href="index.php"> Katalog Buku</a></li>
                <li><a href="../loans/"> Peminjaman Buku</a></li>
                <li><a href="../payments/"> Pembayaran Denda</a></li>
                <li><a href="../attendance/"> Kehadiran</a></li>
                <li><a href="../profile/"> Profil</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR -->
        <div class="navbar">
            <div class="nav-right">
                <div class="user-name" id="navbarUserName">
                    Memuat...
                </div>
                <button class="logout-btn" onclick="logout()">
                    Logout
                </button>
            </div>
        </div>

        <main>
            <div class="container">

                <div class="page-title">
                    <h1>Katalog Buku</h1>
                    <p>Cari dan temukan buku yang tersedia di perpustakaan.</p>
                </div>

                <section class="filter-card">
                    <div class="filter-grid">

                        <div class="field search-field">
                            <label for="search">Cari Judul Buku</label>
                            <input
                                type="search"
                                id="search"
                                placeholder="Ketik judul buku..."
                                autocomplete="off"
                            >
                        </div>

                        <div class="field">
                            <label for="category">Kategori</label>
                            <select id="category">
                                <option value="">Semua Kategori</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="shelf">Rak</label>
                            <select id="shelf">
                                <option value="">Semua Rak</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="type">Jenis Buku</label>
                            <select id="type">
                                <option value="">Semua Jenis</option>
                            </select>
                        </div>

                        <div class="field">
                            <label for="year">Tahun Terbit</label>
                            <select id="year">
                                <option value="">Semua Tahun</option>
                            </select>
                        </div>

                        <button type="button" id="resetBtn" class="reset-btn">
                            Reset
                        </button>

                    </div>
                </section>

                <div id="resultInfo" class="result-info">Memuat buku...</div>

                <div id="loading" class="loading">
                    Memuat katalog buku...
                </div>

                <div id="errorBox" class="error" style="display:none;"></div>

                <div id="emptyBox" class="empty" style="display:none;">
                    Buku tidak ditemukan dengan filter yang dipilih.
                </div>

                <div id="booksGrid" class="books-grid"></div>

                <div id="pagination" class="pagination"></div>

            </div>
        </main>
    </div>

<script>
    const API_BASE = "../../../api";
    const LOGOUT_URL = API_BASE + "/auth/logout.php";
    const LIMIT = 16;

    let currentPage = 1;
    let searchTimer = null;
    let filterRequestId = 0;

    const searchInput = document.getElementById("search");
    const categorySelect = document.getElementById("category");
    const shelfSelect = document.getElementById("shelf");
    const typeSelect = document.getElementById("type");
    const yearSelect = document.getElementById("year");
    const resetBtn = document.getElementById("resetBtn");

    const loading = document.getElementById("loading");
    const errorBox = document.getElementById("errorBox");
    const emptyBox = document.getElementById("emptyBox");
    const booksGrid = document.getElementById("booksGrid");
    const pagination = document.getElementById("pagination");
    const resultInfo = document.getElementById("resultInfo");

    function escapeHtml(value) {
        return String(value ?? "")
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

        return "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=0d3b66&size=600";
    }

    function formatRupiah(value) {
        const number = Number(value);

        if (Number.isNaN(number)) {
            return "-";
        }

        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }).format(number);
    }

    function showError(message) {
        loading.style.display = "none";
        errorBox.textContent = message;
        errorBox.style.display = "block";
    }

    function setLoading(isLoading) {
        loading.style.display = isLoading ? "block" : "none";
        if (isLoading) {
            errorBox.style.display = "none";
            emptyBox.style.display = "none";
        }
    }

    async function getJson(url) {
        const response = await fetch(url, {
            method: "GET",
            credentials: "include"
        });

        const result = await response.json();

        if (response.status === 401) {
            window.location.href = "../../auth/login.php";
            throw new Error("UNAUTHORIZED");
        }

        if (!response.ok || !result.status) {
            throw new Error(result.message || "Gagal mengambil data.");
        }

        return result;
    }

    async function loadUserData() {
        try {
            const result = await getJson(API_BASE + "/dashboard/user.php");
            const user = result.data?.user;
            if (user && user.nama) {
                document.getElementById("navbarUserName").textContent = user.nama;
            }
        } catch (e) {
            // Abaikan jika gagal memuat nama di navbar
        }
    }

    async function loadCategories() {
    const result = await getJson(
        API_BASE + "/books/categories.php"
    );

    categorySelect.innerHTML =
        '<option value="">Semua Kategori</option>';

    (result.data || []).forEach(category => {
        const option = document.createElement("option");

        option.value = category.id_category;
        option.textContent = category.nama_category;

        categorySelect.appendChild(option);
    });
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
    function addUniqueOption(select, value) {
        if (
            value === null ||
            value === undefined ||
            String(value).trim() === ""
        ) {
            return;
        }

        const stringValue = String(value);

        const exists = Array.from(select.options)
            .some(option => option.value === stringValue);

        if (!exists) {
            const option = document.createElement("option");
            option.value = stringValue;
            option.textContent = stringValue;
            select.appendChild(option);
        }
    }

    async function loadFilterValues() {
        const result = await getJson(
            API_BASE + "/books/list.php?page=1&limit=100"
        );

        typeSelect.innerHTML =
            '<option value="">Semua Jenis</option>';

        yearSelect.innerHTML =
            '<option value="">Semua Tahun</option>';

        (result.data || []).forEach(book => {
            addUniqueOption(typeSelect, book.jenis_buku);
            addUniqueOption(yearSelect, book.tahun_terbit);
        });

        const yearOptions = Array.from(yearSelect.options).slice(1);

        yearOptions.sort((a, b) =>
            Number(b.value) - Number(a.value)
        );

        yearSelect.innerHTML = '<option value="">Semua Tahun</option>';

        yearOptions.forEach(option => {
            yearSelect.appendChild(option);
        });
    }

    function buildBooksUrl(page) {
        const params = new URLSearchParams();

        params.set("page", page);
        params.set("limit", LIMIT);

        const search = searchInput.value.trim();
        const category = categorySelect.value;
        const shelf = shelfSelect.value;
        const type = typeSelect.value;
        const year = yearSelect.value;

        if (search) params.set("search", search);
        if (category) params.set("id_category", category);
        if (shelf) params.set("id_shelf", shelf);
        if (type) params.set("jenis_buku", type);
        if (year) params.set("tahun_terbit", year);

        return API_BASE + "/books/list.php?" + params.toString();
    }

    function renderBooks(books) {
        booksGrid.innerHTML = "";

        books.forEach(book => {
            const status = String(book.status || "").toLowerCase();

            const statusClass =
                status === "tersedia"
                    ? "available"
                    : "borrowed";

            const statusText =
                book.status || "Tidak diketahui";

            const category =
                book.kategori?.nama_category || "-";

            const year =
                book.tahun_terbit || "-";

            const card = document.createElement("article");
            card.className = "book-card";

            card.innerHTML = `
                <div class="cover-wrap">
                    <img
                        class="cover"
                        src="${escapeHtml(photo(book.foto))}"
                        alt="Cover ${escapeHtml(book.judul)}"
                    >
                    <span class="status ${statusClass}">
                        ${escapeHtml(statusText)}
                    </span>
                </div>

                <div class="book-body">
                    <div class="book-title">
                        ${escapeHtml(book.judul)}
                    </div>

                    <div class="book-author">
                        ${escapeHtml(book.pengarang || "-")}
                    </div>

                    <div class="book-meta">
                        ${escapeHtml(book.penerbit || "-")}<br>
                        ${escapeHtml(year)} • ${escapeHtml(category)}
                    </div>

                    <a
                        class="detail-btn"
                        href="./detail.php?id_book=${encodeURIComponent(book.id_book)}"
                    >
                        Lihat Detail
                    </a>
                </div>
            `;

            const image = card.querySelector(".cover");

            image.addEventListener("error", function () {
                this.src =
                    "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=0d3b66&size=600";
            });

            booksGrid.appendChild(card);
        });
    }

    function renderPagination(page, totalPage) {
        pagination.innerHTML = "";

        if (!totalPage || totalPage <= 1) {
            return;
        }

        const previous = document.createElement("button");
        previous.className = "page-btn";
        previous.textContent = "‹";
        previous.disabled = page <= 1;
        previous.addEventListener("click", () => loadBooks(page - 1));
        pagination.appendChild(previous);

        let start = Math.max(1, page - 2);
        let end = Math.min(totalPage, page + 2);

        if (page <= 3) {
            end = Math.min(totalPage, 5);
        }

        if (page >= totalPage - 2) {
            start = Math.max(1, totalPage - 4);
        }

        for (let i = start; i <= end; i++) {
            const button = document.createElement("button");
            button.className = "page-btn" + (i === page ? " active" : "");
            button.textContent = i;

            button.addEventListener("click", () => {
                if (i !== page) {
                    loadBooks(i);
                }
            });

            pagination.appendChild(button);
        }

        const next = document.createElement("button");
        next.className = "page-btn";
        next.textContent = "›";
        next.disabled = page >= totalPage;
        next.addEventListener("click", () => loadBooks(page + 1));
        pagination.appendChild(next);
    }

    async function loadBooks(page = 1) {
        const requestId = ++filterRequestId;

        currentPage = page;
        setLoading(true);
        booksGrid.innerHTML = "";
        pagination.innerHTML = "";

        try {
            const result = await getJson(buildBooksUrl(page));

            if (requestId !== filterRequestId) {
                return;
            }

            const books = result.data || [];
            const paginationData = result.pagination || {};

            loading.style.display = "none";

            if (!books.length) {
                emptyBox.style.display = "block";
                resultInfo.textContent = "0 buku ditemukan.";
                return;
            }

            emptyBox.style.display = "none";
            renderBooks(books);

            const total = Number(paginationData.total_data || 0);
            const currentLimit = Number(paginationData.limit || LIMIT);
            const current = Number(paginationData.page || page);
            const totalPage = Number(paginationData.total_page || 1);

            const from = ((current - 1) * currentLimit) + 1;
            const to = Math.min(current * currentLimit, total);

            resultInfo.textContent =
                `Menampilkan ${from}-${to} dari ${total} buku.`;

            renderPagination(current, totalPage);

        } catch (error) {
            if (error.message === "UNAUTHORIZED") {
                return;
            }

            console.error(error);
            showError(error.message || "Gagal mengambil data buku.");
        }
    }

    async function loadFilters() {
        setLoading(true);

        try {
            await Promise.all([
                loadUserData(),
                loadCategories(),
                loadShelves(),
                loadFilterValues()
            ]);

            await loadBooks(1);

        } catch (error) {
            if (error.message === "UNAUTHORIZED") {
                return;
            }

            console.error(error);
            showError(
                error.message ||
                "Gagal memuat pilihan filter."
            );
        }
    }

    function resetFilters() {
        searchInput.value = "";
        categorySelect.value = "";
        shelfSelect.value = "";
        typeSelect.value = "";
        yearSelect.value = "";

        loadBooks(1);
    }

    async function logout() {
        const confirmed = confirm("Yakin ingin logout?");
        if (!confirmed) return;

        try {
            await fetch(LOGOUT_URL, {
                method: "POST",
                credentials: "include"
            });
        } catch (error) {
            console.error(error);
        }

        window.location.href = "../../auth/login.php";
    }

    searchInput.addEventListener("input", () => {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            loadBooks(1);
        }, 400);
    });

    categorySelect.addEventListener("change", () => loadBooks(1));
    shelfSelect.addEventListener("change", () => loadBooks(1));
    typeSelect.addEventListener("change", () => loadBooks(1));
    yearSelect.addEventListener("change", () => loadBooks(1));

    resetBtn.addEventListener("click", resetFilters);

    loadFilters();
</script>

</body>
</html>