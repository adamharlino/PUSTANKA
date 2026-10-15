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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Laporan Kehadiran - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .layout {
            display: flex;
            min-height: 100vh;
        }

        /* =========================
           CONTENT
        ========================= */

        .content {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .header {
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 7px;
        }

        .header p {
            color: #64748b;
        }

        /* =========================
           BACK BUTTON
        ========================= */

        .back-btn-wrapper {
            margin-bottom: 20px;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            background: #e2e8f0;
            color: #334155;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-back:hover {
            background: #cbd5e1;
        }

        /* =========================
           FILTER
        ========================= */

        .filter-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
        }

        .filter-card h3 {
            margin-bottom: 15px;
            font-size: 17px;
        }

        .filter-form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        .form-group input {
            width: 190px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
        }

        .form-group input:focus {
            border-color: #2563eb;
        }

        .btn {
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
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

        .btn-reset {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-reset:hover {
            background: #cbd5e1;
        }

        /* =========================
           SUMMARY
        ========================= */

        .summary {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
            margin-bottom: 20px;
        }

        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
        }

        .summary-card .label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .summary-card .value {
            font-size: 27px;
            font-weight: bold;
            color: #0f172a;
        }

        /* =========================
           TABLE
        ========================= */

        .table-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(15, 23, 42, .05);
        }

        .table-header {
            padding: 20px;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-header h3 {
            font-size: 18px;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            white-space: nowrap;
        }

        th {
            background: #f8fafc;
            color: #475569;
            font-size: 13px;
        }

        td {
            font-size: 14px;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        .member-code {
            font-weight: 600;
            color: #2563eb;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #64748b;
        }

        /* =========================
           PAGINATION
        ========================= */

        .pagination {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            gap: 15px;
        }

        .pagination-info {
            color: #64748b;
            font-size: 14px;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
        }

        .page-btn {
            border: 1px solid #cbd5e1;
            background: white;
            padding: 8px 13px;
            border-radius: 7px;
            cursor: pointer;
        }

        .page-btn:hover:not(:disabled) {
            background: #f1f5f9;
        }

        .page-btn:disabled {
            opacity: .5;
            cursor: not-allowed;
        }

        /* =========================
           MESSAGE
        ========================= */

        .message {
            display: none;
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .message.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
        }

        .message.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 900px) {
            .summary {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 700px) {
            .content {
                padding: 20px;
            }

            .filter-form {
                flex-direction: column;
                align-items: stretch;
            }

            .form-group input {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- =========================
         CONTENT
    ========================= -->

    <main class="content">

        <!-- TOMBOL KEMBALI -->
        <div class="back-btn-wrapper">
            <a href="./dashboard/summary.php" class="btn-back">
                ← Kembali ke Ringkasan laporan
            </a>
        </div>

        <div class="header">

            <h1>Laporan Kehadiran</h1>

            <p>
                Monitoring riwayat kehadiran pengguna perpustakaan.
            </p>

        </div>


        <div id="message" class="message"></div>


        <!-- =========================
             FILTER
        ========================= -->

        <div class="filter-card">

            <h3>Filter Laporan</h3>

            <div class="filter-form">

                <div class="form-group">

                    <label for="tanggalMulai">
                        Tanggal Mulai
                    </label>

                    <input
                        type="date"
                        id="tanggalMulai"
                    >

                </div>


                <div class="form-group">

                    <label for="tanggalSelesai">
                        Tanggal Selesai
                    </label>

                    <input
                        type="date"
                        id="tanggalSelesai"
                    >

                </div>


                <button
                    type="button"
                    class="btn btn-primary"
                    id="filterButton"
                >
                    Filter
                </button>


                <button
                    type="button"
                    class="btn btn-reset"
                    id="resetButton"
                >
                    Reset
                </button>

            </div>

        </div>


        <!-- =========================
             SUMMARY
        ========================= -->

        <div class="summary">

            <div class="summary-card">

                <div class="label">
                    Total Kehadiran
                </div>

                <div
                    class="value"
                    id="totalAttendance"
                >
                    0
                </div>

            </div>


            <div class="summary-card">

                <div class="label">
                    Sudah Check-out
                </div>

                <div
                    class="value"
                    id="checkedOut"
                >
                    0
                </div>

            </div>


            <div class="summary-card">

                <div class="label">
                    Masih Hadir
                </div>

                <div
                    class="value"
                    id="stillPresent"
                >
                    0
                </div>

            </div>

        </div>


        <!-- =========================
             TABLE
        ========================= -->

        <div class="table-card">

            <div class="table-header">

                <h3>
                    Data Kehadiran
                </h3>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>ID Kehadiran</th>
                            
                            <th>ID Anggota</th>

                            <th>Member Code</th>

                            <th>Nama Anggota</th>

                            <th>Tanggal</th>

                            <th>Jam Kedatangan</th>

                            <th>Jam Kepulangan</th>

                        </tr>

                    </thead>

                    <tbody id="attendanceTable">

                        <tr>
                            <td
                                colspan="7"
                                class="empty"
                            >
                                Memuat data...
                            </td>
                        </tr>

                    </tbody>

                </table>

            </div>


            <!-- =========================
                 PAGINATION
            ========================= -->

            <div class="pagination">

                <div
                    class="pagination-info"
                    id="paginationInfo"
                >
                    -
                </div>


                <div class="pagination-buttons">

                    <button
                        type="button"
                        class="page-btn"
                        id="prevButton"
                    >
                        ← Sebelumnya
                    </button>

                    <button
                        type="button"
                        class="page-btn"
                        id="nextButton"
                    >
                        Berikutnya →
                    </button>

                </div>

            </div>

        </div>

    </main>

</div>


<script>

    const API_URL =
        "../../../api/reports/attendance.php";


    let currentPage = 1;

    const limit = 10;


    const attendanceTable =
        document.getElementById("attendanceTable");

    const paginationInfo =
        document.getElementById("paginationInfo");

    const prevButton =
        document.getElementById("prevButton");

    const nextButton =
        document.getElementById("nextButton");

    const message =
        document.getElementById("message");


    const tanggalMulai =
        document.getElementById("tanggalMulai");

    const tanggalSelesai =
        document.getElementById("tanggalSelesai");


    const totalAttendance =
        document.getElementById("totalAttendance");

    const checkedOut =
        document.getElementById("checkedOut");

    const stillPresent =
        document.getElementById("stillPresent");


    function showError(text) {

        message.className = "message error";

        message.textContent = text;

        message.style.display = "block";

    }


    function hideMessage() {

        message.style.display = "none";

        message.textContent = "";

    }


    function escapeHtml(value) {

        if (
            value === null ||
            value === undefined
        ) {
            return "-";
        }

        return String(value)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");

    }


    function formatDate(date) {

        if (!date) {
            return "-";
        }

        const parts =
            String(date).split("-");

        if (parts.length !== 3) {
            return date;
        }

        return (
            parts[2] +
            "/" +
            parts[1] +
            "/" +
            parts[0]
        );

    }


    function getAttendanceData(item) {

        const user =
            item.user ||
            item.pengguna ||
            item.peminjam ||
            {};

        return {

            idAttendance:
                item.id_attendance ??
                item.id_kehadiran ??
                "-",

            idUser:
                item.id_user ??
                "-",

            memberCode:
                item.member_code ??
                user.member_code ??
                "-",

            nama:
                item.nama_user ??
                user.nama_user ??
                "-",

            tanggal:
                item.tanggal ??
                "-",

            jamKedatangan:
                item.jam_kedatangan ??
                "-",

            jamKepulangan:
                item.jam_kepulangan ??
                "-"

        };

    }


    function renderTable(data) {

        if (
            !Array.isArray(data) ||
            data.length === 0
        ) {

            attendanceTable.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="empty"
                    >
                        Tidak ada data kehadiran.
                    </td>
                </tr>
            `;

            return;
        }


        attendanceTable.innerHTML =
            data.map((item, index) => {

                const attendance =
                    getAttendanceData(item);

                const no =
                    ((currentPage - 1) * limit)
                    + index
                    + 1;

                return `
                    <tr>

                        <td>
                            ${no}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.idAttendance
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.idUser
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.memberCode
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.nama
                            )}
                        </td>

                        <td>
                            ${formatDate(
                                attendance.tanggal
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.jamKedatangan
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                attendance.jamKepulangan
                            )}
                        </td>

                    </tr>
                `;

            }).join("");

    }


    function updateSummary(data, pagination) {

        const rows =
            Array.isArray(data)
                ? data
                : [];


        const total =
            pagination?.total ??
            pagination?.total_data ??
            rows.length;


        let checkout = 0;

        let present = 0;


        rows.forEach(item => {

            const attendance =
                getAttendanceData(item);

            if (
                attendance.jamKepulangan &&
                attendance.jamKepulangan !== "-"
            ) {

                checkout++;

            } else {

                present++;

            }

        });


        totalAttendance.textContent =
            total;

        checkedOut.textContent =
            checkout;

        stillPresent.textContent =
            present;

    }


    function updatePagination(pagination) {

        const total =
            Number(
                pagination?.total ??
                pagination?.total_data ??
                0
            );

        const page =
            Number(
                pagination?.page ??
                currentPage
            );

        const perPage =
            Number(
                pagination?.limit ??
                limit
            );

        const totalPages =
            Number(
                pagination?.total_pages ??
                Math.ceil(total / perPage)
            );


        currentPage = page;


        paginationInfo.textContent =
            total > 0
                ? `Halaman ${page} dari ${totalPages} • Total ${total} data`
                : "Tidak ada data";


        prevButton.disabled =
            currentPage <= 1;


        nextButton.disabled =
            currentPage >= totalPages;

    }


    async function loadAttendance() {

        hideMessage();


        attendanceTable.innerHTML = `
            <tr>
                <td
                    colspan="7"
                    class="empty"
                >
                    Memuat data...
                </td>
            </tr>
        `;


        try {

            const params =
                new URLSearchParams();


            params.set(
                "page",
                currentPage
            );


            params.set(
                "limit",
                limit
            );


            if (
                tanggalMulai.value
            ) {

                params.set(
                    "tanggal_mulai",
                    tanggalMulai.value
                );

            }


            if (
                tanggalSelesai.value
            ) {

                params.set(
                    "tanggal_selesai",
                    tanggalSelesai.value
                );

            }


            const response =
                await fetch(
                    `${API_URL}?${params.toString()}`,
                    {
                        method: "GET",
                        credentials: "include"
                    }
                );


            if (
                response.status === 401
            ) {

                window.location.href =
                    "../../auth/login.php";

                return;

            }


            const contentType =
                response.headers.get(
                    "content-type"
                ) || "";


            if (
                !contentType.includes(
                    "application/json"
                )
            ) {

                throw new Error(
                    "API laporan kehadiran tidak mengembalikan JSON. Periksa URL API."
                );

            }


            const result =
                await response.json();


            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal mengambil laporan kehadiran."
                );

            }


            const data =
                Array.isArray(result.data)
                    ? result.data
                    : [];


            const pagination =
                result.pagination || {};


            renderTable(data);

            updateSummary(
                data,
                pagination
            );

            updatePagination(
                pagination
            );


        } catch (error) {

            console.error(error);

            attendanceTable.innerHTML = `
                <tr>
                    <td
                        colspan="7"
                        class="empty"
                    >
                        Gagal memuat data.
                    </td>
                </tr>
            `;

            showError(
                error.message ||
                "Terjadi kesalahan saat mengambil data."
            );

        }

    }


    document
        .getElementById("filterButton")
        .addEventListener(
            "click",
            () => {

                if (
                    tanggalMulai.value &&
                    tanggalSelesai.value &&
                    tanggalMulai.value >
                    tanggalSelesai.value
                ) {

                    showError(
                        "Tanggal mulai tidak boleh lebih besar dari tanggal selesai."
                    );

                    return;

                }


                currentPage = 1;

                loadAttendance();

            }
        );


    document
        .getElementById("resetButton")
        .addEventListener(
            "click",
            () => {

                tanggalMulai.value = "";

                tanggalSelesai.value = "";

                currentPage = 1;

                loadAttendance();

            }
        );


    prevButton.addEventListener(
        "click",
        () => {

            if (
                currentPage > 1
            ) {

                currentPage--;

                loadAttendance();

            }

        }
    );


    nextButton.addEventListener(
        "click",
        () => {

            if (
                !nextButton.disabled
            ) {

                currentPage++;

                loadAttendance();

            }

        }
    );


    loadAttendance();

</script>

</body>
</html>