<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard User - Pustanka</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f4f7fc;
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

        .sidebar-header img {
            width: 38px;
            height: 38px;
            object-fit: contain;
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
            z-index: 10;
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

        /* =========================
           CONTAINER & WELCOME BANNER
        ========================== */
        .container {
            padding: 30px;
        }

        .welcome-banner {
            background: linear-gradient(135deg, #c3d9eb 0%, #b0cee6 100%);
            border-radius: 16px;
            padding: 28px 32px;
            margin-bottom: 25px;
            position: relative;
            box-shadow: 0 4px 12px rgba(13, 59, 102, 0.05);
        }

        .welcome-banner h1 {
            font-size: 1.6rem;
            color: #0d3b66;
            margin-bottom: 8px;
            font-weight: 700;
        }

        .welcome-banner p {
            color: #2c5282;
            font-size: 0.92rem;
            line-height: 1.5;
        }

        /* =========================
           STATISTICS CARDS GRID
        ========================== */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card-stat {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .card-icon {
            width: 46px;
            height: 46px;
            border-radius: 10px;
            background-color: #ebf8ff;
            color: #3182ce;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .card-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .card-title {
            font-size: 0.82rem;
            color: #718096;
            font-weight: 600;
        }

        .card-value {
            font-size: 1.35rem;
            font-weight: 700;
            color: #0d3b66;
        }

        /* =========================
           CONTENT SECTION (PEMINJAMAN TERBARU)
        ========================== */
        .section-box {
            background-color: #ffffff;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        .section-box h2 {
            font-size: 1.15rem;
            color: #0d3b66;
            margin-bottom: 20px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .loan-item {
            padding: 14px 0;
            border-bottom: 1px dashed #e2e8f0;
        }

        .loan-item:last-child {
            border-bottom: none;
        }

        .loan-title {
            font-weight: 600;
            color: #2d3748;
            font-size: 0.95rem;
            margin-bottom: 4px;
        }

        .loan-info {
            font-size: 0.82rem;
            color: #718096;
        }

        .empty {
            text-align: center;
            color: #a0aec0;
            padding: 30px;
            font-size: 0.9rem;
        }

        .loading {
            color: #718096;
            font-size: 0.9rem;
        }

        .error {
            color: #e53e3e;
            font-size: 0.9rem;
        }

        /* =========================
           RESPONSIVE DESIGN
        ========================== */
        @media (max-width: 992px) {
            .cards-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 600px) {
            body {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                height: auto;
            }
            .cards-grid {
                grid-template-columns: 1fr;
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
                <li class="active"><a href="index.php"> Dashboard</a></li>
                <li><a href="../books/"> Katalog Buku</a></li>
                <li><a href="../loans/"> Peminjaman Buku</a></li>
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

        <!-- CONTAINER -->
        <div class="container">

            <!-- WELCOME BANNER -->
            <div class="welcome-banner">
                <h1 id="welcomeText">Halo 👋</h1>
                <p>
                    Selamat datang kembali di PUSTANKA.<br>
                    Semoga hari Anda penuh dengan pengetahuan!
                </p>
            </div>

            <!-- CARDS GRID -->
            <div class="cards-grid">
                <div class="card-stat">
                    <!-- <div class="card-icon">📚</div> -->
                    <div class="card-info">
                        <div class="card-title">Buku Dipinjam</div>
                        <div class="card-value" id="totalBooks">-</div>
                    </div>
                </div>

                <div class="card-stat">
                    <!-- <div class="card-icon">📖</div> -->
                    <div class="card-info">
                        <div class="card-title">Peminjaman Aktif</div>
                        <div class="card-value" id="activeLoans">-</div>
                    </div>
                </div>

                <div class="card-stat">
                    <!-- <div class="card-icon">💰</div> -->
                    <div class="card-info">
                        <div class="card-title">Denda Belum Dibayar</div>
                        <div class="card-value" id="unpaidFines">-</div>
                    </div>
                </div>

                <div class="card-stat">
                    <!-- <div class="card-icon">🕐</div> -->
                    <div class="card-info">
                        <div class="card-title">Kehadiran Hari Ini</div>
                        <div class="card-value" id="attendanceToday">-</div>
                    </div>
                </div>
            </div>

            <!-- PEMINJAMAN TERBARU -->
            <div class="section-box">
                <h2>Peminjaman Terbaru</h2>
                <div id="loanList">
                    <div class="loading">
                        Memuat data peminjaman...
                    </div>
                </div>
            </div>

        </div>
    </div>

<script>

const API_BASE = "../../../api";

/*
|--------------------------------------------------------------------------
| LOAD DASHBOARD
|--------------------------------------------------------------------------
*/

async function loadDashboard() {

    try {

        const response = await fetch(
            API_BASE + "/dashboard/user.php",
            {
                method: "GET",
                credentials: "include"
            }
        );

        const result = await response.json();

        console.log("Dashboard:", result);

        if (!response.ok || !result.status) {

            if (response.status === 401) {
                window.location.href = "../../auth/login.php";
                return;
            }

            throw new Error(
                result.message || "Gagal mengambil dashboard"
            );
        }

        const data = result.data;

        /*
        |--------------------------------------------------------------------------
        | DATA USER
        |--------------------------------------------------------------------------
        */

        const user = data.user;

        if (user) {

            document.getElementById("navbarUserName")
                .textContent = user.nama;

            document.getElementById("welcomeText")
                .textContent = "Halo, " + user.nama + " 👋";
        }


        /*
        |--------------------------------------------------------------------------
        | DATA PEMINJAMAN
        |--------------------------------------------------------------------------
        */

        const loans = data.loans;

        document.getElementById("totalBooks")
            .textContent = loans.jumlah_dipinjam;

        document.getElementById("activeLoans")
            .textContent =
                loans.jumlah_dipinjam;


        /*
        |--------------------------------------------------------------------------
        | DATA DENDA
        |--------------------------------------------------------------------------
        */

        const fines = data.fines;

        document.getElementById("unpaidFines")
            .textContent =
                "Rp " +
                Number(fines.total_belum_bayar)
                    .toLocaleString("id-ID");


        /*
        |--------------------------------------------------------------------------
        | DATA KEHADIRAN
        |--------------------------------------------------------------------------
        */

        const attendance = data.attendance;

        if (attendance.hari_ini) {

            document.getElementById("attendanceToday")
                .textContent = "Hadir";

        } else {

            document.getElementById("attendanceToday")
                .textContent = "Belum";
        }


        /*
        |--------------------------------------------------------------------------
        | PEMINJAMAN TERBARU
        |--------------------------------------------------------------------------
        */

        const loanList =
            document.getElementById("loanList");

        const loanData = loans.data;

        if (
            Array.isArray(loanData) &&
            loanData.length > 0
        ) {

            loanList.innerHTML =
                loanData.map(loan => `

                    <div class="loan-item">

                        <div class="loan-title">
                            ${escapeHtml(
                                loan.judul || "Judul buku"
                            )}
                        </div>

                        <div class="loan-info">
                            Tanggal pinjam:
                            ${escapeHtml(
                                loan.tanggal_pinjam || "-"
                            )}
                        </div>

                        <div class="loan-info">
                            Batas kembali:
                            ${escapeHtml(
                                loan.batas_kembali || "-"
                            )}
                        </div>

                    </div>

                `).join("");

        } else {

            loanList.innerHTML = `
                <div class="empty">
                    Belum ada peminjaman.
                </div>
            `;
        }

    } catch (error) {

        console.error("Dashboard error:", error);

        document.getElementById("loanList").innerHTML = `
            <div class="error">
                Gagal mengambil data dashboard.
            </div>
        `;
    }
}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

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


/*
|--------------------------------------------------------------------------
| SECURITY
|--------------------------------------------------------------------------
*/

function escapeHtml(value) {

    const div = document.createElement("div");

    div.textContent = value ?? "";

    return div.innerHTML;
}


/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

loadDashboard();

</script>

</body>
</html>