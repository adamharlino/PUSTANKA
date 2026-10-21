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

    <title>Denda - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           SIDEBAR (DISESUAIKAN)
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

        .admin-info-sidebar {
            padding: 0 15px 15px;
        }

        .admin-info-sidebar small {
            display: block;
            color: #9ca3af;
            font-size: 11px;
            margin-bottom: 4px;
        }

        .admin-info-sidebar strong {
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
            width: calc(100% - 250px);
            min-height: 100vh;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 20px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 24px;
            color: #111827;
        }

        .topbar p {
            margin-top: 5px;
            font-size: 13px;
            color: #6b7280;
        }

        .admin-info {
            text-align: right;
        }

        .admin-info strong {
            display: block;
            font-size: 14px;
        }

        .admin-info span {
            color: #6b7280;
            font-size: 12px;
        }

        .content {
            padding: 28px;
        }

        /* =========================
           TOOLBAR
        ========================= */

        .toolbar {
            background: white;
            border-radius: 14px;
            padding: 18px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .filter-row {
            display: grid;
            grid-template-columns: minmax(250px, 1fr) 210px auto;
            gap: 12px;
            align-items: center;
        }

        .search-box,
        .filter-select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            padding: 11px 13px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .search-box:focus,
        .filter-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .reset-btn {
            border: none;
            background: #e5e7eb;
            color: #374151;
            padding: 11px 18px;
            border-radius: 9px;
            cursor: pointer;
            font-weight: 600;
        }

        .reset-btn:hover {
            background: #d1d5db;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;
            padding: 13px 15px;
            border-radius: 9px;
            margin-bottom: 18px;
            font-size: 14px;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .message.success {
            background: #dcfce7;
            color: #166534;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .table-header {
            padding: 18px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .table-header h2 {
            font-size: 17px;
        }

        .total-info {
            font-size: 13px;
            color: #6b7280;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .03em;
            padding: 13px 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        td {
            padding: 14px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .borrower-name,
        .book-title {
            font-weight: 600;
            color: #111827;
        }

        .secondary {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
        }

        .amount {
            font-weight: 700;
            color: #dc2626;
            white-space: nowrap;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            white-space: nowrap;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .detail-btn {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 7px 11px;
            border-radius: 7px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .detail-btn:hover {
            background: #dbeafe;
        }

        /* =========================
           EMPTY / LOADING
        ========================= */

        .loading,
        .empty {
            text-align: center;
            padding: 45px 20px;
            color: #64748b;
            font-size: 14px;
        }

        .empty-icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 6px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 34px;
            height: 34px;
            padding: 0 10px;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
        }

        .page-btn:hover {
            background: #f1f5f9;
        }

        .page-btn.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .page-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .filter-row {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 20px;
            }
        }

        @media (max-width: 700px) {

            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }

            .layout {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                padding: 18px;
            }

            .content {
                padding: 15px;
            }

            .admin-info {
                display: none;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- =========================
         SIDEBAR
    ========================== -->

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

            <a href="../books/">
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

            <a href="./index.php" class="active">
                <span>Riwayat Denda</span>
            </a>

            <a href="../payments/">
                <span>Riwayat Pembayaran Denda</span>
            </a>

        </nav>

        <!-- SIDEBAR BOTTOM -->
        <div class="sidebar-bottom">

            <div class="admin-info-sidebar">
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
    ========================== -->

    <main class="main">

        <header class="topbar">

            <div>
                <h1>Denda</h1>

                <p>
                    Monitoring seluruh denda peminjaman buku
                </p>
            </div>

            <div class="admin-info">
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>

                <span>
                    Administrator
                </span>
            </div>

        </header>

        <section class="content">

            <!-- FILTER -->

            <div class="toolbar">

                <div class="filter-row">

                    <input
                        type="text"
                        id="search"
                        class="search-box"
                        placeholder="Cari nama, NIK, atau judul buku..."
                    >

                    <select
                        id="status"
                        class="filter-select"
                    >
                        <option value="">
                            Semua Status
                        </option>

                        <option value="belum_bayar">
                            Belum Dibayar
                        </option>

                        <option value="lunas">
                            Lunas
                        </option>
                    </select>

                    <button
                        type="button"
                        class="reset-btn"
                        onclick="resetFilter()"
                    >
                        Reset
                    </button>

                </div>

            </div>

            <div
                id="message"
                class="message"
            ></div>

            <!-- TABLE -->

            <div class="table-card">

                <div class="table-header">

                    <h2>
                        Data Denda
                    </h2>

                    <div
                        id="totalInfo"
                        class="total-info"
                    >
                        Memuat data...
                    </div>

                </div>

                <div class="table-wrapper">

                    <table>

                        <thead>

                            <tr>
                                <th>No</th>
                                <th>ID Denda</th>
                                <th>Peminjam</th>
                                <th>Buku</th>
                                <th>Tanggal Pinjam</th>
                                <th>Batas Kembali</th>
                                <th>Tanggal Kembali</th>
                                <th>Nominal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>

                        </thead>

                        <tbody id="fineTableBody">

                            <tr>
                                <td colspan="10">

                                    <div class="loading">
                                        Memuat data denda...
                                    </div>

                                </td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div
                    id="pagination"
                    class="pagination"
                ></div>

            </div>

        </section>

    </main>

</div>

<script>

    const API_URL =
        "../../../api/fines/admin_list.php";

    const LOGOUT_URL =
        "../../../api/auth/logout.php";

    const LOGIN_URL =
        "../../auth/login.php";

    let currentPage = 1;

    const limit = 10;

    let searchTimer = null;


    /* =========================
       ELEMENT
    ========================== */

    const searchInput =
        document.getElementById("search");

    const statusSelect =
        document.getElementById("status");

    const tableBody =
        document.getElementById("fineTableBody");

    const pagination =
        document.getElementById("pagination");

    const totalInfo =
        document.getElementById("totalInfo");

    const message =
        document.getElementById("message");


    /* =========================
       GET JSON
    ========================== */

    async function getJson(url) {

        const response = await fetch(
            url,
            {
                method: "GET",
                credentials: "include"
            }
        );

        if (response.status === 401) {

            window.location.href = LOGIN_URL;

            return null;
        }

        let result;

        try {

            result = await response.json();

        } catch (error) {

            throw new Error(
                "Response server tidak valid."
            );
        }

        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Gagal mengambil data."
            );
        }

        return result;
    }


    /* =========================
       LOAD FINES
    ========================== */

    async function loadFines(page = 1) {

        currentPage = page;

        tableBody.innerHTML = `
            <tr>
                <td colspan="10">
                    <div class="loading">
                        Memuat data denda...
                    </div>
                </td>
            </tr>
        `;

        pagination.innerHTML = "";

        try {

            const params =
                new URLSearchParams();

            params.set(
                "page",
                page
            );

            params.set(
                "limit",
                limit
            );

            const search =
                searchInput.value.trim();

            const status =
                statusSelect.value;

            if (search !== "") {

                params.set(
                    "search",
                    search
                );
            }

            if (status !== "") {

                params.set(
                    "status_pembayaran",
                    status
                );
            }

            const result =
                await getJson(
                    API_URL +
                    "?" +
                    params.toString()
                );

            if (!result) {
                return;
            }

            renderFines(
                result.data || [],
                result.pagination || {}
            );

        } catch (error) {

            console.error(
                "Load fines error:",
                error
            );

            tableBody.innerHTML = `
                <tr>
                    <td colspan="10">

                        <div class="empty">

                            <div class="empty-icon">
                                ⚠️
                            </div>

                            <div>
                                ${escapeHtml(
                                    error.message ||
                                    "Gagal mengambil data denda."
                                )}
                            </div>

                        </div>

                    </td>
                </tr>
            `;

            totalInfo.textContent =
                "Gagal memuat data.";
        }
    }


    /* =========================
       RENDER FINES
    ========================== */

    function renderFines(
        data,
        paginationData
    ) {

        if (!data.length) {

            tableBody.innerHTML = `
                <tr>
                    <td colspan="10">

                        <div class="empty">

                            <div class="empty-icon">
                                💰
                            </div>

                            <div>
                                Tidak ada data denda.
                            </div>

                        </div>

                    </td>
                </tr>
            `;

            totalInfo.textContent =
                "0 data";

            pagination.innerHTML = "";

            return;
        }

        const page =
            Number(
                paginationData.page ||
                currentPage
            );

        const pageLimit =
            Number(
                paginationData.limit ||
                limit
            );

        const total =
            Number(
                paginationData.total ||
                data.length
            );

        tableBody.innerHTML =
            data.map(
                (item, index) => {

                    const nomor =
                        ((page - 1) *
                        pageLimit) +
                        index +
                        1;

                    const peminjam =
                        item.peminjam ||
                        {};

                    const buku =
                        item.buku ||
                        {};

                    const loan =
                        item.loan ||
                        {};

                    const status =
                        item.status_pembayaran ||
                        "";

                    const statusLabel =
                        status === "lunas"
                            ? "Lunas"
                            : "Belum Dibayar";

                    const statusClass =
                        status === "lunas"
                            ? "badge-paid"
                            : "badge-unpaid";

                    return `

                        <tr>

                            <td>
                                ${nomor}
                            </td>

                            <td>
                                <strong>
                                    ${Number(
                                        item.id_fine || 0
                                    )}
                                </strong>
                            </td>

                            <td>

                                <div class="borrower-name">
                                    ${escapeHtml(
                                        peminjam.nama ||
                                        "-"
                                    )}
                                </div>

                                <div class="secondary">
                                    NIK:
                                    ${escapeHtml(
                                        peminjam.nik ||
                                        "-"
                                    )}
                                </div>

                            </td>

                            <td>

                                <div class="book-title">
                                    ${escapeHtml(
                                        buku.judul ||
                                        "-"
                                    )}
                                </div>

                                <div class="secondary">
                                    ${escapeHtml(
                                        buku.pengarang ||
                                        "-"
                                    )}
                                </div>

                            </td>

                            <td>
                                ${formatDate(
                                    loan.tanggal_pinjam
                                )}
                            </td>

                            <td>
                                ${formatDate(
                                    loan.batas_kembali
                                )}
                            </td>

                            <td>
                                ${formatDate(
                                    loan.tanggal_kembali
                                )}
                            </td>

                            <td>
                                <span class="amount">
                                    ${formatRupiah(
                                        item.nominal_denda
                                    )}
                                </span>
                            </td>

                            <td>

                                <span
                                    class="badge ${statusClass}"
                                >
                                    ${statusLabel}
                                </span>

                            </td>

                            <td>

                                <a
                                    class="detail-btn"
                                    href="./detail.php?id=${Number(
                                        item.id_fine || 0
                                    )}"
                                >
                                    Detail
                                </a>

                            </td>

                        </tr>

                    `;

                }
            ).join("");

        totalInfo.textContent =
            `${total} data`;

        renderPagination(
            paginationData
        );
    }


    /* =========================
       PAGINATION
    ========================== */

    function renderPagination(
        paginationData
    ) {

        pagination.innerHTML = "";

        const totalPages =
            Number(
                paginationData.total_pages ||
                0
            );

        const page =
            Number(
                paginationData.page ||
                currentPage
            );

        if (totalPages <= 1) {
            return;
        }

        const previous =
            document.createElement("button");

        previous.className =
            "page-btn";

        previous.textContent =
            "‹";

        previous.disabled =
            page <= 1;

        previous.onclick = function() {

            if (page > 1) {
                loadFines(page - 1);
            }

        };

        pagination.appendChild(
            previous
        );


        let startPage =
            Math.max(
                1,
                page - 2
            );

        let endPage =
            Math.min(
                totalPages,
                page + 2
            );


        if (startPage > 1) {

            addPageButton(1, page);

            if (startPage > 2) {

                addDots();

            }

        }


        for (
            let i = startPage;
            i <= endPage;
            i++
        ) {

            addPageButton(
                i,
                page
            );

        }


        if (endPage < totalPages) {

            if (endPage < totalPages - 1) {

                addDots();

            }

            addPageButton(
                totalPages,
                page
            );

        }


        const next =
            document.createElement("button");

        next.className =
            "page-btn";

        next.textContent =
            "›";

        next.disabled =
            page >= totalPages;

        next.onclick = function() {

            if (page < totalPages) {
                loadFines(page + 1);
            }

        };

        pagination.appendChild(
            next
        );
    }


    function addPageButton(
        pageNumber,
        activePage
    ) {

        const button =
            document.createElement("button");

        button.className =
            "page-btn" +
            (
                pageNumber === activePage
                    ? " active"
                    : ""
            );

        button.textContent =
            pageNumber;

        button.onclick = function() {

            loadFines(
                pageNumber
            );

        };

        pagination.appendChild(
            button
        );
    }


    function addDots() {

        const span =
            document.createElement("span");

        span.textContent =
            "...";

        span.style.padding =
            "0 5px";

        span.style.color =
            "#64748b";

        pagination.appendChild(
            span
        );
    }


    /* =========================
       SEARCH
    ========================== */

    searchInput.addEventListener(
        "input",
        function() {

            clearTimeout(
                searchTimer
            );

            searchTimer =
                setTimeout(
                    function() {
                        loadFines(1);
                    },
                    400
                );

        }
    );


    /* =========================
       STATUS FILTER
    ========================== */

    statusSelect.addEventListener(
        "change",
        function() {

            loadFines(1);

        }
    );


    /* =========================
       RESET
    ========================== */

    function resetFilter() {

        searchInput.value = "";

        statusSelect.value = "";

        hideMessage();

        loadFines(1);
    }


    /* =========================
       LOGOUT
    ========================== */

    async function logout() {

        const confirmed =
        confirm(
            "Yakin ingin logout?"
        );

    if (!confirmed) {
        return;
    }

    try {

        await fetch(
            LOGOUT_URL,
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
       FORMAT RUPIAH
    ========================== */

    function formatRupiah(value) {

        const number =
            Number(value || 0);

        return new Intl.NumberFormat(
            "id-ID",
            {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }
        ).format(number);
    }


    /* =========================
       FORMAT DATE
    ========================== */

    function formatDate(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(
                value.includes("T")
                    ? value
                    : value.replace(
                        " ",
                        "T"
                    )
            );

        if (isNaN(date.getTime())) {
            return escapeHtml(value);
        }

        return new Intl.DateTimeFormat(
            "id-ID",
            {
                day: "2-digit",
                month: "short",
                year: "numeric"
            }
        ).format(date);
    }


    /* =========================
       ESCAPE HTML
    ========================== */

    function escapeHtml(value) {

        return String(
            value ?? ""
        )
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );
    }


    /* =========================
       MESSAGE
    ========================== */

    function showMessage(
        text,
        type = "error"
    ) {

        message.textContent =
            text;

        message.className =
            "message show " +
            type;
    }


    function hideMessage() {

        message.className =
            "message";

        message.textContent =
            "";
    }


    /* =========================
       INIT
    ========================== */

    loadFines(1);

</script>

</body>
</html>