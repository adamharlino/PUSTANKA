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

$id_payment = $_GET["id"] ?? null;

if (
    !$id_payment ||
    !is_numeric($id_payment) ||
    (int) $id_payment <= 0
) {
    header("Location: index.php");
    exit;
}

$id_payment = (int) $id_payment;

?>

<!DOCTYPE html>
<html lang="id">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Payment - Pustanka</title>

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

        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 240px;
            height: 100vh;
            background: #0f2747;
            color: white;
            padding: 25px 15px;
            overflow-y: auto;
        }

        .brand {
            font-size: 25px;
            font-weight: bold;
            text-align: center;
            margin-bottom: 30px;
        }

        .menu {
            display: flex;
            flex-direction: column;
            gap: 7px;
        }

        .menu a {
            text-decoration: none;
            color: #dbeafe;
            padding: 12px 15px;
            border-radius: 8px;
            display: block;
            transition: .2s;
        }

        .menu a:hover,
        .menu a.active {
            background: #2563eb;
            color: white;
        }

        .main {
            margin-left: 240px;
            padding: 30px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar h1 {
            color: #0f2747;
            font-size: 28px;
        }

        .admin-name {
            background: white;
            padding: 10px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .back-button {
            display: inline-block;
            text-decoration: none;
            background: #64748b;
            color: white;
            padding: 10px 15px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .back-button:hover {
            opacity: .9;
        }

        .loading {
            background: white;
            padding: 40px;
            text-align: center;
            border-radius: 12px;
            color: #64748b;
        }

        .message {
            display: none;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .message.show {
            display: block;
        }

        .message.error {
            background: #fee2e2;
            color: #991b1b;
        }

        .detail-container {
            display: none;
        }

        .detail-container.show {
            display: block;
        }

        .summary-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .summary-header h2 {
            color: #0f2747;
            font-size: 22px;
        }

        .payment-id {
            color: #64748b;
            margin-top: 5px;
            font-size: 14px;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
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

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 2px 10px rgba(0,0,0,.05);
        }

        .card h3 {
            color: #0f2747;
            margin-bottom: 18px;
            font-size: 18px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 11px 0;
            border-bottom: 1px solid #e5e7eb;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .label {
            color: #64748b;
            font-size: 14px;
        }

        .value {
            text-align: right;
            font-weight: 600;
            font-size: 14px;
            word-break: break-word;
        }

        .nominal {
            font-size: 24px;
            color: #2563eb;
        }

        .profile {
            display: flex;
            gap: 18px;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .profile-photo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-code {
            color: #64748b;
            font-size: 14px;
        }

        .book-photo {
            width: 120px;
            height: 170px;
            object-fit: cover;
            border-radius: 8px;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .book-content {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .book-title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .synopsis {
            margin-top: 15px;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            line-height: 1.6;
            color: #475569;
            font-size: 14px;
        }

        .proof-card {
            grid-column: 1 / -1;
        }

        .proof-area {
            min-height: 220px;
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .proof-image {
            max-width: 100%;
            max-height: 600px;
            border-radius: 8px;
            object-fit: contain;
        }

        .proof-pdf {
            text-align: center;
        }

        .proof-pdf a {
            display: inline-block;
            margin-top: 12px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 10px 16px;
            border-radius: 7px;
        }

        .no-proof {
            color: #64748b;
            text-align: center;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            border-radius: 7px;
            padding: 11px 18px;
            cursor: pointer;
            color: white;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2563eb;
        }

        .btn-secondary {
            background: #64748b;
        }

        .btn-approve {
    background: #16a34a;
        }

        .btn-approve:hover {
            background: #15803d;
        }

        .btn-reject {
            background: #dc2626;
        }

        .btn-reject:hover {
            background: #b91c1c;
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        @media (max-width: 900px) {

            .sidebar {
                width: 200px;
            }

            .main {
                margin-left: 200px;
            }

            .grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 650px) {

            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
                padding: 20px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .book-content {
                flex-direction: column;
            }

            .proof-card {
                grid-column: auto;
            }

        }

    </style>

</head>

<body>

<aside class="sidebar">

    <div class="brand">
        📚 PUSTANKA
    </div>

    <nav class="menu">

        <a href="../dashboard/index.php">
            📊 Dashboard
        </a>

        <a href="../profile/index.php">
            👤 Profile
        </a>

        <a href="../books/index.php">
            📚 Books
        </a>

        <a href="../users/index.php">
            👥 Users
        </a>

        <a href="../loans/index.php">
            📋 Loans
        </a>

        <a href="../attendance/index.php">
            🕐 Attendance
        </a>

        <a href="../returns/index.php">
            ↩️ Returns
        </a>

        <a href="../reports/index.php">
            📈 Reports
        </a>

        <a href="../fines/index.php">
            💰 Fines
        </a>

        <a href="index.php" class="active">
            💳 Payments
        </a>

    </nav>

</aside>

<main class="main">

    <div class="topbar">

        <h1>💳 Detail Payment</h1>

        <div class="admin-name">
            👤 <?= htmlspecialchars($_SESSION["nama"] ?? "Admin") ?>
        </div>

    </div>

    <a href="index.php" class="back-button">
        ← Kembali ke Payments
    </a>

    <div id="message" class="message"></div>

    <div id="loading" class="loading">
        Memuat detail payment...
    </div>

    <div id="detailContainer" class="detail-container">

        <!-- SUMMARY -->

        <div class="summary-card">

            <div class="summary-header">

                <div>

                    <h2>
                        Detail Pembayaran
                    </h2>

                    <div class="payment-id">
                        ID Payment:
                        <strong id="paymentId">-</strong>
                    </div>

                </div>

                <div id="paymentStatus">
                    -
                </div>

            </div>

            <div id="paymentActions" class="actions" style="display:none;">
            <button
                type="button"
                class="btn btn-approve"
                id="approveButton"
            >
                ✅ Setujui Pembayaran
            </button>

            <button
                type="button"
                class="btn btn-reject"
                id="rejectButton"
            >
                ❌ Tolak Pembayaran
            </button>
        </div>

        </div>

        <div class="grid">

            <!-- PEMBAYARAN -->

            <div class="card">

                <h3>
                    💳 Informasi Pembayaran
                </h3>

                <div class="info-row">
                    <span class="label">
                        Tanggal Bayar
                    </span>

                    <span class="value" id="tanggalBayar">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Nominal
                    </span>

                    <span class="value nominal" id="nominal">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Metode
                    </span>

                    <span class="value" id="metode">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Status
                    </span>

                    <span class="value" id="statusDetail">
                        -
                    </span>
                </div>

            </div>

            <!-- DENDA -->

            <div class="card">

                <h3>
                    💰 Informasi Denda
                </h3>

                <div class="info-row">
                    <span class="label">
                        ID Denda
                    </span>

                    <span class="value" id="fineId">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Nominal Denda
                    </span>

                    <span class="value" id="fineNominal">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Status Denda
                    </span>

                    <span class="value" id="fineStatus">
                        -
                    </span>
                </div>

            </div>

            <!-- PEMINJAM -->

            <div class="card">

                <h3>
                    👤 Data Peminjam
                </h3>

                <div class="profile">

                    <img
                        id="userPhoto"
                        class="profile-photo"
                        src=""
                        alt="Foto User"
                    >

                    <div>

                        <div
                            class="profile-name"
                            id="userName"
                        >
                            -
                        </div>

                        <div
                            class="profile-code"
                            id="memberCode"
                        >
                            -
                        </div>

                    </div>

                </div>

                <div class="info-row">
                    <span class="label">NIK</span>
                    <span class="value" id="userNik">-</span>
                </div>

                <div class="info-row">
                    <span class="label">Email</span>
                    <span class="value" id="userEmail">-</span>
                </div>

                <div class="info-row">
                    <span class="label">No. Telepon</span>
                    <span class="value" id="userPhone">-</span>
                </div>

                <div class="info-row">
                    <span class="label">Alamat</span>
                    <span class="value" id="userAddress">-</span>
                </div>

            </div>

            <!-- BUKU -->

            <div class="card">

                <h3>
                    📚 Data Buku
                </h3>

                <div class="book-content">

                    <img
                        id="bookPhoto"
                        class="book-photo"
                        src=""
                        alt="Foto Buku"
                    >

                    <div>

                        <div
                            class="book-title"
                            id="bookTitle"
                        >
                            -
                        </div>

                        <div class="info-row">
                            <span class="label">
                                ISBN
                            </span>

                            <span class="value" id="bookIsbn">
                                -
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="label">
                                Barcode
                            </span>

                            <span class="value" id="bookBarcode">
                                -
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="label">
                                Pengarang
                            </span>

                            <span class="value" id="bookAuthor">
                                -
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="label">
                                Penerbit
                            </span>

                            <span class="value" id="bookPublisher">
                                -
                            </span>
                        </div>

                        <div class="info-row">
                            <span class="label">
                                Tahun
                            </span>

                            <span class="value" id="bookYear">
                                -
                            </span>
                        </div>

                    </div>

                </div>

                <div
                    class="synopsis"
                    id="bookSynopsis"
                >
                    -
                </div>

            </div>

            <!-- PEMINJAMAN -->

            <div class="card">

                <h3>
                    📋 Data Peminjaman
                </h3>

                <div class="info-row">
                    <span class="label">
                        ID Peminjaman
                    </span>

                    <span class="value" id="loanId">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Tanggal Pinjam
                    </span>

                    <span class="value" id="loanDate">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Batas Kembali
                    </span>

                    <span class="value" id="loanDue">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Tanggal Kembali
                    </span>

                    <span class="value" id="loanReturn">
                        -
                    </span>
                </div>

                <div class="info-row">
                    <span class="label">
                        Status
                    </span>

                    <span class="value" id="loanStatus">
                        -
                    </span>
                </div>

            </div>

            <!-- BUKTI PEMBAYARAN -->

            <div class="card proof-card">

                <h3>
                    🧾 Bukti Pembayaran
                </h3>

                <div
                    class="proof-area"
                    id="proofArea"
                >
                    Memuat bukti pembayaran...
                </div>

            </div>

        </div>

    </div>

</main>

<script>

const API_URL =
    "../../../api/payments/admin_detail.php?id=<?= $id_payment ?>";

const loading =
    document.getElementById("loading");

const detailContainer =
    document.getElementById("detailContainer");

const message =
    document.getElementById("message");

const paymentActions =
    document.getElementById("paymentActions");

const approveButton =
    document.getElementById("approveButton");

const rejectButton =
    document.getElementById("rejectButton");

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

function formatRupiah(value) {

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0
    }).format(Number(value || 0));

}

function formatTanggal(value) {

    if (!value) {
        return "-";
    }

    const date =
        new Date(String(value).replace(" ", "T"));

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric"
    });

}

function formatDateTime(value) {

    if (!value) {
        return "-";
    }

    const date =
        new Date(String(value).replace(" ", "T"));

    if (isNaN(date.getTime())) {
        return value;
    }

    return date.toLocaleString("id-ID", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });

}

function statusBadge(status) {

    let className = "badge-pending";
    let label = "Pending";

    if (status === "disetujui") {
        className = "badge-disetujui";
        label = "Disetujui";
    }

    else if (status === "ditolak") {
        className = "badge-ditolak";
        label = "Ditolak";
    }

    else if (status === "pending") {
        className = "badge-pending";
        label = "Pending";
    }

    return `
        <span class="badge ${className}">
            ${label}
        </span>
    `;
}

function metodeBadge(metode) {

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

    return escapeHtml(metode || "-");
}

function imageUrl(path) {

    if (!path) {
        return "";
    }

    if (
        path.startsWith("http://") ||
        path.startsWith("https://") ||
        path.startsWith("data:")
    ) {
        return path;
    }

    return "../../../" + path.replace(/^\/+/, "");

}

function showError(text) {

    message.textContent = text;
    message.className = "message error show";

}

async function verifyPayment(aksi) {

    const paymentId =
        document.getElementById("paymentId").textContent.trim();

    if (!paymentId || paymentId === "-") {
        showError("ID payment tidak ditemukan.");
        return;
    }

    let confirmationMessage = "";

    if (aksi === "setujui") {
        confirmationMessage =
            "Yakin ingin menyetujui pembayaran ini?\n\n" +
            "Setelah disetujui, status denda akan menjadi LUNAS.";
    } else {
        confirmationMessage =
            "Yakin ingin menolak pembayaran ini?\n\n" +
            "Denda akan tetap berstatus BELUM BAYAR.";
    }

    if (!confirm(confirmationMessage)) {
        return;
    }

    approveButton.disabled = true;
    rejectButton.disabled = true;

    try {

        const formData = new FormData();

        formData.append("id_payment", paymentId);
        formData.append("aksi", aksi);

        const response = await fetch(
            "../../../api/payments/verify.php",
            {
                method: "POST",
                credentials: "include",
                body: formData
            }
        );

        const result = await response.json();

        if (response.status === 401) {
            window.location.href =
                "../../auth/login.php";
            return;
        }

        if (!response.ok || !result.status) {
            throw new Error(
                result.message ||
                "Gagal memverifikasi pembayaran."
            );
        }

        alert(result.message);

        await loadDetail();

    } catch (error) {

        showError(error.message);

        approveButton.disabled = false;
        rejectButton.disabled = false;
    }
}

approveButton.addEventListener(
    "click",
    () => verifyPayment("setujui")
);

rejectButton.addEventListener(
    "click",
    () => verifyPayment("tolak")
);

async function loadDetail() {

    try {

        const response = await fetch(
            API_URL,
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

        if (!response.ok || !result.status) {

            throw new Error(
                result.message ||
                "Gagal mengambil detail payment."
            );

        }

        renderDetail(result.data);

        loading.style.display = "none";
        detailContainer.classList.add("show");

    }

    catch (error) {

        loading.style.display = "none";

        showError(error.message);

    }

}

function renderDetail(data) {

    const pembayaran =
        data.pembayaran || {};

    const denda =
        data.denda || {};

    const peminjam =
        data.peminjam || {};

    const buku =
        data.buku || {};

    const peminjaman =
        data.peminjaman || {};

    document.getElementById("paymentId")
        .textContent =
        data.id_payment || "-";

    document.getElementById("paymentStatus")
        .innerHTML =
        statusBadge(pembayaran.status);

    if (pembayaran.status === "pending") {

    paymentActions.style.display = "flex";

    } else {

        paymentActions.style.display = "none";
    }

    document.getElementById("tanggalBayar")
        .textContent =
        formatDateTime(pembayaran.tanggal_bayar);

    document.getElementById("nominal")
        .textContent =
        formatRupiah(pembayaran.nominal);

    document.getElementById("metode")
        .innerHTML =
        metodeBadge(pembayaran.metode);

    document.getElementById("statusDetail")
        .innerHTML =
        statusBadge(pembayaran.status);

    document.getElementById("fineId")
        .textContent =
        denda.id_fine || "-";

    document.getElementById("fineNominal")
        .textContent =
        formatRupiah(denda.nominal_denda);

    document.getElementById("fineStatus")
        .textContent =
        denda.status_pembayaran || "-";

    document.getElementById("userName")
        .textContent =
        peminjam.nama || "-";

    document.getElementById("memberCode")
        .textContent =
        peminjam.member_code || "-";

    document.getElementById("userNik")
        .textContent =
        peminjam.nik || "-";

    document.getElementById("userEmail")
        .textContent =
        peminjam.email || "-";

    document.getElementById("userPhone")
        .textContent =
        peminjam.no_telp || "-";

    document.getElementById("userAddress")
        .textContent =
        peminjam.alamat || "-";

    const userPhoto =
        document.getElementById("userPhoto");

    if (peminjam.foto) {

        userPhoto.src =
            imageUrl(peminjam.foto);

        userPhoto.style.display =
            "block";

    } else {

        userPhoto.style.display =
            "none";

    }

    document.getElementById("bookTitle")
        .textContent =
        buku.judul || "-";

    document.getElementById("bookIsbn")
        .textContent =
        buku.isbn || "-";

    document.getElementById("bookBarcode")
        .textContent =
        buku.barcode || "-";

    document.getElementById("bookAuthor")
        .textContent =
        buku.pengarang || "-";

    document.getElementById("bookPublisher")
        .textContent =
        buku.penerbit || "-";

    document.getElementById("bookYear")
        .textContent =
        buku.tahun_terbit || "-";

    document.getElementById("bookSynopsis")
        .textContent =
        buku.sinopsis || "Tidak ada sinopsis.";

    const bookPhoto =
        document.getElementById("bookPhoto");

    if (buku.foto) {

        bookPhoto.src =
            imageUrl(buku.foto);

        bookPhoto.style.display =
            "block";

    } else {

        bookPhoto.style.display =
            "none";

    }

    document.getElementById("loanId")
        .textContent =
        peminjaman.id_loan || "-";

    document.getElementById("loanDate")
        .textContent =
        formatTanggal(peminjaman.tanggal_pinjam);

    document.getElementById("loanDue")
        .textContent =
        formatTanggal(peminjaman.batas_kembali);

    document.getElementById("loanReturn")
        .textContent =
        formatTanggal(peminjaman.tanggal_kembali);

    document.getElementById("loanStatus")
        .textContent =
        peminjaman.status || "-";

    renderProof(pembayaran.bukti_pembayaran);

}

function renderProof(path) {

    const proofArea =
        document.getElementById("proofArea");

    if (!path) {

        proofArea.innerHTML = `
            <div class="no-proof">
                <div style="font-size:40px;">
                    🧾
                </div>

                <p>
                    Belum ada bukti pembayaran.
                </p>
            </div>
        `;

        return;
    }

    const url =
        imageUrl(path);

    const extension =
        path.split(".").pop().toLowerCase();

    if (
        ["jpg", "jpeg", "png", "webp"].includes(extension)
    ) {

        proofArea.innerHTML = `
            <img
                src="${escapeHtml(url)}"
                class="proof-image"
                alt="Bukti Pembayaran"
            >
        `;

        return;
    }

    if (extension === "pdf") {

        proofArea.innerHTML = `
            <div class="proof-pdf">

                <div style="font-size:50px;">
                    📄
                </div>

                <strong>
                    Bukti pembayaran PDF
                </strong>

                <br>

                <a
                    href="${escapeHtml(url)}"
                    target="_blank"
                    rel="noopener noreferrer"
                >
                    Buka Bukti PDF
                </a>

            </div>
        `;

        return;
    }

    proofArea.innerHTML = `
        <div class="proof-pdf">

            <div style="font-size:50px;">
                📎
            </div>

            <strong>
                File bukti pembayaran
            </strong>

            <br>

            <a
                href="${escapeHtml(url)}"
                target="_blank"
                rel="noopener noreferrer"
            >
                Buka File
            </a>

        </div>
    `;

}

loadDetail();

</script>

</body>
</html>