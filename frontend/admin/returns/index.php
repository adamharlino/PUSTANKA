<?php
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

    <title>Pengembalian Buku - Pustanka</title>

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

        /* ================= SIDEBAR (DISESUAIKAN) ================= */

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
            padding: 25px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 26px;
            margin-bottom: 5px;
        }

        .admin-info {
            color: #64748b;
            font-size: 14px;
        }

        /* ================= SCANNER ================= */

        .scanner-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }

        .scanner-header {
            margin-bottom: 20px;
        }

        .scanner-header h2 {
            font-size: 19px;
            margin-bottom: 6px;
        }

        .scanner-header p {
            color: #64748b;
            font-size: 14px;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto 20px;
        }

        .manual-box {
            max-width: 600px;
            margin: 0 auto;
        }

        .manual-box label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .manual-row {
            display: flex;
            gap: 10px;
        }

        .manual-row input {
            flex: 1;
            padding: 12px 14px;
            border: 1px solid #dbe2ea;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .manual-row input:focus {
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 11px 17px;
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

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-success:hover {
            background: #15803d;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-danger:hover {
            background: #b91c1c;
        }

        /* ================= MESSAGE ================= */

        .message {
            display: none;
            max-width: 600px;
            margin: 15px auto 0;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .message.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        .message.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
        }

        .message.warning {
            display: block;
            background: #fef3c7;
            color: #92400e;
        }

        /* ================= RESULT ================= */

        .result-card {
            display: none;
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
        }

        .result-card.show {
            display: block;
        }

        .result-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .result-header h2 {
            font-size: 19px;
        }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-dipinjam {
            background: #fef3c7;
            color: #92400e;
        }

        .status-terlambat {
            background: #fee2e2;
            color: #991b1b;
        }

        /* ================= DETAIL ================= */

        .detail-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 25px;
        }

        .detail-section {
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            padding: 18px;
        }

        .detail-section h3 {
            font-size: 16px;
            margin-bottom: 16px;
        }

        .book-user {
            display: flex;
            gap: 15px;
            align-items: flex-start;
            margin-bottom: 18px;
        }

        .photo {
            width: 85px;
            height: 105px;
            object-fit: cover;
            border-radius: 8px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .user-photo {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .placeholder {
            width: 85px;
            height: 105px;
            border-radius: 8px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
        }

        .user-placeholder {
            width: 85px;
            height: 85px;
            border-radius: 50%;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            flex-shrink: 0;
        }

        .person-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 6px;
        }

        .book-title {
            font-size: 17px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .muted {
            color: #64748b;
            font-size: 13px;
        }

        .info-list {
            display: grid;
            gap: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding-bottom: 9px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            color: #64748b;
        }

        .info-value {
            text-align: right;
            font-weight: 600;
        }

        /* ================= ACTION ================= */

        .action-area {
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        /* ================= SUCCESS RESULT ================= */

        .return-result {
            display: none;
            margin-top: 20px;
            padding: 18px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 10px;
        }

        .return-result.show {
            display: block;
        }

        .return-result h3 {
            color: #166534;
            margin-bottom: 12px;
        }

        .fine-box {
            margin-top: 15px;
            padding: 15px;
            background: #fff7ed;
            border: 1px solid #fed7aa;
            border-radius: 8px;
        }

        .fine-title {
            color: #9a3412;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .fine-amount {
            font-size: 22px;
            font-weight: bold;
            color: #c2410c;
        }

        .no-fine {
            color: #166534;
            font-weight: 600;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {
            .sidebar {
                width: 210px;
            }

            .main {
                margin-left: 210px;
                width: calc(100% - 210px);
            }

            .detail-layout {
                grid-template-columns: 1fr;
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
                padding: 18px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .manual-row {
                flex-direction: column;
            }

            .action-area {
                flex-direction: column;
            }

            .action-area .btn {
                width: 100%;
            }
        }
    </style>

    <!-- QR / BARCODE SCANNER -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">

        <div class="brand">
            <h2>Pustanka</h2>
            <p>Sistem Informasi Perpustakaan</p>
        </div>

        <div class="menu">

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

            <a href="./index.php" class="active">
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

        </div>

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
                <h1>Pengembalian Buku</h1>

                <div class="admin-info">
                    Scan barcode buku untuk memproses pengembalian
                </div>
            </div>

            <div class="admin-info">
                Admin:
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>
            </div>

        </div>


        <!-- ================= SCANNER ================= -->

        <div class="scanner-card">

            <div class="scanner-header">

                <h2>📷 Scan Barcode Buku</h2>

                <p>
                    Arahkan kamera ke barcode buku yang sedang dikembalikan.
                </p>

            </div>

            <div id="reader"></div>

            <div class="manual-box">

                <label for="barcode">
                    Atau masukkan barcode secara manual
                </label>

                <div class="manual-row">

                    <input
                        type="text"
                        id="barcode"
                        placeholder="Contoh: BK01000"
                        autocomplete="off"
                    >

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="lookupManual()"
                    >
                        Cari
                    </button>

                </div>

            </div>

            <div id="message" class="message"></div>

        </div>


        <!-- ================= RESULT ================= -->

        <div id="resultCard" class="result-card">

            <div class="result-header">

                <h2>Detail Pengembalian</h2>

                <span id="loanStatus" class="status">
                    -
                </span>

            </div>


            <div class="detail-layout">

                <!-- PEMINJAM -->

                <div class="detail-section">

                    <h3>👤 Peminjam</h3>

                    <div class="book-user">

                        <div id="userPhotoContainer">
                            <div class="user-placeholder">
                                👤
                            </div>
                        </div>

                        <div>

                            <div
                                id="userName"
                                class="person-name"
                            >
                                -
                            </div>

                            <div
                                id="userEmail"
                                class="muted"
                            >
                                -
                            </div>

                            <div
                                id="userPhone"
                                class="muted"
                            >
                                -
                            </div>

                        </div>

                    </div>

                    <div class="info-list">

                        <div class="info-row">

                            <span class="info-label">
                                ID Loan
                            </span>

                            <span
                                class="info-value"
                                id="loanId"
                            >
                                -
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                ID User
                            </span>

                            <span
                                class="info-value"
                                id="userId"
                            >
                                -
                            </span>

                        </div>

                    </div>

                </div>


                <!-- BUKU -->

                <div class="detail-section">

                    <h3>📚 Buku</h3>

                    <div class="book-user">

                        <div id="bookPhotoContainer">
                            <div class="placeholder">
                                📚
                            </div>
                        </div>

                        <div>

                            <div
                                id="bookTitle"
                                class="book-title"
                            >
                                -
                            </div>

                            <div
                                id="bookAuthor"
                                class="muted"
                            >
                                -
                            </div>

                            <div
                                id="bookBarcode"
                                class="muted"
                            >
                                -
                            </div>

                        </div>

                    </div>

                    <div class="info-list">

                        <div class="info-row">

                            <span class="info-label">
                                ID Buku
                            </span>

                            <span
                                class="info-value"
                                id="bookId"
                            >
                                -
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Harga Buku
                            </span>

                            <span
                                class="info-value"
                                id="bookPrice"
                            >
                                -
                            </span>

                        </div>

                    </div>

                </div>


                <!-- PEMINJAMAN -->

                <div class="detail-section">

                    <h3>📅 Peminjaman</h3>

                    <div class="info-list">

                        <div class="info-row">

                            <span class="info-label">
                                Tanggal Pinjam
                            </span>

                            <span
                                class="info-value"
                                id="borrowDate"
                            >
                                -
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Batas Kembali
                            </span>

                            <span
                                class="info-value"
                                id="dueDate"
                            >
                                -
                            </span>

                        </div>

                        <div class="info-row">

                            <span class="info-label">
                                Hari Ini
                            </span>

                            <span
                                class="info-value"
                                id="todayDate"
                            >
                                -
                            </span>

                        </div>

                    </div>

                </div>


                <!-- PERINGATAN -->

                <div class="detail-section">

                    <h3>⚠️ Status Pengembalian</h3>

                    <div
                        id="returnInfo"
                        class="muted"
                        style="line-height:1.7;"
                    >
                        -
                    </div>

                </div>

            </div>


            <!-- ACTION -->

            <div class="action-area">

                <button
                    type="button"
                    class="btn btn-secondary"
                    onclick="clearResult()"
                >
                    Batal
                </button>

                <button
                    type="button"
                    id="returnButton"
                    class="btn btn-success"
                    onclick="processReturn()"
                >
                    ↩️ Kembalikan Buku
                </button>

            </div>


            <!-- RESULT -->

            <div
                id="returnResult"
                class="return-result"
            ></div>

        </div>

    </main>

</div>


<script>

const LOOKUP_API =
    "../../../api/returns/lookup.php";

const RETURN_API =
    "../../../api/returns/create.php";

let currentLoan = null;

let scanner = null;

let scannerRunning = false;

let processing = false;


/* ================= HELPER ================= */

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


function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date = new Date(
        dateString + "T00:00:00"
    );

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleDateString(
        "id-ID",
        {
            day: "2-digit",
            month: "long",
            year: "numeric"
        }
    );
}


function formatRupiah(value) {

    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return "-";
    }

    return new Intl.NumberFormat(
        "id-ID",
        {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }
    ).format(Number(value));
}


function showMessage(
    message,
    type = "success"
) {

    const element =
        document.getElementById("message");

    element.textContent = message;

    element.className =
        "message " + type;
}


function hideMessage() {

    const element =
        document.getElementById("message");

    element.textContent = "";

    element.className = "message";
}


/* ================= PHOTO ================= */

function renderUserPhoto(photo) {

    const container =
        document.getElementById(
            "userPhotoContainer"
        );

    if (!photo) {

        container.innerHTML = `
            <div class="user-placeholder">
                👤
            </div>
        `;

        return;
    }

    let photoUrl = photo;

    if (
        !photo.startsWith("http://") &&
        !photo.startsWith("https://") &&
        !photo.startsWith("//")
    ) {
        photoUrl =
            "../../../" + photo;
    }

    container.innerHTML = `
        <img
            src="${escapeHtml(photoUrl)}"
            alt="Foto Peminjam"
            class="user-photo"
            onerror="
                this.parentElement.innerHTML =
                '<div class=&quot;user-placeholder&quot;>👤</div>'
            "
        >
    `;
}


function renderBookPhoto(photo) {

    const container =
        document.getElementById(
            "bookPhotoContainer"
        );

    if (!photo) {

        container.innerHTML = `
            <div class="placeholder">
                📚
            </div>
        `;

        return;
    }

    let photoUrl = photo;

    if (
        !photo.startsWith("http://") &&
        !photo.startsWith("https://") &&
        !photo.startsWith("//")
    ) {
        photoUrl =
            "../../../" + photo;
    }

    container.innerHTML = `
        <img
            src="${escapeHtml(photoUrl)}"
            alt="Foto Buku"
            class="photo"
            onerror="
                this.parentElement.innerHTML =
                '<div class=&quot;placeholder&quot;>📚</div>'
            "
        >
    `;
}


/* ================= LOOKUP ================= */

async function lookupBarcode(barcode) {

    if (processing) {
        return;
    }

    barcode =
        String(barcode || "").trim();

    if (!barcode) {
        return;
    }

    hideMessage();

    try {

        showMessage(
            "Mencari data peminjaman...",
            "warning"
        );

        const response =
            await fetch(
                LOOKUP_API +
                "?barcode=" +
                encodeURIComponent(barcode),
                {
                    method: "GET",
                    credentials: "include"
                }
            );

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }

        const result =
            await response.json();

        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Peminjaman aktif tidak ditemukan."
            );
        }

        currentLoan =
            result.data || null;

        renderLoan(
            currentLoan
        );

        showMessage(
            "Peminjaman aktif ditemukan.",
            "success"
        );

        stopScanner();

    } catch (error) {

        console.error(
            "Lookup error:",
            error
        );

        showMessage(
            error.message ||
            "Gagal mencari peminjaman.",
            "error"
        );
    }
}


