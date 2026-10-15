<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Profil Saya - Pustanka</title>

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
            z-index: 100;
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

        .back-btn {
            display: none; /* Disembunyikan karena sudah ada di sidebar */
        }

        /* =========================
           CONTAINER
        ========================= */

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 30px;
            width: 100%;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 26px;
            margin-bottom: 6px;
            color: #0d3b66;
            font-weight: 700;
        }

        .page-title p {
            color: #718096;
            font-size: 14px;
        }

        /* =========================
           GRID
        ========================= */

        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 25px;
            align-items: start;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
        }

        /* =========================
           PROFILE
        ========================= */

        .profile-card {
            text-align: center;
        }

        .profile-photo {
            width: 130px;
            height: 130px;

            border-radius: 50%;

            object-fit: cover;

            border: 5px solid #ebf8ff;

            margin-bottom: 15px;

            background: #e2e8f0;
        }

        .profile-name {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #0d3b66;
        }

        .profile-email {
            color: #718096;
            margin-bottom: 15px;
            font-size: 14px;
        }

        .status {
            display: inline-block;

            padding: 6px 12px;

            border-radius: 20px;

            font-size: 12px;
            font-weight: bold;

            background: #e9f8ef;
            color: #16794c;
        }

        /* =========================
           DIGITAL MEMBER CARD
        ========================= */

        .member-card {
            margin-top: 25px;

            background: linear-gradient(
                135deg,
                #0d3b66,
                #3182ce
            );

            color: white;

            border-radius: 12px;

            padding: 25px;

            min-height: 260px;

            position: relative;

            overflow: hidden;
        }

        .member-card::before {
            content: "";

            position: absolute;

            width: 220px;
            height: 220px;

            border-radius: 50%;

            background: rgba(255,255,255,0.06);

            right: -80px;
            top: -80px;
        }

        .member-card::after {
            content: "";

            position: absolute;

            width: 160px;
            height: 160px;

            border-radius: 50%;

            background: rgba(255,255,255,0.05);

            left: -70px;
            bottom: -80px;
        }

        .member-header {
            display: flex;
            justify-content: space-between;
            align-items: center;

            position: relative;
            z-index: 2;
        }

        .member-title {
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 1px;
        }

        .member-label {
            font-size: 11px;
            opacity: 0.7;
            margin-top: 3px;
        }

        .member-icon {
            font-size: 25px;
        }

        .member-content {
            display: flex;
            justify-content: space-between;
            align-items: center;

            margin-top: 30px;

            position: relative;
            z-index: 2;
        }

        .member-info h2 {
            font-size: 18px;
            margin-bottom: 8px;
        }

        .member-code {
            font-size: 14px;
            letter-spacing: 2px;
            opacity: 0.9;
        }

        .barcode-container {
            background: white;
            padding: 10px;
            border-radius: 8px;

            width: 170px;

            display: flex;
            justify-content: center;
        }

        #barcode {
            max-width: 150px;
            height: 70px;
        }

        .member-footer {
            position: absolute;

            bottom: 18px;
            left: 25px;
            right: 25px;

            display: flex;
            justify-content: space-between;

            font-size: 11px;

            opacity: 0.75;

            z-index: 2;
        }

        .member-card-actions {
            margin-top: 14px;
            display: flex;
            justify-content: center;
        }

        .download-card-btn {
            border: none;
            background: #0d3b66;
            color: #ffffff;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .download-card-btn:hover {
            background: #092c4e;
            transform: translateY(-1px);
        }

        .download-card-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        /* =========================
           PROFILE INFORMATION
        ========================= */

        .section-title {
            font-size: 18px;
            margin-bottom: 20px;
            color: #0d3b66;
            font-weight: 700;
        }

        .info-list {
            display: grid;
            gap: 18px;
        }

        .info-item {
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 14px;
        }

        .info-item:last-child {
            border-bottom: none;
        }

        .info-label {
            font-size: 12px;
            color: #718096;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .info-value {
            font-size: 15px;
            font-weight: 600;
            color: #25364d;
            word-break: break-word;
        }

        /* =========================
           BUTTONS
        ========================= */

        .actions {
            display: flex;
            gap: 12px;

            margin-top: 25px;
        }

        .btn {
            flex: 1;

            border: none;

            padding: 12px;

            border-radius: 8px;

            cursor: pointer;

            font-weight: bold;

            text-decoration: none;

            text-align: center;
            font-size: 14px;
        }

        .btn-primary {
            background: #0d3b66;
            color: white;
        }

        .btn-primary:hover {
            background: #092c4e;
        }

        .btn-secondary {
            background: #edf3f9;
            color: #36506f;
        }

        .btn-secondary:hover {
            background: #dfeaf5;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            text-align: center;
            padding: 50px;
            color: #718096;
        }

        .error {
            text-align: center;
            padding: 50px;
            color: #e53e3e;
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

            .profile-grid {
                grid-template-columns: 1fr;
            }

            .member-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .barcode-container {
                align-self: flex-end;
            }
        }

        @media (max-width: 500px) {
            .container {
                padding: 25px 15px;
            }

            .page-title h1 {
                font-size: 22px;
            }

            .member-card {
                padding: 20px;
            }

            .member-footer {
                left: 20px;
                right: 20px;
            }

            .actions {
                flex-direction: column;
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
                <li><a href="../attendance/"> Kehadiran</a></li>
                <li class="active"><a href="index.php"> Profil</a></li>
            </ul>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">
        <!-- NAVBAR -->
        <nav class="navbar">
            <div class="nav-right">
                <div class="user-name" id="navbarUserName">
                    Memuat...
                </div>
                <button class="logout-btn" onclick="logout()">
                    Logout
                </button>
            </div>
        </nav>

        <div class="container">

            <div class="page-title">

                <h1>Profil Saya</h1>

                <p>
                    Kelola informasi akun dan lihat kartu anggota digital kamu.
                </p>

            </div>


            <!-- LOADING -->

            <div id="loading" class="loading">
                Memuat profil...
            </div>


            <!-- CONTENT -->

            <div
                id="profileContent"
                class="profile-grid"
                style="display:none;"
            >

                <!-- =========================
                     LEFT
                ========================= -->

                <div>

                    <div class="card profile-card">

                        <img
                            id="profilePhoto"
                            class="profile-photo"
                            src=""
                            alt="Foto Profil"
                        >

                        <div
                            id="profileName"
                            class="profile-name"
                        >
                            -
                        </div>

                        <div
                            id="profileEmail"
                            class="profile-email"
                        >
                            -
                        </div>

                        <span
                            id="profileStatus"
                            class="status"
                        >
                            -
                        </span>

                    </div>


                    <!-- DIGITAL MEMBER CARD -->

                    <div class="member-card">

                        <div class="member-header">

                            <div>

                                <div class="member-title">
                                    PUSTANKA
                                </div>

                                <div class="member-label">
                                    DIGITAL MEMBER CARD
                                </div>

                            </div>

                            <div class="member-icon">
                                🪪
                            </div>

                        </div>


                        <div class="member-content">

                            <div class="member-info">

                                <h2 id="cardName">
                                    -
                                </h2>

                                <div
                                    id="cardMemberCode"
                                    class="member-code"
                                >
                                    -
                                </div>

                            </div>


                            <div class="barcode-container">

                                <svg
                                    id="barcode"
                                ></svg>

                            </div>

                        </div>


                        <div class="member-footer">

                            <span>
                                MEMBER
                            </span>

                            <span id="cardId">
                                ID: -
                            </span>

                        </div>

                    </div>

                    <div class="member-card-actions">
                    <button
                        type="button"
                        class="download-card-btn"
                        id="downloadCardButton"
                    >
                        Download Kartu Anggota
                    </button>
                    </div>

                </div>


                <!-- =========================
                     RIGHT
                ========================= -->

                <div class="card">

                    <h2 class="section-title">
                        Informasi Akun
                    </h2>


                    <div class="info-list">

                        <div class="info-item">

                            <div class="info-label">
                                Nama Lengkap
                            </div>

                            <div
                                id="infoNama"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                NIK
                            </div>

                            <div
                                id="infoNik"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                Email
                            </div>

                            <div
                                id="infoEmail"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                Nomor Telepon
                            </div>

                            <div
                                id="infoTelepon"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                Alamat
                            </div>

                            <div
                                id="infoAlamat"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                Member Code
                            </div>

                            <div
                                id="infoMemberCode"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>


                        <div class="info-item">

                            <div class="info-label">
                                Status Akun
                            </div>

                            <div
                                id="infoStatus"
                                class="info-value"
                            >
                                -
                            </div>

                        </div>

                    </div>


                    <!-- BUTTON -->

                    <div class="actions">

                        <a
                            href="edit.php"
                            class="btn btn-primary"
                        >
                            Edit Profil
                        </a>

                        <a
                            href="../password/"
                            class="btn btn-secondary"
                        >
                            Ganti Password
                        </a>

                    </div>

                </div>

            </div>


            <!-- ERROR -->

            <div
                id="error"
                class="error"
                style="display:none;"
            >
                Gagal mengambil data profil.
            </div>

        </div>
    </div>


<!--
====================================================
 JsBarcode
====================================================
-->

<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>

const API_BASE = "../../../api";
const LOGOUT_URL = API_BASE + "/auth/logout.php";


/*
|--------------------------------------------------------------------------
| LOAD PROFILE
|--------------------------------------------------------------------------
*/

async function loadProfile() {

    try {

        const response = await fetch(
            API_BASE + "/users/profile.php",
            {
                method: "GET",
                credentials: "include"
            }
        );


        const result = await response.json();

        console.log("Profile:", result);


        /*
        |--------------------------------------------------------------------------
        | CHECK LOGIN
        |--------------------------------------------------------------------------
        */

        if (
            response.status === 401
        ) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK API
        |--------------------------------------------------------------------------
        */

        if (
            !response.ok ||
            !result.status
        ) {

            throw new Error(
                result.message ||
                "Gagal mengambil profil"
            );
        }


        const user = result.data;


        /*
        |--------------------------------------------------------------------------
        | NAVBAR USER NAME
        |--------------------------------------------------------------------------
        */
        if (user && user.nama) {
            document.getElementById("navbarUserName").textContent = user.nama;
        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE PHOTO
        |--------------------------------------------------------------------------
        */

        const photoElement =
            document.getElementById(
                "profilePhoto"
            );


        if (user.foto) {

            photoElement.src =
                "../../../uploads/users/" +
                user.foto;

        } else {

            photoElement.src =
                "https://ui-avatars.com/api/?name=" +
                encodeURIComponent(user.nama) +
                "&background=0d3b66&color=fff&size=256";

        }


        /*
        |--------------------------------------------------------------------------
        | PROFILE HEADER
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "profileName"
        ).textContent =
            user.nama || "-";


        document.getElementById(
            "profileEmail"
        ).textContent =
            user.email || "-";


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        const status =
            document.getElementById(
                "profileStatus"
            );


        status.textContent =
            user.status || "-";


        /*
        |--------------------------------------------------------------------------
        | DIGITAL MEMBER CARD
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "cardName"
        ).textContent =
            user.nama || "-";


        document.getElementById(
            "cardMemberCode"
        ).textContent =
            user.member_code || "-";


        document.getElementById(
            "cardId"
        ).textContent =
            "ID: " +
            (user.id_user || "-");

        const downloadCardButton = document.getElementById(
            "downloadCardButton"
        );

        downloadCardButton.addEventListener(
            "click",
            async function () {

                const card = document.querySelector(
                    ".member-card"
                );

                if (!card) {
                    alert("Kartu anggota tidak ditemukan.");
                    return;
                }

                const memberCode =
                    document.getElementById(
                        "cardMemberCode"
                    ).textContent.trim();

                if (
                    !memberCode ||
                    memberCode === "-"
                ) {
                    alert(
                        "Data kartu anggota belum selesai dimuat."
                    );
                    return;
                }

                downloadCardButton.disabled = true;
                downloadCardButton.textContent =
                    "⏳ Menyiapkan kartu...";

                try {

                    const canvas = await html2canvas(
                        card,
                        {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: null
                        }
                    );

                    const image = canvas.toDataURL(
                        "image/png"
                    );

                    const link =
                        document.createElement("a");

                    link.href = image;

                    link.download =
                        `kartu-anggota-${memberCode}.png`;

                    document.body.appendChild(link);

                    link.click();

                    document.body.removeChild(link);

                } catch (error) {

                    console.error(
                        "Gagal download kartu:",
                        error
                    );

                    alert(
                        "Kartu anggota gagal didownload."
                    );

                } finally {

                    downloadCardButton.disabled = false;

                    downloadCardButton.textContent =
                        "Download Kartu Anggota";
                }
            }
        );


        /*
        |--------------------------------------------------------------------------
        | GENERATE BARCODE
        |--------------------------------------------------------------------------
        */

        if (user.member_code) {

            JsBarcode(
                "#barcode",
                user.member_code,
                {
                    format: "CODE128",
                    width: 2,
                    height: 65,
                    displayValue: true,
                    fontSize: 12,
                    margin: 5
                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | INFORMATION
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "infoNama"
        ).textContent =
            user.nama || "-";


        document.getElementById(
            "infoNik"
        ).textContent =
            user.nik || "-";


        document.getElementById(
            "infoEmail"
        ).textContent =
            user.email || "-";


        document.getElementById(
            "infoTelepon"
        ).textContent =
            user.no_telp || "-";


        document.getElementById(
            "infoAlamat"
        ).textContent =
            user.alamat || "-";


        document.getElementById(
            "infoMemberCode"
        ).textContent =
            user.member_code || "-";


        document.getElementById(
            "infoStatus"
        ).textContent =
            user.status || "-";


        /*
        |--------------------------------------------------------------------------
        | SHOW CONTENT
        |--------------------------------------------------------------------------
        */

        document.getElementById(
            "loading"
        ).style.display = "none";


        document.getElementById(
            "profileContent"
        ).style.display = "grid";


    } catch (error) {

        console.error(
            "Profile error:",
            error
        );


        document.getElementById(
            "loading"
        ).style.display = "none";


        document.getElementById(
            "error"
        ).style.display = "block";

    }

}


/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/

async function logout() {
    const confirmed = confirm("Yakin ingin logout?");
    if (!confirmed) return;

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

    }


    window.location.href =
        "../../auth/login.php";

}


/*
|--------------------------------------------------------------------------
| START
|--------------------------------------------------------------------------
*/

loadProfile();

</script>

</body>
</html>