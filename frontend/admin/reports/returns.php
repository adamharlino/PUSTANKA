<?php
session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Pengembalian - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .header p {
            color: #64748b;
        }

        /* =========================
           BACK BUTTON
        ========================= */

        .back-btn-wrapper {
            margin-bottom: 20px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: #e2e8f0;
            color: #334155;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        /* =========================
           FILTER
        ========================= */

        .filter-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .form-group input {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-reset {
            background: #e5e7eb;
            color: #374151;
            margin-left: 8px;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
        }

        .table-header h2 {
            font-size: 19px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px 15px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            white-space: nowrap;
        }

        th {
            background: #f8fafc;
            font-size: 13px;
            color: #475569;
        }

        td {
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-dikembalikan {
            background: #dcfce7;
            color: #166534;
        }

        .status-lain {
            background: #e2e8f0;
            color: #475569;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding: 20px;
        }

        .pagination button {
            border: 1px solid #d1d5db;
            background: white;
            padding: 8px 12px;
            border-radius: 7px;
            cursor: pointer;
        }

        .pagination button.active {
            background: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        @media (max-width: 900px) {
            .filter-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .content {
                padding: 15px;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- CONTENT -->
    <main class="content">

        <!-- TOMBOL KEMBALI -->
        <div class="back-btn-wrapper">
            <a href="./dashboard/summary.php" class="btn-back">
                ← Kembali ke Ringkasan Laporan
            </a>
        </div>

        <div class="header">
            <h1>Laporan Pengembalian</h1>
            <p>Menampilkan riwayat pengembalian buku.</p>
        </div>

        <div id="errorBox" class="error"></div>

        <!-- FILTER -->
        <div class="filter-card">

            <div class="filter-grid">

                <div class="form-group">
                    <label for="tanggalMulai">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="tanggalMulai"
                    >
                </div>

                <div class="form-group">
                    <label for="tanggalSelesai">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        id="tanggalSelesai"
                    >
                </div>

                <div>
                    <button
                        class="btn btn-primary"
                        onclick="loadData()"
                    >
                        Filter
                    </button>

                    <button
                        class="btn btn-reset"
                        onclick="resetFilter()"
                    >
                        Reset
                    </button>
                </div>

            </div>

        </div>

        <!-- TABLE -->
        <div class="table-card">

            <div class="table-header">
                <h2 id="totalData">
                    Laporan Pengembalian
                </h2>
            </div>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>No</th>
                            <th>ID Peminjaman</th>
                            <th>ID Anggota</th>
                            <th>Member Code</th>
                            <th>Nama Anggota</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">

                        <tr>
                            <td
                                colspan="8"
                                class="empty"
                            >
                                Memuat data...
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

const API_URL = "../../../api/reports/returns.php";

let currentPage = 1;
const limit = 10;


/* =========================
   FORMAT TANGGAL
========================= */

function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date = new Date(dateString);

    if (isNaN(date)) {
        return dateString;
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });
}


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
   LOAD DATA
========================= */

async function loadData(page = 1) {

    currentPage = page;

    const tbody = document.getElementById("tableBody");
    const errorBox = document.getElementById("errorBox");

    errorBox.style.display = "none";

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="empty">
                Memuat data...
            </td>
        </tr>
    `;

    const params = new URLSearchParams();

    params.append("page", currentPage);
    params.append("limit", limit);

    const tanggalMulai =
        document.getElementById("tanggalMulai").value;

    const tanggalSelesai =
        document.getElementById("tanggalSelesai").value;

    if (tanggalMulai) {
        params.append("tanggal_mulai", tanggalMulai);
    }

    if (tanggalSelesai) {
        params.append("tanggal_selesai", tanggalSelesai);
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
                result.message ||
                "Gagal mengambil laporan pengembalian."
            );
        }

        renderTable(result.data || []);

        renderPagination(
            result.pagination || {}
        );

    } catch (error) {

        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty">
                    Gagal memuat data.
                </td>
            </tr>
        `;

        errorBox.textContent = error.message;
        errorBox.style.display = "block";
    }
}


/* =========================
   RENDER TABLE
========================= */

function renderTable(data) {

    const tbody =
        document.getElementById("tableBody");

    const totalData =
        document.getElementById("totalData");

    if (!data.length) {

        tbody.innerHTML = `
            <tr>
                <td colspan="8" class="empty">
                    Tidak ada data pengembalian.
                </td>
            </tr>
        `;

        totalData.textContent =
            "Laporan Pengembalian — 0 data";

        return;
    }

    totalData.textContent =
        `Laporan Pengembalian — ${data.length} data`;

    tbody.innerHTML = data.map((item, index) => {

        const nomor =
            ((currentPage - 1) * limit) +
            index +
            1;

        const status =
            item.status || "dikembalikan";

        const statusClass =
            status === "dikembalikan"
                ? "status-dikembalikan"
                : "status-lain";

        return `
            <tr>

                <td>
                    ${nomor}
                </td>

                <td>
                    ${escapeHtml(item.id_loan)}
                </td>

                <td>
                    ${escapeHtml(item.id_user)}
                </td>

                <td>
                    ${escapeHtml(
                        item.member_code ||
                        "-"
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        item.nama_user ||
                        item.nama ||
                        "-"
                    )}
                </td>

                <td>
                    ${escapeHtml(
                        item.judul_buku ||
                        item.judul ||
                        "-"
                    )}
                </td>

                <td>
                    ${formatDate(
                        item.tanggal_pinjam
                    )}
                </td>

                <td>
                    ${formatDate(
                        item.batas_kembali
                    )}
                </td>

                <td>
                    ${formatDate(
                        item.tanggal_kembali
                    )}
                </td>

                <td>
                    <span class="status ${statusClass}">
                        ${escapeHtml(status)}
                    </span>
                </td>

            </tr>
        `;

    }).join("");
}


/* =========================
   PAGINATION
========================= */

function renderPagination(pagination) {

    const container =
        document.getElementById("pagination");

    const totalPages =
        Number(
            pagination.total_pages ||
            pagination.pages ||
            1
        );

    if (totalPages <= 1) {

        container.innerHTML = "";
        return;
    }

    let html = "";

    html += `
        <button
            onclick="loadData(${currentPage - 1})"
            ${currentPage <= 1 ? "disabled" : ""}
        >
            ←
        </button>
    `;

    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        html += `
            <button
                class="${i === currentPage ? "active" : ""}"
                onclick="loadData(${i})"
            >
                ${i}
            </button>
        `;
    }

    html += `
        <button
            onclick="loadData(${currentPage + 1})"
            ${currentPage >= totalPages ? "disabled" : ""}
        >
            →
        </button>
    `;

    container.innerHTML = html;
}


/* =========================
   RESET
========================= */

function resetFilter() {

    document.getElementById(
        "tanggalMulai"
    ).value = "";

    document.getElementById(
        "tanggalSelesai"
    ).value = "";

    loadData(1);
}


/* =========================
   INITIAL LOAD
========================= */

loadData();

</script>

</body>
</html>