/* ================= RENDER LOAN ================= */

function renderLoan(data) {

    if (!data) {
        return;
    }

    document
        .getElementById("resultCard")
        .classList.add("show");

    document.getElementById("loanId").textContent =
        data.id_loan || "-";

    document.getElementById("userId").textContent =
        data.id_user || "-";

    document.getElementById("userName").textContent =
        data.nama_user || "-";

    document.getElementById("userEmail").textContent =
        data.email_user || "-";

    document.getElementById("userPhone").textContent =
        data.no_telp_user || "-";

    document.getElementById("bookId").textContent =
        data.id_book || "-";

    document.getElementById("bookTitle").textContent =
        data.judul || "-";

    document.getElementById("bookAuthor").textContent =
        data.pengarang || "-";

    document.getElementById("bookBarcode").textContent =
        "Barcode: " +
        (data.barcode || "-");

    document.getElementById("bookPrice").textContent =
        formatRupiah(data.harga);

    document.getElementById("borrowDate").textContent =
        formatDate(data.tanggal_pinjam);

    document.getElementById("dueDate").textContent =
        formatDate(data.batas_kembali);

    document.getElementById("todayDate").textContent =
        formatDate(
            new Date().toISOString().split("T")[0]
        );


    const statusElement =
        document.getElementById(
            "loanStatus"
        );

    const status =
        data.status_peminjaman || "";

    statusElement.textContent =
        status === "terlambat"
            ? "Terlambat"
            : "Dipinjam";

    statusElement.className =
        status === "terlambat"
            ? "status status-terlambat"
            : "status status-dipinjam";


    /* STATUS PENGEMBALIAN */

    const due =
        new Date(
            data.batas_kembali +
            "T00:00:00"
        );

    const today =
        new Date();

    today.setHours(0, 0, 0, 0);

    const toleranceDate =
        new Date(due);

    toleranceDate.setDate(
        toleranceDate.getDate() + 7
    );

    const returnInfo =
        document.getElementById(
            "returnInfo"
        );

    if (today <= due) {

        returnInfo.innerHTML = `
            Buku dikembalikan sebelum atau tepat
            pada batas waktu.
            <br>
            <strong>Tidak ada denda.</strong>
        `;

    } else if (today <= toleranceDate) {

        returnInfo.innerHTML = `
            Buku melewati batas pengembalian,
            tetapi masih dalam masa toleransi
            7 hari.
            <br>
            <strong>Tidak ada denda.</strong>
        `;

    } else {

        returnInfo.innerHTML = `
            Buku sudah melewati masa toleransi
            7 hari.
            <br>
            <strong style="color:#dc2626;">
                Pengembalian akan dikenakan denda
                10% dari harga buku.
            </strong>
        `;
    }


    renderUserPhoto(
        data.foto_user
    );

    renderBookPhoto(
        data.foto
    );

    document
        .getElementById("returnResult")
        .classList.remove("show");
}


