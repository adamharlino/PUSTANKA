<?php
session_set_cookie_params([
    "httponly" => true,
    "secure" => false, // ubah true jika production HTTPS
    "samesite" => "Lax"
]);

session_start();

// ==========================================
// VALIDASI SESSION ADMIN
// ==========================================
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

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Admin - Pustanka</title>

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

            background: #f5f7fb;
            color: #1f2937;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        button {
            font-family: inherit;
        }

        /* ==========================================
           LAYOUT
        ========================================== */

        .layout {
            min-height: 100vh;
            display: flex;
        }

        /* ==========================================
           SIDEBAR
        ========================================== */

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

        /* ==========================================
           SIDEBAR BOTTOM
        ========================================== */

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

        /* ==========================================
           MAIN
        ========================================== */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);

            min-height: 100vh;

            padding: 30px;
        }

        /* ==========================================
           HEADER
        ========================================== */

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 20px;

            margin-bottom: 30px;
        }

        .header-left h1 {
            font-size: 30px;
            color: #111827;

            margin-bottom: 8px;
        }

        .header-left p {
            color: #6b7280;
            font-size: 15px;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* ==========================================
           ERROR
        ========================================== */

        .error-box {
            display: none;

            background: #fee2e2;
            color: #b91c1c;

            padding: 15px 18px;

            border-radius: 10px;

            margin-bottom: 20px;
        }

        /* ==========================================
           SECTION & STATISTICS
        ========================================== */

        .section {
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;

            color: #111827;

            margin-bottom: 15px;
        }

        .stats-grid {
            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 20px;
        }

        .stat-card {
            background: white;

            border-radius: 16px;

            padding: 22px;

            box-shadow:
                0 4px 15px
                rgba(0, 0, 0, 0.05);

            transition: 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);

            box-shadow:
                0 8px 20px
                rgba(0, 0, 0, 0.08);
        }

        .stat-top {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-bottom: 18px;
        }

        .stat-title {
            color: #6b7280;

            font-size: 14px;

            font-weight: 600;
        }

        .stat-icon {
            width: 42px;
            height: 42px;

            border-radius: 12px;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 20px;
        }

        .stat-value {
            font-size: 30px;

            font-weight: 700;

            color: #111827;

            margin-bottom: 6px;
        }

        .stat-description {
            font-size: 13px;

            color: #9ca3af;
        }

        /* COLORS */

        .blue {
            background: #eff6ff;
        }

        .green {
            background: #ecfdf5;
        }

        .orange {
            background: #fff7ed;
        }

        .red {
            background: #fef2f2;
        }

        .purple {
            background: #f5f3ff;
        }

        .yellow {
            background: #fefce8;
        }

        .loading {
            color: #6b7280;
            font-size: 14px;
        }

        /* ==========================================
           MOBILE HEADER
        ========================================== */

        .mobile-topbar {
            display: none;
        }

        /* ==========================================
           RESPONSIVE
        ========================================== */

        @media (max-width: 1200px) {

            .stats-grid {
                grid-template-columns:
                    repeat(2, 1fr);
            }
        }

        @media (max-width: 800px) {

            .sidebar {
                display: none;
            }

            .main {
                margin-left: 0;
                width: 100%;

                padding: 20px;
            }

            .mobile-topbar {
                display: flex;

                align-items: center;
                justify-content: space-between;

                background: #061e3f;
                color: white;

                padding: 15px 20px;

                margin: -20px -20px 25px;
            }

            .mobile-topbar strong {
                font-size: 18px;
            }

            .mobile-menu-btn {
                border: none;

                background: #2563eb;
                color: white;

                padding: 8px 12px;

                border-radius: 8px;

                cursor: pointer;
            }

            .header {
                flex-direction: column;

                align-items: flex-start;
            }

            .header-right {
                width: 100%;

                justify-content: space-between;
            }
        }

        @media (max-width: 550px) {

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .header-left h1 {
                font-size: 25px;
            }

            .header-right {
                flex-direction: column;
                align-items: stretch;
            }
        }

    </style>

</head>

<body>

