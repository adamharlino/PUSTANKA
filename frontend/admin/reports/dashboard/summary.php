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
    header("Location: ../../../../auth/login.php");
    exit;
}

$adminName =$_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Reports Summary - Pustanka</title>

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
            margin: 15px 0 10px;
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
            margin-bottom: 24px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        /* =========================
           FILTER
        ========================= */

        .filter-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }

        .filter-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            color: #475569;
            font-weight: 600;
        }

        .form-group input {
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            font-size: 14px;
            min-width: 180px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 10px 18px;
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

        /* =========================
           STATUS
        ========================= */

        .loading {
            display: none;
            background: #eff6ff;
            color: #1d4ed8;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .error {
            display: none;
            background: #fef2f2;
            color: #b91c1c;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .period-info {
            display: none;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            color: #475569;
        }

        /* =========================
           STAT CARDS
        ========================= */

        .section-title {
            font-size: 18px;
            margin-bottom: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 20px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }

        .stat-icon {
            font-size: 26px;
            margin-bottom: 12px;
        }

        .stat-label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .stat-value {
            font-size: 25px;
            font-weight: bold;
            color: #0f172a;
        }

        .stat-description {
            font-size: 12px;
            color: #94a3b8;
            margin-top: 6px;
        }

        /* =========================
           REPORT GRID
        ========================= */

        .report-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }

        .report-card {
            background: white;
            border-radius: 14px;
            padding: 22px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.06);
        }

        .report-card h3 {
            margin-bottom: 18px;
            font-size: 17px;
        }

        .report-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #e2e8f0;
        }

        .report-row:last-child {
            border-bottom: none;
        }

        .report-row span:first-child {
            color: #64748b;
            font-size: 14px;
        }

        .report-row strong {
            font-size: 15px;
            color: #0f172a;
        }

        .warning {
            color: #dc2626 !important;
        }

        .success {
            color: #16a34a !important;
        }

        .money {
            color: #2563eb !important;
        }

        /* =========================
           QUICK REPORT
        ========================= */

        .quick-links {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
        }

        .quick-link {
            display: block;
            text-decoration: none;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 18px;
            color: #1f2937;
            transition: 0.2s;
        }

        .quick-link:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(37, 99, 235, 0.08);
        }

        .quick-link .icon {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .quick-link strong {
            display: block;
            margin-bottom: 4px;
        }

        .quick-link span {
            font-size: 12px;
            color: #64748b;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1100px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .quick-links {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 800px) {
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
                padding: 20px;
            }

            .report-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .stats-grid,
            .quick-links {
                grid-template-columns: 1fr;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group input {
                width: 100%;
            }

            .btn {
                width: 100%;
            }

            .header h1 {
                font-size: 23px;
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
         SIDEBAR
    ========================== -->

    <aside class="sidebar">

        <div class="brand">
            <h2>Pustanka</h2>
            <p>Sistem Informasi Perpustakaan</p>
        </div>

        <nav class="menu">

            <a href="../../dashboard/">
                <span>Dashboard</span>
            </a>

            <a href="../../profile/">
                <span>Profil</span>
            </a>

            <a href="../../books/">
                <span>Buku</span>
            </a>

            <a href="../../users/">
                <span>Anggota</span>
            </a>

            <a href="../../loans/">
                <span>Peminjaman</span>
            </a>

            <a href="../../attendance/">
                <span>Kehadiran</span>
            </a>

            <a href="../../returns/">
                <span>Pengembalian</span>
            </a>

            <a href="./summary.php" class="active">
                <span>Laporan</span>
            </a>

            <a href="../../fines/">
                <span>Denda</span>
            </a>

            <a href="../../payments/">
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

        <div class="header">

            <h1>
                Ringkasan Laporan
            </h1>

            <p>
                Ringkasan statistik sistem perpustakaan.
            </p>

        </div>

        <button type="button" class="pdf-download-btn" onclick="window.print()">⬇ Cetak / Simpan PDF</button>


        <!-- =========================
             FILTER
        ========================== -->

        <div class="filter-card">

            <div class="filter-title">
                Filter Periode Laporan
            </div>

            <form
                id="filterForm"
                class="filter-form"
            >

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


                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Terapkan
                </button>


                <button
                    type="button"
                    class="btn btn-secondary"
                    id="resetButton"
                >
                    Reset
                </button>

            </form>

        </div>


        <div
            id="loading"
            class="loading"
        >
            Memuat data laporan...
        </div>


        <div
            id="error"
            class="error"
        ></div>


        <div
            id="periodInfo"
            class="period-info"
        ></div>


        <!-- =========================
             STATISTICS
        ========================== -->

        <h2 class="section-title">
            Statistik Utama
        </h2>


        <div class="stats-grid">

            <div class="stat-card">

                <div class="stat-label">
                    Total Anggota
                </div>

                <div
                    class="stat-value"
                    id="totalUser"
                >
                    -
                </div>

                <div class="stat-description">
                    Jumlah seluruh anggota
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Total Buku
                </div>

                <div
                    class="stat-value"
                    id="totalBuku"
                >
                    -
                </div>

                <div class="stat-description">
                    Seluruh koleksi buku
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Total Peminjaman Buku
                </div>

                <div
                    class="stat-value"
                    id="totalPeminjaman"
                >
                    -
                </div>

                <div class="stat-description">
                    Semua transaksi peminjaman buku
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Total Pengembalian buku
                </div>

                <div
                    class="stat-value"
                    id="totalPengembalian"
                >
                    -
                </div>

                <div class="stat-description">
                    Buku yang telah dikembalikan
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Total Denda
                </div>

                <div
                    class="stat-value"
                    id="totalDenda"
                >
                    -
                </div>

                <div class="stat-description">
                    Akumulasi seluruh denda
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Pembayaran Disetujui
                </div>

                <div
                    class="stat-value"
                    id="totalPembayaran"
                >
                    -
                </div>

                <div class="stat-description">
                    Total pembayaran yang disetujui
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Total Kehadiran Anggota
                </div>

                <div
                    class="stat-value"
                    id="totalKehadiran"
                >
                    -
                </div>

                <div class="stat-description">
                    Riwayat kehadiran anggota
                </div>

            </div>


            <div class="stat-card">

                <div class="stat-label">
                    Peminjaman Terlambat
                </div>

                <div
                    class="stat-value warning"
                    id="peminjamanTerlambat"
                >
                    -
                </div>

                <div class="stat-description">
                    Melewati batas toleransi
                </div>

            </div>

        </div>


        <!-- =========================
             DETAIL REPORT
        ========================== -->

        <h2 class="section-title">
            Detail Ringkasan
        </h2>


        <div class="report-grid">

            <!-- BUKU -->

            <div class="report-card">

                <h3>
                    Status Buku
                </h3>

                <div class="report-row">

                    <span>
                        Total Buku
                    </span>

                    <strong id="detailTotalBuku">
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Buku Tersedia
                    </span>

                    <strong
                        class="success"
                        id="bukuTersedia"
                    >
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Sedang Dipinjam
                    </span>

                    <strong id="bukuDipinjam">
                        -
                    </strong>

                </div>

            </div>


            <!-- PEMINJAMAN -->

            <div class="report-card">

                <h3>
                    Peminjaman
                </h3>

                <div class="report-row">

                    <span>
                        Total Peminjaman
                    </span>

                    <strong id="detailPeminjaman">
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Peminjaman Aktif
                    </span>

                    <strong id="peminjamanAktif">
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Terlambat
                    </span>

                    <strong
                        class="warning"
                        id="detailPeminjamanTerlambat"
                    >
                        -
                    </strong>

                </div>

            </div>


            <!-- DENDA -->

            <div class="report-card">

                <h3>
                    Denda
                </h3>

                <div class="report-row">

                    <span>
                        Jumlah Denda
                    </span>

                    <strong id="jumlahDenda">
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Total Denda
                    </span>

                    <strong
                        class="money"
                        id="detailTotalDenda"
                    >
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Belum Dibayar
                    </span>

                    <strong
                        class="warning"
                        id="dendaBelumBayar"
                    >
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Nominal Belum Dibayar
                    </span>

                    <strong
                        class="warning"
                        id="totalDendaBelumBayar"
                    >
                        -
                    </strong>

                </div>

            </div>


            <!-- PEMBAYARAN -->

            <div class="report-card">

                <h3>
                    Pembayaran
                </h3>

                <div class="report-row">

                    <span>
                        Pembayaran Disetujui
                    </span>

                    <strong
                        class="success"
                        id="jumlahPembayaran"
                    >
                        -
                    </strong>

                </div>

                <div class="report-row">

                    <span>
                        Total Pembayaran
                    </span>

                    <strong
                        class="money"
                        id="detailTotalPembayaran"
                    >
                        -
                    </strong>

                </div>

            </div>


            <!-- KEHADIRAN -->

            <div class="report-card">

                <h3>
                    Kehadiran
                </h3>

                <div class="report-row">

                    <span>
                        Total Kehadiran
                    </span>

                    <strong id="detailKehadiran">
                        -
                    </strong>

                </div>

            </div>


            <!-- PENGEMBALIAN -->

            <div class="report-card">

                <h3>
                    Pengembalian
                </h3>

                <div class="report-row">

                    <span>
                        Total Pengembalian
                    </span>

                    <strong id="detailPengembalian">
                        -
                    </strong>

                </div>

            </div>

        </div>


        <!-- =========================
             QUICK REPORTS
        ========================== -->

        <h2 class="section-title">
            Laporan Detail
        </h2>


        <div class="quick-links">

            <a
                href="../loans.php"
                class="quick-link"
            >

                <strong>
                    Laporan Peminjaman
                </strong>

                <span>
                    Lihat seluruh transaksi peminjaman.
                </span>
            </a>


            <a
                href="../returns.php"
                class="quick-link"
            >

                <strong>
                    Laporan Pengembalian
                </strong>

                <span>
                    Lihat seluruh transaksi pengembalian.
                </span>
            </a>


            <a
                href="../fines.php"
                class="quick-link"
            >

                <strong>
                    Laporan Denda
                </strong>

                <span>
                    Lihat seluruh data denda.
                </span>
            </a>


            <a
                href="../payment.php"
                class="quick-link"
            >

                <strong>
                    Laporan Pembayaran
                </strong>

                <span>
                    Lihat seluruh pembayaran denda.
                </span>
            </a>


            <a
                href="../attendance.php"
                class="quick-link"
            >

                <strong>
                    Laporan Kehadiran
                </strong>

                <span>
                    Lihat riwayat kehadiran anggota.
                </span>
            </a>


            <a
                href="../users.php"
                class="quick-link"
            >

                <strong>
                    Laporan Anggota
                </strong>

                <span>
                    Lihat data seluruh anggota.
                </span>
            </a>

        </div>

    </main>

</div>


<script>

    const API_URL =
        "../../../../api/reports/summary.php";


    const filterForm =
        document.getElementById("filterForm");

    const resetButton =
        document.getElementById("resetButton");

    const tanggalMulai =
        document.getElementById("tanggalMulai");

    const tanggalSelesai =
        document.getElementById("tanggalSelesai");

    const loading =
        document.getElementById("loading");

    const errorBox =
        document.getElementById("error");

    const periodInfo =
        document.getElementById("periodInfo");


    /* =========================
       FORMAT ANGKA
    ========================== */

    function formatNumber(value) {

        return new Intl.NumberFormat("id-ID")
            .format(Number(value || 0));

    }


    /* =========================
       FORMAT RUPIAH
    ========================== */

    function formatRupiah(value) {

        return new Intl.NumberFormat(
            "id-ID",
            {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }
        ).format(Number(value || 0));

    }


    /* =========================
       LOAD SUMMARY
    ========================== */

    async function loadSummary() {

        loading.style.display = "block";
        errorBox.style.display = "none";

        let url = API_URL;

        const params = new URLSearchParams();


        if (tanggalMulai.value) {

            params.append(
                "tanggal_mulai",
                tanggalMulai.value
            );

        }


        if (tanggalSelesai.value) {

            params.append(
                "tanggal_selesai",
                tanggalSelesai.value
            );

        }


        if (params.toString()) {

            url += "?" + params.toString();

        }


        try {

            const response = await fetch(
                url,
                {
                    method: "GET",
                    credentials: "include"
                }
            );


            const result =
                await response.json();


            if (response.status === 401) {

                window.location.href =
                    "../../../auth/login.php";

                return;

            }


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal mengambil summary laporan."
                );

            }


            renderSummary(result);


        } catch (error) {

            console.error(error);

            errorBox.textContent =
                error.message ||
                "Terjadi kesalahan.";

            errorBox.style.display =
                "block";

        } finally {

            loading.style.display = "none";

        }

    }


    /* =========================
       RENDER SUMMARY
    ========================== */

    function renderSummary(result) {

        const summary =
            result.summary || {};

        const periode =
            result.periode || {};


        /* =========================
           PERIODE
        ========================== */

        if (
            periode.tanggal_mulai ||
            periode.tanggal_selesai
        ) {

            const mulai =
                periode.tanggal_mulai || "-";

            const selesai =
                periode.tanggal_selesai || "-";

            periodInfo.textContent =
                `Periode laporan: ${mulai} s/d ${selesai}`;

            periodInfo.style.display =
                "block";

        } else {

            periodInfo.style.display =
                "none";

        }


        /* =========================
           STAT CARDS
        ========================== */

        document.getElementById(
            "totalUser"
        ).textContent =
            formatNumber(
                summary.total_user
            );


        document.getElementById(
            "totalBuku"
        ).textContent =
            formatNumber(
                summary.total_buku
            );


        document.getElementById(
            "totalPeminjaman"
        ).textContent =
            formatNumber(
                summary.total_peminjaman
            );


        document.getElementById(
            "totalPengembalian"
        ).textContent =
            formatNumber(
                summary.total_pengembalian
            );


        document.getElementById(
            "totalDenda"
        ).textContent =
            formatRupiah(
                summary.total_denda
            );


        document.getElementById(
            "totalPembayaran"
        ).textContent =
            formatRupiah(
                summary.total_pembayaran_disetujui
            );


        document.getElementById(
            "totalKehadiran"
        ).textContent =
            formatNumber(
                summary.total_kehadiran
            );


        document.getElementById(
            "peminjamanTerlambat"
        ).textContent =
            formatNumber(
                summary.peminjaman_terlambat
            );


        /* =========================
           BUKU
        ========================== */

        document.getElementById(
            "detailTotalBuku"
        ).textContent =
            formatNumber(
                summary.total_buku
            );


        document.getElementById(
            "bukuTersedia"
        ).textContent =
            formatNumber(
                summary.buku_tersedia
            );


        document.getElementById(
            "bukuDipinjam"
        ).textContent =
            formatNumber(
                summary.buku_dipinjam
            );


        /* =========================
           PEMINJAMAN
        ========================== */

        document.getElementById(
            "detailPeminjaman"
        ).textContent =
            formatNumber(
                summary.total_peminjaman
            );


        document.getElementById(
            "peminjamanAktif"
        ).textContent =
            formatNumber(
                summary.peminjaman_aktif
            );


        document.getElementById(
            "detailPeminjamanTerlambat"
        ).textContent =
            formatNumber(
                summary.peminjaman_terlambat
            );


        /* =========================
           DENDA
        ========================== */

        document.getElementById(
            "jumlahDenda"
        ).textContent =
            formatNumber(
                summary.jumlah_denda
            );


        document.getElementById(
            "detailTotalDenda"
        ).textContent =
            formatRupiah(
                summary.total_denda
            );


        document.getElementById(
            "dendaBelumBayar"
        ).textContent =
            formatNumber(
                summary.denda_belum_bayar
            );


        document.getElementById(
            "totalDendaBelumBayar"
        ).textContent =
            formatRupiah(
                summary.total_denda_belum_bayar
            );


        /* =========================
           PEMBAYARAN
        ========================== */

        document.getElementById(
            "jumlahPembayaran"
        ).textContent =
            formatNumber(
                summary.jumlah_pembayaran_disetujui
            );


        document.getElementById(
            "detailTotalPembayaran"
        ).textContent =
            formatRupiah(
                summary.total_pembayaran_disetujui
            );


        /* =========================
           KEHADIRAN
        ========================== */

        document.getElementById(
            "detailKehadiran"
        ).textContent =
            formatNumber(
                summary.total_kehadiran
            );


        /* =========================
           PENGEMBALIAN
        ========================== */

        document.getElementById(
            "detailPengembalian"
        ).textContent =
            formatNumber(
                summary.total_pengembalian
            );

    }


    /* =========================
       FILTER SUBMIT
    ========================== */

    filterForm.addEventListener(
        "submit",
        function(event) {

            event.preventDefault();

            loadSummary();

        }
    );


    /* =========================
       RESET
    ========================== */

    resetButton.addEventListener(
        "click",
        function() {

            tanggalMulai.value = "";
            tanggalSelesai.value = "";

            loadSummary();

        }
    );


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
            "../../../auth/login.php";
    }


    /* =========================
       LOAD AWAL
    ========================== */

    loadSummary();

</script>

</body>
</html>