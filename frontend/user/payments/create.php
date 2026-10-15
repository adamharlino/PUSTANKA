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
    
$id_fine = isset($_GET["id_fine"])
    ? (int) $_GET["id_fine"]
    : 0;

if ($id_fine <= 0) {
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

    <title>Bayar Denda - Pustanka</title>

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
            max-width: 850px;
            margin: 0 auto;
            padding: 30px 20px 50px;
        }

        /* =========================
           HEADER
        ========================= */

        .page-header {
            margin-bottom: 25px;
        }

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
            margin-bottom: 20px;
        }

        .card-title {
            margin: 0 0 20px;
            font-size: 19px;
        }

        /* =========================
           FINE DETAIL
        ========================= */

        .fine-detail {
            display: grid;
            gap: 14px;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 13px;
            border-bottom: 1px solid #f0f0f0;
        }

        .detail-row:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .detail-label {
            color: #6b7280;
        }

        .detail-value {
            font-weight: 600;
            text-align: right;
        }

        .nominal {
            color: #dc2626;
            font-size: 21px;
        }

        /* =========================
           METHOD
        ========================= */

        .method-list {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .method-option {
            position: relative;
        }

        .method-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .method-label {
            display: block;
            padding: 18px;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            cursor: pointer;
            transition: 0.2s;
        }

        .method-label:hover {
            border-color: #93c5fd;
        }

        .method-option input:checked
        + .method-label {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .method-icon {
            font-size: 27px;
            margin-bottom: 8px;
        }

        .method-name {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .method-description {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        .payment-info {
            margin-top: 16px;
            padding: 18px;
            border: 1px solid #dbeafe;
            background: #f8fbff;
            border-radius: 12px;
        }

        .payment-info h3 {
            margin: 0 0 12px;
            font-size: 17px;
        }

        .qris-image {
            display: block;
            width: min(100%, 280px);
            height: auto;
            margin: 12px auto;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }

        .rekening-box {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 14px;
            border-radius: 9px;
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .rekening-box strong {
            font-size: 22px;
            letter-spacing: 1px;
        }

        /* =========================
           PAYMENT GUIDE
        ========================= */

        .payment-guide {
            margin-top: 18px;
            padding: 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            line-height: 1.6;
        }

        .payment-guide strong {
            color: #1f2937;
        }

        /* =========================
           UPLOAD
        ========================= */

        .upload-box {
            border: 2px dashed #d1d5db;
            border-radius: 12px;
            padding: 25px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .upload-box:hover {
            border-color: #2563eb;
            background: #f8fbff;
        }

        .upload-icon {
            font-size: 35px;
            margin-bottom: 10px;
        }

        .upload-title {
            font-weight: 700;
            margin-bottom: 5px;
        }

        .upload-description {
            font-size: 13px;
            color: #6b7280;
            line-height: 1.5;
        }

        #buktiPembayaran {
            display: none;
        }

        .file-name {
            margin-top: 12px;
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
            word-break: break-word;
        }

        .file-warning {
            margin-top: 8px;
            color: #dc2626;
            font-size: 13px;
            display: none;
        }

        /* =========================
           BUTTON
        ========================= */

        .button-group {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 25px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 9px;
            border: none;
            text-decoration: none;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
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

        .button-primary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;
            padding: 13px 15px;
            border-radius: 9px;
            margin-bottom: 18px;
            font-size: 14px;
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

            .method-list {
                grid-template-columns: 1fr;
            }

            .detail-row {
                flex-direction: column;
                gap: 5px;
            }

            .detail-value {
                text-align: left;
            }

            .button-group {
                flex-direction: column-reverse;
            }

            .button {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<div class="container">

    <!-- HEADER -->

    <div class="page-header">

        <a
            href="./index.php"
            class="back-link"
        >
            ← Kembali ke Denda
        </a>

        <h1>💳 Bayar Denda</h1>

        <p>
            Pilih metode pembayaran dan upload
            bukti pembayaran kamu.
        </p>

    </div>


    <!-- MESSAGE -->

    <div
        id="message"
        class="message"
    ></div>


    <!-- DETAIL DENDA -->

    <div class="card">

        <h2 class="card-title">
            Detail Denda
        </h2>

        <div
            id="fineLoading"
            class="loading"
        >

            <div class="spinner"></div>

            Memuat detail denda...

        </div>


        <div
            id="fineDetail"
            class="fine-detail"
            style="display:none;"
        >

            <div class="detail-row">

                <span class="detail-label">
                    Buku
                </span>

                <span
                    class="detail-value"
                    id="bookTitle"
                >
                    -
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Pengarang
                </span>

                <span
                    class="detail-value"
                    id="bookAuthor"
                >
                    -
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Tanggal Pinjam
                </span>

                <span
                    class="detail-value"
                    id="loanDate"
                >
                    -
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Batas Kembali
                </span>

                <span
                    class="detail-value"
                    id="dueDate"
                >
                    -
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Nominal Denda
                </span>

                <span
                    class="detail-value nominal"
                    id="fineAmount"
                >
                    -
                </span>

            </div>


            <div class="detail-row">

                <span class="detail-label">
                    Status
                </span>

                <span
                    class="detail-value"
                    id="fineStatus"
                >
                    -
                </span>

            </div>

        </div>

    </div>


    <!-- PAYMENT FORM -->

    <form
        id="paymentForm"
        class="card"
        style="display:none;"
    >

        <h2 class="card-title">
            Metode Pembayaran
        </h2>


        <div class="method-list">

            <!-- QRIS -->

            <div class="method-option">

                <input
                    type="radio"
                    name="metode"
                    id="qris"
                    value="qris"
                    checked
                >

                <label
                    for="qris"
                    class="method-label"
                >

                    <div class="method-icon">
                        📱
                    </div>

                    <div class="method-name">
                        QRIS
                    </div>

                    <div class="method-description">
                        Bayar menggunakan QRIS,
                        kemudian upload bukti pembayaran.
                    </div>

                </label>

            </div>


            
            <!-- TRANSFER -->
            <div class="method-option">
                <input
                    type="radio"
                    name="metode"
                    id="transferBank"
                    value="transfer_bank"
                >

                <label
                    for="transferBank"
                    class="method-label"
                >
                    <div class="method-icon">
                        🏦
                    </div>

                    <div class="method-name">
                        Transfer Bank
                    </div>

                    <div class="method-description">
                        Transfer melalui rekening bank,
                        kemudian upload bukti pembayaran.
                    </div>
                </label>
            </div>

            <!-- INFORMASI QRIS -->
            <div id="qrisInfo" class="payment-info">
                <h3>Scan QRIS untuk Membayar</h3>
                <p>Scan kode QRIS menggunakan aplikasi pembayaran kamu.</p>
                <img
                    src="../../../assets/images/qris-pustanka.png"
                    alt="Kode QRIS Pustanka"
                    class="qris-image"
                >
                <p>Setelah pembayaran asli dilakukan, upload bukti pembayaran.</p>
            </div>

            <!-- INFORMASI TRANSFER BANK -->
            <div id="transferInfo" class="payment-info" hidden>
                <h3>Transfer Bank</h3>

                <div class="rekening-box">
                    <span>Nomor Rekening</span>
                    <strong id="nomorRekening">
                        (BRI) 3170057681
                        (BCA) 3170057786
                        (MANDIRI) 3170057565
                    </strong>
                </div>

                <p>
                    Silakan transfer sesuai nominal denda,
                    lalu upload bukti pembayaran.
                </p>
            </div>


        <div class="payment-guide">

            <strong>Catatan pembayaran:</strong>

            <br>

            Silakan lakukan pembayaran sesuai
            metode yang dipilih, kemudian upload
            bukti pembayaran yang jelas.

            <br><br>

            Format yang diperbolehkan:
            <strong>JPG, PNG, WEBP, atau PDF</strong>.

            <br>

            Ukuran maksimal:
            <strong>2 MB</strong>.

        </div>


        <br>


        <h2 class="card-title">
            Bukti Pembayaran
        </h2>


        <label
            for="buktiPembayaran"
            class="upload-box"
        >

            <div class="upload-icon">
                📎
            </div>

            <div class="upload-title">
                Pilih Bukti Pembayaran
            </div>

            <div class="upload-description">
                Klik area ini untuk memilih file
            </div>

            <div
                id="fileName"
                class="file-name"
            >
            </div>

        </label>


        <input
            type="file"
            id="buktiPembayaran"
            name="bukti_pembayaran"
            accept=".jpg,.jpeg,.png,.webp,.pdf"
        >


        <div
            id="fileWarning"
            class="file-warning"
        >
        </div>


        <div class="button-group">

            <a
                href="./index.php"
                class="button button-secondary"
            >
                Batal
            </a>

            <button
                type="submit"
                class="button button-primary"
                id="submitButton"
            >
                💳 Kirim Pembayaran
            </button>

        </div>

    </form>

</div>


<script>

    const ID_FINE =
        <?= json_encode($id_fine) ?>;

    const API_BASE =
        "../../../api";


    const message =
        document.getElementById("message");

    const fineLoading =
        document.getElementById("fineLoading");

    const fineDetail =
        document.getElementById("fineDetail");

    const paymentForm =
        document.getElementById("paymentForm");

    const fileInput =
        document.getElementById(
            "buktiPembayaran"
        );

    const fileName =
        document.getElementById(
            "fileName"
        );

    const fileWarning =
        document.getElementById(
            "fileWarning"
        );

    const submitButton =
        document.getElementById(
            "submitButton"
        );

    const metodePembayaran = document.querySelectorAll(
    'input[name="metode"]'
         );

    const qrisInfo = document.getElementById("qrisInfo");
    const transferInfo = document.getElementById("transferInfo");

    function tampilkanMetodePembayaran() {
        const pilihan = document.querySelector(
            'input[name="metode"]:checked'
        );

        const metode = pilihan ? pilihan.value : "qris";

        qrisInfo.hidden = metode !== "qris";
        transferInfo.hidden = metode !== "transfer_bank";
         }

    metodePembayaran.forEach((radio) => {
        radio.addEventListener("change", tampilkanMetodePembayaran);
        });

    tampilkanMetodePembayaran();


    // =========================
    // MESSAGE
    // =========================

    function showMessage(
        text,
        type = "error"
    ) {

        message.textContent = text;

        message.className =
            "message " + type;
    }


    function hideMessage() {

        message.textContent = "";

        message.className =
            "message";
    }


    // =========================
    // ESCAPE HTML
    // =========================

    function escapeHtml(value) {

        return String(value ?? "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
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
    // GET DETAIL DENDA
    // =========================

    async function loadFine() {

        try {

            /*
             * Kita menggunakan endpoint
             * fines/list.php karena endpoint
             * tersebut sudah tersedia dan
             * ownership user sudah dicek
             */

            const response =
                await fetch(
                    `${API_BASE}/fines/list.php?limit=50`,
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
                    "Gagal mengambil data denda."
                );
            }


            const fines =
                Array.isArray(result.data)
                    ? result.data
                    : [];


            const fine =
                fines.find(
                    item =>
                        Number(
                            item.id_fine
                        ) === ID_FINE
                );


            if (!fine) {

                throw new Error(
                    "Denda tidak ditemukan atau bukan milik akun kamu."
                );
            }


            const denda =
                fine.denda || {};

            const buku =
                fine.buku || {};

            const peminjaman =
                fine.peminjaman || {};

            const payment =
                fine.payment_terakhir || null;


            // =========================
            // CEK STATUS
            // =========================

            if (
                denda.status_pembayaran ===
                "lunas"
            ) {

                throw new Error(
                    "Denda ini sudah lunas."
                );
            }


            if (
                payment &&
                payment.status ===
                "pending"
            ) {

                throw new Error(
                    "Pembayaran untuk denda ini sedang menunggu verifikasi admin."
                );
            }


            // =========================
            // RENDER
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
                "fineAmount"
            ).textContent =
                formatRupiah(
                    denda.nominal
                );


            document.getElementById(
                "fineStatus"
            ).textContent =
                payment &&
                payment.status ===
                    "ditolak"

                    ? "Pembayaran sebelumnya ditolak"

                    : "Belum Bayar";


            fineLoading.style.display =
                "none";

            fineDetail.style.display =
                "grid";

            paymentForm.style.display =
                "block";


        } catch (error) {

            fineLoading.style.display =
                "none";

            paymentForm.style.display =
                "none";

            showMessage(
                error.message ||
                "Gagal memuat detail denda."
            );
        }
    }


    // =========================
    // FILE SELECT
    // =========================

    fileInput.addEventListener(
        "change",
        () => {

            hideMessage();

            fileWarning.style.display =
                "none";

            fileWarning.textContent =
                "";


            const file =
                fileInput.files[0];


            if (!file) {

                fileName.textContent =
                    "";

                return;
            }


            const maxSize =
                2 * 1024 * 1024;


            if (
                file.size >
                maxSize
            ) {

                fileWarning.textContent =
                    "Ukuran file maksimal 2 MB.";

                fileWarning.style.display =
                    "block";

                fileInput.value =
                    "";

                fileName.textContent =
                    "";

                return;
            }


            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp",
                "application/pdf"
            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                fileWarning.textContent =
                    "Format file harus JPG, PNG, WEBP, atau PDF.";

                fileWarning.style.display =
                    "block";

                fileInput.value =
                    "";

                fileName.textContent =
                    "";

                return;
            }


            fileName.textContent =
                `📎 ${file.name}`;
        }
    );


    // =========================
    // SUBMIT PAYMENT
    // =========================

    paymentForm.addEventListener(
        "submit",
        async (event) => {

            event.preventDefault();

            hideMessage();


            const metode =
                document.querySelector(
                    'input[name="metode"]:checked'
                );


            const file =
                fileInput.files[0];


            if (!metode) {

                showMessage(
                    "Silakan pilih metode pembayaran."
                );

                return;
            }


            if (!file) {

                showMessage(
                    "Silakan upload bukti pembayaran."
                );

                return;
            }


            if (
                file.size >
                2 * 1024 * 1024
            ) {

                showMessage(
                    "Ukuran bukti pembayaran maksimal 2 MB."
                );

                return;
            }


            const allowedTypes = [
                "image/jpeg",
                "image/png",
                "image/webp",
                "application/pdf"
            ];


            if (
                !allowedTypes.includes(
                    file.type
                )
            ) {

                showMessage(
                    "Format bukti pembayaran harus JPG, PNG, WEBP, atau PDF."
                );

                return;
            }


            const confirmation =
                confirm(
                    "Yakin ingin mengirim pembayaran ini?\n\n" +
                    "Setelah dikirim, pembayaran akan berstatus PENDING " +
                    "dan menunggu verifikasi admin."
                );


            if (!confirmation) {
                return;
            }


            submitButton.disabled =
                true;

            submitButton.textContent =
                "Mengirim pembayaran...";


            try {

                const formData =
                    new FormData();


                formData.append(
                    "id_fine",
                    ID_FINE
                );


                formData.append(
                    "metode",
                    metode.value
                );


                formData.append(
                    "bukti_pembayaran",
                    file
                );


                const response =
                    await fetch(
                        `${API_BASE}/payments/create.php`,
                        {
                            method: "POST",
                            credentials: "include",
                            body: formData
                        }
                    );


                const result =
                    await response.json();


                if (
                    response.status ===
                    401
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
                        "Gagal mengirim pembayaran."
                    );
                }


                showMessage(
                    result.message ||
                    "Pembayaran berhasil dikirim dan menunggu verifikasi admin.",
                    "success"
                );


                submitButton.textContent =
                    "Pembayaran Terkirim";


                /*
                 * Beri waktu sebentar agar
                 * user melihat pesan sukses.
                 */

                setTimeout(
                    () => {

                        window.location.href =
                            "./index.php";

                    },
                    1500
                );


            } catch (error) {

                showMessage(
                    error.message ||
                    "Terjadi kesalahan saat mengirim pembayaran."
                );


                submitButton.disabled =
                    false;

                submitButton.textContent =
                    "💳 Kirim Pembayaran";
            }

        }
    );


    // =========================
    // START
    // =========================

    loadFine();

</script>

</body>

</html>