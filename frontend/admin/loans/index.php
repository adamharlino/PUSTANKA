<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

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

    <title>Admin - Peminjaman</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
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
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
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
           TOOLBAR
        ========================= */

        .toolbar {
            background: white;
            padding: 18px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
            margin-bottom: 20px;
        }

        .filter-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 240px;
        }

        .search-box input,
        .filter-row select {
            width: 100%;
            height: 42px;
            padding: 0 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            background: white;
        }

        .search-box input:focus,
        .filter-row select:focus {
            border-color: #2563eb;
        }

        .filter-select {
            width: 180px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 11px 16px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
        }

        .btn-reset {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-reset:hover {
            background: #d1d5db;
        }

        .btn-detail {
            background: #2563eb;
            color: white;
        }

        .btn-detail:hover {
            background: #1d4ed8;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .05);
            overflow: hidden;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1100px;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            vertical-align: middle;
            white-space: nowrap;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
        }

        td {
            font-size: 14px;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .book-title {
            font-weight: 600;
            color: #1e293b;
        }

        .borrower-name {
            font-weight: 600;
        }

        .sub-text {
            color: #64748b;
            font-size: 12px;
            margin-top: 4px;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-dipinjam {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-dikembalikan {
            background: #dcfce7;
            color: #15803d;
        }

        .status-terlambat {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-default {
            background: #e5e7eb;
            color: #374151;
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

        .error-message {
            background: #fee2e2;
            color: #b91c1c;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: none;
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
            min-width: 38px;
            height: 38px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 7px;
            cursor: pointer;
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
                padding: 20px;
            }

            .filter-select {
                width: 150px;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .layout {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .filter-row {
                flex-direction: column;
            }

            .search-box,
            .filter-select {
                width: 100%;
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
                <span>Buku</span>
            </a>

            <a href="../users/">
                <span>Anggota</span>
            </a>

            <a href="./index.php" class="active">
                <span>Peminjaman</span>
            </a>

            <a href="../attendance/">
                <span>Kehadiran</span>
            </a>

            <a href="../returns/">
                <span>Pengembalian</span>
            </a>

            <a href="../reports/dashboard/summary.php">
                <span>Laporan</span>
            </a>

            <a href="../fines/">
                <span>Denda</span>
            </a>

            <a href="../payments/">
                <span>Pembayaran</span>
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

        <div class="topbar">

            <div>
                <h1>Peminjaman</h1>

                <div class="admin-info">
                    Monitoring seluruh data peminjaman buku
                </div>
            </div>

            <div class="admin-info">
                Admin:
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>
            </div>

        </div>


        <!-- ERROR -->

        <div
            id="errorMessage"
            class="error-message"
        ></div>


        <!-- =========================
             FILTER
        ========================== -->

        <div class="toolbar">

            <div class="filter-row">

                <div class="search-box">
                    <input
                        type="text"
                        id="search"
                        placeholder="Cari nama, NIK, judul buku, pengarang..."
                    >
                </div>

                <div class="filter-select">
                    <select id="status">
                        <option value="">
                            Semua Status
                        </option>

                        <option value="dipinjam">
                            Dipinjam
                        </option>

                        <option value="dikembalikan">
                            Dikembalikan
                        </option>

                        <option value="terlambat">
                            Terlambat
                        </option>
                    </select>
                </div>

                <button
                    type="button"
                    class="btn btn-reset"
                    onclick="resetFilter()"
                >
                    Reset
                </button>

            </div>

        </div>


        <!-- =========================
             TABLE
        ========================== -->

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>ID Peminjaman</th>
                            <th>Nama Anggota</th>
                            <th>Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody id="loanTableBody">

                        <tr>
                            <td colspan="9">
                                <div class="loading">
                                    Memuat data peminjaman...
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

    </main>

</div>


<script>

    const API_BASE = "../../../api";

    const API_URL =
        API_BASE + "/loans/admin_list.php";

    let currentPage = 1;
    const limit = 10;

    let searchTimer = null;


    /* =========================
       GET JSON
    ========================== */

    async function getJson(url) {

        const response = await fetch(url, {
            method: "GET",
            credentials: "include"
        });

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return null;
        }

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
       BUILD URL
    ========================== */

    function buildLoansUrl(page = 1) {

        const params =
            new URLSearchParams();

        params.set("page", page);
        params.set("limit", limit);

        const search =
            document
                .getElementById("search")
                .value
                .trim();

        const status =
            document
                .getElementById("status")
                .value;

        if (search !== "") {
            params.set("search", search);
        }

        if (status !== "") {
            params.set("status", status);
        }

        return API_URL + "?" + params.toString();
    }


    /* =========================
       LOAD LOANS
    ========================== */

    async function loadLoans(page = 1) {

        const tbody =
            document.getElementById(
                "loanTableBody"
            );

        const errorBox =
            document.getElementById(
                "errorMessage"
            );

        errorBox.style.display = "none";

        tbody.innerHTML = `
            <tr>
                <td colspan="9">
                    <div class="loading">
                        Memuat data peminjaman...
                    </div>
                </td>
            </tr>
        `;

        try {

            const result =
                await getJson(
                    buildLoansUrl(page)
                );

            if (!result) return;

            currentPage = page;

            renderLoans(
                result.data || [],
                result.pagination || {}
            );

        } catch (error) {

            console.error(error);

            tbody.innerHTML = `
                <tr>
                    <td colspan="9">
                        <div class="empty">
                            Gagal memuat data peminjaman.
                        </div>
                    </td>
                </tr>
            `;

            errorBox.textContent =
                error.message ||
                "Terjadi kesalahan.";

            errorBox.style.display = "block";
        }
    }


    /* =========================
       RENDER LOANS
    ========================== */

    function renderLoans(
        loans,
        pagination
    ) {

        const tbody =
            document.getElementById(
                "loanTableBody"
            );

        if (!loans.length) {

            tbody.innerHTML = `
                <tr>
                    <td colspan="9">
                        <div class="empty">
                            Tidak ada data peminjaman.
                        </div>
                    </td>
                </tr>
            `;

            renderPagination(
                pagination
            );

            return;
        }


        tbody.innerHTML =
            loans.map((loan, index) => {

                const peminjam =
                    loan.peminjam || {};

                const buku =
                    loan.buku || {};

                const nomor =
                    ((pagination.page || 1) - 1)
                    * (pagination.limit || limit)
                    + index + 1;

                return `
                    <tr>

                        <td>
                            ${nomor}
                        </td>

                        <td>
                            ${escapeHtml(
                                loan.id_loan
                            )}
                        </td>

                        <td>
                            <div class="borrower-name">
                                ${escapeHtml(
                                    peminjam.nama || "-"
                                )}
                            </div>

                            <div class="sub-text">
                                NIK:
                                ${escapeHtml(
                                    peminjam.nik || "-"
                                )}
                            </div>
                        </td>

                        <td>
                            <div class="book-title">
                                ${escapeHtml(
                                    buku.judul || "-"
                                )}
                            </div>

                            <div class="sub-text">
                                ${escapeHtml(
                                    buku.pengarang || "-"
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
                            ${
                                loan.tanggal_kembali
                                ? formatDate(
                                    loan.tanggal_kembali
                                )
                                : "-"
                            }
                        </td>

                        <td>
                            ${statusBadge(
                                loan.status
                            )}
                        </td>

                        <td>
                            <a
                                href="detail.php?id=${encodeURIComponent(
                                    loan.id_loan
                                )}"
                                class="btn btn-detail"
                            >
                                Detail
                            </a>
                        </td>

                    </tr>
                `;

            }).join("");


        renderPagination(
            pagination
        );
    }


    /* =========================
       STATUS BADGE
    ========================== */

    function statusBadge(status) {

        let className =
            "status-default";

        let label =
            status || "-";

        if (status === "dipinjam") {

            className =
                "status-dipinjam";

            label =
                "Dipinjam";

        } else if (status === "dikembalikan") {

            className =
                "status-dikembalikan";

            label =
                "Dikembalikan";

        } else if (status === "terlambat") {

            className =
                "status-terlambat";

            label =
                "Terlambat";
        }

        return `
            <span class="status ${className}">
                ${escapeHtml(label)}
            </span>
        `;
    }


    /* =========================
       PAGINATION
    ========================== */

    function renderPagination(
        pagination
    ) {

        const container =
            document.getElementById(
                "pagination"
            );

        const page =
            Number(pagination.page || 1);

        const totalPages =
            Number(
                pagination.total_pages || 0
            );

        if (totalPages <= 1) {

            container.innerHTML = "";
            return;
        }

        let html = "";

        html += `
            <button
                class="page-btn"
                ${page <= 1 ? "disabled" : ""}
                onclick="loadLoans(${page - 1})"
            >
                ‹
            </button>
        `;


        const start =
            Math.max(1, page - 2);

        const end =
            Math.min(
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
                    class="page-btn ${
                        i === page
                            ? "active"
                            : ""
                    }"
                    onclick="loadLoans(${i})"
                >
                    ${i}
                </button>
            `;
        }


        html += `
            <button
                class="page-btn"
                ${page >= totalPages ? "disabled" : ""}
                onclick="loadLoans(${page + 1})"
            >
                ›
            </button>
        `;

        container.innerHTML = html;
    }


    /* =========================
       RESET FILTER
    ========================== */

    function resetFilter() {

        document.getElementById(
            "search"
        ).value = "";

        document.getElementById(
            "status"
        ).value = "";

        loadLoans(1);
    }


    /* =========================
       SEARCH
    ========================== */

    document
        .getElementById("search")
        .addEventListener(
            "input",
            function () {

                clearTimeout(
                    searchTimer
                );

                searchTimer =
                    setTimeout(() => {
                        loadLoans(1);
                    }, 400);
            }
        );


    /* =========================
       STATUS FILTER
    ========================== */

    document
        .getElementById("status")
        .addEventListener(
            "change",
            function () {
                loadLoans(1);
            }
        );


    /* =========================
       FORMAT DATE
    ========================== */

    function formatDate(dateString) {

        if (!dateString) {
            return "-";
        }

        const parts =
            String(dateString).split("-");

        if (parts.length !== 3) {
            return escapeHtml(dateString);
        }

        return `
            ${parts[2]}-${parts[1]}-${parts[0]}
        `;
    }


    /* =========================
       ESCAPE HTML
    ========================== */

    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return "";
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    /* =========================
       LOGOUT
    ========================== */

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
       INIT
    ========================== */

    loadLoans(1);

</script>

</body>
</html>