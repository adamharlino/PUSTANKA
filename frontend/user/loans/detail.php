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

$id_loan = isset($_GET["id_loan"]) ? (int) $_GET["id_loan"] : 0;

if ($id_loan <= 0) {
    header("Location: ./");
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

    <title>Detail Peminjaman - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family:
                Arial,
                Helvetica,
                sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 25px 20px 50px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 7px;

            margin-bottom: 20px;

            padding: 10px 15px;

            background: #ffffff;
            color: #374151;

            border: 1px solid #e5e7eb;
            border-radius: 9px;

            text-decoration: none;
            font-weight: 600;
        }

        .back-button:hover {
            background: #f9fafb;
        }

        .page-title {
            margin-bottom: 20px;
        }

        .page-title h1 {
            margin: 0 0 7px;
            font-size: 28px;
        }

        .page-title p {
            margin: 0;
            color: #6b7280;
        }

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 18px;
            padding: 25px;
            margin-bottom: 18px;
        }

        .book-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;

            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }

        .book-title {
            margin: 0 0 7px;
            font-size: 23px;
            line-height: 1.4;
        }

        .book-author {
            margin: 0;
            color: #6b7280;
        }

        .status {
            flex-shrink: 0;

            display: inline-flex;
            align-items: center;
            gap: 6px;

            padding: 8px 13px;

            border-radius: 999px;

            font-size: 13px;
            font-weight: 700;
        }

        .status-dipinjam {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-terlambat {
            background: #fef2f2;
            color: #b91c1c;
        }

        .status-dikembalikan {
            background: #ecfdf5;
            color: #047857;
        }

        .status-default {
            background: #f3f4f6;
            color: #4b5563;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 18px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .info-item {
            padding: 15px;
            background: #f9fafb;
            border-radius: 11px;
        }

        .info-label {
            margin-bottom: 7px;
            color: #6b7280;
            font-size: 13px;
        }

        .info-value {
            font-weight: 700;
            font-size: 15px;
            line-height: 1.5;
        }

        .late-box {
            margin-top: 18px;
            padding: 16px;

            border-radius: 11px;

            background: #fef2f2;
            border: 1px solid #fecaca;

            color: #991b1b;
        }

        .fine-box {
            padding: 20px;

            background: #fff7ed;
            border: 1px solid #fed7aa;

            border-radius: 13px;
        }

        .fine-label {
            color: #9a3412;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .fine-value {
            color: #c2410c;
            font-size: 25px;
            font-weight: 800;
        }

        .fine-status {
            margin-top: 7px;
            color: #7c2d12;
            font-size: 14px;
        }

        .loading {
            background: #ffffff;
            border-radius: 15px;
            padding: 50px 20px;
            text-align: center;
            color: #6b7280;
        }

        .spinner {
            width: 32px;
            height: 32px;

            margin: 0 auto 12px;

            border: 3px solid #e5e7eb;
            border-top-color: #2563eb;

            border-radius: 50%;

            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        .error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;

            padding: 20px;
            border-radius: 14px;

            text-align: center;
        }

        .not-found {
            text-align: center;
            padding: 30px;
            color: #6b7280;
        }

        @media (max-width: 650px) {

            .container {
                padding: 18px 14px 40px;
            }

            .page-title h1 {
                font-size: 23px;
            }

            .card {
                padding: 18px;
            }

            .book-header {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a
        href="./"
        class="back-button"
    >
        ← Kembali ke Peminjaman
    </a>


    <div class="page-title">

        <h1>
            Detail Peminjaman
        </h1>

        <p>
            Informasi lengkap mengenai peminjaman buku kamu.
        </p>

    </div>


    <div id="content">

        <div class="loading">

            <div class="spinner"></div>

            Memuat detail peminjaman...

        </div>

    </div>

</div>


<script>

    const API_BASE = "../../../api";

    const idLoan = <?= json_encode($id_loan) ?>;

    const content =
        document.getElementById("content");


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


    function getBookTitle(data) {

        return (
            data.judul_buku ||
            data.judul ||
            data.book?.judul ||
            "Buku"
        );
    }


    function getAuthor(data) {

        return (
            data.pengarang ||
            data.book?.pengarang ||
            "-"
        );
    }


    async function loadDetail() {

        try {

            const response =
                await fetch(
                    API_BASE +
                    "/loans/detail.php?id_loan=" +
                    encodeURIComponent(idLoan),
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


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Detail peminjaman tidak ditemukan."
                );
            }


            /*
             * Backend bisa mengembalikan
             * object langsung pada data.
             */
            const data =
                result.data;


            if (!data) {

                throw new Error(
                    "Data peminjaman kosong."
                );
            }


            renderDetail(data);


        } catch (error) {

            console.error(error);

            content.innerHTML = `
                <div class="error">
                    ❌ ${escapeHtml(
                        error.message ||
                        "Gagal memuat detail peminjaman."
                    )}
                </div>
            `;
        }
    }


    function renderDetail(data) {

    const peminjam =
        data.peminjam || {};

    const buku =
        data.buku || {};

    const peminjaman =
        data.peminjaman || {};


    const status =
        getStatusInfo(
            peminjaman.status
        );


    const title =
        buku.judul || "Buku";


    const author =
        buku.pengarang || "-";


    const isLate =
        String(peminjaman.status || "")
            .toLowerCase()
            .trim() === "terlambat";


    content.innerHTML = `

        <!-- INFORMASI BUKU -->
        <div class="card">

            <div class="book-header">

                <div>

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

        </div>


        <!-- INFORMASI PEMINJAMAN -->
        <div class="card">

            <h2 class="section-title">
                Informasi Peminjaman
            </h2>


            <div class="info-grid">

                <!-- ID PEMINJAMAN -->
                <div class="info-item">

                    <div class="info-label">
                        ID Peminjaman
                    </div>

                    <div class="info-value">
                        #${escapeHtml(
                            data.id_loan
                        )}
                    </div>

                </div>


                <!-- ID BUKU -->
                <div class="info-item">

                    <div class="info-label">
                        ID Buku
                    </div>

                    <div class="info-value">
                        ${escapeHtml(
                            buku.id_book
                        )}
                    </div>

                </div>


                <!-- PEMINJAM -->
                <div class="info-item">

                    <div class="info-label">
                        Peminjam
                    </div>

                    <div class="info-value">
                        ${escapeHtml(
                            peminjam.nama
                        )}
                    </div>

                </div>


                <!-- ISBN -->
                <div class="info-item">

                    <div class="info-label">
                        ISBN
                    </div>

                    <div class="info-value">
                        ${escapeHtml(
                            buku.isbn || "-"
                        )}
                    </div>

                </div>


                <!-- TANGGAL PINJAM -->
                <div class="info-item">

                    <div class="info-label">
                        Tanggal Pinjam
                    </div>

                    <div class="info-value">
                        ${formatDate(
                            peminjaman.tanggal_pinjam
                        )}
                    </div>

                </div>


                <!-- BATAS KEMBALI -->
                <div class="info-item">

                    <div class="info-label">
                        Batas Kembali
                    </div>

                    <div class="info-value ${
                        isLate
                            ? "late-text"
                            : ""
                    }">
                        ${formatDate(
                            peminjaman.batas_kembali
                        )}
                    </div>

                </div>


                <!-- TANGGAL KEMBALI -->
                <div class="info-item">

                    <div class="info-label">
                        Tanggal Kembali
                    </div>

                    <div class="info-value">

                        ${
                            peminjaman.tanggal_kembali
                                ? formatDate(
                                    peminjaman.tanggal_kembali
                                )
                                : "Belum dikembalikan"
                        }

                    </div>

                </div>


                <!-- STATUS -->
                <div class="info-item">

                    <div class="info-label">
                        Status
                    </div>

                    <div class="info-value">
                        ${status.icon}
                        ${escapeHtml(
                            status.text
                        )}
                    </div>

                </div>

            </div>


            ${
                isLate
                    ? `
                        <div class="late-box">

                            ⚠️ Buku ini sudah melewati
                            batas toleransi pengembalian.
                            Silakan segera mengembalikan
                            buku kepada petugas.

                        </div>
                    `
                    : ""
            }

        </div>

    `;
}

    loadDetail();

</script>

</body>
</html>