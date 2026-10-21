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

$adminName = $_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Admin - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
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
           MAIN CONTENT
        ========================================== */

        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
            padding: 30px;
            background: #f4f7fb;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 28px;
            color: #0f172a;
        }

        .back-btn {
            text-decoration: none;
            background: #0f172a;
            color: white;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 14px;
        }

        .profile-card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        .profile-header {
            display: flex;
            align-items: center;
            gap: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid #e5e7eb;
        }

        .profile-photo {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            background: #e2e8f0;
            border: 4px solid #dbeafe;
        }

        .profile-name {
            font-size: 25px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        .profile-role {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 28px;
        }

        .info-box {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px;
        }

        .info-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 7px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #0f172a;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
            flex-wrap: wrap;
        }

        .btn {
            border: none;
            padding: 12px 18px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-dark {
            background: #0f172a;
            color: white;
        }

        .loading {
            text-align: center;
            padding: 50px;
            color: #64748b;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
        }

        /* ==========================================
           MOBILE TOPBAR & RESPONSIVE
        ========================================== */

        .mobile-topbar {
            display: none;
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
        }

        @media (max-width: 700px) {
            .info-grid {
                grid-template-columns: 1fr;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .back-btn {
                text-align: center;
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
            <p>Sistem Informasi Perpustakaan</p>
        </div>

        <nav class="menu">
            <a href="../dashboard/">
                <span>Dashboard</span>
            </a>

            <a href="../profile/" class="active">
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

            <a href="../returns/">
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
        </nav>

        <!-- SIDEBAR BOTTOM -->
        <div class="sidebar-bottom">
            <div class="admin-info">
                <small>Login sebagai</small>
                <strong><?= htmlspecialchars($adminName) ?></strong>
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

    <!-- ==========================================
         MAIN CONTENT
    ========================================== -->

    <main class="main">

        <!-- MOBILE TOPBAR -->
        <div class="mobile-topbar">
            <strong>Pustanka</strong>
            <button class="mobile-menu-btn" onclick="toggleMobileMenu()">
                ☰ Menu
            </button>
        </div>

        <div class="topbar">
            <h1>Profil Admin</h1>
        </div>

        <div id="errorMessage" class="error"></div>

        <div class="profile-card">

            <div id="loading" class="loading">
                Memuat data profil...
            </div>

            <div id="profileContent" style="display:none;">

                <div class="profile-header">

                    <img
                        id="profilePhoto"
                        class="profile-photo"
                        src=""
                        alt="Foto Admin"
                    >

                    <div>
                        <div id="profileName" class="profile-name">
                            -
                        </div>

                        <span class="profile-role">
                            ADMIN
                        </span>
                    </div>

                </div>

                <div class="info-grid">

                    <div class="info-box">
                        <div class="info-label">
                            ID Admin
                        </div>

                        <div id="idAdmin" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            Nama
                        </div>

                        <div id="nama" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            Username
                        </div>

                        <div id="username" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            Email
                        </div>

                        <div id="email" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            No. Telepon
                        </div>

                        <div id="noTelp" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            Alamat
                        </div>

                        <div id="alamat" class="info-value">
                            -
                        </div>
                    </div>

                    <div class="info-box">
                        <div class="info-label">
                            Kode Kartu Admin
                        </div>

                        <div id="cardCode" class="info-value">
                            -
                        </div>
                    </div>

                </div>

                <div class="actions">

                    <a
                        href="./edit.php"
                        class="btn btn-primary"
                    >
                        Edit Profil
                    </a>

                    <a
                        href="../password/index.php"
                        class="btn btn-dark"
                    >
                        Ganti Password
                    </a>

                </div>

            </div>

        </div>

    </main>

</div>

<script>

const API_URL = "../../../api/admins/detail.php";

async function loadProfile() {

    const loading = document.getElementById("loading");
    const content = document.getElementById("profileContent");
    const errorMessage = document.getElementById("errorMessage");

    try {

        const response = await fetch(API_URL, {
            method: "GET",
            credentials: "include"
        });

        const result = await response.json();

        if (!response.ok || !result.status) {
            throw new Error(
                result.message || "Gagal mengambil data profil."
            );
        }

        const data = result.data;

        document.getElementById("idAdmin").textContent =
            data.id_admin ?? "-";

        document.getElementById("profileName").textContent =
            data.nama ?? "-";

        document.getElementById("nama").textContent =
            data.nama ?? "-";

        document.getElementById("username").textContent =
            data.username ?? "-";

        document.getElementById("email").textContent =
            data.email ?? "-";

        document.getElementById("noTelp").textContent =
            data.no_telp ?? "-";

        document.getElementById("alamat").textContent =
            data.alamat ?? "-";

        document.getElementById("cardCode").textContent =
            data.card_code ?? "-";


        // Foto admin
        if (data.foto) {

    const fotoUrl =
        "../../../" + data.foto;

    document.getElementById("profilePhoto").src = fotoUrl;

} else {

    document.getElementById("profilePhoto").src =
        "https://ui-avatars.com/api/?name=" +
        encodeURIComponent(data.nama || "Admin") +
        "&background=2563eb&color=fff";
}


        loading.style.display = "none";
        content.style.display = "block";

    } catch (error) {

        loading.style.display = "none";

        errorMessage.textContent =
            error.message || "Terjadi kesalahan.";

        errorMessage.style.display = "block";
    }
}

loadProfile();


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

            "../../../api/auth/logout.php",

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

</script>

</body>
</html>