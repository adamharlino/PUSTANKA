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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Kehadiran - Admin Pustanka</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;
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
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 28px;
        }

        .admin-info {
            color: #64748b;
            font-size: 14px;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: white;

            border-radius: 16px;

            padding: 22px;

            box-shadow:
                0 5px 20px rgba(15, 23, 42, 0.06);
        }

        /* =========================
           FILTER
        ========================= */

        .filter-box {
            display: grid;

            grid-template-columns:
                minmax(200px, 1fr)
                180px
                auto
                auto;

            gap: 12px;

            margin-bottom: 22px;
        }

        .input {
            width: 100%;

            padding: 11px 13px;

            border:
                1px solid #d1d5db;

            border-radius: 9px;

            outline: none;

            font-size: 14px;
        }

        .input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.1);
        }

        .btn {
            border: none;

            padding: 11px 17px;

            border-radius: 9px;

            cursor: pointer;

            font-size: 14px;

            font-weight: 600;
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

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-scan {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 17px;
            border-radius: 9px;
            background: #16a34a;
            color: white;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-scan:hover {
            background: #15803d;
        }

        /* =========================
           SUMMARY
        ========================= */

        .summary {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 18px;

            color: #64748b;

            font-size: 14px;
        }

        .summary strong {
            color: #1e293b;
        }

        /* =========================
           TABLE
        ========================= */

        .table-wrapper {
            width: 100%;

            overflow-x: auto;
        }

        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 850px;
        }

        thead {
            background: #f8fafc;
        }

        th,
        td {
            padding: 14px 12px;

            text-align: left;

            border-bottom:
                1px solid #e5e7eb;

            font-size: 14px;

            white-space: nowrap;
        }

        th {
            color: #475569;

            font-size: 13px;

            text-transform: uppercase;

            letter-spacing: 0.3px;
        }

        td {
            color: #334155;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .nik {
            color: #64748b;
            font-size: 13px;
        }

        .time {
            font-weight: 600;
        }

        .empty {
            text-align: center;

            padding: 45px 20px;

            color: #64748b;
        }

        .loading {
            text-align: center;

            padding: 40px;

            color: #64748b;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-flex;

            align-items: center;

            padding: 6px 10px;

            border-radius: 999px;

            font-size: 12px;

            font-weight: 600;
        }

        .status-hadir {
            background: #dcfce7;
            color: #166534;
        }

        .status-belum {
            background: #fef3c7;
            color: #92400e;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;

            justify-content: center;

            align-items: center;

            gap: 6px;

            margin-top: 22px;

            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 36px;
            height: 36px;

            padding: 0 10px;

            border:
                1px solid #d1d5db;

            background: white;

            border-radius: 8px;

            cursor: pointer;

            font-size: 13px;
        }

        .page-btn:hover {
            background: #f1f5f9;
        }

        .page-btn.active {
            background: #2563eb;
            border-color: #2563eb;
            color: white;
        }

        .page-btn:disabled {
            opacity: 0.45;
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

            .filter-box {
                grid-template-columns:
                    1fr 1fr;
            }

        }

        @media (max-width: 650px) {

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
                padding: 15px;
            }

            .topbar {
                align-items: flex-start;

                flex-direction: column;

                gap: 8px;
            }

            .filter-box {
                grid-template-columns: 1fr;
            }

            .card {
                padding: 15px;
            }

        }

    </style>
</head>

<body>

