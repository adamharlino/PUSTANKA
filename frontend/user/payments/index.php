<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

if (
    !isset($_SESSION["id_user"]) ||
    !is_numeric($_SESSION["id_user"]) ||
    (int) $_SESSION["id_user"] <= 0 ||
    ($_SESSION["role"] ?? "") !== "user"
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

    <title>Denda & Pembayaran - Pustanka</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family:
                'Segoe UI',
                Tahoma,
                Geneva,
                Verdana,
                sans-serif;
            background: #f4f7fc;
            color: #333;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* =========================
           SIDEBAR STYLING
        ========================== */
        .sidebar {
            width: 260px;
            background-color: #0d3b66;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 20px 0;
            flex-shrink: 0;
        }

        .sidebar-header {
            padding: 0 20px 30px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }

        .sidebar-menu {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 25px;
            color: #c0d1e3;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu li.active a, 
        .sidebar-menu li a:hover {
            background-color: #092c4e;
            color: #ffffff;
            border-left: 4px solid #3182ce;
        }

        /* =========================
           MAIN CONTENT & NAVBAR
        ========================== */
        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .navbar {
            background-color: #ffffff;
            padding: 15px 30px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            border-bottom: 1px solid #e2e8f0;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .user-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0d3b66;
        }

        .logout-btn {
            background-color: #e53e3e;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }

        .logout-btn:hover {
            background-color: #c53030;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
            padding: 30px;
        }

        /* =========================
           HEADER
        ========================= */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 25px;
        }

        .header-left h1 {
            margin: 0 0 6px;
            font-size: 26px;
            color: #0d3b66;
            font-weight: 700;
        }

        .header-left p {
            margin: 0;
            color: #718096;
            font-size: 14px;
            line-height: 1.5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            background: #ffffff;
            color: #0d3b66;
            border: 1px solid #e2e8f0;
            font-weight: 600;
            transition: 0.2s;
        }

        .back-button:hover {
            background: #f8fafc;
        }

        /* =========================
           SUMMARY
        ========================= */

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .summary-label {
            font-size: 13px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .summary-value {
            font-size: 27px;
            font-weight: 700;
        }

        .summary-card.unpaid .summary-value {
            color: #e53e3e;
        }

        .summary-card.pending .summary-value {
            color: #d69e2e;
        }

        .summary-card.paid .summary-value {
            color: #16794c;
        }

        /* =========================
           TOOLBAR
        ========================= */

        .toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 18px;
        }

        .toolbar-title {
            font-size: 18px;
            font-weight: 700;
            color: #0d3b66;
        }

        .filter-select {
            border: 1px solid #dce3ec;
            border-radius: 8px;
            padding: 10px 13px;
            background: #ffffff;
            font-size: 14px;
            outline: none;
            color: #25364d;
        }

        .filter-select:focus {
            border-color: #3182ce;
            box-shadow: 0 0 0 3px rgba(49, 130, 206, .1);
        }

        /* =========================
           FINES
        ========================= */

        .fines-list {
            display: grid;
            gap: 16px;
        }

        .fine-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition:
                transform 0.2s,
                box-shadow 0.2s;
        }

        .fine-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 59, 102, 0.08);
        }

        .fine-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 18px;
        }

        .book-info {
            min-width: 0;
        }

        .book-title {
            margin: 0 0 6px;
            font-size: 18px;
            font-weight: 700;
            color: #0d3b66;
            line-height: 1.4;
        }

        .book-author {
            margin: 0;
            color: #66758a;
            font-size: 14px;
        }

        /* =========================
           PAYMENT STATUS
        ========================= */

        .status {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-belum-bayar {
            background: #fff5f5;
            color: #e53e3e;
        }

        .status-pending {
            background: #fefcbf;
            color: #b7791f;
        }

        .status-lunas {
            background: #e9f8ef;
            color: #16794c;
        }

        .status-ditolak {
            background: #fff5f5;
            color: #e53e3e;
        }

        .status-default {
            background: #edf2f7;
            color: #4a5568;
        }

        /* =========================
           INFO
        ========================= */

        .fine-info {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 12px;
            padding: 15px 0;
            border-top: 1px solid #e2e8f0;
            border-bottom: 1px solid #e2e8f0;
        }

        .info-item {
            min-width: 0;
        }

        .info-label {
            color: #718096;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-weight: 600;
            font-size: 14px;
            color: #25364d;
        }

        .nominal {
            color: #e53e3e;
            font-size: 17px;
        }

        /* =========================
           PAYMENT INFO
        ========================= */

        .payment-info {
            margin-top: 14px;
            padding: 12px 14px;
            border-radius: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #4a5568;
        }

        .payment-info strong {
            color: #0d3b66;
        }

        /* =========================
           FOOTER
        ========================= */

        .fine-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 15px;
        }

        .detail-button,
        .pay-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s;
            cursor: pointer;
        }

        .detail-button {
            background: #ffffff;
            color: #334155;
            border: 1px solid #dce3ec;
        }

        .detail-button:hover {
            background: #f8fafc;
        }

        .pay-button {
            background: #0d3b66;
            color: #ffffff;
            border: 1px solid #0d3b66;
        }

        .pay-button:hover {
            background: #092c4e;
            border-color: #092c4e;
        }

        .pay-button.retry {
            background: #d69e2e;
            border-color: #d69e2e;
            color: #ffffff;
        }

        .pay-button.retry:hover {
            background: #b7791f;
            border-color: #b7791f;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            padding: 40px 20px;
            text-align: center;
            color: #718096;
        }

        .spinner {
            width: 32px;
            height: 32px;
            margin: 0 auto 12px;
            border: 3px solid #e2e8f0;
            border-top-color: #3182ce;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* =========================
           EMPTY
        ========================= */

        .empty-state {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 55px 20px;
            text-align: center;
        }

        .empty-icon {
            font-size: 50px;
            margin-bottom: 15px;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0d3b66;
        }

        .empty-state p {
            margin: 0 0 20px;
            color: #718096;
        }

        /* =========================
           ERROR
        ========================= */

        .error-state {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 20px;
            color: #e53e3e;
            text-align: center;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 25px;
        }

        .page-button {
            min-width: 38px;
            height: 38px;
            padding: 0 10px;
            border: 1px solid #dce3ec;
            border-radius: 8px;
            background: #ffffff;
            color: #36506f;
            cursor: pointer;
            font-weight: 700;
        }

        .page-button:hover:not(:disabled) {
            background: #edf3f9;
        }

        .page-button.active {
            background: #0d3b66;
            border-color: #0d3b66;
            color: #ffffff;
        }

        .page-button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .page-info {
            color: #718096;
            font-size: 14px;
            margin: 0 7px;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            body {
                flex-direction: column;
                height: auto;
                overflow: auto;
            }

            .sidebar {
                width: 100%;
                height: auto;
            }

            .main-content {
                overflow: visible;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .fine-info {
                grid-template-columns: 1fr 1fr;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 18px 14px 40px;
            }

            .header-left h1 {
                font-size: 22px;
            }

            .toolbar {
                align-items: stretch;
                flex-direction: column;
            }

            .filter-select {
                width: 100%;
            }

            .fine-top {
                flex-direction: column;
            }

            .fine-info {
                grid-template-columns: 1fr;
            }

            .fine-footer {
                flex-direction: column;
                align-items: stretch;
            }

            .detail-button,
            .pay-button {
                width: 100%;
            }

            .pagination {
                flex-wrap: wrap;
            }
        }

    </style>

</head>

<body>

    <!-- SIDEBAR -->
    <div class="sidebar">
        <div>
            <div class="sidebar-header">
                <h2>PUSTANKA</h2>
            </div>
            <ul class="sidebar-menu">
                <li><a href="../dashboard/index.php"> Dashboard</a></li>
                <li><a href="../books/"> Katalog Buku</a></li>
                <li><a href="../loans/"> Peminjaman Buku</a></li>
                <li class="active"><a href="index.php"> Pembayaran Denda</a></li>
                <li><a href="../attendance/"> Kehadiran</a></li>
                <li><a href="../profile/"> Profil</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR -->
        <div class="navbar">
            <div class="nav-right">
                <div class="user-name" id="navbarUserName">
                    Memuat...
                </div>
                <button class="logout-btn" onclick="logout()">
                    Logout
                </button>
            </div>
        </div>

        <div class="container">

            <!-- HEADER -->

            <div class="page-header">

                <div class="header-left">

                    <h1>Denda & Pembayaran</h1>

                    <p>
                        Lihat denda dan status pembayaran kamu.
                    </p>

                </div>

                

            </div>


            <!-- SUMMARY -->

            <div class="summary">

                <div class="summary-card unpaid">

                    <div class="summary-label">
                        Belum Bayar
                    </div>

                    <div
                        class="summary-value"
                        id="unpaidCount"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card pending">

                    <div class="summary-label">
                        Menunggu Verifikasi
                    </div>

                    <div
                        class="summary-value"
                        id="pendingCount"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card paid">

                    <div class="summary-label">
                        Lunas
                    </div>

                    <div
                        class="summary-value"
                        id="paidCount"
                    >
                        0
                    </div>

                </div>

            </div>


            <!-- TOOLBAR -->

            <div class="toolbar">

                <div class="toolbar-title">
                    Riwayat Denda
                </div>

                <select
                    id="statusFilter"
                    class="filter-select"
                >

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


            <!-- CONTENT -->

            <div id="content">

                <div class="loading">

                    <div class="spinner"></div>

                    Memuat data denda...

                </div>

            </div>


            <!-- PAGINATION -->

            <div
                id="pagination"
                class="pagination"
                style="display: none;"
            ></div>

        </div>
    </div>


<script>

    const API_BASE = "../../../api";
    const LOGOUT_URL = API_BASE + "/auth/logout.php";

    const content =
        document.getElementById("content");

    const pagination =
        document.getElementById("pagination");

    const statusFilter =
        document.getElementById("statusFilter");

    const unpaidCount =
        document.getElementById("unpaidCount");

    const pendingCount =
        document.getElementById("pendingCount");

    const paidCount =
        document.getElementById("paidCount");


    let currentPage = 1;

    const limit = 10;


    async function getJson(url) {

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
                "../../auth/login.php";

            return null;
        }

        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Gagal mengambil data."
            );
        }

        return result;
    }


    async function loadUserData() {
        try {
            const result = await getJson(API_BASE + "/dashboard/user.php");
            const user = result.data?.user;
            if (user && user.nama) {
                document.getElementById("navbarUserName").textContent = user.nama;
            }
        } catch (e) {
            // Abaikan jika gagal memuat nama di navbar
        }
    }


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


    function formatDate(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(value + "T00:00:00");

        if (isNaN(date.getTime())) {
            return value;
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


    function escapeHtml(value) {

        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function getPaymentStatusInfo(
        fine,
        payment
    ) {

        const fineStatus =
            String(
                fine?.status_pembayaran || ""
            )
                .toLowerCase()
                .trim();

        const paymentStatus =
            String(
                payment?.status || ""
            )
                .toLowerCase()
                .trim();


        if (paymentStatus === "ditolak") {

            return {
                className: "status-ditolak",
                icon: "❌",
                text: "Pembayaran Ditolak"
            };
        }


        if (paymentStatus === "pending") {

            return {
                className: "status-pending",
                icon: "⏳",
                text: "Menunggu Verifikasi"
            };
        }


        if (fineStatus === "lunas") {

            return {
                className: "status-lunas",
                icon: "✅",
                text: "Lunas"
            };
        }


        if (fineStatus === "belum_bayar") {

            return {
                className: "status-belum-bayar",
                icon: "⚠️",
                text: "Belum Bayar"
            };
        }


        return {
            className: "status-default",
            icon: "ℹ️",
            text: fineStatus || "-"
        };
    }


    function updateSummary(fines) {

        let unpaid = 0;
        let pending = 0;
        let paid = 0;


        fines.forEach(fine => {

            const status =
                String(
                    fine?.denda
                        ?.status_pembayaran || ""
                )
                    .toLowerCase()
                    .trim();

            const paymentStatus =
                String(
                    fine?.payment_terakhir
                        ?.status || ""
                )
                    .toLowerCase()
                    .trim();


            if (status === "lunas") {

                paid++;

            } else if (
                paymentStatus === "pending"
            ) {

                pending++;

            } else {

                unpaid++;
            }

        });


        unpaidCount.textContent = unpaid;
        pendingCount.textContent = pending;
        paidCount.textContent = paid;
    }


    function renderFine(fine) {

        const denda =
            fine?.denda || {};

        const buku =
            fine?.buku || {};

        const peminjaman =
            fine?.peminjaman || {};

        const payment =
            fine?.payment_terakhir || null;


        const status =
            getPaymentStatusInfo(
                denda,
                payment
            );


        const title =
            buku.judul ||
            "Buku";


        const author =
            buku.pengarang ||
            "-";


        let actionHtml = "";


        if (
            denda.status_pembayaran === "lunas"
        ) {
            if (payment && payment.id_payment) {
                actionHtml = `
                    <a
                        href="./detail.php?id=${encodeURIComponent(
                            payment.id_payment
                        )}"
                        class="detail-button"
                    >
                        Lihat Detail →
                    </a>
                `;
            }

        } else if (
            payment &&
            payment.status === "pending"
        ) {
            actionHtml = `
                <a
                    href="./detail.php?id=${encodeURIComponent(
                        payment.id_payment
                    )}"
                    class="detail-button"
                >
                    Lihat Detail →
                </a>
            `;

        } else {
            const isRetry =
                payment &&
                payment.status === "ditolak";

            actionHtml = `
                ${
                    isRetry
                        ? `
                            <a
                                href="./detail.php?id=${encodeURIComponent(
                                    payment.id_payment
                                )}"
                                class="detail-button"
                            >
                                Lihat Detail →
                            </a>
                        `
                        : ""
                }

                <a
                    href="./create.php?id_fine=${encodeURIComponent(
                        fine.id_fine
                    )}"
                    class="pay-button ${isRetry ? "retry" : ""}"
                >
                    ${
                        isRetry
                            ? "Bayar Lagi →"
                            : "Bayar Denda →"
                    }
                </a>
            `;
        }


        let paymentInfo = "";

        if (payment) {

            let paymentText = "";

            if (
                payment.status === "pending"
            ) {

                paymentText =
                    `Pembayaran ${formatRupiah(
                        payment.nominal
                    )} sedang menunggu verifikasi admin.`;

            } else if (
                payment.status === "ditolak"
            ) {

                paymentText =
                    `Pembayaran sebelumnya ditolak. Kamu dapat melakukan pembayaran kembali.`;

            } else if (
                payment.status === "disetujui"
            ) {

                paymentText =
                    `Pembayaran ${formatRupiah(
                        payment.nominal
                    )} telah disetujui admin.`;
            }


            if (paymentText) {

                paymentInfo = `
                    <div class="payment-info">
                        ${escapeHtml(paymentText)}
                    </div>
                `;
            }
        }


        return `

            <div class="fine-card">

                <div class="fine-top">

                    <div class="book-info">

                        <h2 class="book-title">
                            ${escapeHtml(title)}
                        </h2>

                        <p class="book-author">
                            Oleh ${escapeHtml(author)}
                        </p>

                    </div>


                    <span
                        class="status ${status.className}"
                    >
                        ${status.icon}
                        ${escapeHtml(status.text)}
                    </span>

                </div>


                <div class="fine-info">

                    <div class="info-item">

                        <div class="info-label">
                            Nominal Denda
                        </div>

                        <div class="info-value nominal">
                            ${formatRupiah(
                                denda.nominal
                            )}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Tanggal Pinjam
                        </div>

                        <div class="info-value">
                            ${formatDate(
                                peminjaman
                                    .tanggal_pinjam
                            )}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Batas Kembali
                        </div>

                        <div class="info-value">
                            ${formatDate(
                                peminjaman
                                    .batas_kembali
                            )}
                        </div>

                    </div>

                </div>


                ${paymentInfo}


                <div class="fine-footer">

                    ${actionHtml}

                </div>

            </div>

        `;
    }


    async function loadFines(page = 1) {

        currentPage = page;


        content.innerHTML = `

            <div class="loading">

                <div class="spinner"></div>

                Memuat data denda...

            </div>

        `;


        pagination.style.display =
            "none";


        try {

            const status =
                statusFilter.value;


            const params =
                new URLSearchParams();


            params.set(
                "page",
                page
            );

            params.set(
                "limit",
                limit
            );


            if (status) {

                params.set(
                    "status",
                    status
                );
            }


            const result =
                await getJson(
                    API_BASE +
                    "/fines/list.php?" +
                    params.toString()
                );


            if (!result) {
                return;
            }


            const data =
                result.data;


            let fines = [];


            if (
                Array.isArray(data)
            ) {

                fines = data;

            } else if (
                data &&
                Array.isArray(data.data)
            ) {

                fines = data.data;
            }


            updateSummary(fines);


            if (fines.length === 0) {

                content.innerHTML = `

                    <div class="empty-state">

                        <div class="empty-icon">
                            💰
                        </div>

                        <h3>
                            Belum Ada Denda
                        </h3>

                        <p>
                            Kamu belum memiliki
                            data denda pembayaran.
                        </p>

                    </div>

                `;

                return;
            }


            content.innerHTML = `

                <div class="fines-list">

                    ${fines
                        .map(renderFine)
                        .join("")}

                </div>

            `;


            renderPagination(result);


        } catch (error) {

            console.error(error);


            content.innerHTML = `

                <div class="error-state">

                    ❌ ${
                        escapeHtml(
                            error.message ||
                            "Gagal memuat data denda."
                        )
                    }

                </div>

            `;
        }
    }


    function renderPagination(data) {

        pagination.innerHTML = "";


        const current =
            Number(
                data?.pagination?.page ??
                currentPage
            );


        const totalPages =
            Number(
                data?.pagination?.total_page ??
                1
            );


        if (
            !Number.isFinite(totalPages) ||
            totalPages <= 1
        ) {

            pagination.style.display =
                "none";

            return;
        }


        pagination.style.display =
            "flex";


        const prev =
            document.createElement(
                "button"
            );

        prev.className =
            "page-button";

        prev.textContent =
            "‹";

        prev.disabled =
            current <= 1;


        prev.addEventListener(
            "click",
            () => {

                if (current > 1) {

                    loadFines(
                        current - 1
                    );
                }

            }
        );


        pagination.appendChild(prev);


        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            const button =
                document.createElement(
                    "button"
                );

            button.className =
                "page-button";


            if (page === current) {

                button.classList.add(
                    "active"
                );
            }


            button.textContent =
                page;


            button.addEventListener(
                "click",
                () => loadFines(page)
            );


            pagination.appendChild(
                button
            );
        }


        const next =
            document.createElement(
                "button"
            );

        next.className =
            "page-button";

        next.textContent =
            "›";

        next.disabled =
            current >= totalPages;


        next.addEventListener(
            "click",
            () => {

                if (
                    current < totalPages
                ) {

                    loadFines(
                        current + 1
                    );
                }

            }
        );


        pagination.appendChild(next);
    }


    statusFilter.addEventListener(
        "change",
        () => {

            loadFines(1);

        }
    );


    async function logout() {
        const confirmed = confirm("Yakin ingin logout?");
        if (!confirmed) return;

        try {
            await fetch(LOGOUT_URL, {
                method: "POST",
                credentials: "include"
            });
        } catch (error) {
            console.error(error);
        }

        window.location.href = "../../auth/login.php";
    }


    loadUserData();
    loadFines(1);

</script>

</body>

</html>