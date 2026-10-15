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

$adminName =$_SESSION["nama"] ?? "Admin";

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Scan Attendance - Pustanka</title>

    <script src="https://unpkg.com/html5-qrcode"></script>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background: #f4f7fb;

            color: #1f2937;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* =========================
           MAIN
        ========================= */

        .main {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            min-height: 100vh;
            padding: 30px;
        }

        .topbar {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 25px;
        }

        .topbar h1 {
            font-size: 28px;
        }

        .admin-info {
            color: #64748b;

            font-size: 14px;
        }

        /* =========================
           SCANNER CARD
        ========================= */

        .scanner-card {
            max-width: 720px;

            margin: 0 auto;

            background: white;

            border-radius: 18px;

            padding: 25px;

            box-shadow:
                0 5px 20px
                rgba(15, 23, 42, 0.07);
        }

        .scanner-header {
            text-align: center;

            margin-bottom: 20px;
        }

        .scanner-header h2 {
            font-size: 22px;

            margin-bottom: 7px;
        }

        .scanner-header p {
            color: #64748b;

            font-size: 14px;
        }

        #reader {
            width: 100%;

            max-width: 500px;

            margin: 0 auto;

            border-radius: 14px;

            overflow: hidden;
        }

        /* =========================
           RESULT
        ========================= */

        .result {
            display: none;

            margin-top: 20px;

            padding: 18px;

            border-radius: 12px;

            text-align: center;
        }

        .result.success {
            display: block;

            background: #dcfce7;

            color: #166534;
        }

        .result.error {
            display: block;

            background: #fee2e2;

            color: #991b1b;
        }

        .result.warning {
            display: block;

            background: #fef3c7;

            color: #92400e;
        }

        .result-title {
            font-size: 18px;

            font-weight: bold;

            margin-bottom: 7px;
        }

        .result-info {
            font-size: 14px;

            line-height: 1.6;
        }

        /* =========================
           MANUAL INPUT
        ========================= */

        .manual {
            margin-top: 25px;

            padding-top: 22px;

            border-top:
                1px solid #e5e7eb;
        }

        .manual-title {
            font-size: 14px;

            font-weight: bold;

            margin-bottom: 10px;
        }

        .manual-form {
            display: flex;

            gap: 10px;
        }

        .manual-input {
            flex: 1;

            padding: 12px 14px;

            border:
                1px solid #d1d5db;

            border-radius: 9px;

            outline: none;

            font-size: 14px;
        }

        .manual-input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.1);
        }

        .btn {
            border: none;

            padding: 12px 18px;

            border-radius: 9px;

            cursor: pointer;

            font-weight: 600;
        }

        .btn-primary {
            background: #2563eb;

            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e2e8f0;

            color: #334155;
        }

        .btn-secondary:hover {
            background: #cbd5e1;
        }

        /* =========================
           BACK
        ========================= */

        .back {
            display: block;

            text-align: center;

            margin-top: 20px;

            color: #2563eb;

            font-size: 14px;

            font-weight: 600;
        }

        .back:hover {
            text-decoration: underline;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 700px) {

            .main {
                padding: 15px;
            }

            .topbar {
                flex-direction: column;

                align-items: flex-start;

                gap: 8px;
            }

            .scanner-card {
                padding: 18px;
            }

            .manual-form {
                flex-direction: column;
            }

        }

    </style>

</head>

<body>

<!-- =========================
     MAIN
========================= -->

<main class="main">

    <div class="topbar">

        <div>

            <h1>
                Scan Attendance
            </h1>

            <div class="admin-info">
                Scan kartu digital anggota
            </div>

        </div>

        <div class="admin-info">

            Admin:
            <strong>
                <?= htmlspecialchars($adminName) ?>
            </strong>

        </div>

    </div>


    <div class="scanner-card">

        <div class="scanner-header">

            <h2>
                Scan Member Card
            </h2>

            <p>
                Arahkan kamera ke barcode
                pada kartu digital anggota.
            </p>

        </div>


        <div id="reader"></div>


        <div
            id="result"
            class="result"
        ></div>


        <div class="manual">

            <div class="manual-title">
                Input manual
            </div>

            <div class="manual-form">

                <input
                    type="text"
                    id="memberCode"
                    class="manual-input"
                    placeholder="Contoh: PSTK-000001"
                    autocomplete="off"
                >

                <button
                    type="button"
                    class="btn btn-primary"
                    onclick="submitManual()"
                >
                    Proses
                </button>

            </div>

        </div>


        <a
            href="index.php"
            class="back"
        >
            ← Kembali
        </a>

    </div>

</main>


<script>

const API_URL =
    "../../../api/attendance/scan.php";

const LOGOUT_URL =
    "../../../api/auth/logout.php";

let scanner = null;

let processing = false;


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
   SHOW RESULT
========================= */

function showResult(
    type,
    title,
    message
) {

    const result =
        document.getElementById(
            "result"
        );

    result.className =
        "result " + type;

    result.innerHTML = `
        <div class="result-title">
            ${escapeHtml(title)}
        </div>

        <div class="result-info">
            ${message}
        </div>
    `;

}


/* =========================
   PROCESS MEMBER CODE
========================= */

