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

$idLoan = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($idLoan <= 0) {
    header("Location: ./index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Peminjaman - Pustanka</title>

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

        /* ================= MAIN ================= */

        .main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
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
            font-size: 26px;
            margin-bottom: 5px;
        }

        .admin-info {
            color: #64748b;
            font-size: 14px;
        }

        /* ================= BUTTON ================= */

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            background: white;
            color: #334155;
            border: 1px solid #dbe2ea;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .btn-back:hover {
            background: #f8fafc;
        }

        /* ================= CARD ================= */

        .card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
            margin-bottom: 20px;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            font-size: 18px;
            color: #111827;
        }

        .loan-id {
            font-size: 13px;
            color: #64748b;
        }

        /* ================= PROFILE ================= */

        .profile {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            object-fit: cover;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .profile-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 32px;
            flex-shrink: 0;
        }

        .profile-info {
            flex: 1;
        }

        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .member-code {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            padding: 5px 9px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        /* ================= GRID ================= */

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .info-item {
            background: #f8fafc;
            border-radius: 9px;
            padding: 13px 15px;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 14px;
            color: #1e293b;
            word-break: break-word;
        }

        /* ================= BOOK ================= */

        .book-layout {
            display: flex;
            gap: 25px;
        }

        .book-cover {
            width: 170px;
            height: 230px;
            object-fit: cover;
            border-radius: 10px;
            background: #e2e8f0;
            flex-shrink: 0;
        }

        .book-placeholder {
            width: 170px;
            height: 230px;
            border-radius: 10px;
            background: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #64748b;
            font-size: 50px;
            flex-shrink: 0;
        }

        .book-info {
            flex: 1;
        }

        .book-title {
            font-size: 22px;
            font-weight: bold;
            color: #111827;
            margin-bottom: 8px;
        }

        .book-author {
            color: #64748b;
            margin-bottom: 18px;
        }

        .sinopsis {
            margin-top: 18px;
        }

        .sinopsis-title {
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .sinopsis-text {
            font-size: 14px;
            color: #475569;
            line-height: 1.7;
            white-space: pre-line;
        }

        /* ================= STATUS ================= */

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-dipinjam {
            background: #fef3c7;
            color: #92400e;
        }

        .status-dikembalikan {
            background: #dcfce7;
            color: #166534;
        }

        .status-terlambat {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-default {
            background: #e2e8f0;
            color: #475569;
        }

        /* ================= LOADING ================= */

        .loading {
            text-align: center;
            padding: 50px 20px;
            color: #64748b;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        /* ================= RESPONSIVE ================= */

        @media (max-width: 900px) {
            .book-layout {
                flex-direction: column;
            }

            .book-cover,
            .book-placeholder {
                width: 150px;
                height: 210px;
            }
        }

        @media (max-width: 700px) {
            .main {
                padding: 18px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile {
                flex-direction: column;
            }

            .book-layout {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- MAIN -->
    <main class="main">

        <div class="topbar">

            <div>
                <h1>Detail Peminjaman</h1>

                <div class="admin-info">
                    Informasi lengkap peminjaman buku
                </div>
            </div>

            <div class="admin-info">
                Admin:
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>
            </div>

        </div>

        <a href="./index.php" class="btn-back">
            ← Kembali
        </a>

        <div id="errorBox" class="error"></div>

        <div id="loading" class="loading">
            Memuat detail peminjaman...
        </div>

        <div id="content" style="display:none;">

            <!-- ================= PEMINJAM ================= -->

            <div class="card">

                <div class="card-header">

                    <h2>Data Peminjam</h2>

                    <div class="loan-id">
                        ID Loan:
                        <strong id="loanId">-</strong>
                    </div>

                </div>

                <div class="profile">

                    <div id="userPhotoContainer">
                        <div class="profile-placeholder">
                            👤
                        </div>
                    </div>

                    <div class="profile-info">

                        <div class="profile-name" id="userName">
                            -
                        </div>

                        <div class="member-code" id="memberCode">
                            -
                        </div>

                        <div class="info-grid">

                            <div class="info-item">
                                <span class="info-label">
                                    NIK
                                </span>

                                <div class="info-value" id="userNik">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Email
                                </span>

                                <div class="info-value" id="userEmail">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    No. Telepon
                                </span>

                                <div class="info-value" id="userPhone">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    ID User
                                </span>

                                <div class="info-value" id="userId">
                                    -
                                </div>
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================= BUKU ================= -->

            <div class="card">

                <div class="card-header">

                    <h2>Data Buku</h2>

                </div>

                <div class="book-layout">

                    <div id="bookPhotoContainer">
                        <div class="book-placeholder">
                            📚
                        </div>
                    </div>

                    <div class="book-info">

                        <div class="book-title" id="bookTitle">
                            -
                        </div>

                        <div class="book-author" id="bookAuthor">
                            -
                        </div>

                        <div class="info-grid">

                            <div class="info-item">
                                <span class="info-label">
                                    ISBN
                                </span>

                                <div class="info-value" id="bookIsbn">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Barcode
                                </span>

                                <div class="info-value" id="bookBarcode">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Penerbit
                                </span>

                                <div class="info-value" id="bookPublisher">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Tahun Terbit
                                </span>

                                <div class="info-value" id="bookYear">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Jenis Buku
                                </span>

                                <div class="info-value" id="bookType">
                                    -
                                </div>
                            </div>

                            <div class="info-item">
                                <span class="info-label">
                                    Harga
                                </span>

                                <div class="info-value" id="bookPrice">
                                    -
                                </div>
                            </div>

                        </div>

                        <div class="sinopsis">

                            <div class="sinopsis-title">
                                Sinopsis
                            </div>

                            <div class="sinopsis-text" id="bookSynopsis">
                                -
                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- ================= PEMINJAMAN ================= -->

            <div class="card">

                <div class="card-header">

                    <h2>Informasi Peminjaman</h2>

                </div>

                <div class="info-grid">

                    <div class="info-item">
                        <span class="info-label">
                            Tanggal Pinjam
                        </span>

                        <div class="info-value" id="borrowDate">
                            -
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            Batas Kembali
                        </span>

                        <div class="info-value" id="dueDate">
                            -
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            Tanggal Kembali
                        </span>

                        <div class="info-value" id="returnDate">
                            -
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            Status
                        </span>

                        <div class="info-value" id="loanStatus">
                            -
                        </div>
                    </div>

                    <div class="info-item">
                        <span class="info-label">
                            Dibuat
                        </span>

                        <div class="info-value" id="createdAt">
                            -
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </main>

</div>


<script>

const ID_LOAN = <?= $idLoan ?>;

const API_URL =
    "../../../api/loans/admin_detail.php?id_loan=" +
    encodeURIComponent(ID_LOAN);


/* ================= HELPER ================= */

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


function formatDate(dateString) {

    if (!dateString) {
        return "-";
    }

    const date = new Date(dateString);

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric"
    });
}


function formatDateTime(dateString) {

    if (!dateString) {
        return "-";
    }

    const date = new Date(dateString);

    if (isNaN(date.getTime())) {
        return dateString;
    }

    return date.toLocaleString("id-ID", {
        day: "2-digit",
        month: "long",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit"
    });
}


function formatRupiah(value) {

    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return "-";
    }

    const number = Number(value);

    if (isNaN(number)) {
        return value;
    }

    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        maximumFractionDigits: 0
    }).format(number);
}


function getStatusClass(status) {

    switch (status) {

        case "dipinjam":
            return "status status-dipinjam";

        case "dikembalikan":
            return "status status-dikembalikan";

        case "terlambat":
            return "status status-terlambat";

        default:
            return "status status-default";
    }
}


function getStatusText(status) {

    switch (status) {

        case "dipinjam":
            return "Dipinjam";

        case "dikembalikan":
            return "Dikembalikan";

        case "terlambat":
            return "Terlambat";

        default:
            return status || "-";
    }
}


/* ================= PHOTO ================= */

function renderUserPhoto(photo) {

    const container =
        document.getElementById("userPhotoContainer");

    if (!photo) {

        container.innerHTML = `
            <div class="profile-placeholder">
                👤
            </div>
        `;

        return;
    }

    const photoUrl =
        "../../../uploads/users/" + photo;

    container.innerHTML = `
        <img
            src="${escapeHtml(photoUrl)}"
            alt="Foto Peminjam"
            class="profile-photo"
            onerror="
                this.parentElement.innerHTML =
                '<div class=&quot;profile-placeholder&quot;>👤</div>'
            "
        >
    `;
}


function renderBookPhoto(photo) {

    const container =
        document.getElementById("bookPhotoContainer");

    if (!photo) {

        container.innerHTML = `
            <div class="book-placeholder">
                📚
            </div>
        `;

        return;
    }

    let photoUrl = photo;

    // Kalau foto adalah path lokal
    if (
        !photo.startsWith("http://") &&
        !photo.startsWith("https://") &&
        !photo.startsWith("//")
    ) {
        photoUrl = "../../../" + photo;
    }

    container.innerHTML = `
        <img
            src="${escapeHtml(photoUrl)}"
            alt="Foto Buku"
            class="book-cover"
            onerror="
                this.parentElement.innerHTML =
                '<div class=&quot;book-placeholder&quot;>📚</div>'
            "
        >
    `;
}


/* ================= LOAD DETAIL ================= */

async function loadDetail() {

    const loading =
        document.getElementById("loading");

    const content =
        document.getElementById("content");

    const errorBox =
        document.getElementById("errorBox");

    try {

        const response = await fetch(
            API_URL,
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
                "Gagal mengambil detail peminjaman."
            );
        }

        const data =
            result.data || {};

        const peminjam =
            data.peminjam || {};

        const buku =
            data.buku || {};

        const peminjaman =
            data.peminjaman || {};


        /* ================= PEMINJAM ================= */

        document.getElementById("loanId").textContent =
            data.id_loan || "-";

        document.getElementById("userId").textContent =
            peminjam.id_user || "-";

        document.getElementById("userName").textContent =
            peminjam.nama || "-";

        document.getElementById("memberCode").textContent =
            peminjam.member_code || "-";

        document.getElementById("userNik").textContent =
            peminjam.nik || "-";

        document.getElementById("userEmail").textContent =
            peminjam.email || "-";

        document.getElementById("userPhone").textContent =
            peminjam.no_telp || "-";

        renderUserPhoto(
            peminjam.foto
        );


        /* ================= BUKU ================= */

        document.getElementById("bookTitle").textContent =
            buku.judul || "-";

        document.getElementById("bookAuthor").textContent =
            buku.pengarang
                ? "Oleh " + buku.pengarang
                : "-";

        document.getElementById("bookIsbn").textContent =
            buku.isbn || "-";

        document.getElementById("bookBarcode").textContent =
            buku.barcode || "-";

        document.getElementById("bookPublisher").textContent =
            buku.penerbit || "-";

        document.getElementById("bookYear").textContent =
            buku.tahun_terbit || "-";

        document.getElementById("bookType").textContent =
            buku.jenis_buku || "-";

        document.getElementById("bookPrice").textContent =
            formatRupiah(buku.harga_buku);

        document.getElementById("bookSynopsis").textContent =
            buku.sinopsis || "-";

        renderBookPhoto(
            buku.foto
        );


        /* ================= PEMINJAMAN ================= */

        document.getElementById("borrowDate").textContent =
            formatDate(
                peminjaman.tanggal_pinjam
            );

        document.getElementById("dueDate").textContent =
            formatDate(
                peminjaman.batas_kembali
            );

        document.getElementById("returnDate").textContent =
            formatDate(
                peminjaman.tanggal_kembali
            );

        const statusElement =
            document.getElementById("loanStatus");

        const status =
            peminjaman.status || "";

        statusElement.innerHTML = `
            <span class="${getStatusClass(status)}">
                ${escapeHtml(getStatusText(status))}
            </span>
        `;

        document.getElementById("createdAt").textContent =
            formatDateTime(
                peminjaman.created_at
            );


        loading.style.display = "none";

        content.style.display = "block";

    } catch (error) {

        console.error(
            "Load loan detail error:",
            error
        );

        loading.style.display = "none";

        errorBox.textContent =
            error.message ||
            "Terjadi kesalahan saat mengambil data.";

        errorBox.style.display = "block";
    }
}


/* ================= INIT ================= */

loadDetail();

</script>

</body>
</html>