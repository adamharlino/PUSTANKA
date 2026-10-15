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

    <title>Riwayat Kehadiran - Pustanka</title>

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
            max-width: 1050px;
            margin: 0 auto;
            padding: 30px;
        }

        /* HEADER */

        .page-header {
            display: flex;
            align-items: flex-start;
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
            background: #ffffff;
            color: #0d3b66;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            white-space: nowrap;
        }

        .back-button:hover {
            background: #f8fafc;
        }

        /* TODAY */

        .today-card {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 20px;
            padding: 22px;
            margin-bottom: 25px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .today-label {
            margin-bottom: 7px;
            color: #718096;
            font-size: 13px;
            font-weight: 600;
        }

        .today-date {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 700;
            color: #0d3b66;
        }

        .today-description {
            margin: 0;
            color: #718096;
            font-size: 14px;
        }

        .today-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
            white-space: nowrap;
        }

        .today-status.present {
            background: #e9f8ef;
            color: #16794c;
        }

        .today-status.pending {
            background: #fefcbf;
            color: #b7791f;
        }

        .today-status.complete {
            background: #ebf8ff;
            color: #3182ce;
        }

        /* SUMMARY */

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 25px;
        }

        .summary-card {
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .summary-label {
            margin-bottom: 8px;
            color: #718096;
            font-size: 13px;
            font-weight: 600;
        }

        .summary-value {
            font-size: 26px;
            font-weight: 800;
        }

        .summary-card.hadir .summary-value {
            color: #16794c;
        }

        .summary-card.complete .summary-value {
            color: #3182ce;
        }

        .summary-card.incomplete .summary-value {
            color: #d69e2e;
        }

        /* TITLE */

        .section-title {
            margin: 0 0 16px;
            font-size: 18px;
            color: #0d3b66;
            font-weight: 700;
        }

        /* ATTENDANCE LIST */

        .attendance-list {
            display: grid;
            gap: 15px;
        }

        .attendance-card {
            display: grid;
            grid-template-columns: 1.3fr 1fr 1fr auto;
            align-items: center;
            gap: 20px;
            padding: 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .attendance-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(13, 59, 102, 0.08);
        }

        .attendance-date {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .date-main {
            font-size: 15px;
            font-weight: 700;
            color: #0d3b66;
        }

        .date-day {
            color: #718096;
            font-size: 13px;
        }

        .time-item {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .time-label {
            color: #718096;
            font-size: 12px;
            font-weight: 600;
        }

        .time-value {
            font-size: 14px;
            font-weight: 600;
            color: #25364d;
        }

        .time-value.empty {
            color: #a0aec0;
        }

        .attendance-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
            white-space: nowrap;
        }

        .status-complete {
            background: #e9f8ef;
            color: #16794c;
        }

        .status-incomplete {
            background: #fefcbf;
            color: #b7791f;
        }

        /* LOADING */

        .loading {
            padding: 40px 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
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
            padding: 55px 20px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            text-align: center;
        }

        .empty-icon {
            margin-bottom: 15px;
            font-size: 50px;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            font-size: 20px;
            color: #0d3b66;
        }

        .empty-state p {
            margin: 0;
            color: #718096;
        }

        /* ERROR */

        .error-state {
            padding: 20px;
            background: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 12px;
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
            padding: 0 5px;
            color: #718096;
            font-size: 13px;
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

            .attendance-card {
                grid-template-columns: 1fr 1fr;
            }

            .attendance-status {
                justify-self: start;
            }
        }

        @media (max-width: 700px) {
            .page-header {
                flex-direction: column;
            }

            .summary {
                grid-template-columns: 1fr;
            }

            .today-card {
                grid-template-columns: 1fr;
            }

            .today-status {
                justify-self: start;
            }
        }

        @media (max-width: 500px) {
            .container {
                padding: 18px 14px 40px;
            }

            .header-left h1 {
                font-size: 22px;
            }

            .attendance-card {
                grid-template-columns: 1fr;
                gap: 14px;
            }

            .attendance-status {
                justify-self: start;
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
                <li><a href="../payments/"> Pembayaran Denda</a></li>
                <li class="active"><a href="index.php"> Kehadiran</a></li>
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

                    <h1>
                        Riwayat Kehadiran
                    </h1>

                    <p>
                        Lihat riwayat kehadiran kamu di perpustakaan.
                    </p>

                </div>


            </div>


            <!-- TODAY -->

            <div class="today-card">

                <div>

                    <div class="today-label">
                        Kehadiran Hari Ini
                    </div>

                    <h2
                        class="today-date"
                        id="todayDate"
                    >
                        -
                    </h2>

                    <p
                        class="today-description"
                        id="todayDescription"
                    >
                        Memuat status kehadiran...
                    </p>

                </div>

                <div
                    id="todayStatus"
                    class="today-status pending"
                >
                    Memuat...
                </div>

            </div>


            <!-- SUMMARY -->

            <div class="summary">

                <div class="summary-card hadir">

                    <div class="summary-label">
                        Total Kehadiran
                    </div>

                    <div
                        class="summary-value"
                        id="totalAttendance"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card complete">

                    <div class="summary-label">
                        Kehadiran Lengkap
                    </div>

                    <div
                        class="summary-value"
                        id="completeAttendance"
                    >
                        0
                    </div>

                </div>


                <div class="summary-card incomplete">

                    <div class="summary-label">
                        Belum Check-out
                    </div>

                    <div
                        class="summary-value"
                        id="incompleteAttendance"
                    >
                        0
                    </div>

                </div>

            </div>


            <!-- LIST -->

            <h2 class="section-title">
                📋 Riwayat Kehadiran
            </h2>


            <div id="content">

                <div class="loading">

                    <div class="spinner"></div>

                    Memuat riwayat kehadiran...

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

    const todayDate =
        document.getElementById("todayDate");

    const todayDescription =
        document.getElementById("todayDescription");

    const todayStatus =
        document.getElementById("todayStatus");

    const totalAttendance =
        document.getElementById("totalAttendance");

    const completeAttendance =
        document.getElementById("completeAttendance");

    const incompleteAttendance =
        document.getElementById("incompleteAttendance");


    let currentPage = 1;

    const limit = 10;

    let allAttendance = [];


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
            window.location.href = "../../auth/login.php";
            return null;
        }

        if (!response.ok || !result.status) {
            throw new Error(result.message || "Gagal mengambil data.");
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


    function escapeHtml(value) {

        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }


    function formatDate(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(
                String(value).substring(0, 10)
                + "T00:00:00"
            );

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


    function formatDay(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(
                String(value).substring(0, 10)
                + "T00:00:00"
            );

        if (isNaN(date.getTime())) {
            return "";
        }

        return date.toLocaleDateString(
            "id-ID",
            {
                weekday: "long"
            }
        );
    }


    function formatTime(value) {

        if (!value) {
            return "-";
        }

        const text =
            String(value);

        if (text.length >= 5) {
            return text.substring(0, 5);
        }

        return text;
    }


    function extractAttendance(result) {

        if (Array.isArray(result.data)) {
            return result.data;
        }

        if (
            result.data &&
            Array.isArray(result.data.data)
        ) {
            return result.data.data;
        }

        if (
            result.data &&
            Array.isArray(result.data.attendance)
        ) {
            return result.data.attendance;
        }

        return [];
    }


    async function getAttendance(page) {

        const url =
            API_BASE +
            "/attendance/list.php" +
            "?page=" +
            encodeURIComponent(page) +
            "&limit=" +
            encodeURIComponent(limit);


        const response =
            await fetch(
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
                "Gagal mengambil data kehadiran."
            );
        }


        return result;
    }


    function findTodayAttendance(data) {

        const today =
            new Date()
                .toISOString()
                .substring(0, 10);


        return data.find(item => {

            const tanggal =
                String(
                    item.tanggal || ""
                ).substring(0, 10);

            return tanggal === today;

        }) || null;
    }


    function renderToday(data) {

        const now =
            new Date();


        todayDate.textContent =
            now.toLocaleDateString(
                "id-ID",
                {
                    weekday: "long",
                    day: "2-digit",
                    month: "long",
                    year: "numeric"
                }
            );


        const attendance =
            findTodayAttendance(data);


        todayStatus.className =
            "today-status";


        if (!attendance) {

            todayStatus.classList.add(
                "pending"
            );

            todayStatus.textContent =
                "Belum Hadir";

            todayDescription.textContent =
                "Belum ada data kehadiran hari ini.";

            return;
        }


        const jamMasuk =
            attendance.jam_kedatangan ||
            attendance.jam_masuk ||
            null;


        const jamPulang =
            attendance.jam_kepulangan ||
            attendance.jam_pulang ||
            null;


        if (jamMasuk && jamPulang) {

            todayStatus.classList.add(
                "complete"
            );

            todayStatus.textContent =
                "✓ Hadir Lengkap";

            todayDescription.textContent =
                "Masuk " +
                formatTime(jamMasuk) +
                " · Pulang " +
                formatTime(jamPulang);

        } else {

            todayStatus.classList.add(
                "present"
            );

            todayStatus.textContent =
                "✓ Sudah Hadir";

            todayDescription.textContent =
                "Check-in " +
                formatTime(jamMasuk) +
                " · Belum check-out";
        }
    }


    function renderSummary(data) {

        let total = data.length;

        let complete = 0;

        let incomplete = 0;


        data.forEach(item => {

            const masuk =
                item.jam_kedatangan ||
                item.jam_masuk;


            const pulang =
                item.jam_kepulangan ||
                item.jam_pulang;


            if (masuk && pulang) {

                complete++;

            } else if (masuk) {

                incomplete++;
            }
        });


        totalAttendance.textContent =
            total;

        completeAttendance.textContent =
            complete;

        incompleteAttendance.textContent =
            incomplete;
    }


    function renderAttendance(item) {

        const tanggal =
            item.tanggal || "-";


        const jamMasuk =
            item.jam_kedatangan ||
            item.jam_masuk ||
            null;


        const jamPulang =
            item.jam_kepulangan ||
            item.jam_pulang ||
            null;


        const complete =
            Boolean(
                jamMasuk &&
                jamPulang
            );


        return `

            <div class="attendance-card">

                <div class="attendance-date">

                    <div class="date-main">
                        ${escapeHtml(
                            formatDate(tanggal)
                        )}
                    </div>

                    <div class="date-day">
                        ${escapeHtml(
                            formatDay(tanggal)
                        )}
                    </div>

                </div>


                <div class="time-item">

                    <div class="time-label">
                        🟢 Jam Kedatangan
                    </div>

                    <div class="time-value ${
                        jamMasuk
                            ? ""
                            : "empty"
                    }">

                        ${
                            jamMasuk
                                ? formatTime(
                                    jamMasuk
                                )
                                : "Belum tercatat"
                        }

                    </div>

                </div>


                <div class="time-item">

                    <div class="time-label">
                        🔵 Jam Kepulangan
                    </div>

                    <div class="time-value ${
                        jamPulang
                            ? ""
                            : "empty"
                    }">

                        ${
                            jamPulang
                                ? formatTime(
                                    jamPulang
                                )
                                : "Belum tercatat"
                        }

                    </div>

                </div>


                <div>

                    <span
                        class="attendance-status ${
                            complete
                                ? "status-complete"
                                : "status-incomplete"
                        }"
                    >

                        ${
                            complete
                                ? "✓ Lengkap"
                                : "⏳ Belum Checkout"
                        }

                    </span>

                </div>

            </div>

        `;
    }


    function renderList(data) {

        if (!data.length) {

            content.innerHTML = `

                <div class="empty-state">

                    <div class="empty-icon">
                        🕐
                    </div>

                    <h3>
                        Belum Ada Riwayat Kehadiran
                    </h3>

                    <p>
                        Riwayat kehadiran kamu
                        akan muncul di sini setelah
                        admin melakukan scan kartu.
                    </p>

                </div>

            `;

            pagination.style.display =
                "none";

            return;
        }


        content.innerHTML = `

            <div class="attendance-list">

                ${data
                    .map(renderAttendance)
                    .join("")}

            </div>

        `;
    }


    function renderPagination(dataWrapper) {

        pagination.innerHTML = "";


        const paginationData =
            dataWrapper?.pagination ||
            dataWrapper?.data?.pagination ||
            null;


        if (!paginationData) {

            pagination.style.display =
                "none";

            return;
        }


        const current =
            Number(
                paginationData.current_page ??
                currentPage
            );


        const totalPages =
            Number(
                paginationData.total_pages ??
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


        const previous =
            document.createElement(
                "button"
            );


        previous.className =
            "page-button";

        previous.textContent =
            "‹";

        previous.disabled =
            current <= 1;


        previous.addEventListener(
            "click",
            () => {

                if (current > 1) {
                    loadAttendance(
                        current - 1
                    );
                }

            }
        );


        pagination.appendChild(
            previous
        );


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
                () => loadAttendance(page)
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
                    loadAttendance(
                        current + 1
                    );
                }

            }
        );


        pagination.appendChild(
            next
        );
    }


    async function loadAttendance(page = 1) {

        currentPage = page;


        content.innerHTML = `

            <div class="loading">

                <div class="spinner"></div>

                Memuat riwayat kehadiran...

            </div>

        `;


        try {

            const result =
                await getAttendance(page);


            if (!result) {
                return;
            }


            const data =
                extractAttendance(result);


            allAttendance =
                data;


            renderToday(data);

            renderSummary(data);

            renderList(data);


            const wrapper =
                result.data || result;


            renderPagination(wrapper);

        } catch (error) {

            console.error(error);


            content.innerHTML = `

                <div class="error-state">

                    ❌ ${escapeHtml(
                        error.message ||
                        "Gagal memuat riwayat kehadiran."
                    )}

                </div>

            `;

        }
    }


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
    loadAttendance(1);

</script>

</body>

</html>