<div class="layout">

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

            <a href="../books/">
                <span>Kelola Data Buku</span>
            </a>

            <a href="../users/">
                <span>Kelola Data Anggota</span>
            </a>

            <a href="../loans/">
                <span>Riwayat Peminjaman Buku</span>
            </a>

            <a href="../attendance/" class="active">
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
    ========================= -->

    <main class="main">

        <div class="topbar">
            <div>
                <h1>Kehadiran</h1>

                <div class="admin-info">
                    Monitoring kehadiran anggota
                </div>
            </div>

            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <a href="./scan.php" class="btn-scan">
                    Scan Member Card
                </a>

                <div class="admin-info">
                    Admin:
                    <strong>
                        <?= htmlspecialchars($adminName) ?>
                    </strong>
                </div>
            </div>
        </div>


        <div class="card">

            <!-- FILTER -->

            <div class="filter-box">

                <input
                    type="text"
                    id="search"
                    class="input"
                    placeholder="Cari nama atau NIK..."
                >

                <input
                    type="date"
                    id="tanggal"
                    class="input"
                >

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="applyFilter()"
                >
                    Cari
                </button>

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="resetFilter()"
                >
                    Reset
                </button>

            </div>


            <!-- SUMMARY -->

            <div class="summary">

                <div>
                    Total attendance:
                    <strong id="totalData">
                        0
                    </strong>
                </div>

                <div>
                    Halaman
                    <strong id="currentPage">
                        1
                    </strong>
                    /
                    <strong id="totalPages">
                        1
                    </strong>
                </div>

            </div>


            <!-- TABLE -->

            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>Nama</th>

                            <th>NIK</th>

                            <th>Tanggal</th>

                            <th>Jam Datang</th>

                            <th>Jam Pulang</th>

                            <th>Status</th>

                        </tr>

                    </thead>

                    <tbody id="attendanceTable">

                        <tr>
                            <td
                                colspan="7"
                                class="loading"
                            >
                                Memuat data attendance...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- PAGINATION -->

            <div
                class="pagination"
                id="pagination"
            ></div>

        </div>

    </main>

</div>


<script>

const API_URL =
    "../../../api/attendance/admin_list.php";

const LOGOUT_URL =
    "../../../api/auth/logout.php";

const limit = 10;

let currentPage = 1;


/* =========================
   HELPER
========================= */

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
   LOAD ATTENDANCE
========================= */

async function loadAttendance(page = 1) {

    const table =
        document.getElementById(
            "attendanceTable"
        );

    table.innerHTML = `
        <tr>
            <td
                colspan="7"
                class="loading"
            >
                Memuat data attendance...
            </td>
        </tr>
    `;

    const search =
        document
            .getElementById("search")
            .value
            .trim();

    const tanggal =
        document
            .getElementById("tanggal")
            .value;

    const params =
        new URLSearchParams();

    params.set("page", page);
    params.set("limit", limit);

    if (search !== "") {
        params.set("search", search);
    }

    if (tanggal !== "") {
        params.set("tanggal", tanggal);
    }

    try {

        const response = await fetch(
            `${API_URL}?${params.toString()}`,
            {
                method: "GET",
                credentials: "include"
            }
        );

        const result =
            await response.json();

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }

        if (
            !response.ok ||
            !result.status
        ) {
            throw new Error(
                result.message ||
                "Gagal mengambil data attendance."
            );
        }

        renderAttendance(
            result.data || [],
            result.pagination || {}
        );

    } catch (error) {

        console.error(error);

        table.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="empty"
                >
                    ${escapeHtml(
                        error.message ||
                        "Terjadi kesalahan."
                    )}
                </td>
            </tr>
        `;

    }

}


/* =========================
   RENDER DATA
========================= */

function renderAttendance(
    data,
    pagination
) {

    const table =
        document.getElementById(
            "attendanceTable"
        );

    const total =
        Number(pagination.total || 0);

    const page =
        Number(pagination.page || 1);

    const totalPages =
        Number(
            pagination.total_pages || 1
        );

    currentPage = page;

    document.getElementById(
        "totalData"
    ).textContent = total;

    document.getElementById(
        "currentPage"
    ).textContent = page;

    document.getElementById(
        "totalPages"
    ).textContent =
        totalPages;


    if (!data.length) {

        table.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="empty"
                >
                    Tidak ada data attendance.
                </td>
            </tr>
        `;

        renderPagination(
            page,
            totalPages
        );

        return;
    }


    const startNumber =
        (page - 1) * limit;


    table.innerHTML =
        data.map(
            (item, index) => {

                const user =
                    item.user || {};

                const nama =
                    user.nama || "-";

                const nik =
                    user.nik || "-";

                const tanggal =
                    item.tanggal || "-";

                const datang =
                    item.jam_kedatangan || "-";

                const pulang =
                    item.jam_kepulangan || null;

                let statusHtml = "";

                if (pulang) {

                    statusHtml = `
                        <span
                            class="status status-hadir"
                        >
                            ✓ Selesai
                        </span>
                    `;

                } else {

                    statusHtml = `
                        <span
                            class="status status-belum"
                        >
                            ⏳ Belum Pulang
                        </span>
                    `;

                }


                return `
                    <tr>

                        <td>
                            ${startNumber + index + 1}
                        </td>

                        <td>
                            <div class="user-name">
                                ${escapeHtml(nama)}
                            </div>
                        </td>

                        <td>
                            <span class="nik">
                                ${escapeHtml(nik)}
                            </span>
                        </td>

                        <td>
                            ${escapeHtml(tanggal)}
                        </td>

                        <td>
                            <span class="time">
                                ${escapeHtml(datang)}
                            </span>
                        </td>

                        <td>
                            <span class="time">
                                ${escapeHtml(
                                    pulang || "-"
                                )}
                            </span>
                        </td>

                        <td>
                            ${statusHtml}
                        </td>

                    </tr>
                `;

            }
        ).join("");


    renderPagination(
        page,
        totalPages
    );

}


