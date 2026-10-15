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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Buku - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #172033;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e7ebf2;
            padding: 18px 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            color: #164e8a;
            font-size: 22px;
            font-weight: 800;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #164e8a;
            font-weight: 700;
            border: 1px solid #d9e3f0;
            background: #fff;
            padding: 10px 15px;
            border-radius: 10px;
        }

        .back-btn:hover {
            background: #f1f6fc;
        }

        main {
            padding: 34px 0 50px;
        }

        .loading,
        .error-box {
            background: #fff;
            border-radius: 18px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 8px 28px rgba(25, 48, 82, 0.07);
        }

        .error-box {
            color: #b42318;
        }

        .detail-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 10px 35px rgba(25, 48, 82, 0.08);
            padding: 30px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 330px 1fr;
            gap: 38px;
        }

        .cover-wrap {
            background: #eef3f8;
            border-radius: 18px;
            padding: 16px;
            min-height: 440px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cover {
            width: 100%;
            max-width: 300px;
            height: 420px;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            background: #e9eef5;
        }

        .book-content h1 {
            color: #132f55;
            font-size: 34px;
            line-height: 1.2;
            margin-bottom: 12px;
        }

        .author {
            color: #65748b;
            font-size: 17px;
            margin-bottom: 18px;
        }

        .status {
            display: inline-flex;
            padding: 7px 13px;
            border-radius: 999px;
            font-size: 13px;
            font-weight: 800;
            margin-bottom: 24px;
            background: #eef2f6;
            color: #536273;
        }

        .status.available {
            background: #e9f8ef;
            color: #16794c;
        }

        .status.borrowed {
            background: #fff1e8;
            color: #b54708;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 28px;
        }

        .info-item {
            background: #f7f9fc;
            border: 1px solid #edf0f5;
            border-radius: 12px;
            padding: 13px 15px;
        }

        .label {
            display: block;
            color: #78869a;
            font-size: 12px;
            margin-bottom: 5px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .value {
            color: #26374e;
            font-size: 14px;
            font-weight: 600;
            word-break: break-word;
        }

        .sinopsis {
            border-top: 1px solid #e8edf3;
            padding-top: 24px;
        }

        .sinopsis h2 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #173b68;
        }

        .sinopsis p {
            color: #5e6c7f;
            line-height: 1.75;
            white-space: pre-line;
        }

        .rak-box {
            margin-top: 16px;
            background: #f1f6fc;
            border: 1px solid #dce9f7;
            border-radius: 14px;
            padding: 15px;
            color: #35516f;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 26px;
            flex-wrap: wrap;
        }

        .btn {
            border: 0;
            border-radius: 11px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #164e8a;
            color: white;
        }

        .btn-primary:hover {
            background: #123e6e;
        }

        .btn-primary:disabled {
            opacity: .55;
            cursor: not-allowed;
        }

        .btn-secondary {
            background: #edf3f9;
            color: #164e8a;
        }

        .message {
            margin-top: 15px;
            padding: 12px 14px;
            border-radius: 10px;
            display: none;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.success {
            display: block;
            background: #eaf8ef;
            color: #176b45;
        }

        .message.error {
            display: block;
            background: #fff0ef;
            color: #b42318;
        }

        @media (max-width: 850px) {
            .detail-grid {
                grid-template-columns: 1fr;
            }

            .cover-wrap {
                min-height: auto;
            }

            .cover {
                max-width: 260px;
                height: 360px;
            }
        }

        @media (max-width: 600px) {
            .container {
                width: min(100% - 20px, 1180px);
            }

            main {
                padding-top: 20px;
            }

            .detail-card {
                padding: 18px;
                border-radius: 16px;
            }

            .book-content h1 {
                font-size: 26px;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .cover {
                height: 330px;
            }

            .topbar-inner {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>

<header class="topbar">
    <div class="container topbar-inner">
        <div class="brand">PUSTANKA</div>
        <a href="./" class="back-btn">← Kembali ke Buku</a>
    </div>
</header>

<main>
    <div class="container">
        <div id="loading" class="loading">
            Memuat detail buku...
        </div>

        <div id="errorBox" class="error-box" style="display:none;"></div>

        <div id="detailCard" class="detail-card" style="display:none;">
            <div class="detail-grid">

                <div class="cover-wrap">
                    <img id="bookCover" class="cover" alt="Cover buku">
                </div>

                <div class="book-content">
                    <h1 id="bookTitle">-</h1>
                    <p class="author" id="bookAuthor">-</p>

                    <span id="bookStatus" class="status">-</span>

                    <div class="info-grid">
                        <div class="info-item">
                            <span class="label">ISBN</span>
                            <span class="value" id="bookIsbn">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Barcode</span>
                            <span class="value" id="bookBarcode">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Penerbit</span>
                            <span class="value" id="bookPublisher">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Tahun Terbit</span>
                            <span class="value" id="bookYear">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Jenis Buku</span>
                            <span class="value" id="bookType">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Kategori</span>
                            <span class="value" id="bookCategory">-</span>
                        </div>

                        <div class="info-item">
                            <span class="label">Harga</span>
                            <span class="value" id="bookPrice">-</span>
                        </div>
                    </div>

                    <div class="rak-box">
                        <strong>Lokasi Buku</strong><br>
                        <span id="bookShelf">-</span>
                    </div>

                    <div class="sinopsis">
                        <h2>Sinopsis</h2>
                        <p id="bookSynopsis">Sinopsis belum tersedia.</p>
                    </div>

                    <div class="actions">
                        <button id="borrowBtn" class="btn btn-primary">
                            Pinjam Buku
                        </button>

                        <a href="./" class="btn btn-secondary">
                            Kembali
                        </a>
                    </div>

                    <div id="message" class="message"></div>
                </div>

            </div>
        </div>
    </div>
</main>

<script>
    const API_BASE = "../../../api";

    const params = new URLSearchParams(window.location.search);
    const idBook = params.get("id_book");

    const loading = document.getElementById("loading");
    const errorBox = document.getElementById("errorBox");
    const detailCard = document.getElementById("detailCard");
    const borrowBtn = document.getElementById("borrowBtn");
    const message = document.getElementById("message");

    function escapeText(value) {
        return value === null || value === undefined || value === ""
            ? "-"
            : String(value);
    }

    function formatRupiah(value) {
        const number = Number(value);

        if (Number.isNaN(number)) {
            return "-";
        }

        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0
        }).format(number);
    }

    function photo(value) {
        if (value) {
            return value;
        }

        return "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";
    }

    function showError(text) {
        loading.style.display = "none";
        detailCard.style.display = "none";
        errorBox.textContent = text;
        errorBox.style.display = "block";
    }

    function showMessage(text, type) {
        message.textContent = text;
        message.className = "message " + type;
    }

    async function loadDetail() {
        if (!idBook || !/^\d+$/.test(idBook) || Number(idBook) <= 0) {
            showError("ID buku tidak valid.");
            return;
        }

        try {
            const response = await fetch(
                API_BASE + "/books/detail.php?id=" + encodeURIComponent(idBook),
                {
                    method: "GET",
                    credentials: "include"
                }
            );

            const result = await response.json();

            if (response.status === 401) {
                window.location.href = "../../auth/login.php";
                return;
            }

            if (!response.ok || !result.status || !result.data) {
                showError(result.message || "Detail buku gagal dimuat.");
                return;
            }

            const book = result.data;

            document.getElementById("bookCover").src = photo(book.foto);
            document.getElementById("bookCover").onerror = function () {
                this.src = "https://ui-avatars.com/api/?name=Buku&background=e9eef5&color=164e8a&size=600";
            };

            document.getElementById("bookTitle").textContent = escapeText(book.judul);
            document.getElementById("bookAuthor").textContent =
                "Oleh " + escapeText(book.pengarang);

            document.getElementById("bookIsbn").textContent = escapeText(book.isbn);
            document.getElementById("bookBarcode").textContent = escapeText(book.barcode);
            document.getElementById("bookPublisher").textContent = escapeText(book.penerbit);
            document.getElementById("bookYear").textContent = escapeText(book.tahun_terbit);
            document.getElementById("bookType").textContent = escapeText(book.jenis_buku);
            document.getElementById("bookCategory").textContent =
                book.kategori?.nama_category || "-";
            document.getElementById("bookPrice").textContent = formatRupiah(book.harga);

            const rak = book.rak || {};

            let shelfText = [];

            if (rak.no_rak) {
                shelfText.push("Rak " + rak.no_rak);
            }

            if (rak.lantai) {
                shelfText.push("Lantai " + rak.lantai);
            }

            if (rak.keterangan) {
                shelfText.push(rak.keterangan);
            }

            document.getElementById("bookShelf").textContent =
                shelfText.length ? shelfText.join(" • ") : "Lokasi rak belum tersedia.";

            document.getElementById("bookSynopsis").textContent =
                book.sinopsis || "Sinopsis belum tersedia.";

            const status = String(book.status || "").toLowerCase();
            const statusElement = document.getElementById("bookStatus");

            statusElement.textContent = book.status || "Tidak diketahui";
            statusElement.className = "status";

            if (status === "tersedia") {
                statusElement.classList.add("available");
                borrowBtn.disabled = false;
                borrowBtn.textContent = "Pinjam Buku";
            } else {
                statusElement.classList.add("borrowed");
                borrowBtn.disabled = true;
                borrowBtn.textContent = "Buku Tidak Tersedia";
            }

            borrowBtn.dataset.idBook = book.id_book;

            loading.style.display = "none";
            errorBox.style.display = "none";
            detailCard.style.display = "block";

        } catch (error) {
            console.error(error);
            showError("Tidak dapat terhubung ke server.");
        }
    }

    async function borrowBook() {
        const id = borrowBtn.dataset.idBook;

        if (!id || borrowBtn.disabled) {
            return;
        }

        const confirmed = confirm(
            "Apakah kamu yakin ingin meminjam buku ini?"
        );

        if (!confirmed) {
            return;
        }

        borrowBtn.disabled = true;
        borrowBtn.textContent = "Memproses...";

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
                        id_book: Number(id)
                    })
                }
            );

            const result = await response.json();

            if (response.status === 401) {
                window.location.href = "../../auth/login.php";
                return;
            }

            if (!response.ok || !result.status) {
                showMessage(
                    result.message || "Buku gagal dipinjam.",
                    "error"
                );

                borrowBtn.disabled = false;
                borrowBtn.textContent = "📚 Pinjam Buku";
                return;
            }

            showMessage(
                result.message || "Buku berhasil dipinjam.",
                "success"
            );

            borrowBtn.disabled = true;
            borrowBtn.textContent = "Buku Sudah Dipinjam";

        } catch (error) {
            console.error(error);

            showMessage(
                "Tidak dapat terhubung ke server.",
                "error"
            );

            borrowBtn.disabled = false;
            borrowBtn.textContent = "📚 Pinjam Buku";
        }
    }

    borrowBtn.addEventListener("click", () => {
    const id = borrowBtn.dataset.idBook;

    if (!id || borrowBtn.disabled) {
        return;
    }

    window.location.href =
        "./scan.php?id_book=" +
        encodeURIComponent(id);
});

    loadDetail();
</script>

</body>
</html>
