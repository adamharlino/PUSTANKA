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

$id_payment = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id_payment <= 0) {
    header("Location: ./index.php");
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

    <title>Detail Pembayaran - Pustanka</title>

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
            padding: 30px 20px 50px;
        }

        /* =========================
           HEADER
        ========================= */

        .back-link {
            display: inline-block;
            margin-bottom: 15px;
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 7px;
            font-size: 28px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
        }

        /* =========================
           CARD
        ========================= */

        .card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            padding: 25px;
            margin-bottom: 18px;
        }

        .card-title {
            margin: 0 0 20px;
            font-size: 19px;
        }

        /* =========================
           PAYMENT HEADER
        ========================= */

        .payment-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
        }

        .payment-id {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 7px;
        }

        .payment-title {
            margin: 0;
            font-size: 23px;
        }

        /* =========================
           STATUS
        ========================= */

        .status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
            padding: 8px 13px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 700;
        }

        .status-pending {
            background: #fffbeb;
            color: #b45309;
        }

        .status-approved {
            background: #ecfdf5;
            color: #047857;
        }

        .status-rejected {
            background: #fef2f2;
            color: #b91c1c;
        }

        .status-default {
            background: #f3f4f6;
            color: #4b5563;
        }

        /* =========================
           INFO GRID
        ========================= */

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .info-item {
            padding: 15px;
            background: #f9fafb;
            border-radius: 10px;
            border: 1px solid #f0f0f0;
        }

        .info-label {
            color: #6b7280;
            font-size: 12px;
            margin-bottom: 6px;
        }

        .info-value {
            font-weight: 700;
            word-break: break-word;
        }

        .nominal {
            color: #dc2626;
            font-size: 20px;
        }

        /* =========================
           BOOK
        ========================= */

        .book-box {
            display: flex;
            gap: 18px;
            align-items: flex-start;
        }

        .book-cover {
            width: 100px;
            height: 135px;
            border-radius: 9px;
            object-fit: cover;
            background: #e5e7eb;
            flex-shrink: 0;
        }

        .book-placeholder {
            width: 100px;
            height: 135px;
            border-radius: 9px;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            flex-shrink: 0;
        }

        .book-content h3 {
            margin: 0 0 7px;
            font-size: 20px;
        }

        .book-content p {
            margin: 4px 0;
            color: #6b7280;
            font-size: 14px;
        }

        /* =========================
           PROOF
        ========================= */

        .proof-box {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 18px;
            background: #f9fafb;
        }

        .proof-image {
            display: block;
            max-width: 100%;
            max-height: 500px;
            margin: 0 auto;
            border-radius: 9px;
            object-fit: contain;
            background: #ffffff;
        }

        .proof-pdf {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 120px;
            text-align: center;
        }

        .proof-pdf a {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 11px 15px;
            border-radius: 9px;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            font-weight: 700;
        }

        .proof-pdf a:hover {
            background: #1d4ed8;
        }

        .no-proof {
            color: #6b7280;
            text-align: center;
            padding: 25px;
        }

        /* =========================
           ACTIONS
        ========================= */

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 43px;
            padding: 10px 17px;
            border-radius: 9px;
            text-decoration: none;
            font-weight: 700;
            border: none;
            cursor: pointer;
        }

        .button-secondary {
            background: #ffffff;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .button-secondary:hover {
            background: #f9fafb;
        }

        .button-primary {
            background: #2563eb;
            color: #ffffff;
        }

        .button-primary:hover {
            background: #1d4ed8;
        }

        .button-warning {
            background: #d97706;
            color: #ffffff;
        }

        .button-warning:hover {
            background: #b45309;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 18px;
            line-height: 1.5;
        }

        .message.error {
            display: block;
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
        }

        .message.success {
            display: block;
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            color: #047857;
        }

        /* =========================
           LOADING
        ========================= */

        .loading {
            background: #ffffff;
            border-radius: 15px;
            padding: 45px 20px;
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

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 650px) {

            .container {
                padding: 20px 14px 40px;
            }

            .card {
                padding: 18px;
            }

            .payment-header {
                flex-direction: column;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .book-box {
                flex-direction: column;
            }

            .book-cover,
            .book-placeholder {
                width: 120px;
                height: 160px;
            }

            .actions {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <a
        href="./index.php"
        class="back-link"
    >
        ← Kembali ke Denda
    </a>


    <div class="page-header">

        <h1>Detail Pembayaran</h1>

        <p>
            Informasi lengkap pembayaran denda kamu.
        </p>

    </div>


    <div
        id="message"
        class="message"
    ></div>


    <!-- LOADING -->

    <div
        id="loading"
        class="loading"
    >

        <div class="spinner"></div>

        Memuat detail pembayaran...

    </div>


    <!-- CONTENT -->

    <div
        id="content"
        style="display:none;"
    >

        <!-- =========================
             PAYMENT
        ========================= -->

        <div class="card">

            <div class="payment-header">

                <div>

                    <div class="payment-id">
                        ID Payment:
                        <strong id="paymentId">
                            -
                        </strong>
                    </div>

                    <h2 class="payment-title">
                        Pembayaran Denda
                    </h2>

                </div>


                <span
                    id="paymentStatus"
                    class="status status-default"
                >
                    -
                </span>

            </div>

        </div>


        <!-- =========================
             PAYMENT INFO
        ========================= -->

        <div class="card">

            <h2 class="card-title">
                Informasi Pembayaran
            </h2>


            <div class="info-grid">

                <div class="info-item">

                    <div class="info-label">
                        Nominal
                    </div>

                    <div
                        class="info-value nominal"
                        id="paymentNominal"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Metode Pembayaran
                    </div>

                    <div
                        class="info-value"
                        id="paymentMethod"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Tanggal Pembayaran
                    </div>

                    <div
                        class="info-value"
                        id="paymentDate"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Status Denda
                    </div>

                    <div
                        class="info-value"
                        id="fineStatus"
                    >
                        -
                    </div>

                </div>

            </div>

        </div>


        <!-- =========================
             BOOK
        ========================= -->

        <div class="card">

            <h2 class="card-title">
                Buku
            </h2>


            <div class="book-box">

                <div id="bookImageContainer">

                    <div class="book-placeholder">
                        📚
                    </div>

                </div>


                <div class="book-content">

                    <h3 id="bookTitle">
                        -
                    </h3>

                    <p>
                        Pengarang:
                        <strong id="bookAuthor">
                            -
                        </strong>
                    </p>

                    <p>
                        ISBN:
                        <strong id="bookIsbn">
                            -
                        </strong>
                    </p>

                    <p>
                        Barcode:
                        <strong id="bookBarcode">
                            -
                        </strong>
                    </p>

                </div>

            </div>

        </div>


        <!-- =========================
             LOAN
        ========================= -->

        <div class="card">

            <h2 class="card-title">
                Informasi Peminjaman
            </h2>


            <div class="info-grid">

                <div class="info-item">

                    <div class="info-label">
                        Tanggal Pinjam
                    </div>

                    <div
                        class="info-value"
                        id="loanDate"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Batas Kembali
                    </div>

                    <div
                        class="info-value"
                        id="dueDate"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Tanggal Kembali
                    </div>

                    <div
                        class="info-value"
                        id="returnDate"
                    >
                        -
                    </div>

                </div>


                <div class="info-item">

                    <div class="info-label">
                        Status Peminjaman
                    </div>

                    <div
                        class="info-value"
                        id="loanStatus"
                    >
                        -
                    </div>

                </div>

            </div>

        </div>


        <!-- =========================
             PROOF
        ========================= -->

        <div class="card">

            <h2 class="card-title">
                Bukti Pembayaran
            </h2>


            <div
                id="proofContainer"
                class="proof-box"
            >

                <div class="no-proof">
                    Memuat bukti pembayaran...
                </div>

            </div>

        </div>


        <!-- =========================
             ACTION
        ========================= -->

        <div class="card">

            <div
                id="rejectedNotice"
                style="display:none;"
            >

                <div class="message error">

                    ❌ Pembayaran ini ditolak oleh admin.

                    <br>

                    Kamu dapat melakukan pembayaran
                    kembali menggunakan tombol
                    <strong>Bayar Lagi</strong>.

                </div>

            </div>


            <div class="actions">

                <a
                    href="./index.php"
                    class="button button-secondary"
                >
                    ← Kembali
                </a>


                <a
                    id="retryButton"
                    href="#"
                    class="button button-warning"
                    style="display:none;"
                >
                    🔄 Bayar Lagi
                </a>

            </div>

        </div>

    </div>

</div>


<script>

    const ID_PAYMENT =
        <?= json_encode($id_payment) ?>;

    const API_BASE =
        "../../../api";


    const loading =
        document.getElementById(
            "loading"
        );

    const content =
        document.getElementById(
            "content"
        );

    const message =
        document.getElementById(
            "message"
        );


    // =========================
    // MESSAGE
    // =========================

    function showError(text) {

        message.textContent =
            text;

        message.className =
            "message error";
    }


    // =========================
    // FORMAT RUPIAH
    // =========================

    function formatRupiah(value) {

        return new Intl.NumberFormat(
            "id-ID",
            {
                style: "currency",
                currency: "IDR",
                minimumFractionDigits: 0
            }
        ).format(
            Number(value || 0)
        );
    }


    // =========================
    // FORMAT DATE
    // =========================

    function formatDateTime(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(
                value.replace(" ", "T")
            );

        if (
            isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return date.toLocaleString(
            "id-ID",
            {
                day: "2-digit",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit"
            }
        );
    }


    function formatDate(value) {

        if (!value) {
            return "-";
        }

        const date =
            new Date(
                value + "T00:00:00"
            );

        if (
            isNaN(
                date.getTime()
            )
        ) {
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


    // =========================
    // STATUS
    // =========================

    function getStatus(status) {

        switch (
            String(status || "")
                .toLowerCase()
        ) {

            case "pending":

                return {
                    className:
                        "status-pending",
                    icon: "⏳",
                    text:
                        "Menunggu Verifikasi"
                };


            case "disetujui":

                return {
                    className:
                        "status-approved",
                    icon: "✅",
                    text:
                        "Pembayaran Disetujui"
                };


            case "ditolak":

                return {
                    className:
                        "status-rejected",
                    icon: "❌",
                    text:
                        "Pembayaran Ditolak"
                };


            default:

                return {
                    className:
                        "status-default",
                    icon: "ℹ️",
                    text:
                        status || "-"
                };
        }
    }


    // =========================
    // METHOD
    // =========================

    function getMethod(method) {

        switch (
            String(method || "")
                .toLowerCase()
        ) {

            case "qris":
                return "📱 QRIS";

            case "transfer_bank":
                return "🏦 Transfer Bank";

            default:
                return method || "-";
        }
    }


    // =========================
    // PROOF
    // =========================

    function renderProof(path) {

        const container =
            document.getElementById(
                "proofContainer"
            );


        if (!path) {

            container.innerHTML = `
                <div class="no-proof">
                    Tidak ada bukti pembayaran.
                </div>
            `;

            return;
        }


        /*
         * Backend menyimpan path:
         *
         * uploads/payments/filename.ext
         *
         * Dari:
         * frontend/user/payments/
         *
         * kita perlu naik 3 folder.
         */

        const proofUrl =
            "../../../" + path;


        const extension =
            path
                .split(".")
                .pop()
                .toLowerCase();


        const imageExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp"
        ];


        if (
            imageExtensions.includes(
                extension
            )
        ) {

            container.innerHTML = `

                <img
                    src="${proofUrl}"
                    class="proof-image"
                    alt="Bukti Pembayaran"
                    onerror="
                        this.parentElement.innerHTML =
                        '<div class=&quot;no-proof&quot;>Bukti pembayaran tidak dapat ditampilkan.</div>';
                    "
                >

            `;

            return;
        }


        if (
            extension === "pdf"
        ) {

            container.innerHTML = `

                <div class="proof-pdf">

                    <a
                        href="${proofUrl}"
                        target="_blank"
                        rel="noopener"
                    >
                        📄 Buka Bukti PDF
                    </a>

                </div>

            `;

            return;
        }


        container.innerHTML = `

            <div class="proof-pdf">

                <a
                    href="${proofUrl}"
                    target="_blank"
                    rel="noopener"
                >
                    📎 Buka Bukti Pembayaran
                </a>

            </div>

        `;
    }


    // =========================
    // LOAD DETAIL
    // =========================

    async function loadDetail() {

        try {

            const response =
                await fetch(
                    `${API_BASE}/payments/detail.php?id=${encodeURIComponent(ID_PAYMENT)}`,
                    {
                        method: "GET",
                        credentials: "include"
                    }
                );


            const result =
                await response.json();


            if (
                response.status === 401
            ) {

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
                    "Gagal mengambil detail pembayaran."
                );
            }


            const data =
                result.data;


            const pembayaran =
                data.pembayaran || {};

            const denda =
                data.denda || {};

            const buku =
                data.buku || {};

            const peminjaman =
                data.peminjaman || {};


            // =========================
            // PAYMENT
            // =========================

            document.getElementById(
                "paymentId"
            ).textContent =
                data.id_payment || "-";


            document.getElementById(
                "paymentNominal"
            ).textContent =
                formatRupiah(
                    pembayaran.nominal
                );


            document.getElementById(
                "paymentMethod"
            ).textContent =
                getMethod(
                    pembayaran.metode
                );


            document.getElementById(
                "paymentDate"
            ).textContent =
                formatDateTime(
                    pembayaran.tanggal_bayar
                );


            document.getElementById(
                "fineStatus"
            ).textContent =
                denda.status_pembayaran ||
                "-";


            // =========================
            // PAYMENT STATUS
            // =========================

            const status =
                getStatus(
                    pembayaran.status
                );


            const statusElement =
                document.getElementById(
                    "paymentStatus"
                );


            statusElement.className =
                "status " +
                status.className;


            statusElement.innerHTML =
                status.icon +
                " " +
                status.text;


            // =========================
            // BOOK
            // =========================

            document.getElementById(
                "bookTitle"
            ).textContent =
                buku.judul || "-";


            document.getElementById(
                "bookAuthor"
            ).textContent =
                buku.pengarang || "-";


            document.getElementById(
                "bookIsbn"
            ).textContent =
                buku.isbn || "-";


            document.getElementById(
                "bookBarcode"
            ).textContent =
                buku.barcode || "-";


            // =========================
            // BOOK IMAGE
            // =========================

            const bookImageContainer =
                document.getElementById(
                    "bookImageContainer"
                );


            if (buku.foto) {

                const bookImageUrl =
                    "../../../" +
                    buku.foto;


                bookImageContainer.innerHTML = `

                    <img
                        src="${bookImageUrl}"
                        class="book-cover"
                        alt="Cover Buku"
                        onerror="
                            this.style.display='none';
                        "
                    >

                `;
            }


            // =========================
            // LOAN
            // =========================

            document.getElementById(
                "loanDate"
            ).textContent =
                formatDate(
                    peminjaman.tanggal_pinjam
                );


            document.getElementById(
                "dueDate"
            ).textContent =
                formatDate(
                    peminjaman.batas_kembali
                );


            document.getElementById(
                "returnDate"
            ).textContent =
                formatDate(
                    peminjaman.tanggal_kembali
                );


            document.getElementById(
                "loanStatus"
            ).textContent =
                peminjaman.status || "-";


            // =========================
            // PROOF
            // =========================

            renderProof(
                pembayaran.bukti_pembayaran
            );


            // =========================
            // RETRY
            // =========================

            const retryButton =
                document.getElementById(
                    "retryButton"
                );

            const rejectedNotice =
                document.getElementById(
                    "rejectedNotice"
                );


            if (
                pembayaran.status ===
                "ditolak"
            ) {

                retryButton.href =
                    "./create.php?id_fine=" +
                    encodeURIComponent(
                        denda.id_fine
                    );

                retryButton.style.display =
                    "inline-flex";

                rejectedNotice.style.display =
                    "block";

            } else {

                retryButton.style.display =
                    "none";

                rejectedNotice.style.display =
                    "none";
            }


            // =========================
            // SHOW CONTENT
            // =========================

            loading.style.display =
                "none";

            content.style.display =
                "block";


        } catch (error) {

            loading.style.display =
                "none";

            showError(
                error.message ||
                "Gagal memuat detail pembayaran."
            );
        }
    }


    // =========================
    // START
    // =========================

    loadDetail();

</script>

</body>

</html>