<div class="layout">

    <!-- ==========================================
         SIDEBAR
    ========================================== -->

    <aside class="sidebar">

        <div class="brand">

            <h2>Pustanka</h2>

            <p>
                Sistem Informasi Perpustakaan
            </p>

        </div>

        <nav class="menu">

            <a
                href="../dashboard/"
                class="active"
            >
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

            <a href="../loans/">
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



                <span>
                    Logout
                </span>

            </button>

        </div>

    </aside>


    <!-- ==========================================
         MAIN
    ========================================== -->

    <main class="main">

        <!-- MOBILE TOPBAR -->

        <div class="mobile-topbar">

            <strong>
                Pustanka
            </strong>

            <button
                class="mobile-menu-btn"
                onclick="toggleMobileMenu()"
            >
                ☰ Menu
            </button>

        </div>


        <!-- HEADER -->

        <div class="header">

            <div class="header-left">

                <h1>
                    Dashboard Admin
                </h1>

                <p>
                    Selamat datang di sistem informasi
                    perpustakaan Pustanka.
                </p>

            </div>

        </div>


        <!-- ERROR -->

        <div
            id="errorBox"
            class="error-box"
        ></div>


        <!-- ==========================================
             STATISTICS
        ========================================== -->

        <section class="section">

            <div class="section-title">
                Statistik Perpustakaan
            </div>

            <div class="stats-grid">


                <!-- TOTAL USER -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Total Anggota
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="totalUser"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Jumlah seluruh anggota
                    </div>

                </div>


                <!-- TOTAL BUKU -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Total Buku
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="totalBook"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Jumlah seluruh buku
                    </div>

                </div>


                <!-- BUKU TERSEDIA -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Buku Tersedia
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="bookTersedia"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Buku yang dapat dipinjam
                    </div>

                </div>


                <!-- BUKU DIPINJAM -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Buku Dipinjam
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="bookDipinjam"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Buku sedang dipinjam
                    </div>

                </div>


                <!-- LOAN AKTIF -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Peminjaman Aktif
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="loanAktif"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Peminjaman belum dikembalikan
                    </div>

                </div>


                <!-- LOAN TERLAMBAT -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Peminjaman Terlambat
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="loanTerlambat"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Melewati masa toleransi
                    </div>

                </div>


                <!-- FINE -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Denda Belum Diayar
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="fineBelumBayar"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Jumlah denda yang belum lunas
                    </div>

                </div>


                <!-- ATTENDANCE -->

                <div class="stat-card">

                    <div class="stat-top">

                        <span class="stat-title">
                            Kehadiran Hari Ini
                        </span>

                    </div>

                    <div
                        class="stat-value"
                        id="attendanceHariIni"
                    >
                        <span class="loading">
                            ...
                        </span>
                    </div>

                    <div class="stat-description">
                        Anggota yang hadir hari ini
                    </div>

                </div>

            </div>

        </section>

    </main>

</div>


<script>

const API_BASE = "../../../api";


/* ==========================================
   FORMAT ANGKA
========================================== */

function formatNumber(value) {

    return new Intl.NumberFormat("id-ID").format(
        Number(value || 0)
    );

}


/* ==========================================
   FORMAT RUPIAH
========================================== */

function formatRupiah(value) {

    return new Intl.NumberFormat("id-ID", {

        style: "currency",

        currency: "IDR",

        maximumFractionDigits: 0

    }).format(
        Number(value || 0)
    );

}


/* ==========================================
   ERROR
========================================== */

function showError(message) {

    const errorBox =
        document.getElementById("errorBox");

    errorBox.textContent = message;

    errorBox.style.display = "block";

}


function hideError() {

    const errorBox =
        document.getElementById("errorBox");

    errorBox.style.display = "none";

}


/* ==========================================
   LOAD DASHBOARD
========================================== */

async function loadDashboard() {

    hideError();

    try {

        const response = await fetch(

            `${API_BASE}/dashboard/admin.php`,

            {
                method: "GET",

                credentials: "include"
            }

        );


        const result =
            await response.json();


        /* SESSION EXPIRED */

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        /* API ERROR */

        if (!response.ok || !result.status) {

            throw new Error(

                result.message ||
                "Gagal mengambil data dashboard."

            );

        }


        const data =
            result.data || {};


        const users =
            data.users || {};

        const books =
            data.books || {};

        const loans =
            data.loans || {};

        const fines =
            data.fines || {};

        const payments =
            data.payments || {};

        const attendance =
            data.attendance || {};


        /* ==========================================
           USER
        ========================================== */

        document.getElementById(
            "totalUser"
        ).textContent =
            formatNumber(users.total);


        /* ==========================================
           BOOKS
        ========================================== */

        document.getElementById(
            "totalBook"
        ).textContent =
            formatNumber(books.total);


        document.getElementById(
            "bookTersedia"
        ).textContent =
            formatNumber(books.tersedia);


        document.getElementById(
            "bookDipinjam"
        ).textContent =
            formatNumber(books.dipinjam);


        /* ==========================================
           LOANS
        ========================================== */

        document.getElementById(
            "loanAktif"
        ).textContent =
            formatNumber(loans.aktif);


        document.getElementById(
            "loanTerlambat"
        ).textContent =
            formatNumber(loans.terlambat);


        /* ==========================================
           FINE
        ========================================== */

        document.getElementById(
            "fineBelumBayar"
        ).textContent =
            formatNumber(fines.belum_bayar);


        /* ==========================================
           ATTENDANCE
        ========================================== */

        document.getElementById(
            "attendanceHariIni"
        ).textContent =
            formatNumber(attendance.hari_ini);


    } catch (error) {

        console.error(error);

        showError(

            error.message ||

            "Terjadi kesalahan saat mengambil data dashboard."

        );

    }

}


/* ==========================================
   LOGOUT ADMIN
========================================== */

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


/* ==========================================
   MOBILE MENU
========================================== */

function toggleMobileMenu() {

    const sidebar =
        document.querySelector(".sidebar");


    if (!sidebar) {
        return;
    }


    if (
        sidebar.style.display ===
        "block"
    ) {

        sidebar.style.display =
            "none";

    } else {

        sidebar.style.display =
            "block";

        sidebar.style.position =
            "fixed";

        sidebar.style.zIndex =
            "1000";

        sidebar.style.width =
            "250px";

    }

}


/* ==========================================
   LOAD SAAT HALAMAN DIBUKA
========================================== */

document.addEventListener(
    "DOMContentLoaded",
    loadDashboard
);

</script>

</body>
</html>