/* ================= PROCESS RETURN ================= */

async function processReturn() {

    if (
        !currentLoan ||
        !currentLoan.id_loan
    ) {

        showMessage(
            "Data peminjaman belum tersedia.",
            "error"
        );

        return;
    }

    if (processing) {
        return;
    }

    const confirmed =
        confirm(
            "Yakin ingin memproses pengembalian buku ini?"
        );

    if (!confirmed) {
        return;
    }

    processing = true;

    const button =
        document.getElementById(
            "returnButton"
        );

    button.disabled = true;

    button.textContent =
        "Memproses...";

    hideMessage();

    try {

        const response =
            await fetch(
                RETURN_API,
                {
                    method: "POST",
                    credentials: "include",
                    headers: {
                        "Content-Type":
                            "application/json"
                    },
                    body: JSON.stringify({
                        id_loan:
                            Number(
                                currentLoan.id_loan
                            )
                    })
                }
            );

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }

        const result =
            await response.json();

        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Gagal memproses pengembalian."
            );
        }

        renderReturnSuccess(
            result.data || {}
        );

        showMessage(
            result.message ||
            "Buku berhasil dikembalikan.",
            "success"
        );

        setTimeout(function() {
            resetForNextScan();
        }, 1500);

    } catch (error) {

        console.error(
            "Return error:",
            error
        );

        showMessage(
            error.message ||
            "Gagal memproses pengembalian.",
            "error"
        );

    } finally {

        processing = false;

        button.disabled = false;

        button.textContent =
            "↩️ Kembalikan Buku";
    }
}