async function processMemberCode(
    memberCode
) {

    if (processing) {
        return;
    }

    memberCode =
        String(memberCode || "")
            .trim()
            .toUpperCase();

    if (!memberCode) {

        showResult(
            "error",
            "Member Code Kosong",
            "Member code tidak boleh kosong."
        );

        return;
    }

    processing = true;


    try {

        const formData =
            new FormData();

        formData.append(
            "member_code",
            memberCode
        );


        const response =
            await fetch(
                API_URL,
                {
                    method: "POST",

                    credentials: "include",

                    body: formData
                }
            );


        const result =
            await response.json();


        if (response.status === 401) {

            window.location.href =
                "../../auth/login.php";

            return;
        }


        if (
            response.ok &&
            result.status
        ) {

            const data =
                result.data || {};

            if (
                data.aksi ===
                "check_in"
            ) {

                showResult(
                    "success",
                    "✅ Check-in Berhasil",
                    `
                        <strong>
                            ${escapeHtml(
                                data.nama || "-"
                            )}
                        </strong>
                        <br>
                        Member Code:
                        ${escapeHtml(
                            data.member_code || "-"
                        )}
                        <br>
                        Tanggal:
                        ${escapeHtml(
                            data.tanggal || "-"
                        )}
                        <br>
                        Jam masuk:
                        <strong>
                            ${escapeHtml(
                                data.jam_kedatangan || "-"
                            )}
                        </strong>
                    `
                );

            } else if (
                data.aksi ===
                "check_out"
            ) {

                showResult(
                    "success",
                    "✅ Check-out Berhasil",
                    `
                        <strong>
                            ${escapeHtml(
                                data.nama || "-"
                            )}
                        </strong>
                        <br>
                        Member Code:
                        ${escapeHtml(
                            data.member_code || "-"
                        )}
                        <br>
                        Tanggal:
                        ${escapeHtml(
                            data.tanggal || "-"
                        )}
                        <br>
                        Jam masuk:
                        ${escapeHtml(
                            data.jam_kedatangan || "-"
                        )}
                        <br>
                        Jam pulang:
                        <strong>
                            ${escapeHtml(
                                data.jam_kepulangan || "-"
                            )}
                        </strong>
                    `
                );

            } else {

                showResult(
                    "success",
                    "Berhasil",
                    escapeHtml(
                        result.message ||
                        "Attendance berhasil diproses."
                    )
                );

            }


            document.getElementById(
                "memberCode"
            ).value = "";


        } else {

            let type = "error";

            if (
                response.status === 409
            ) {
                type = "warning";
            }


            const data =
                result.data || {};


            let extra = "";


            if (
                data.nama ||
                data.jam_kedatangan ||
                data.jam_kepulangan
            ) {

                extra = `
                    <br><br>

                    <strong>
                        ${escapeHtml(
                            data.nama || "-"
                        )}
                    </strong>
                    <br>

                    Check-in:
                    ${escapeHtml(
                        data.jam_kedatangan || "-"
                    )}
                    <br>

                    Check-out:
                    ${escapeHtml(
                        data.jam_kepulangan || "-"
                    )}
                `;

            }


            showResult(
                type,
                response.status === 409
                    ? "⚠️ Attendance Sudah Selesai"
                    : "❌ Gagal",
                `
                    ${escapeHtml(
                        result.message ||
                        "Gagal memproses attendance."
                    )}

                    ${extra}
                `
            );

        }

    } catch (error) {

        console.error(error);

        showResult(
            "error",
            "❌ Terjadi Kesalahan",
            "Tidak dapat terhubung ke server."
        );

    } finally {

        /*
         * Beri jeda supaya barcode yang
         * masih berada di depan kamera
         * tidak langsung diproses ulang.
         */

        setTimeout(() => {

            processing = false;

        }, 1500);

    }

}


/* =========================
   MANUAL
========================= */

function submitManual() {

    const input =
        document.getElementById(
            "memberCode"
        );

    processMemberCode(
        input.value
    );

}


/* =========================
   ENTER MANUAL
========================= */

document
    .getElementById("memberCode")
    .addEventListener(
        "keydown",
        function(event) {

            if (
                event.key === "Enter"
            ) {

                submitManual();

            }

        }
    );


/* =========================
   START SCANNER
========================= */

function startScanner() {

    scanner =
        new Html5Qrcode(
            "reader"
        );


    const config = {
        fps: 10,

        qrbox: {
            width: 250,
            height: 150
        }
    };


    scanner
        .start(
            {
                facingMode: "environment"
            },

            config,

            function(decodedText) {

                processMemberCode(
                    decodedText
                );

            },

            function(errorMessage) {

                /*
                 * Error scan biasa tidak perlu
                 * ditampilkan karena scanner
                 * terus mencari barcode.
                 */

            }
        )
        .catch(function(error) {

            console.error(
                "Scanner error:",
                error
            );

            showResult(
                "error",
                "❌ Kamera Tidak Dapat Dibuka",
                `
                    Pastikan browser memiliki
                    izin menggunakan kamera.
                    <br><br>
                    Jika menggunakan HP,
                    gunakan HTTPS atau localhost.
                `
            );

        });

}


/* =========================
   START
========================= */

startScanner();

</script>

</body>
</html>