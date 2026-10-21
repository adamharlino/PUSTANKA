<?php
session_start();

if (
    !isset($_SESSION["id_admin"]) ||
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

    <title>Kelola Data Buku - Pustanka</title>

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
           SIDEBAR (DISESUAIKAN DENGAN DASHBOARD)
        ========================= */

        .sidebar {
            width: 250px;
            background: #061e3f;
            color: white;
            min-height: 100vh;
            padding: 25px 15px;

            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;

            overflow-y: auto;
            z-index: 1000;
        }

        .brand {
            padding: 5px 15px 25px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 20px;
        }

        .brand h2 {
            font-size: 22px;
            color: white;
            margin-bottom: 5px;
        }

        .brand p {
            font-size: 11px;
            color: #9ca3af;
        }

        .menu-title {
            font-size: 11px;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: 1px;

            padding: 0 15px;
            margin-bottom: 10px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 12px;

            padding: 12px 15px;

            border-radius: 10px;

            font-size: 14px;
            color: #d1d5db;

            text-decoration: none;
            transition: 0.2s ease;
        }

        .menu a:hover {
            background: #0f2c59;
            color: white;
        }

        .menu a.active {
            background: #0f2c59;
            color: white;
        }

        .menu-icon {
            width: 24px;
            text-align: center;
            font-size: 17px;
        }

        /* =========================
           SIDEBAR BOTTOM
        ========================= */

        .sidebar-bottom {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .admin-info {
            padding: 0 15px 15px;
        }

        .admin-info small {
            display: block;
            color: #9ca3af;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .admin-info strong {
            display: block;
            color: white;
            font-size: 14px;
        }

        .logout-btn {
            width: 100%;

            border: none;
            background: #991b1b;
            color: white;

            padding: 12px 15px;

            border-radius: 10px;

            cursor: pointer;

            display: flex;
            align-items: center;
            gap: 12px;

            font-size: 14px;
            text-align: left;

            transition: 0.2s ease;
        }

        .logout-btn:hover {
            background: #b91c1c;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            margin-left: 250px;
            padding: 30px;
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
           TOOLBAR
        ========================= */

        .toolbar {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .06);
            margin-bottom: 20px;
        }

        .toolbar-top {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .search-box {
            flex: 1;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
        }

        .search-box input:focus,
        select:focus {
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 10px;
            padding: 11px 17px;
            cursor: pointer;
            font-weight: bold;
            font-size: 13px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
        }

        select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #dbe2ea;
            border-radius: 10px;
            background: white;
            outline: none;
            color: #334155;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-header h2 {
            font-size: 18px;
            color: #0f172a;
        }

        .total-data {
            font-size: 13px;
            color: #64748b;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1200px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            padding: 14px 12px;
            text-align: left;
            white-space: nowrap;
        }

        td {
            padding: 14px 12px;
            border-top: 1px solid #eef2f7;
            font-size: 13px;
            color: #334155;
            vertical-align: middle;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .book-title {
            font-weight: bold;
            color: #0f172a;
            max-width: 230px;
        }

        .barcode {
            font-family: monospace;
            font-size: 12px;
            color: #475569;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
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

        .actions {
            display: flex;
            gap: 6px;
            white-space: nowrap;
        }

        .action-btn {
            border: none;
            padding: 7px 9px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 11px;
            font-weight: bold;
        }

        .detail-btn {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .edit-btn {
            background: #fef3c7;
            color: #92400e;
        }

        .delete-btn {
            background: #fee2e2;
            color: #b91c1c;
        }

        /* =========================
           EMPTY / LOADING
        ========================= */

        .loading,
        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }

        .error {
            padding: 15px 20px;
            margin: 15px;
            border-radius: 10px;
            background: #fee2e2;
            color: #991b1b;
            display: none;
            font-size: 13px;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            border-top: 1px solid #e2e8f0;
            gap: 10px;
        }

        .pagination-info {
            font-size: 13px;
            color: #64748b;
        }

        .pagination-buttons {
            display: flex;
            gap: 5px;
        }

        .page-btn {
            min-width: 34px;
            height: 34px;
            border: 1px solid #dbe2ea;
            background: white;
            border-radius: 7px;
            cursor: pointer;
            color: #475569;
        }

        .page-btn:hover {
            background: #eff6ff;
        }

        .page-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .page-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* =========================
           MOBILE
        ========================= */

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                padding: 20px;
            }

            .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 650px) {
            .sidebar {
                position: static;
                width: 100%;
                height: auto;
            }

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

            .toolbar-top {
                flex-direction: column;
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .pagination {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<!-- =========================
     SIDEBAR
========================= -->

<aside class="sidebar">

    <div class="brand">
        <h2>Pustanka</h2>
        <p>Sistem Informasi Perpustakaan</p>
    </div>

    <nav class="menu">

        <a href="../dashboard/">
            <span>Dashboard</span>
        </a>

        <a href="../profile/">
            <span>Profil</span>
        </a>

        <a href="../books/" class="active">
            <span>Kelola Data Buku</span>
        </a>

        <a href="../users/">
            <span>Kelola Data Anggota</span>
        </a>

        <a href="../loans/">
            <span>Riwayat Peminjaman Buku</span>
        </a>

        <a href="../attendance/">
            <span>Kehadiran Anggota</span>
        </a>

        <a href="../returns/">
            <span>Scan Pengembalian Buku</span>
        </a>

        <a href="../reports/dashboard/summary.php">
            <span>Laporan</span>
        </a>

        <a href="../fines/">
            <span>Riwayat Denda</span>
        </a>

        <a href="../payments/">
            <span>Riwayat Pembayaran Denda</span>
        </a>

    </nav>

    <!-- SIDEBAR BOTTOM -->

    <div class="sidebar-bottom">

        <div class="admin-info">
            <small>Login sebagai</small>
            <strong>
                <?= htmlspecialchars($adminName) ?>
            </strong>
        </div>

        <button
            type="button"
            class="logout-btn"
            onclick="logout()"
        >
            <span>Logout</span>
        </button>

    </div>

</aside>


<!-- =========================
     MAIN
========================= -->

<main class="main">

    <div class="topbar">

        <div>
            <h1>Kelola Data Buku</h1>
            <p>
                Kelola seluruh data buku perpustakaan
            </p>
        </div>

        <div>
            Admin:
            <span class="admin-name">
                <?= htmlspecialchars($_SESSION["nama"] ?? "Admin") ?>
            </span>
        </div>

    </div>


    <!-- =========================
         TOOLBAR
    ========================= -->

    <section class="toolbar">

        <div class="toolbar-top">

            <div class="search-box">
                <input
                    type="text"
                    id="search"
                    placeholder="Cari judul buku..."
                >
            </div>

            <button
                class="btn btn-primary"
                onclick="goCreate()"
            >
                + Tambah Buku
            </button>

            <button
                class="btn btn-secondary"
                onclick="resetFilter()"
            >
                Reset
            </button>

        </div>


        <div class="filter-row">

            <select id="category">
                <option value="">Semua Kategori</option>
            </select>

            <select id="shelf">
                <option value="">Semua Rak</option>
            </select>

            <select id="jenis">
                <option value="">Semua Jenis</option>
            </select>

            <select id="tahun">
                <option value="">Semua Tahun</option>
            </select>

        </div>

    </section>


    <!-- =========================
         TABLE
    ========================= -->

    <section class="table-card">

        <div class="table-header">

            <h2>Daftar Buku</h2>

            <div class="total-data" id="totalData">
                Memuat data...
            </div>

        </div>

        <div
            class="error"
            id="errorBox"
        ></div>

        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>
                        <th>No</th>
                        <th>Barcode</th>
                        <th>ISBN</th>
                        <th>Judul</th>
                        <th>Pengarang</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Jenis</th>
                        <th>Kategori</th>
                        <th>Rak</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>

                </thead>

                <tbody id="bookTable">

                    <tr>
                        <td
                            colspan="12"
                            class="loading"
                        >
                            Memuat data buku...
                        </td>
                    </tr>

                </tbody>

            </table>

        </div>


        <!-- PAGINATION -->

        <div class="pagination">

            <div
                class="pagination-info"
                id="paginationInfo"
            >
                -
            </div>

            <div
                class="pagination-buttons"
                id="paginationButtons"
            ></div>

        </div>

    </section>

</main>


<script>

const API_BASE = "../../../api";
const API_URL = API_BASE + "/books/list.php";

let currentPage = 1;
const limit = 10;
let searchTimer = null;


/* =========================
   ELEMENT
========================= */

const searchInput = document.getElementById("search");
const categorySelect = document.getElementById("category");
const shelfSelect = document.getElementById("shelf");
const jenisSelect = document.getElementById("jenis");
const tahunSelect = document.getElementById("tahun");


/* =========================
   GET JSON
========================= */

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
        throw new Error(
            result.message || "Gagal mengambil data."
        );
    }

    return result;
}


/* =========================
   LOAD CATEGORY
========================= */

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


/* =========================
   LOAD SHELF
========================= */

async function loadShelves() {

    const result = await getJson(
        API_BASE + "/books/shelves.php"
    );

    shelfSelect.innerHTML =
        '<option value="">Semua Rak</option>';

    (result.data || []).forEach(shelf => {

        const option = document.createElement("option");

        option.value = shelf.id_shelf;

        let label = "Rak " + (
            shelf.no_rak || "-"
        );

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


/* =========================
   ADD UNIQUE OPTION
========================= */

function addUniqueOption(select, value) {

    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return;
    }

    const exists = Array.from(
        select.options
    ).some(option => option.value == value);

    if (exists) {
        return;
    }

    const option = document.createElement("option");

    option.value = value;
    option.textContent = value;

    select.appendChild(option);
}


/* =========================
   LOAD JENIS & TAHUN
========================= */

async function loadFilterValues() {

    const result = await getJson(
        API_BASE +
        "/books/list.php?page=1&limit=100"
    );

    jenisSelect.innerHTML =
        '<option value="">Semua Jenis</option>';

    tahunSelect.innerHTML =
        '<option value="">Semua Tahun</option>';

    (result.data || []).forEach(book => {

        addUniqueOption(
            jenisSelect,
            book.jenis_buku
        );

        addUniqueOption(
            tahunSelect,
            book.tahun_terbit
        );
    });


    /* =========================
       SORT TAHUN TERBARU
    ========================= */

    const yearOptions =
        Array.from(tahunSelect.options)
            .slice(1);

    yearOptions.sort(
        (a, b) =>
            Number(b.value) - Number(a.value)
    );

    tahunSelect.innerHTML =
        '<option value="">Semua Tahun</option>';

    yearOptions.forEach(option => {
        tahunSelect.appendChild(option);
    });
}


/* =========================
   BUILD URL
========================= */

function buildBooksUrl(page) {

    const params = new URLSearchParams();

    params.set("page", page);
    params.set("limit", limit);


    const search =
        searchInput.value.trim();

    const category =
        categorySelect.value;

    const shelf =
        shelfSelect.value;

    const jenis =
        jenisSelect.value;

    const tahun =
        tahunSelect.value;


    if (search) {
        params.set("search", search);
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

    if (jenis) {
        params.set(
            "jenis_buku",
            jenis
        );
    }

    if (tahun) {
        params.set(
            "tahun_terbit",
            tahun
        );
    }


    return API_URL + "?" + params.toString();
}


/* =========================
   LOAD BOOKS
========================= */

async function loadBooks(page = 1) {

    currentPage = page;

    const tbody =
        document.getElementById("bookTable");

    const errorBox =
        document.getElementById("errorBox");


    errorBox.style.display = "none";


    tbody.innerHTML = `
        <tr>
            <td colspan="12" class="loading">
                Memuat data buku...
            </td>
        </tr>
    `;


    try {

        const result =
            await getJson(
                buildBooksUrl(page)
            );


        renderBooks(
            result.data || []
        );

        renderPagination(
            result.pagination
        );


    } catch (error) {

        console.error(error);


        if (error.message === "UNAUTHORIZED") {
            return;
        }


        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="empty">
                    Gagal mengambil data buku.
                </td>
            </tr>
        `;


        errorBox.textContent =
            error.message;

        errorBox.style.display =
            "block";


        document.getElementById(
            "totalData"
        ).textContent =
            "Data gagal dimuat";
    }
}


/* =========================
   RENDER BOOKS
========================= */

function renderBooks(books) {

    const tbody =
        document.getElementById("bookTable");


    if (!books.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="12" class="empty">
                    📚 Tidak ada data buku.
                </td>
            </tr>
        `;


        document.getElementById(
            "totalData"
        ).textContent =
            "0 buku";


        return;
    }


    tbody.innerHTML =
        books.map((book, index) => {

            const nomor =
                ((currentPage - 1) * limit) +
                index + 1;


            const kategori =
                book.kategori?.nama_category ||
                "-";


            const rak =
                book.rak?.no_rak
                    ? `Rak ${escapeHtml(
                        book.rak.no_rak
                    )}`
                    : "-";


            const barcode =
                book.barcode || "-";


            const status =
                book.status || "-";


            let statusClass =
                "status-other";


            if (
                status.toLowerCase() ===
                "tersedia"
            ) {
                statusClass =
                    "status-available";
            }


            if (
                status.toLowerCase() ===
                "dipinjam"
            ) {
                statusClass =
                    "status-borrowed";
            }


            return `
                <tr>

                    <td>
                        ${nomor}
                    </td>

                    <td class="barcode">
                        ${escapeHtml(
                            barcode
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            book.isbn || "-"
                        )}
                    </td>

                    <td class="book-title">
                        ${escapeHtml(
                            book.judul || "-"
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            book.pengarang || "-"
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            book.penerbit || "-"
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            book.tahun_terbit || "-"
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            book.jenis_buku || "-"
                        )}
                    </td>

                    <td>
                        ${escapeHtml(
                            kategori
                        )}
                    </td>

                    <td>
                        ${rak}
                    </td>

                    <td>
                        <span
                            class="status ${statusClass}"
                        >
                            ${escapeHtml(
                                status
                            )}
                        </span>
                    </td>

                    <td>

                        <div class="actions">

                            <button
                                class="action-btn detail-btn"
                                onclick="detailBook(${book.id_book})"
                            >
                                Detail
                            </button>

                            <button
                                class="action-btn edit-btn"
                                onclick="editBook(${book.id_book})"
                            >
                                Edit
                            </button>

                            <button
                                class="action-btn delete-btn"
                                onclick="deleteBook(${book.id_book})"
                            >
                                Hapus
                            </button>

                        </div>

                    </td>

                </tr>
            `;

        }).join("");


    document.getElementById(
        "totalData"
    ).textContent =
        `${books.length} data ditampilkan`;
}


/* =========================
   PAGINATION
========================= */

function renderPagination(pagination) {

    if (!pagination) {
        return;
    }


    const totalData =
        Number(
            pagination.total_data || 0
        );


    const totalPage =
        Number(
            pagination.total_page || 0
        );


    const page =
        Number(
            pagination.page || 1
        );


    document.getElementById(
        "paginationInfo"
    ).textContent =
        totalData > 0
            ? `Halaman ${page} dari ${totalPage} • Total ${totalData} buku`
            : "Tidak ada data";


    const container =
        document.getElementById(
            "paginationButtons"
        );


    container.innerHTML = "";


    if (totalPage <= 1) {
        return;
    }


    /* PREVIOUS */

    const prev =
        document.createElement(
            "button"
        );

    prev.className =
        "page-btn";

    prev.textContent =
        "‹";

    prev.disabled =
        page <= 1;


    prev.onclick = () => {

        if (page > 1) {
            loadBooks(page - 1);
        }
    };


    container.appendChild(prev);


    /* PAGE NUMBER */

    let start =
        Math.max(
            1,
            page - 2
        );


    let end =
        Math.min(
            totalPage,
            page + 2
        );


    for (
        let i = start;
        i <= end;
        i++
    ) {

        const button =
            document.createElement(
                "button"
            );


        button.className =
            "page-btn" +
            (
                i === page
                    ? " active"
                    : ""
            );


        button.textContent =
            i;


        button.onclick = () => {
            loadBooks(i);
        };


        container.appendChild(
            button
        );
    }


    /* NEXT */

    const next =
        document.createElement(
            "button"
        );


    next.className =
        "page-btn";

    next.textContent =
        "›";

    next.disabled =
        page >= totalPage;


    next.onclick = () => {

        if (page < totalPage) {
            loadBooks(page + 1);
        }
    };


    container.appendChild(next);
}


/* =========================
   SEARCH
========================= */

searchInput.addEventListener(
    "input",
    () => {

        clearTimeout(searchTimer);


        searchTimer =
            setTimeout(() => {

                loadBooks(1);

            }, 400);
    }
);


/* =========================
   FILTER
========================= */

categorySelect.addEventListener(
    "change",
    () => loadBooks(1)
);


shelfSelect.addEventListener(
    "change",
    () => loadBooks(1)
);


jenisSelect.addEventListener(
    "change",
    () => loadBooks(1)
);


tahunSelect.addEventListener(
    "change",
    () => loadBooks(1)
);


/* =========================
   RESET
========================= */

function resetFilter() {

    searchInput.value = "";

    categorySelect.value = "";

    shelfSelect.value = "";

    jenisSelect.value = "";

    tahunSelect.value = "";


    loadBooks(1);
}


/* =========================
   NAVIGATION
========================= */

function goCreate() {

    window.location.href =
        "./create.php";
}


function detailBook(id) {

    window.location.href =
        `./detail.php?id=${encodeURIComponent(id)}`;
}


function editBook(id) {

    window.location.href =
        `./edit.php?id=${encodeURIComponent(id)}`;
}


/* =========================
   DELETE
========================= */

async function deleteBook(id) {

    const confirmed = confirm(
        "Yakin ingin menghapus buku ini?"
    );

    if (!confirmed) {
        return;
    }

    try {

        const response = await fetch(
            API_BASE + "/books/delete.php",
            {
                method: "DELETE",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_book: Number(id)
                })
            }
        );

        const result = await response.json();

        if (!response.ok || !result.status) {
            throw new Error(
                result.message ||
                "Gagal menghapus buku."
            );
        }

        alert(result.message);

        loadBooks(1);

    } catch (error) {

        console.error(error);

        alert(
            error.message ||
            "Terjadi kesalahan saat menghapus buku."
        );
    }
}


/* =========================
   LOGOUT
========================= */

async function logout() {

    const confirmLogout =
        confirm(
            "Apakah kamu yakin ingin logout?"
        );


    if (!confirmLogout) {
        return;
    }


    try {

        const response = await fetch(

            `${API_BASE}/auth/logout.php`,

            {
                method: "POST",

                credentials: "include"
            }

        );


        const result =
            await response.json();


        if (
            response.ok &&
            result.status
        ) {

            window.location.href =
                "../../auth/login.php";

            return;

        }


        throw new Error(

            result.message ||
            "Logout gagal."

        );


    } catch (error) {

        console.error(error);

        alert(
            error.message ||
            "Terjadi kesalahan saat logout."
        );

    }

}


/* =========================
   ESCAPE HTML
========================= */

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
   INITIAL LOAD
========================= */

async function init() {

    try {

        /*
         * Load dropdown terlebih dahulu
         */

        await Promise.all([
            loadCategories(),
            loadShelves(),
            loadFilterValues()
        ]);


        /*
         * Setelah dropdown siap,
         * baru load daftar buku.
         */

        await loadBooks(1);


    } catch (error) {

        console.error(error);


        const errorBox =
            document.getElementById(
                "errorBox"
            );


        errorBox.textContent =
            error.message ||
            "Gagal memuat filter buku.";


        errorBox.style.display =
            "block";


        /*
         * Tetap coba tampilkan
         * daftar buku.
         */

        loadBooks(1);
    }
}


init();

</script>

</body>
</html>