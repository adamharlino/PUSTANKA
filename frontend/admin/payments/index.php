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

    <title>Pembayaran Denda - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
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
            padding: 30px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .header h1 {
            font-size: 28px;
            color: #0f2747;
        }

        .admin-name {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .filters {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 180px 180px auto;
            gap: 12px;
        }

        input,
        select,
        button {
            height: 42px;
            border-radius: 7px;
            border: 1px solid #d1d5db;
            padding: 0 12px;
            font-size: 14px;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #2563eb;
        }

        button {
            cursor: pointer;
            border: none;
            background: #2563eb;
            color: white;
            font-weight: bold;
            padding: 0 18px;
        }

        button:hover {
            opacity: .9;
        }

        .btn-reset {
            background: #64748b;
        }

        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
            overflow: hidden;
        }

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
            padding: 14px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }

        td {
            padding: 13px 14px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        tr:hover td {
            background: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-disetujui {
            background: #dcfce7;
            color: #166534;
        }

        .badge-ditolak {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-qris {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-transfer {
            background: #ede9fe;
            color: #6d28d9;
        }

        .btn-detail {
            background: #2563eb;
            padding: 8px 12px;
            font-size: 12px;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .loading {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }

        .message {
            display: none;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            gap: 15px;
        }

        .pagination-info {
            color: #64748b;
            font-size: 14px;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
        }

        .pagination-buttons button {
            min-width: 40px;
        }

        .pagination-buttons button:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
        }

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .filter-grid {
                grid-template-columns: 1fr 1fr;
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
                padding: 20px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
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

            <a href="./index.php" class="active">
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

    <main class="main">

        <div class="header">
            <h1>Pembayaran Denda</h1>

            <div class="admin-name">
                <?= htmlspecialchars($_SESSION["nama"] ?? "Admin") ?>
            </div>
        </div>

        <div id="message" class="message"></div>

        <div class="filters">

            <div class="filter-grid">

                <input
                    type="text"
                    id="search"
                    placeholder="Cari nama, NIK, member code, atau judul buku..."
                >

                <select id="status">
                    <option value="">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="disetujui">Disetujui</option>
                    <option value="ditolak">Ditolak</option>
                </select>

                <select id="metode">
                    <option value="">Semua Metode</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer_bank">Transfer Bank</option>
                </select>

                <button class="btn-reset" onclick="resetFilter()">
                    Reset
                </button>

            </div>

        </div>

        <div class="table-card">

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Peminjam</th>
                            <th>Buku</th>
                            <th>Nominal</th>
                            <th>Metode</th>
                            <th>Tanggal</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody id="paymentTable">

                        <tr>
                            <td colspan="8" class="loading">
                                Memuat data...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>

            <div class="pagination">

                <div id="paginationInfo" class="pagination-info">
                    -
                </div>

                <div class="pagination-buttons">

                    <button
                        id="prevButton"
                        onclick="changePage(-1)"
                    >
                        ‹
                    </button>

                    <button
                        id="nextButton"
                        onclick="changePage(1)"
                    >
                        ›
                    </button>

                </div>

            </div>

        </div>

    </main>

</div>

<script>

const API_URL = "../../../api/payments/admin_list.php";
const LOGOUT_URL = "../../../api/auth/logout.php";

let currentPage = 1;
let totalPages = 1;

let searchTimer = null;

const paymentTable = document.getElementById("paymentTable");
const paginationInfo = document.getElementById("paginationInfo");
const prevButton = document.getElementById("prevButton");
const nextButton = document.getElementById("nextButton");
const message = document.getElementById("message");

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

function formatRupiah(value) {

    const number = Number(value || 0);

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(number);
}

function formatTanggal(value) {

    if (!value) {
        return "-";
    }

    const date = new Date(value.replace(" ", "T"));

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
}

function getStatusBadge(status) {

    status = status || "";

    let className = "badge-pending";
    let label = status;

    if (status === "pending") {
        className = "badge-pending";
        label = "Pending";
    } 
    else if (status === "disetujui") {
        className = "badge-disetujui";
        label = "Disetujui";
    } 
    else if (status === "ditolak") {
        className = "badge-ditolak";
        label = "Ditolak";
    }

    return `
        <span class="badge ${className}">
            ${escapeHtml(label)}
        </span>
    `;
}

function getMetodeBadge(metode) {

    if (metode === "qris") {

        return `
            <span class="badge badge-qris">
                QRIS
            </span>
        `;
    }

    if (metode === "transfer_bank") {

        return `
            <span class="badge badge-transfer">
                Transfer Bank
            </span>
        `;
    }

    return `
        <span class="badge">
            ${escapeHtml(metode || "-")}
        </span>
    `;
}

async function loadPayments() {

    paymentTable.innerHTML = `
        <tr>
            <td colspan="8" class="loading">
                Memuat data...
            </td>
        </tr>
    `;

    hideMessage();

    const search = document.getElementById("search").value.trim();
    const status = document.getElementById("status").value;
    const metode = document.getElementById("metode").value;

    const params = new URLSearchParams();

    params.set("page", currentPage);
    params.set("limit", 10);

    if (search) {
        params.set("search", search);
    }

    if (status) {
        params.set("status", status);
    }

    if (metode) {
        params.set("metode", metode);
    }

    try {

        const response = await fetch(
            `${API_URL}?${params.toString()}`,
            {
                method: "GET",
                credentials: "include"
            }
        );

        const result = await response.json();

        if (response.status === 401) {

            window.location.href = "../../auth/login.php";
            return;
        }

        if (!response.ok || !result.status) {

            throw new Error(
                result.message || "Gagal mengambil data payment."
            );
        }

        renderPayments(result.data || []);
        renderPagination(result.pagination);

    } catch (error) {

        paymentTable.innerHTML = `
            <tr>
                <td colspan="8" class="empty">
                    Gagal mengambil data payment.
                </td>
            </tr>
        `;

        showError(error.message);
    }
}

function renderPayments(data) {

    if (!data.length) {

        paymentTable.innerHTML = `
            <tr>
                <td colspan="8" class="empty">
                    Belum ada data payment.
                </td>
            </tr>
        `;

        return;
    }

    paymentTable.innerHTML = data.map((item, index) => {

        const nomor =
            ((currentPage - 1) * 10) + index + 1;

        const peminjam =
            item.peminjam || {};

        const buku =
            item.buku || {};

        return `
            <tr>

                <td>
                    ${nomor}
                </td>

                <td>
                    <strong>
                        ${escapeHtml(peminjam.nama || "-")}
                    </strong>

                    <br>

                    <small>
                        ${escapeHtml(
                            peminjam.member_code || peminjam.nik || "-"
                        )}
                    </small>
                </td>

                <td>
                    ${escapeHtml(buku.judul || "-")}
                </td>

                <td>
                    <strong>
                        ${formatRupiah(item.nominal)}
                    </strong>
                </td>

                <td>
                    ${getMetodeBadge(item.metode)}
                </td>

                <td>
                    ${formatTanggal(item.tanggal_bayar)}
                </td>

                <td>
                    ${getStatusBadge(item.status || item.status_payment)}
                </td>

                <td>
                    <button
                        class="btn-detail"
                        onclick="openDetail(${Number(item.id_payment)})"
                    >
                        Detail
                    </button>
                </td>

            </tr>
        `;

    }).join("");
}

function renderPagination(pagination) {

    if (!pagination) {
        paginationInfo.textContent = "-";
        return;
    }

    const page =
        Number(pagination.page || currentPage);

    const total =
        Number(pagination.total || 0);

    totalPages =
        Number(pagination.total_pages || 1);

    currentPage = page;

    paginationInfo.textContent =
        `Halaman ${page} dari ${totalPages} • Total ${total} payment`;

    prevButton.disabled = page <= 1;
    nextButton.disabled = page >= totalPages;
}

function changePage(direction) {

    const nextPage = currentPage + direction;

    if (nextPage < 1 || nextPage > totalPages) {
        return;
    }

    currentPage = nextPage;

    loadPayments();
}

function resetFilter() {

    document.getElementById("search").value = "";
    document.getElementById("status").value = "";
    document.getElementById("metode").value = "";

    currentPage = 1;

    loadPayments();
}

function openDetail(id) {

    if (!id) {
        return;
    }

    window.location.href =
        `detail.php?id=${encodeURIComponent(id)}`;
}

function showError(text) {

    message.textContent = text;
    message.className = "message error show";
}

function hideMessage() {

    message.textContent = "";
    message.className = "message";
}

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

document.getElementById("search")
    .addEventListener("input", function() {

        clearTimeout(searchTimer);

        searchTimer = setTimeout(function() {

            currentPage = 1;
            loadPayments();

        }, 400);
    });

document.getElementById("status")
    .addEventListener("change", function() {

        currentPage = 1;
        loadPayments();

    });

document.getElementById("metode")
    .addEventListener("change", function() {

        currentPage = 1;
        loadPayments();

    });

loadPayments();

</script>

</body>
</html>