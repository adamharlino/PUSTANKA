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

$id_book = isset($_GET["id_book"]) ? (int) $_GET["id_book"] : 0;

if ($id_book <= 0) {
    header("Location: ./");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Scan Barcode Buku - Pustanka</title>

    <!-- Barcode Scanner -->
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
        }

        .container {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            padding: 25px 20px 50px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
            color: #2563eb;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        h1 {
            margin: 0 0 8px;
            font-size: 26px;
        }

        .subtitle {
            margin: 0 0 25px;
            color: #6b7280;
            line-height: 1.6;
        }

        #reader {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 14px;
        }

        .status {
            margin-top: 20px;
            padding: 14px 16px;
            border-radius: 10px;
            background: #eff6ff;
            color: #1d4ed8;
            text-align: center;
            line-height: 1.5;
        }

        .status.error {
            background: #fef2f2;
            color: #b91c1c;
        }

        .status.success {
            background: #ecfdf5;
            color: #047857;
        }

        .book-result {
            display: none;
            margin-top: 25px;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            background: #fafafa;
        }

        .book-result.show {
            display: block;
        }

        .book-title {
            font-size: 21px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .book-info {
            display: grid;
            grid-template-columns: 150px 1fr;
            gap: 10px;
            margin-bottom: 20px;
        }

        .book-info .label {
            color: #6b7280;
        }

        .book-info .value {
            font-weight: 600;
        }

        .btn {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 13px 18px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            margin-top: 10px;
            background: #e5e7eb;
            color: #374151;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .hidden {
            display: none !important;
        }

        @media (max-width: 600px) {
            .container {
                padding: 18px 14px 40px;
            }

            .card {
                padding: 18px;
                border-radius: 14px;
            }

            h1 {
                font-size: 22px;
            }

            .book-info {
                grid-template-columns: 1fr;
                gap: 4px;
            }

            .book-info .label {
                margin-top: 8px;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a
        class="back"
        href="./detail.php?id_book=<?= htmlspecialchars((string) $id_book) ?>"
    >
        ← Kembali ke Detail Buku
    </a>

    <div class="card">

        <h1>Scan Barcode Buku</h1>

        <p class="subtitle">
            Arahkan kamera ke barcode fisik buku yang ingin dipinjam.
        </p>

        <div id="reader"></div>

        <div id="statusMessage" class="status">
            Menyiapkan kamera...
        </div>

        <div id="bookResult" class="book-result">

            <div class="book-title" id="bookTitle">
                -
            </div>

            <div class="book-info">

                <div class="label">
                    Barcode
                </div>

                <div class="value" id="bookBarcode">
                    -
                </div>

                <div class="label">
                    Pengarang
                </div>

                <div class="value" id="bookAuthor">
                    -
                </div>

                <div class="label">
                    Penerbit
                </div>

                <div class="value" id="bookPublisher">
                    -
                </div>

                <div class="label">
                    Tahun Terbit
                </div>

                <div class="value" id="bookYear">
                    -
                </div>

                <div class="label">
                    Status
                </div>

                <div class="value" id="bookStatus">
                    -
                </div>

            </div>

            <button
                type="button"
                id="confirmBorrowBtn"
                class="btn btn-primary"
            >
                📚 Konfirmasi Pinjam
            </button>

            <button
                type="button"
                id="scanAgainBtn"
                class="btn btn-secondary"
            >
                🔄 Scan Lagi
            </button>

        </div>

    </div>

</div>

<script>
    const API_BASE = "../../../api";

    const contextIdBook = <?= json_encode($id_book) ?>;

    const readerElement = document.getElementById("reader");
    const statusMessage = document.getElementById("statusMessage");

    const bookResult = document.getElementById("bookResult");

    const bookTitle = document.getElementById("bookTitle");
    const bookBarcode = document.getElementById("bookBarcode");
    const bookAuthor = document.getElementById("bookAuthor");
    const bookPublisher = document.getElementById("bookPublisher");
    const bookYear = document.getElementById("bookYear");
    const bookStatus = document.getElementById("bookStatus");

    const confirmBorrowBtn =
        document.getElementById("confirmBorrowBtn");

    const scanAgainBtn =
        document.getElementById("scanAgainBtn");

    let html5QrCode = null;
    let scannerRunning = false;
    let scanLocked = false;
    let scannedBook = null;


    function setStatus(message, type = "") {
        statusMessage.textContent = message;

        statusMessage.className = "status";

        if (type) {
            statusMessage.classList.add(type);
        }
    }


    async function startScanner() {

        scanLocked = false;

        bookResult.classList.remove("show");

        setStatus("Meminta akses kamera...");

        try {

            html5QrCode = new Html5Qrcode("reader");

            await html5QrCode.start(

                {
                    facingMode: "environment"
                },

                {
                    fps: 10,

                    qrbox: {
                        width: 300,
                        height: 150
                    },

                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.CODE_93,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E
                    ]
                },

                onScanSuccess,

                () => {
                    // Jangan tampilkan error setiap frame gagal scan.
                }
            );

            scannerRunning = true;

            setStatus(
                "Kamera aktif. Arahkan barcode buku ke dalam kotak."
            );

        } catch (error) {

            console.error(error);

            scannerRunning = false;

            setStatus(
                "Kamera tidak dapat digunakan. Pastikan izin kamera diberikan.",
                "error"
            );
        }
    }


    async function stopScanner() {

        if (!html5QrCode || !scannerRunning) {
            return;
        }

        try {

            await html5QrCode.stop();

        } catch (error) {

            console.warn("Gagal menghentikan scanner:", error);

        }

        scannerRunning = false;
    }


    async function onScanSuccess(decodedText) {

        if (scanLocked) {
            return;
        }

        scanLocked = true;

        const barcode = String(decodedText).trim();

        if (!barcode) {
            scanLocked = false;
            return;
        }

        setStatus(
            "Barcode terbaca. Mencari data buku..."
        );

        await stopScanner();

        await findBookByBarcode(barcode);
    }


    async function findBookByBarcode(barcode) {

        try {

            const url =
                API_BASE +
                "/books/barcode.php?barcode=" +
                encodeURIComponent(barcode);

            const response = await fetch(url, {
                method: "GET",
                credentials: "include"
            });

            const result = await response.json();

            if (response.status === 401) {

                window.location.href = "../../auth/login.php";
                return;
            }

            if (!response.ok || !result.status) {

                throw new Error(
                    result.message ||
                    "Buku dengan barcode tersebut tidak ditemukan."
                );
            }

            const book = result.data;

            /*
             * Pastikan barcode yang discan adalah buku
             * yang dipilih dari halaman detail.
             */
            if (
                contextIdBook &&
                Number(book.id_book) !== Number(contextIdBook)
            ) {

                throw new Error(
                    "Barcode tidak sesuai dengan buku yang sedang dipilih."
                );
            }

            scannedBook = book;

            bookTitle.textContent =
                book.judul || "-";

            bookBarcode.textContent =
                book.barcode || barcode;

            bookAuthor.textContent =
                book.pengarang || "-";

            bookPublisher.textContent =
                book.penerbit || "-";

            bookYear.textContent =
                book.tahun_terbit || "-";

            bookStatus.textContent =
                book.status || "-";


            if (book.status !== "tersedia") {

                confirmBorrowBtn.disabled = true;

                setStatus(
                    "Buku ditemukan, tetapi buku sedang tidak tersedia.",
                    "error"
                );

            } else {

                confirmBorrowBtn.disabled = false;

                setStatus(
                    "Buku ditemukan. Silakan konfirmasi peminjaman.",
                    "success"
                );
            }

            bookResult.classList.add("show");

        } catch (error) {

            console.error(error);

            scannedBook = null;

            bookResult.classList.remove("show");

            setStatus(
                error.message ||
                "Terjadi kesalahan saat membaca barcode.",
                "error"
            );

            scanLocked = false;

            /*
             * Beri kesempatan scan ulang.
             */
        }
    }


    async function borrowBook() {

        if (!scannedBook) {
            setStatus(
                "Data buku belum tersedia.",
                "error"
            );
            return;
        }

        if (scannedBook.status !== "tersedia") {
            setStatus(
                "Buku tidak tersedia untuk dipinjam.",
                "error"
            );
            return;
        }

        const confirmed = confirm(
            "Apakah kamu yakin ingin meminjam buku \"" +
            scannedBook.judul +
            "\"?"
        );

        if (!confirmed) {
            return;
        }

        confirmBorrowBtn.disabled = true;

        setStatus(
            "Memproses peminjaman..."
        );

        try {

            const response = await fetch(
                API_BASE + "/loans/create.php",
                {
                    method: "POST",

                    credentials: "include",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    body: JSON.stringify({
                        id_book: Number(scannedBook.id_book)
                    })
                }
            );

            const result = await response.json();

            if (response.status === 401) {

                window.location.href =
                    "../../auth/login.php";

                return;
            }

            if (!response.ok || !result.status) {

                throw new Error(
                    result.message ||
                    "Peminjaman buku gagal."
                );
            }

            setStatus(
                result.message ||
                "Buku berhasil dipinjam.",
                "success"
            );

            confirmBorrowBtn.disabled = true;
            scanAgainBtn.disabled = true;

            setTimeout(() => {

                window.location.href =
                    "../loans/";

            }, 1500);

        } catch (error) {

            console.error(error);

            confirmBorrowBtn.disabled = false;

            setStatus(
                error.message ||
                "Terjadi kesalahan saat memproses peminjaman.",
                "error"
            );
        }
    }


    scanAgainBtn.addEventListener("click", async () => {

        scannedBook = null;

        bookResult.classList.remove("show");

        confirmBorrowBtn.disabled = false;

        if (scannerRunning) {
            return;
        }

        /*
         * Buat instance baru supaya kamera
         * bisa dimulai kembali dengan bersih.
         */
        html5QrCode = null;

        readerElement.innerHTML = "";

        await startScanner();
    });


    confirmBorrowBtn.addEventListener(
        "click",
        borrowBook
    );


    window.addEventListener("beforeunload", () => {

        if (html5QrCode && scannerRunning) {

            html5QrCode.stop().catch(() => {});

        }

    });


    startScanner();
</script>

</body>
</html>