/* ================= RETURN SUCCESS ================= */

function renderReturnSuccess(data) {

    const result =
        document.getElementById(
            "returnResult"
        );

    const terlambat =
        data.terlambat === true ||
        data.terlambat === 1;

    const nominal =
        Number(
            data.nominal_denda || 0
        );

    let html = `
        <h3>
            ✅ Buku Berhasil Dikembalikan
        </h3>

        <div class="info-list">

            <div class="info-row">
                <span class="info-label">
                    Tanggal Kembali
                </span>

                <span class="info-value">
                    ${escapeHtml(
                        formatDate(
                            data.tanggal_kembali
                        )
                    )}
                </span>
            </div>

            <div class="info-row">
                <span class="info-label">
                    Status
                </span>

                <span class="info-value">
                    Dikembalikan
                </span>
            </div>

        </div>
    `;

    if (
        terlambat &&
        nominal > 0
    ) {

        html += `
            <div class="fine-box">

                <div class="fine-title">
                    ⚠️ Denda Pengembalian
                </div>

                <div class="fine-amount">
                    ${formatRupiah(nominal)}
                </div>

                <div
                    class="muted"
                    style="margin-top:5px;"
                >
                    Denda sebesar 10% dari harga buku
                    karena melewati masa toleransi.
                </div>

            </div>
        `;

    } else {

        html += `
            <div
                class="no-fine"
                style="margin-top:15px;"
            >
                ✓ Tidak ada denda.
            </div>
        `;
    }

    result.innerHTML = html;

    result.classList.add("show");

    document
        .getElementById("returnButton")
        .style.display = "none";
}


