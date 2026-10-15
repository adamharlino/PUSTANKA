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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Laporan Denda - Pustanka</title>

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
            grid-template-columns: 1fr 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: bold;
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

        .btn-reset:hover {
            background: #d1d5db;
        }

        /* =========================
           SUMMARY
        ========================= */

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .summary-title {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 25px;
            font-weight: bold;
        }

        .summary-subtitle {
            color: #94a3b8;
            font-size: 12px;
            margin-top: 5px;
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

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-belum-bayar {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-lunas {
            background: #dcfce7;
            color: #166534;
        }

        .status-lain {
            background: #e2e8f0;
            color: #475569;
        }

        /* =========================
           NOMINAL
        ========================= */

        .nominal {
            font-weight: bold;
        }

        /* =========================
           EMPTY / ERROR
        ========================= */

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

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

            .summary-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 700px) {

            .content {
                padding: 15px;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

        }

    
        /* CETAK / SIMPAN SEBAGAI PDF */
        .pdf-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #173b70;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            margin: 8px 0 18px;
        }
        .pdf-download-btn:hover { background: #24539a; }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: #fff !important; color: #111 !important; }
            .sidebar, aside, .back-btn-wrapper, .filter-card, .pdf-download-btn,
            .logout-btn, nav, .pagination, .filter-actions, .filter-buttons,
            button, input, select { display: none !important; }
            .layout, .main, .content, main { display: block !important; width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
            .header { margin: 0 0 14px !important; padding: 0 0 10px !important; box-shadow: none !important; border-bottom: 1px solid #999; }
            table { width: 100% !important; border-collapse: collapse !important; font-size: 9pt !important; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            th, td { border: 1px solid #999 !important; color: #111 !important; padding: 5px !important; }
            .error, .message { display: none !important; }
            canvas, svg { max-width: 100% !important; }
        }

</style>

</head>

<body>

<div class="layout">

    <!-- =========================
         CONTENT
    ========================= -->

    <main class="content">

        <!-- TOMBOL KEMBALI -->
        <div class="back-btn-wrapper">
            <a href="./dashboard/summary.php" class="btn-back">
                ← Kembali ke Ringkasan Laporan
            </a>
        </div>

        <div class="header">

            <h1>
                Laporan Denda
            </h1>

            <p>
                Menampilkan seluruh data denda peminjaman buku.
            </p>

        </div>

        <button type="button" class="pdf-download-btn" onclick="window.print()">⬇ Cetak / Simpan PDF</button>


        <div
            id="errorBox"
            class="error"
        ></div>


        <!-- =========================
             FILTER
        ========================= -->

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

                    <label for="statusFilter">
                        Status Pembayaran
                    </label>

                    <select id="statusFilter">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="belum_bayar">
                            Belum Bayar
                        </option>

                        <option value="lunas">
                            Lunas
                        </option>

                    </select>

                </div>


                <div>

                    <button
                        class="btn btn-primary"
                        onclick="loadData(1)"
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


        <!-- =========================
             SUMMARY
        ========================= -->

        <div class="summary-grid">

            <div class="summary-card">

                <div class="summary-title">
                    Total Data Denda
                </div>

                <div
                    class="summary-value"
                    id="summaryTotal"
                >
                    0
                </div>

                <div class="summary-subtitle">
                    Seluruh denda
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-title">
                    Belum Bayar
                </div>

                <div
                    class="summary-value"
                    id="summaryBelumBayar"
                >
                    0
                </div>

                <div class="summary-subtitle">
                    Denda yang belum lunas
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-title">
                    Lunas
                </div>

                <div
                    class="summary-value"
                    id="summaryLunas"
                >
                    0
                </div>

                <div class="summary-subtitle">
                    Denda yang sudah dibayar
                </div>

            </div>

        </div>


        <!-- =========================
             TABLE
        ========================= -->

        <div class="table-card">

            <div class="table-header">

                <h2 id="totalData">
                    Laporan Denda
                </h2>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                No
                            </th>

                            <th>
                                ID Denda
                            </th>

                            <th>
                                ID Peminjaman
                            </th>

                            <th>
                                ID Anggota
                            </th>

                            <th>
                                Member Code
                            </th>

                            <th>
                                Nama Anggota
                            </th>

                            <th>
                                Judul Buku
                            </th>

                            <th>
                                Nominal Denda
                            </th>

                            <th>
                                Status Pembayaran
                            </th>

                            <th>
                                Tanggal Denda
                            </th>

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

const API_URL =
    "../../../api/reports/fines.php";

let currentPage = 1;

const limit = 10;


/* =========================
   FORMAT RUPIAH
========================= */

function formatRupiah(value) {

    const number =
        Number(value || 0);

    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }
    ).format(number);

}


/* =========================
   FORMAT TANGGAL
========================= */

function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date =
        new Date(dateString);

    if (isNaN(date)) {
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


/* =========================
   ESCAPE HTML
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
   LOAD DATA
========================= */

async function loadData(page = 1) {

    currentPage = page;

    const tbody =
        document.getElementById(
            "tableBody"
        );

    const errorBox =
        document.getElementById(
            "errorBox"
        );

    errorBox.style.display = "none";

    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="empty">
                Memuat data...
            </td>
        </tr>
    `;


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
            "statusFilter"
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
            "status_pembayaran",
            status
        );

    }


    try {

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
                "Gagal mengambil laporan denda."
            );

        }


        const data =
            Array.isArray(result.data)
                ? result.data
                : [];


        renderTable(data);

        renderSummary(data);

        renderPagination(
            result.pagination || {}
        );


    } catch (error) {

        tbody.innerHTML = `
            <tr>
                <td
                    colspan="8"
                    class="empty"
                >
                    Gagal memuat data.
                </td>
            </tr>
        `;


        errorBox.textContent =
            error.message;


        errorBox.style.display =
            "block";

    }

}


/* =========================
   RENDER SUMMARY
========================= */

function renderSummary(data) {

    const total =
        data.length;

    const belumBayar =
        data.filter(
            item =>
                (
                    item.status_pembayaran ||
                    item.denda?.status_pembayaran
                ) === "belum_bayar"
        ).length;


    const lunas =
        data.filter(
            item =>
                (
                    item.status_pembayaran ||
                    item.denda?.status_pembayaran
                ) === "lunas"
        ).length;


    document.getElementById(
        "summaryTotal"
    ).textContent = total;


    document.getElementById(
        "summaryBelumBayar"
    ).textContent = belumBayar;


    document.getElementById(
        "summaryLunas"
    ).textContent = lunas;

}


/* =========================
   RENDER TABLE
========================= */

function renderTable(data) {

    const tbody =
        document.getElementById(
            "tableBody"
        );


    const totalData =
        document.getElementById(
            "totalData"
        );


    if (!data.length) {

        tbody.innerHTML = `
            <tr>
                <td
                    colspan="8"
                    class="empty"
                >
                    Tidak ada data denda.
                </td>
            </tr>
        `;


        totalData.textContent =
            "Laporan Denda — 0 data";

        renderSummary([]);

        return;

    }


    totalData.textContent =
        `Laporan Denda — ${data.length} data`;


    tbody.innerHTML =
        data.map(
            (item, index) => {

                const nomor =
                    (
                        (currentPage - 1) *
                        limit
                    ) +
                    index +
                    1;


                const denda =
                    item.denda ||
                    item;


                const status =
                    denda.status_pembayaran ||
                    item.status_pembayaran ||
                    "belum_bayar";


                const statusClass =
                    status === "lunas"
                        ? "status-lunas"
                        : status === "belum_bayar"
                            ? "status-belum-bayar"
                            : "status-lain";


                const nama =
                    item.nama_peminjam ||
                    item.nama_user ||
                    item.nama ||
                    item.peminjam?.nama ||
                    "-";


                const judul =
                    item.judul_buku ||
                    item.judul ||
                    item.buku?.judul ||
                    "-";


                const idLoan =
                    item.id_loan ||
                    item.peminjaman?.id_loan ||
                    "-";


                const idFine =
                    item.id_fine ||
                    denda.id_fine ||
                    "-";

                const idUser =
                    item.id_user ||
                    "-";

                const memberCode =
                    item.member_code ||
                    "-";


                const nominal =
                    denda.nominal_denda ||
                    item.nominal_denda ||
                    0;


                const tanggal =
                    denda.created_at ||
                    item.created_at ||
                    "-";


                return `
                    <tr>

                        <td>
                            ${nomor}
                        </td>

                        <td>
                            ${escapeHtml(idFine)}
                        </td>

                        <td>
                            ${escapeHtml(idLoan)}
                        </td>

                        <td>
                            ${escapeHtml(idUser)}
                        </td>

                        <td>
                            ${escapeHtml(memberCode)}
                        </td>

                        <td>
                            ${escapeHtml(nama)}
                        </td>

                        <td>
                            ${escapeHtml(judul)}
                        </td>

                        <td class="nominal">
                            ${formatRupiah(nominal)}
                        </td>

                        <td>

                            <span
                                class="status ${statusClass}"
                            >
                                ${
                                    status === "lunas"
                                        ? "Lunas"
                                        : status === "belum_bayar"
                                            ? "Belum Bayar"
                                            : escapeHtml(status)
                                }
                            </span>

                        </td>

                        <td>
                            ${formatDate(tanggal)}
                        </td>

                    </tr>
                `;

            }
        ).join("");

}


/* =========================
   PAGINATION
========================= */

function renderPagination(
    pagination
) {

    const container =
        document.getElementById(
            "pagination"
        );


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
            ${
                currentPage >= totalPages
                    ? "disabled"
                    : ""
            }
        >
            →
        </button>
    `;


    container.innerHTML = html;

}


/* =========================
   RESET FILTER
========================= */

function resetFilter() {

    document.getElementById(
        "tanggalMulai"
    ).value = "";


    document.getElementById(
        "tanggalSelesai"
    ).value = "";


    document.getElementById(
        "statusFilter"
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