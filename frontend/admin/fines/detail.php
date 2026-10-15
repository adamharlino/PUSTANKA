<?php

session_start();

if (
    !isset($_SESSION["id_admin"]) ||
    ($_SESSION["role"] ?? "") !== "admin"
) {
    header("Location: ../../auth/login.php");
    exit;
}

$adminName = $_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Denda - Pustanka</title>

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
           SIDEBAR
        ========================= */

        .sidebar {
            width: 250px;
            background: #0f172a;
            color: white;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            padding: 24px 16px;
            overflow-y: auto;
            z-index: 100;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 6px;
            padding: 0 12px;
        }

        .brand-subtitle {
            color: #94a3b8;
            font-size: 12px;
            padding: 0 12px;
            margin-bottom: 28px;
        }

        .menu-title {
            font-size: 11px;
            text-transform: uppercase;
            color: #64748b;
            padding: 0 12px;
            margin: 18px 0 8px;
            font-weight: bold;
        }

        .menu a {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 11px 12px;
            border-radius: 9px;
            color: #cbd5e1;
            margin-bottom: 4px;
            font-size: 14px;
            transition: .2s;
        }

        .menu a:hover {
            background: #1e293b;
            color: white;
        }

        .menu a.active {
            background: #2563eb;
            color: white;
        }

        .logout-btn {
            width: 100%;
            border: none;
            background: #dc2626;
            color: white;
            padding: 11px 12px;
            border-radius: 9px;
            cursor: pointer;
            margin-top: 20px;
            font-size: 14px;
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
           BACK BUTTON
        ========================= */

        .back-row {
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: white;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 10px 15px;
            border-radius: 9px;
            font-size: 13px;
            font-weight: 600;
        }

        .back-btn:hover {
            background: #f8fafc;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        /* =========================
           FINE SUMMARY
        ========================= */

        .fine-summary {
            background: white;
            border-radius: 14px;
            padding: 24px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .summary-left {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .fine-icon {
            width: 58px;
            height: 58px;
            border-radius: 14px;
            background: #fee2e2;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .summary-label {
            color: #64748b;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .summary-amount {
            color: #dc2626;
            font-size: 28px;
            font-weight: 800;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 7px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-unpaid {
            background: #fee2e2;
            color: #b91c1c;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
        }

        /* =========================
           GRID
        ========================= */

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
            overflow: hidden;
        }

        .card-header {
            padding: 17px 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .card-header h2 {
            font-size: 16px;
            color: #111827;
        }

        .card-body {
            padding: 20px;
        }

        /* =========================
           PROFILE
        ========================= */

        .profile-top {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .profile-photo {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            border: 2px solid #e5e7eb;
        }

        .profile-placeholder {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
        }

        .profile-name {
            font-size: 17px;
            font-weight: 700;
        }

        .profile-member {
            margin-top: 4px;
            color: #2563eb;
            font-size: 12px;
            font-weight: 600;
        }

        /* =========================
           BOOK
        ========================= */

        .book-top {
            display: flex;
            gap: 18px;
            margin-bottom: 20px;
        }

        .book-photo-container {
            width: 100px;
            height: 135px;
            flex-shrink: 0;
            border-radius: 9px;
            overflow: hidden;
            background: #e2e8f0;
        }

        .book-cover {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .book-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
        }

        .book-title {
            font-size: 18px;
            font-weight: 700;
            line-height: 1.4;
            color: #111827;
        }

        .book-author {
            margin-top: 6px;
            color: #64748b;
            font-size: 13px;
        }

        /* =========================
           INFO LIST
        ========================= */

        .info-list {
            display: grid;
            gap: 13px;
        }

        .info-row {
            display: grid;
            grid-template-columns: 145px 1fr;
            gap: 12px;
            font-size: 13px;
        }

        .info-label {
            color: #64748b;
        }

        .info-value {
            color: #111827;
            font-weight: 600;
            word-break: break-word;
        }

        .description {
            color: #475569;
            font-size: 13px;
            line-height: 1.7;
            margin-top: 6px;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            background: white;
            border-radius: 14px;
            padding: 60px 20px;
            text-align: center;
            color: #64748b;
            box-shadow: 0 2px 8px rgba(15, 23, 42, .05);
        }

        .loading-icon {
            font-size: 42px;
            margin-bottom: 10px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 1000px) {

            .detail-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 900px) {

            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .content {
                padding: 20px;
            }
        }

        @media (max-width: 700px) {

            .layout {
                display: block;
            }

            .sidebar {
                position: static;
                width: 100%;
                min-height: auto;
            }

            .main {
                margin-left: 0;
                width: 100%;
            }

            .topbar {
                padding: 18px;
            }

            .admin-info {
                display: none;
            }

            .content {
                padding: 15px;
            }

            .fine-summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-row {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .book-top {
                flex-direction: column;
            }

            .book-photo-container {
                width: 120px;
                height: 160px;
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
            Pustanka
        </div>

        <div class="brand-subtitle">
            Admin Panel
        </div>

        <div class="menu">

            <div class="menu-title">
                Utama
            </div>

            <a href="../dashboard/index.php">
                📊 Dashboard
            </a>

            <a href="../profile/index.php">
                👤 Profile
            </a>

            <div class="menu-title">
                Perpustakaan
            </div>

            <a href="../books/index.php">
                📚 Books
            </a>

            <a href="../users/index.php">
                👥 Users
            </a>

            <a href="../loans/index.php">
                📖 Loans
            </a>

            <a href="../returns/index.php">
                ↩️ Returns
            </a>

            <a href="../attendance/index.php">
                🕐 Attendance
            </a>

            <div class="menu-title">
                Keuangan
            </div>

            <a href="./index.php" class="active">
                💰 Fines
            </a>

            <a href="../payments/index.php">
                💳 Payments
            </a>

            <div class="menu-title">
                Laporan
            </div>

            <a href="../reports/index.php">
                📈 Reports
            </a>

        </div>

        <button
            type="button"
            class="logout-btn"
            onclick="logout()"
        >
            🚪 Logout
        </button>

    </aside>

    <!-- =========================
         MAIN
    ========================== -->

    <main class="main">

        <header class="topbar">

            <div>
                <h1>Detail Denda</h1>

                <p>
                    Informasi lengkap denda peminjaman
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

            <div class="back-row">

                <a
                    href="./index.php"
                    class="back-btn"
                >
                    ← Kembali ke Fines
                </a>

            </div>

            <div
                id="message"
                class="message"
            ></div>

            <div id="content">

                <div class="loading">

                    <div class="loading-icon">
                        💰
                    </div>

                    Memuat detail denda...

                </div>

            </div>

        </section>

    </main>

</div>

<script>

    const API_URL =
        "../../../api/fines/admin_detail.php";

    const LOGOUT_URL =
        "../../../api/auth/logout.php";

    const LOGIN_URL =
        "../../auth/login.php";


    /* =========================
       GET ID
    ========================== */

    const params =
        new URLSearchParams(
            window.location.search
        );

    const idFine =
        Number(
            params.get("id")
        );


    /* =========================
       ELEMENT
    ========================== */

    const content =
        document.getElementById(
            "content"
        );

    const message =
        document.getElementById(
            "message"
        );


    /* =========================
       LOAD DETAIL
    ========================== */

    async function loadDetail() {

        if (!idFine || idFine <= 0) {

            showError(
                "ID denda tidak valid."
            );

            return;
        }

        try {

            const response =
                await fetch(
                    API_URL +
                    "?id=" +
                    encodeURIComponent(
                        idFine
                    ),
                    {
                        method: "GET",
                        credentials: "include"
                    }
                );

            if (response.status === 401) {

                window.location.href =
                    LOGIN_URL;

                return;
            }

            let result;

            try {

                result =
                    await response.json();

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
                    "Gagal mengambil detail denda."
                );
            }

            renderDetail(
                result.data || {}
            );

        } catch (error) {

            console.error(
                "Load fine detail error:",
                error
            );

            showError(
                error.message ||
                "Terjadi kesalahan saat mengambil detail denda."
            );
        }
    }


    /* =========================
       RENDER DETAIL
    ========================== */

    function renderDetail(data) {

        const peminjam =
            data.peminjam ||
            {};

        const buku =
            data.buku ||
            {};

        const peminjaman =
            data.peminjaman ||
            {};

        const status =
            data.status_pembayaran ||
            "";

        const statusLabel =
            status === "lunas"
                ? "Lunas"
                : "Belum Dibayar";

        const statusClass =
            status === "lunas"
                ? "badge-paid"
                : "badge-unpaid";

        content.innerHTML = `

            <!-- =========================
                 FINE SUMMARY
            ========================== -->

            <div class="fine-summary">

                <div class="summary-left">

                    <div>

                        <div class="summary-label">
                            Nominal Denda
                        </div>

                        <div class="summary-amount">
                            ${formatRupiah(
                                data.nominal_denda
                            )}
                        </div>

                    </div>

                </div>

                <div>

                    <div class="summary-label">
                        Status Pembayaran
                    </div>

                    <span
                        class="badge ${statusClass}"
                    >
                        ${statusLabel}
                    </span>

                </div>

            </div>


            <!-- =========================
                 GRID
            ========================== -->

            <div class="detail-grid">


                <!-- =====================
                     PEMINJAM
                ====================== -->

                <div class="card">

                    <div class="card-header">

                        <span>👤</span>

                        <h2>
                            Data Peminjam
                        </h2>

                    </div>

                    <div class="card-body">

                        <div class="profile-top">

                            ${renderUserPhoto(
                                peminjam.foto
                            )}

                            <div>

                                <div class="profile-name">
                                    ${escapeHtml(
                                        peminjam.nama ||
                                        "-"
                                    )}
                                </div>

                                <div class="profile-member">
                                    ${escapeHtml(
                                        peminjam.member_code ||
                                        "-"
                                    )}
                                </div>

                            </div>

                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    ID User
                                </div>

                                <div class="info-value">
                                    ${Number(
                                        peminjam.id_user ||
                                        0
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    NIK
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        peminjam.nik ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Email
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        peminjam.email ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    No. Telepon
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        peminjam.no_telp ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Alamat
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        peminjam.alamat ||
                                        "-"
                                    )}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================
                     BUKU
                ====================== -->

                <div class="card">

                    <div class="card-header">

                        <span>📚</span>

                        <h2>
                            Data Buku
                        </h2>

                    </div>

                    <div class="card-body">

                        <div class="book-top">

                            <div
                                id="bookPhotoContainer"
                                class="book-photo-container"
                            >
                                <div class="book-placeholder">
                                    📚
                                </div>
                            </div>

                            <div>

                                <div class="book-title">
                                    ${escapeHtml(
                                        buku.judul ||
                                        "-"
                                    )}
                                </div>

                                <div class="book-author">
                                    ${escapeHtml(
                                        buku.pengarang ||
                                        "-"
                                    )}
                                </div>

                            </div>

                        </div>

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    ID Buku
                                </div>

                                <div class="info-value">
                                    ${Number(
                                        buku.id_book ||
                                        0
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    ISBN
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        buku.isbn ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Barcode
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        buku.barcode ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Penerbit
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        buku.penerbit ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tahun Terbit
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        buku.tahun_terbit ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Jenis Buku
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        buku.jenis_buku ||
                                        "-"
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Harga Buku
                                </div>

                                <div class="info-value">
                                    ${formatRupiah(
                                        buku.harga
                                    )}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================
                     PEMINJAMAN
                ====================== -->

                <div class="card">

                    <div class="card-header">

                        <span>📖</span>

                        <h2>
                            Data Peminjaman
                        </h2>

                    </div>

                    <div class="card-body">

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    ID Loan
                                </div>

                                <div class="info-value">
                                    #${Number(
                                        peminjaman.id_loan ||
                                        0
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tanggal Pinjam
                                </div>

                                <div class="info-value">
                                    ${formatDate(
                                        peminjaman.tanggal_pinjam
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Batas Kembali
                                </div>

                                <div class="info-value">
                                    ${formatDate(
                                        peminjaman.batas_kembali
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Tanggal Kembali
                                </div>

                                <div class="info-value">
                                    ${formatDate(
                                        peminjaman.tanggal_kembali
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Status Peminjaman
                                </div>

                                <div class="info-value">
                                    ${escapeHtml(
                                        peminjaman.status ||
                                        "-"
                                    )}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================
                     DENDA
                ====================== -->

                <div class="card">

                    <div class="card-header">

                        <span>💰</span>

                        <h2>
                            Informasi Denda
                        </h2>

                    </div>

                    <div class="card-body">

                        <div class="info-list">

                            <div class="info-row">
                                <div class="info-label">
                                    ID Denda
                                </div>

                                <div class="info-value">
                                    #${Number(
                                        data.id_fine ||
                                        0
                                    )}
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Nominal Denda
                                </div>

                                <div class="info-value">
                                    <span
                                        style="
                                            color:#dc2626;
                                            font-size:17px;
                                        "
                                    >
                                        ${formatRupiah(
                                            data.nominal_denda
                                        )}
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Status
                                </div>

                                <div class="info-value">
                                    <span
                                        class="badge ${statusClass}"
                                    >
                                        ${statusLabel}
                                    </span>
                                </div>
                            </div>

                            <div class="info-row">
                                <div class="info-label">
                                    Dibuat
                                </div>

                                <div class="info-value">
                                    ${formatDateTime(
                                        data.created_at
                                    )}
                                </div>
                            </div>

                        </div>

                    </div>

                </div>


                <!-- =====================
                     SINOPSIS
                ====================== -->

                <div class="card">

                    <div class="card-header">

                        <span>📝</span>

                        <h2>
                            Sinopsis Buku
                        </h2>

                    </div>

                    <div class="card-body">

                        <div class="description">
                            ${escapeHtml(
                                buku.sinopsis ||
                                "Tidak ada sinopsis."
                            )}
                        </div>

                    </div>

                </div>

            </div>
        `;


        renderBookPhoto(
            buku.foto
        );
    }


    /* =========================
       USER PHOTO
    ========================= */

    function renderUserPhoto(photo) {

        if (!photo) {

            return `
                <div class="profile-placeholder">
                    👤
                </div>
            `;
        }

        let photoUrl =
            photo;

        if (
            !photo.startsWith("http://") &&
            !photo.startsWith("https://") &&
            !photo.startsWith("//")
        ) {

            photoUrl =
                "../../../" +
                photo;
        }

        return `
            <img
                src="${escapeHtml(
                    photoUrl
                )}"
                alt="Foto Peminjam"
                class="profile-photo"
                onerror="
                    this.outerHTML =
                    '<div class=&quot;profile-placeholder&quot;>👤</div>'
                "
            >
        `;
    }


    /* =========================
       BOOK PHOTO
    ========================= */

    function renderBookPhoto(photo) {

        const container =
            document.getElementById(
                "bookPhotoContainer"
            );

        if (!container) {
            return;
        }

        if (!photo) {

            container.innerHTML = `
                <div class="book-placeholder">
                    📚
                </div>
            `;

            return;
        }

        let photoUrl =
            photo;

        if (
            !photo.startsWith("http://") &&
            !photo.startsWith("https://") &&
            !photo.startsWith("//")
        ) {

            photoUrl =
                "../../../" +
                photo;
        }

        container.innerHTML = `
            <img
                src="${escapeHtml(
                    photoUrl
                )}"
                alt="Foto Buku"
                class="book-cover"
                onerror="
                    this.parentElement.innerHTML =
                    '<div class=&quot;book-placeholder&quot;>📚</div>'
                "
            >
        `;
    }


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
                minimumFractionDigits: 0
            }
        ).format(number);
    }


    /* =========================
       FORMAT DATE
    ========================= */

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

        if (
            isNaN(
                date.getTime()
            )
        ) {

            return escapeHtml(
                value
            );
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
       FORMAT DATETIME
    ========================= */

    function formatDateTime(value) {

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

        if (
            isNaN(
                date.getTime()
            )
        ) {

            return escapeHtml(
                value
            );
        }

        return new Intl.DateTimeFormat(
            "id-ID",
            {
                day: "2-digit",
                month: "short",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            }
        ).format(date);
    }


    /* =========================
       ESCAPE HTML
    ========================= */

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
       ERROR
    ========================= */

    function showError(text) {

        message.textContent =
            text;

        message.className =
            "message show error";

        content.innerHTML = `
            <div class="loading">

                <div class="loading-icon">
                    ⚠️
                </div>

                Gagal memuat detail denda.

            </div>
        `;
    }


    /* =========================
       LOGOUT
    ========================= */

    async function logout() {

        try {

            await fetch(
                LOGOUT_URL,
                {
                    method: "POST",
                    credentials: "include"
                }
            );

        } catch (error) {

            console.error(
                "Logout error:",
                error
            );

        } finally {

            window.location.href =
                LOGIN_URL;

        }
    }


    /* =========================
       INIT
    ========================= */

    loadDetail();

</script>

</body>
</html>