<?php
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

    <title>Peminjaman Saya - Pustanka</title>

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

        /* HEADER */

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

        /* SUMMARY */

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

        .summary-card.active .summary-value {
            color: #3182ce;
        }

        .summary-card.late .summary-value {
            color: #e53e3e;
        }

        .summary-card.returned .summary-value {
            color: #16794c;
        }

        /* TOOLBAR */

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

        /* LOANS */

        .loans-list {
            display: grid;
            gap: 16px;
        }

        .loan-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition:
                transform 0.2s,
                box-shadow 0.2s;
        }

        .loan-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 59, 102, 0.08);
        }

        .loan-top {
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

        /* STATUS */

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

        .status-dipinjam {
            background: #ebf8ff;
            color: #3182ce;
        }

        .status-terlambat {
            background: #fff5f5;
            color: #e53e3e;
        }

        .status-dikembalikan {
            background: #e9f8ef;
            color: #16794c;
        }

        .status-default {
            background: #edf2f7;
            color: #4a5568;
        }

        /* INFO */

        .loan-info {
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

        .late-text {
            color: #e53e3e;
        }

        /* FOOTER CARD */

        .loan-footer {
            display: flex;
            justify-content: flex-end;
            margin-top: 15px;
        }

        .detail-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 15px;
            border-radius: 8px;
            background: #0d3b66;
            color: #ffffff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s;
        }

        .detail-button:hover {
            background: #092c4e;
        }

        /* LOADING */

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

        /* EMPTY */

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

        .browse-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 18px;
            border-radius: 8px;
            background: #0d3b66;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .browse-button:hover {
            background: #092c4e;
        }

        /* ERROR */

        .error-state {
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
            padding: 20px;
            color: #e53e3e;
            text-align: center;
        }

        /* PAGINATION */

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

        /* RESPONSIVE */

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

            .loan-info {
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

            .loan-top {
                flex-direction: column;
            }

            .loan-info {
                grid-template-columns: 1fr;
            }

            .loan-footer {
                justify-content: stretch;
            }

            .detail-button {
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
                <li class="active"><a href="index.php"> Peminjaman Buku</a></li>
                <li><a href="../payments/"> Pembayaran Denda</a></li>
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
                    <h1>Peminjaman Saya</h1>

                    <p>
                        Lihat dan pantau buku yang sedang kamu pinjam.
                    </p>
                </div>

            </div>


            <!-- SUMMARY -->
            <div class="summary">

                <div class="summary-card active">

                    <div class="summary-label">
                        Sedang Dipinjam
                    </div>

                    <div
                        class="summary-value"
                        id="activeCount"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card late">

                    <div class="summary-label">
                        Terlambat
                    </div>

                    <div
                        class="summary-value"
                        id="lateCount"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card returned">

                    <div class="summary-label">
                        Dikembalikan
                    </div>

                    <div
                        class="summary-value"
                        id="returnedCount"
                    >
                        0
                    </div>

                </div>

            </div>


            <!-- TOOLBAR -->
            <div class="toolbar">

                <div class="toolbar-title">
                    Riwayat Peminjaman
                </div>

                <select
                    id="statusFilter"
                    class="filter-select"
                >
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


            <!-- CONTENT -->
            <div id="content">

                <div class="loading">

                    <div class="spinner"></div>

                    Memuat data peminjaman...

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

    const activeCount =
        document.getElementById("activeCount");

    const lateCount =
        document.getElementById("lateCount");

    const returnedCount =
        document.getElementById("returnedCount");


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

        const result = await response.json();

        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return null;
        }

        if (!response.ok || !result.status) {

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


    function getStatusInfo(status) {

        const normalized =
            String(status || "")
                .toLowerCase()
                .trim();

        if (normalized === "dipinjam") {

            return {
                className: "status-dipinjam",
                icon: "📖",
                text: "Dipinjam"
            };
        }

        if (normalized === "terlambat") {

            return {
                className: "status-terlambat",
                icon: "⚠️",
                text: "Terlambat"
            };
        }

        if (normalized === "dikembalikan") {

            return {
                className: "status-dikembalikan",
                icon: "✅",
                text: "Dikembalikan"
            };
        }

        return {
            className: "status-default",
            icon: "ℹ️",
            text: status || "-"
        };
    }


    function renderLoan(loan) {

        const status =
            getStatusInfo(loan.status);

        const isLate =
            String(loan.status || "")
                .toLowerCase() === "terlambat";

        const title =
            loan.judul_buku ||
            loan.judul ||
            "Buku";

        const author =
            loan.pengarang ||
            "-";

        const idLoan =
            loan.id_loan;

        return `
            <div class="loan-card">

                <div class="loan-top">

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


                <div class="loan-info">

                    <div class="info-item">

                        <div class="info-label">
                            Tanggal Pinjam
                        </div>

                        <div class="info-value">
                            ${formatDate(
                                loan.tanggal_pinjam
                            )}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Batas Kembali
                        </div>

                        <div
                            class="info-value ${
                                isLate
                                    ? "late-text"
                                    : ""
                            }"
                        >
                            ${formatDate(
                                loan.batas_kembali
                            )}
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            Tanggal Kembali
                        </div>

                        <div class="info-value">

                            ${
                                loan.tanggal_kembali
                                    ? formatDate(
                                        loan.tanggal_kembali
                                    )
                                    : "Belum dikembalikan"
                            }

                        </div>

                    </div>

                </div>


                <div class="loan-footer">

                    <a
                        class="detail-button"
                        href="./detail.php?id_loan=${encodeURIComponent(
                            idLoan
                        )}"
                    >
                        Lihat Detail →
                    </a>

                </div>

            </div>
        `;
    }


    function escapeHtml(value) {

        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function updateSummary(data) {

        const loans =
            Array.isArray(data)
                ? data
                : [];


        let active = 0;
        let late = 0;
        let returned = 0;


        loans.forEach(loan => {

            const status =
                String(loan.status || "")
                    .toLowerCase()
                    .trim();

            if (status === "dipinjam") {
                active++;
            }

            else if (status === "terlambat") {
                late++;
            }

            else if (status === "dikembalikan") {
                returned++;
            }

        });


        activeCount.textContent = active;
        lateCount.textContent = late;
        returnedCount.textContent = returned;
    }


    async function loadLoans(page = 1) {

        currentPage = page;

        content.innerHTML = `
            <div class="loading">

                <div class="spinner"></div>

                Memuat data peminjaman...

            </div>
        `;

        pagination.style.display = "none";


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
                    "/loans/list.php?" +
                    params.toString()
                );


            if (!result) {
                return;
            }


            const data =
                result.data;


            let loans = [];


            if (Array.isArray(data)) {

                loans = data;

            } else if (
                data &&
                Array.isArray(data.data)
            ) {

                loans = data.data;
            }


            updateSummary(loans);


            if (loans.length === 0) {

                content.innerHTML = `
                    <div class="empty-state">

                        <div class="empty-icon">
                            📚
                        </div>

                        <h3>
                            Belum Ada Peminjaman
                        </h3>

                        <p>
                            Kamu belum memiliki
                            data peminjaman buku.
                        </p>

                        <a
                            href="../books/"
                            class="browse-button"
                        >
                            🔎 Cari Buku
                        </a>

                    </div>
                `;

                return;
            }


            content.innerHTML = `
                <div class="loans-list">
                    ${loans
                        .map(renderLoan)
                        .join("")}
                </div>
            `;


            renderPagination(
                data
            );

        } catch (error) {

            console.error(error);

            content.innerHTML = `
                <div class="error-state">

                    ❌ ${escapeHtml(
                        error.message ||
                        "Gagal memuat data peminjaman."
                    )}

                </div>
            `;
        }
    }


    function renderPagination(data) {

        pagination.innerHTML = "";


        let current =
            Number(
                data?.pagination?.current_page ??
                data?.current_page ??
                currentPage
            );


        let totalPages =
            Number(
                data?.pagination?.total_pages ??
                data?.total_pages ??
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
            document.createElement("button");

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
                    loadLoans(current - 1);
                }

            }
        );


        pagination.appendChild(prev);


        for (
            let page = 1;
            page <= totalPages;
            page++
        ) {

            if (
                totalPages > 7 &&
                page > 2 &&
                page < totalPages - 1 &&
                Math.abs(page - current) > 1
            ) {

                if (
                    page === 3 ||
                    page === totalPages - 2
                ) {

                    const dots =
                        document.createElement(
                            "span"
                        );

                    dots.className =
                        "page-info";

                    dots.textContent =
                        "...";

                    pagination.appendChild(
                        dots
                    );
                }

                continue;
            }


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
                () => loadLoans(page)
            );

            pagination.appendChild(
                button
            );
        }


        const next =
            document.createElement("button");

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
                    loadLoans(current + 1);
                }

            }
        );


        pagination.appendChild(next);
    }


    statusFilter.addEventListener(
        "change",
        () => {

            loadLoans(1);

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
    loadLoans(1);

</script>

</body>
</html>