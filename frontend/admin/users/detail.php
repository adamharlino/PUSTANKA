<?php
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

$adminName =$_SESSION["nama"] ?? "Admin";
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail User - PUSTANKA</title>

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

        /* MAIN */

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
            gap: 20px;
            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 28px;
            margin-bottom: 6px;
        }

        .admin-info {
            font-size: 13px;
            color: #64748b;
        }

        /* BUTTON */

        .btn {
            border: none;
            padding: 10px 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        /* PROFILE CARD */

        .profile-card {
            background: white;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
            overflow: hidden;
        }

        .profile-header {
            background: linear-gradient(
                135deg,
                #1d4ed8,
                #2563eb
            );
            padding: 30px;
            color: white;
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .profile-photo {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid rgba(255,255,255,.8);
            background: #e2e8f0;
        }

        .profile-placeholder {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            border: 4px solid rgba(255,255,255,.8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            font-weight: 700;
        }

        .profile-header h2 {
            font-size: 25px;
            margin-bottom: 8px;
        }

        .member-code {
            display: inline-block;
            background: rgba(255,255,255,.16);
            border: 1px solid rgba(255,255,255,.25);
            padding: 7px 11px;
            border-radius: 7px;
            font-family: monospace;
            font-size: 13px;
        }

        .profile-body {
            padding: 28px;
        }

        .section-title {
            font-size: 17px;
            margin-bottom: 18px;
            color: #1e293b;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .info-item {
            padding: 16px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fafafa;
        }

        .info-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 7px;
        }

        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #1e293b;
            word-break: break-word;
        }

        .status {
            display: inline-flex;
            padding: 6px 11px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .status-aktif {
            background: #dcfce7;
            color: #166534;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        /* LOADING */

        .loading,
        .error {
            background: white;
            border-radius: 14px;
            padding: 50px 20px;
            text-align: center;
        }

        .error {
            color: #b91c1c;
        }

        /* RESPONSIVE */

        @media (max-width: 700px) {

            .main {
                padding: 16px;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .info-grid {
                grid-template-columns: 1fr;
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

                <h1>Detail User</h1>

                <div class="admin-info">
                    Informasi lengkap anggota perpustakaan
                </div>

            </div>

            <div class="admin-info">
                Admin:
                <strong>
                    <?= htmlspecialchars($adminName) ?>
                </strong>
            </div>

        </div>


        <div id="content">

            <div class="loading">
                Memuat detail user...
            </div>

        </div>

    </main>

</div>


<script>

const API_BASE = "../../../api";
const DETAIL_API = API_BASE + "/users/detail.php";
const LOGOUT_URL = API_BASE + "/auth/logout.php";


/* =========================
   ESCAPE HTML
========================= */

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


/* =========================
   GET USER ID
========================= */

const params =
    new URLSearchParams(window.location.search);

const idUser =
    params.get("id");


/* =========================
   LOAD DETAIL
========================= */

async function loadDetail() {

    const content =
        document.getElementById("content");


    if (
        !idUser ||
        !/^\d+$/.test(idUser) ||
        Number(idUser) <= 0
    ) {

        content.innerHTML = `
            <div class="error">
                ❌ ID user tidak valid.
                <br><br>
                <a
                    href="./index.php"
                    class="btn btn-secondary"
                >
                    ← Kembali ke Users
                </a>
            </div>
        `;

        return;
    }


    try {

        const response = await fetch(
            DETAIL_API +
            "?id=" +
            encodeURIComponent(idUser),
            {
                method: "GET",
                credentials: "include",
                headers: {
                    "Accept": "application/json"
                }
            }
        );


        let result;

        try {
            result = await response.json();
        } catch (error) {
            throw new Error(
                "Response API tidak valid."
            );
        }


        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        if (!response.ok || !result.status) {

            throw new Error(
                result.message ||
                "Gagal mengambil detail user."
            );
        }


        renderDetail(result.data);

    } catch (error) {

        console.error(error);

        content.innerHTML = `
            <div class="error">

                ❌ ${escapeHtml(error.message)}

                <br><br>

                <a
                    href="./index.php"
                    class="btn btn-secondary"
                >
                    ← Kembali ke Users
                </a>

            </div>
        `;
    }
}


/* =========================
   RENDER DETAIL
========================= */

function renderDetail(data) {

    const content =
        document.getElementById("content");


    /*
     * API bisa mengembalikan object
     * langsung atau nested data.
     */

    const user =
        data.user ||
        data;


    const id =
        user.id_user || "-";

    const nama =
        user.nama || "-";

    const memberCode =
        user.member_code || "-";

    const nik =
        user.nik || "-";

    const alamat =
        user.alamat || "-";

    const email =
        user.email || "-";

    const noTelp =
        user.no_telp || "-";

    const status =
        user.status || "-";

    const foto =
        user.foto || "";

    const createdAt =
        user.created_at || "-";


    const initial =
        nama !== "-"
            ? nama.charAt(0).toUpperCase()
            : "U";


    let photoHtml;


    if (foto) {

        photoHtml = `
            <img
                src="../../../uploads/users/${escapeHtml(foto)}"
                class="profile-photo"
                alt="Foto ${escapeHtml(nama)}"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
            >

            <div
                class="profile-placeholder"
                style="display:none;"
            >
                ${escapeHtml(initial)}
            </div>
        `;

    } else {

        photoHtml = `
            <div class="profile-placeholder">
                ${escapeHtml(initial)}
            </div>
        `;
    }


    const statusClass =
        String(status).toLowerCase() === "aktif"
            ? "status-aktif"
            : "status-nonaktif";


    content.innerHTML = `

        <div class="profile-card">

            <div class="profile-header">

                ${photoHtml}

                <div>

                    <h2>
                        ${escapeHtml(nama)}
                    </h2>

                    <span class="member-code">
                        ${escapeHtml(memberCode)}
                    </span>

                </div>

            </div>


            <div class="profile-body">

                <h3 class="section-title">
                    Informasi User
                </h3>


                <div class="info-grid">

                    <div class="info-item">

                        <span class="info-label">
                            ID User
                        </span>

                        <div class="info-value">
                            ${escapeHtml(id)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Member Code
                        </span>

                        <div class="info-value">
                            ${escapeHtml(memberCode)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Nama Lengkap
                        </span>

                        <div class="info-value">
                            ${escapeHtml(nama)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            NIK
                        </span>

                        <div class="info-value">
                            ${escapeHtml(nik)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Email
                        </span>

                        <div class="info-value">
                            ${escapeHtml(email)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            No. Telepon
                        </span>

                        <div class="info-value">
                            ${escapeHtml(noTelp)}
                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Status
                        </span>

                        <div class="info-value">

                            <span
                                class="status ${statusClass}"
                            >
                                ${escapeHtml(status)}
                            </span>

                        </div>

                    </div>


                    <div class="info-item">

                        <span class="info-label">
                            Terdaftar
                        </span>

                        <div class="info-value">
                            ${escapeHtml(createdAt)}
                        </div>

                    </div>


                    <div
                        class="info-item"
                        style="grid-column: 1 / -1;"
                    >

                        <span class="info-label">
                            Alamat
                        </span>

                        <div class="info-value">
                            ${escapeHtml(alamat)}
                        </div>

                    </div>

                </div>


                <div class="actions">

                    <a
                        href="./index.php"
                        class="btn btn-secondary"
                    >
                        ← Kembali
                    </a>

                    <a
                        href="./edit.php?id=${encodeURIComponent(id)}"
                        class="btn btn-primary"
                    >
                        Edit User
                    </a>

                </div>

            </div>

        </div>
    `;
}


/* =========================
   LOGOUT
========================= */

async function logout() {

    try {

        await fetch(LOGOUT_URL, {
            method: "POST",
            credentials: "include"
        });

    } catch (error) {

        console.error(error);

    } finally {

        window.location.href =
            "../../auth/login.php";
    }
}


/* =========================
   INIT
========================= */

loadDetail();

</script>

</body>
</html>