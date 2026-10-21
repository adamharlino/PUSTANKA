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

    <title>Users - PUSTANKA</title>

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

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* ================= SIDEBAR (DISESUAIKAN DENGAN BOOKS) ================= */

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

        /* ================= SIDEBAR BOTTOM ================= */

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

        /* ================= MAIN ================= */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
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
            font-size: 13px;
            color: #64748b;
        }

        /* ================= CARD ================= */

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
            padding: 22px;
        }

        /* ================= TOOLBAR ================= */

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .search-box {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 260px;
        }

        .search-box input {
            width: 100%;
            padding: 11px 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
        }

        .search-box input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .1);
        }

        .btn {
            border: none;
            padding: 11px 16px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
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

        .btn-danger {
            background: #fee2e2;
            color: #b91c1c;
        }

        .btn-danger:hover {
            opacity: .8;
        }

        /* ================= TABLE ================= */

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .4px;
            text-align: left;
            padding: 13px 12px;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        td {
            padding: 14px 12px;
            border-bottom: 1px solid #eef2f7;
            font-size: 13px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .avatar-placeholder {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #dbeafe;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            flex-shrink: 0;
        }

        .user-name {
            font-weight: 600;
            color: #1e293b;
        }

        .member-code {
            font-family: monospace;
            font-size: 12px;
            background: #f1f5f9;
            padding: 5px 8px;
            border-radius: 6px;
            color: #334155;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 5px 9px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
        }

        .status-aktif {
            background: #dcfce7;
            color: #166534;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-buttons {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }

        .action-btn {
            border: none;
            padding: 7px 10px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-detail {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .btn-edit {
            background: #fef3c7;
            color: #92400e;
        }

        .action-btn:hover {
            opacity: .8;
        }

        /* ================= PAGINATION ================= */

        .pagination-wrapper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .pagination-info {
            color: #64748b;
            font-size: 13px;
        }

        .pagination {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .page-btn {
            min-width: 36px;
            height: 36px;
            border: 1px solid #dbe2ea;
            background: white;
            border-radius: 7px;
            cursor: pointer;
            color: #475569;
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
            opacity: .5;
            cursor: not-allowed;
        }

        /* ================= EMPTY / LOADING ================= */

        .loading,
        .empty,
        .error {
            text-align: center;
            padding: 45px 20px;
            color: #64748b;
        }

        .error {
            color: #b91c1c;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
                padding: 20px;
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                position: relative;
                width: 100%;
                min-height: auto;
            }

            .layout {
                display: block;
            }

            .main {
                margin-left: 0;
                width: 100%;
                padding: 16px;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .search-box {
                min-width: 100%;
            }

            .toolbar {
                align-items: stretch;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
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

            <a href="./index.php" class="active">
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


    <!-- MAIN -->
    <main class="main">

        <div class="topbar">

            <div>
                <h1>Users</h1>

                <div class="admin-info">
                    Kelola data anggota perpustakaan
                </div>
            </div>

            <div class="admin-info">
                Admin:
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>
            </div>

        </div>


        <div class="card">

            <div class="toolbar">

                <div class="search-box">
                    <input
                        type="text"
                        id="search"
                        placeholder="Cari nama, NIK, email, atau member code..."
                        autocomplete="off"
                    >
                </div>

                <div style="display:flex; gap:8px; flex-wrap:wrap;">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="resetSearch()"
                    >
                        ↻ Reset
                    </button>

                    <a
                        href="./create.php"
                        class="btn btn-primary"
                    >
                        + Tambah User
                    </a>

                </div>

            </div>


            <div id="tableContainer">

                <div class="loading">
                    Memuat data user...
                </div>

            </div>


            <div class="pagination-wrapper">

                <div
                    class="pagination-info"
                    id="paginationInfo"
                >
                    -
                </div>

                <div
                    class="pagination"
                    id="pagination"
                ></div>

            </div>

        </div>

    </main>

</div>


<script>

const API_BASE = "../../../api";
const API_URL = API_BASE + "/users/list.php";
const LOGOUT_URL = API_BASE + "/auth/logout.php";

const LIMIT = 10;

let currentPage = 1;
let totalPages = 1;
let searchTimer = null;


/* =========================
   ESCAPE HTML
========================= */

function escapeHtml(value) {

    if (value === null || value === undefined) {
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
   GET DATA
========================= */

async function getJson(url) {

    const response = await fetch(url, {
        method: "GET",
        credentials: "include",
        headers: {
            "Accept": "application/json"
        }
    });

    let result;

    try {
        result = await response.json();
    } catch (error) {
        throw new Error("Response API tidak valid.");
    }

    if (response.status === 401) {
        window.location.href = "../../auth/login.php";
        return null;
    }

    if (!response.ok || !result.status) {
        throw new Error(
            result.message || "Gagal mengambil data user."
        );
    }

    return result;
}


/* =========================
   LOAD USERS
========================= */

async function loadUsers(page = 1) {

    const container = document.getElementById("tableContainer");

    container.innerHTML = `
        <div class="loading">
            Memuat data user...
        </div>
    `;

    try {

        const search = document
            .getElementById("search")
            .value
            .trim();

        const params = new URLSearchParams();

        params.set("page", page);
        params.set("limit", LIMIT);

        if (search !== "") {
            params.set("search", search);
        }

        const result = await getJson(
            API_URL + "?" + params.toString()
        );

        if (!result) {
            return;
        }

        const users = Array.isArray(result.data)
            ? result.data
            : [];

        const pagination = result.pagination || {};

        currentPage = Number(
            pagination.page || page
        );

        totalPages = Number(
            pagination.total_pages || 1
        );

        renderUsers(users);
        renderPagination(pagination);

    } catch (error) {

        console.error(error);

        container.innerHTML = `
            <div class="error">
                ❌ ${escapeHtml(error.message)}
            </div>
        `;

        document.getElementById("pagination").innerHTML = "";
        document.getElementById("paginationInfo").textContent = "-";
    }
}


/* =========================
   RENDER USERS
========================= */

function renderUsers(users) {

    const container = document.getElementById("tableContainer");

    if (!users.length) {

        container.innerHTML = `
            <div class="empty">
                👥 Tidak ada data user ditemukan.
            </div>
        `;

        return;
    }


    let html = `
        <div class="table-wrapper">

            <table>

                <thead>
                    <tr>
                        <th>No</th>
                        <th>Member Code</th>
                        <th>User</th>
                        <th>NIK</th>
                        <th>Email</th>
                        <th>No. Telp</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
    `;


    users.forEach((user, index) => {

        const no =
            ((currentPage - 1) * LIMIT) +
            index +
            1;

        const idUser = user.id_user;

        const nama = user.nama || "-";
        const memberCode = user.member_code || "-";
        const nik = user.nik || "-";
        const email = user.email || "-";
        const noTelp = user.no_telp || "-";
        const status = user.status || "-";
        


        const statusClass =
            String(status).toLowerCase() === "aktif"
                ? "status-aktif"
                : "status-nonaktif";


        html += `
            <tr>

                <td>
                    ${no}
                </td>

                <td>
                    <span class="member-code">
                        ${escapeHtml(memberCode)}
                    </span>
                </td>

                <td>

                    <div class="user-info">

                        

                        <div>
                            <div class="user-name">
                                ${escapeHtml(nama)}
                            </div>
                        </div>

                    </div>

                </td>

                <td>
                    ${escapeHtml(nik)}
                </td>

                <td>
                    ${escapeHtml(email)}
                </td>

                <td>
                    ${escapeHtml(noTelp)}
                </td>

                <td>

                    <span class="status ${statusClass}">
                        ${escapeHtml(status)}
                    </span>

                </td>

                <td>

                    <div class="action-buttons">

                        <a
                            href="./detail.php?id=${encodeURIComponent(idUser)}"
                            class="action-btn btn-detail"
                        >
                            Detail
                        </a>

                        <a
                            href="./edit.php?id=${encodeURIComponent(idUser)}"
                            class="action-btn btn-edit"
                        >
                            Edit
                        </a>

                        <button
                            class="action-btn btn-danger"
                            onclick="deleteUser(${user.id_user})"
                        >
                            🗑️ Hapus
                        </button>

                    </div>

                </td>

            </tr>
        `;
    });


    html += `
                </tbody>

            </table>

        </div>
    `;

    container.innerHTML = html;
}


/* =========================
   PAGINATION
========================= */

function renderPagination(pagination) {

    const paginationEl =
        document.getElementById("pagination");

    const infoEl =
        document.getElementById("paginationInfo");


    const page =
        Number(pagination.page || currentPage);

    const limit =
        Number(pagination.limit || LIMIT);

    const total =
        Number(pagination.total || 0);

    totalPages =
        Number(pagination.total_pages || 1);


    if (total === 0) {

        infoEl.textContent = "Tidak ada data";

        paginationEl.innerHTML = "";

        return;
    }


    const start =
        ((page - 1) * limit) + 1;

    const end =
        Math.min(page * limit, total);


    infoEl.textContent =
        `Menampilkan ${start}-${end} dari ${total} user`;


    let html = "";


    html += `
        <button
            class="page-btn"
            onclick="loadUsers(${page - 1})"
            ${page <= 1 ? "disabled" : ""}
        >
            ‹
        </button>
    `;


    const maxButtons = 5;

    let startPage =
        Math.max(1, page - 2);

    let endPage =
        Math.min(totalPages, startPage + maxButtons - 1);


    if (endPage - startPage < maxButtons - 1) {

        startPage =
            Math.max(1, endPage - maxButtons + 1);
    }


    for (
        let i = startPage;
        i <= endPage;
        i++
    ) {

        html += `
            <button
                class="page-btn ${i === page ? "active" : ""}"
                onclick="loadUsers(${i})"
            >
                ${i}
            </button>
        `;
    }


    html += `
        <button
            class="page-btn"
            onclick="loadUsers(${page + 1})"
            ${page >= totalPages ? "disabled" : ""}
        >
            ›
        </button>
    `;


    paginationEl.innerHTML = html;
}


/* =========================
   SEARCH
========================= */

document
    .getElementById("search")
    .addEventListener("input", function() {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            loadUsers(1);
        }, 400);

    });


/* =========================
   RESET
========================= */

function resetSearch() {

    document.getElementById("search").value = "";

    loadUsers(1);
}


/* =========================
   DELETE USER
========================= */

async function deleteUser(id) {
    const confirmed = confirm(
        "Yakin ingin menghapus anggota ini?\n\n" +
        "Data yang sudah dihapus tidak dapat dikembalikan."
    );

    if (!confirmed) return;

    try {
        const response = await fetch(
            API_BASE + "/users/delete.php",
            {
                method: "DELETE",
                credentials: "include",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    id_user: Number(id)
                })
            }
        );

        if (response.status === 401) {
            window.location.href = "../../auth/login.php";
            return;
        }

        const result = await response.json();

        if (!response.ok || !result.status) {
            throw new Error(
                result.message || "Gagal menghapus anggota."
            );
        }

        alert(
            result.message ||
            "Anggota berhasil dihapus."
        );

        loadUsers(1);

    } catch (error) {
        console.error("Delete user error:", error);

        alert(
            error.message ||
            "Terjadi kesalahan saat menghapus anggota."
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
   INIT
========================= */

loadUsers(1);

</script>

</body>
</html>