/* ================= CLEAR ================= */

function clearResult() {

    currentLoan = null;

    document
        .getElementById("resultCard")
        .classList.remove("show");

    document
        .getElementById("returnResult")
        .classList.remove("show");

    document
        .getElementById("returnButton")
        .style.display = "inline-block";

    document
        .getElementById("barcode")
        .value = "";

    hideMessage();

    startScanner();
}

function resetForNextScan() {

    currentLoan = null;

    document
        .getElementById("resultCard")
        .classList.remove("show");

    document
        .getElementById("returnResult")
        .classList.remove("show");

    const returnButton =
        document.getElementById("returnButton");

    returnButton.style.display =
        "inline-block";

    returnButton.disabled = false;

    returnButton.textContent =
        "↩️ Kembalikan Buku";

    document
        .getElementById("barcode")
        .value = "";

    hideMessage();

    if (!scannerRunning) {
        startScanner();
    }

    document
        .getElementById("barcode")
        .focus();
}

/* ================= MANUAL ================= */

function lookupManual() {

    const barcode =
        document
            .getElementById("barcode")
            .value
            .trim();

    if (!barcode) {

        showMessage(
            "Silakan masukkan barcode buku.",
            "error"
        );

        return;
    }

    lookupBarcode(barcode);
}


/* ================= ENTER ================= */

document
    .getElementById("barcode")
    .addEventListener(
        "keydown",
        function(event) {

            if (
                event.key === "Enter"
            ) {

                event.preventDefault();

                lookupManual();
            }
        }
    );


/* ================= SCANNER ================= */

function startScanner() {

    if (scannerRunning) {
        return;
    }

    try {

        scanner =
            new Html5Qrcode(
                "reader"
            );

        scanner.start(
            {
                facingMode:
                    "environment"
            },
            {
                fps: 10,
                qrbox: {
                    width: 300,
                    height: 150
                }
            },
            function(decodedText) {

                if (processing) {
                    return;
                }

                const barcode =
                    String(
                        decodedText || ""
                    ).trim();

                if (!barcode) {
                    return;
                }

                document
                    .getElementById("barcode")
                    .value = barcode;

                lookupBarcode(
                    barcode
                );
            },
            function(errorMessage) {
            }
        )
        .then(function() {

            scannerRunning = true;

        })
        .catch(function(error) {

            console.error(
                "Scanner start error:",
                error
            );

            showMessage(
                "Kamera tidak dapat digunakan. Gunakan input barcode manual.",
                "warning"
            );
        });

    } catch (error) {

        console.error(
            "Scanner error:",
            error
        );
    }
}


function stopScanner() {

    if (
        !scanner ||
        !scannerRunning
    ) {
        return;
    }

    scanner
        .stop()
        .then(function() {

            scannerRunning = false;

        })
        .catch(function(error) {

            console.error(
                "Scanner stop error:",
                error
            );

            scannerRunning = false;
        });
}


/* ================= LOGOUT ================= */

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


/* ================= INIT ================= */

startScanner();

</script>

</body>
</html>