/* =========================
   PAGINATION
========================= */

function renderPagination(
    page,
    totalPages
) {

    const pagination =
        document.getElementById(
            "pagination"
        );

    pagination.innerHTML = "";

    if (totalPages <= 1) {
        return;
    }


    const prev =
        document.createElement("button");

    prev.className = "page-btn";

    prev.textContent = "‹";

    prev.disabled =
        page <= 1;

    prev.onclick = () => {

        if (page > 1) {
            loadAttendance(page - 1);
        }

    };

    pagination.appendChild(prev);


    let start =
        Math.max(1, page - 2);

    let end =
        Math.min(totalPages, page + 2);


    if (start > 1) {

        addPageButton(
            pagination,
            1,
            page
        );

        if (start > 2) {

            const dots =
                document.createElement("span");

            dots.textContent = "...";

            dots.style.padding = "0 5px";

            pagination.appendChild(dots);
        }
    }


    for (
        let i = start;
        i <= end;
        i++
    ) {

        addPageButton(
            pagination,
            i,
            page
        );

    }


    if (end < totalPages) {

        if (end < totalPages - 1) {

            const dots =
                document.createElement("span");

            dots.textContent = "...";

            dots.style.padding = "0 5px";

            pagination.appendChild(dots);
        }

        addPageButton(
            pagination,
            totalPages,
            page
        );

    }


    const next =
        document.createElement("button");

    next.className = "page-btn";

    next.textContent = "›";

    next.disabled =
        page >= totalPages;

    next.onclick = () => {

        if (page < totalPages) {
            loadAttendance(page + 1);
        }

    };

    pagination.appendChild(next);

}


/* =========================
   PAGE BUTTON
========================= */

function addPageButton(
    container,
    number,
    activePage
) {

    const button =
        document.createElement("button");

    button.className =
        "page-btn" +
        (
            number === activePage
                ? " active"
                : ""
        );

    button.textContent = number;

    button.onclick = () => {

        loadAttendance(number);

    };

    container.appendChild(button);

}


/* =========================
   FILTER
========================= */

function applyFilter() {

    loadAttendance(1);

}


function resetFilter() {

    document.getElementById(
        "search"
    ).value = "";

    document.getElementById(
        "tanggal"
    ).value = "";

    loadAttendance(1);

}


/* =========================
   ENTER SEARCH
========================= */

document
    .getElementById("search")
    .addEventListener(
        "keydown",
        function(event) {

            if (event.key === "Enter") {

                applyFilter();

            }

        }
    );


/* =========================
   LOGOUT
========================= */

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
   INITIAL LOAD
========================= */

loadAttendance(1);

</script>

</body>
</html>