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

    <title>Laporan Peminjaman - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            margin-bottom: 24px;
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
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
            border-color: #2563eb;
        }

        .filter-actions {
            display: flex;
            gap: 8px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 16px;
            cursor: pointer;
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

        /* =========================
           INFO
        ========================= */

        .info-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px;
        }

        .total-text {
            color: #64748b;
            font-size: 14px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 23, 42, 0.06);
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead {
            background: #f8fafc;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            color: #475569;
            font-weight: 700;
            white-space: nowrap;
        }

        td {
            color: #334155;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        /* =========================
           BADGE
        ========================= */

        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-dipinjam {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-dikembalikan {
            background: #dcfce7;
            color: #15803d;
        }

        .badge-terlambat {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-default {
            background: #e2e8f0;
            color: #475569;
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

        .page-btn {
            min-width: 38px;
            height: 38px;
            border: 1px solid #d1d5db;
            background: white;
            border-radius: 8px;
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

        .empty {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {
            .filter-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .main {
                padding: 15px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- =========================
         MAIN
    ========================== -->

    <main class="main">

        <!-- TOMBOL KEMBALI -->
        <div class="back-btn-wrapper">
            <a href="./dashboard/summary.php" class="btn-back">
                ← Kembali ke Ringkasan Laporan
            </a>
        </div>

        <div class="header">
            <h1>Laporan Peminjaman</h1>
            <p>
                Monitoring dan laporan seluruh data peminjaman buku.
            </p>
        </div>


        <!-- ERROR -->

        <div id="errorBox" class="error"></div>


        <!-- =========================
             FILTER
        ========================== -->

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


                <div class="form-group">
                    <label for="status">
                        Status
                    </label>

                    <select id="status">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="dipinjam">
                            Dipinjam
                        </option>

                        <option value="terlambat">
                            Terlambat
                        </option>

                        <option value="dikembalikan">
                            Dikembalikan
                        </option>

                    </select>
                </div>


                <div class="filter-actions">

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="loadLoans()"
                    >
                        Tampilkan
                    </button>

                    <button
                        type="button"
                        class="btn btn-secondary"
                        onclick="resetFilter()"
                    >
                        Reset
                    </button>

                </div>

            </div>

        </div>


        <!-- =========================
             INFO
        ========================== -->

        <div class="info-bar">

            <div class="total-text">
                Total data:
                <strong id="totalData">0</strong>
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

                    <tbody id="loanTableBody">

                        <tr>
                            <td colspan="8">
                                <div class="loading">
                                    Memuat data...
                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- PAGINATION -->

            <div
                id="pagination"
                class="pagination"
            ></div>

        </div>

    </main>

</div>


<script>

const API_URL =
    "../../../api/reports/loans.php";

let currentPage = 1;
const limit = 10;


/* ==========================================
   ELEMENT
========================================== */

const tableBody =
    document.getElementById("loanTableBody");

const pagination =
    document.getElementById("pagination");

const totalData =
    document.getElementById("totalData");

const errorBox =
    document.getElementById("errorBox");


/* ==========================================
   ERROR
========================================== */

function showError(message) {

    errorBox.textContent = message;
    errorBox.style.display = "block";
}


function hideError() {

    errorBox.textContent = "";
    errorBox.style.display = "none";
}


/* ==========================================
   FORMAT TANGGAL
========================================== */

function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date =
        new Date(dateString);

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleDateString(
        "id-ID",
        {
            day: "2-digit",
            month: "2-digit",
            year: "numeric"
        }
    );
}


/* ==========================================
   STATUS BADGE
========================================== */

function statusBadge(status) {

    if (!status) {
        return "-";
    }

    const value =
        String(status).toLowerCase();

    let className =
        "badge-default";

    if (value === "dipinjam") {
        className = "badge-dipinjam";
    }

    if (value === "dikembalikan") {
        className = "badge-dikembalikan";
    }

    if (value === "terlambat") {
        className = "badge-terlambat";
    }

    return `
        <span class="badge ${className}">
            ${status}
        </span>
    `;
}


/* ==========================================
   LOAD DATA
========================================== */

async function loadLoans(page = 1) {

    currentPage = page;

    hideError();

    tableBody.innerHTML = `
        <tr>
            <td colspan="8">
                <div class="loading">
                    Memuat data...
                </div>
            </td>
        </tr>
    `;

    pagination.innerHTML = "";

    try {

        const params =
            new URLSearchParams();

        params.append(
            "page",
            currentPage
        );

        params.append(
            "limit",
            limit
        );


        const tanggalMulai =
            document.getElementById(
                "tanggalMulai"
            ).value;

        const tanggalSelesai =
            document.getElementById(
                "tanggalSelesai"
            ).value;

        const status =
            document.getElementById(
                "status"
            ).value;


        if (tanggalMulai) {

            params.append(
                "tanggal_mulai",
                tanggalMulai
            );

        }


        if (tanggalSelesai) {

            params.append(
                "tanggal_selesai",
                tanggalSelesai
            );

        }


        if (status) {

            params.append(
                "status",
                status
            );

        }


        const response =
            await fetch(
                `${API_URL}?${params.toString()}`,
                {
                    method: "GET",
                    credentials: "include"
                }
            );


        const result =
            await response.json();


        /* =========================
           SESSION ADMIN
        ========================= */

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
                "Gagal mengambil laporan peminjaman."
            );

        }


        /*
         * API dapat mengembalikan data
         * dalam beberapa bentuk.
         */

        let data = [];

        if (Array.isArray(result.data)) {

            data = result.data;

        } else if (
            result.data &&
            Array.isArray(result.data.data)
        ) {

            data = result.data.data;

        } else if (
            result.data &&
            Array.isArray(result.data.loans)
        ) {

            data = result.data.loans;

        } else if (
            Array.isArray(result.loans)
        ) {

            data = result.loans;

        }


        /*
         * Ambil informasi pagination
         */

        const paginationData =
            result.pagination ||
            result.data?.pagination ||
            {};


        const total =
            Number(
                paginationData.total ??
                result.total ??
                data.length
            );


        totalData.textContent =
            total;


        renderTable(data);

        renderPagination(
            paginationData
        );

    } catch (error) {

        console.error(error);

        tableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty">
                        Tidak dapat memuat data.
                    </div>
                </td>
            </tr>
        `;

        showError(
            error.message
        );

    }
}


/* ==========================================
   RENDER TABLE
========================================== */

function renderTable(data) {

    if (!Array.isArray(data) || data.length === 0) {

        tableBody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty">
                        📭 Tidak ada data peminjaman.
                    </div>
                </td>
            </tr>
        `;

        return;
    }


    tableBody.innerHTML =
        data.map(
            (loan, index) => {

                const nomor =
                    (
                        (currentPage - 1) *
                        limit
                    ) +
                    index +
                    1;


                const idLoan =
                    loan.id_loan ??
                    "-";


                const idUser =
                    loan.id_user ??
                    "-";


                const memberCode =
                    loan.member_code ??
                    "-";


                const namaUser =
                    loan.nama_user ??
                    loan.nama ??
                    loan.peminjam ??
                    "-";


                const judul =
                    loan.judul_buku ??
                    loan.judul ??
                    "-";


                const tanggalPinjam =
                    loan.tanggal_pinjam ??
                    null;


                const batasKembali =
                    loan.batas_kembali ??
                    null;


                const tanggalKembali =
                    loan.tanggal_kembali ??
                    null;


                const status =
                    loan.status ??
                    "-";


                return `
                    <tr>

                        <td>
                            ${nomor}
                        </td>

                        <td>
                            ${idLoan}
                        </td>

                        <td>
                            ${idUser}
                        </td>

                        <td>
                            ${escapeHtml(
                                memberCode
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                namaUser
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                judul
                            )}
                        </td>

                        <td>
                            ${formatDate(
                                tanggalPinjam
                            )}
                        </td>

                        <td>
                            ${formatDate(
                                batasKembali
                            )}
                        </td>

                        <td>
                            ${formatDate(
                                tanggalKembali
                            )}
                        </td>

                        <td>
                            ${statusBadge(
                                status
                            )}
                        </td>

                    </tr>
                `;

            }
        ).join("");

}


/* ==========================================
   PAGINATION
========================================== */

function renderPagination(data) {

    pagination.innerHTML = "";

    const totalPages =
        Number(
            data.total_pages ??
            data.pages ??
            1
        );


    if (totalPages <= 1) {
        return;
    }


    for (
        let i = 1;
        i <= totalPages;
        i++
    ) {

        const button =
            document.createElement(
                "button"
            );

        button.className =
            "page-btn" +
            (
                i === currentPage
                    ? " active"
                    : ""
            );

        button.textContent = i;

        button.onclick = () => {

            loadLoans(i);

            window.scrollTo({
                top: 0,
                behavior: "smooth"
            });

        };

        pagination.appendChild(
            button
        );

    }

}


/* ==========================================
   RESET
========================================== */

function resetFilter() {

    document.getElementById(
        "tanggalMulai"
    ).value = "";

    document.getElementById(
        "tanggalSelesai"
    ).value = "";

    document.getElementById(
        "status"
    ).value = "";

    loadLoans(1);
}


/* ==========================================
   ESCAPE HTML
========================================== */

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


/* ==========================================
   LOAD AWAL
========================================== */

loadLoans();

</script>

</body>
</html>