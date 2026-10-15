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

    <title>Laporan Pengguna - Pustanka</title>

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

        .form-group input,
        .form-group select {
            width: 200px;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus {
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
            min-width: 1050px;
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

        .status {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-aktif {
            background: #dcfce7;
            color: #166534;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-lain {
            background: #e2e8f0;
            color: #475569;
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

            .form-group input,
            .form-group select {
                width: 100%;
            }
        }
    
        /* CETAK / SIMPAN SEBAGAI PDF */
        .pdf-download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border: 0;
            border-radius: 8px;
            background: #173b70;
            color: #fff;
            font-weight: 600;
            cursor: pointer;
            margin: 8px 0 18px;
        }
        .pdf-download-btn:hover { background: #24539a; }
        @media print {
            @page { size: A4 landscape; margin: 12mm; }
            body { background: #fff !important; color: #111 !important; }
            .sidebar, aside, .back-btn-wrapper, .filter-card, .pdf-download-btn,
            .logout-btn, nav, .pagination, .filter-actions, .filter-buttons,
            button, input, select { display: none !important; }
            .layout, .main, .content, main { display: block !important; width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
            .header { margin: 0 0 14px !important; padding: 0 0 10px !important; box-shadow: none !important; border-bottom: 1px solid #999; }
            table { width: 100% !important; border-collapse: collapse !important; font-size: 9pt !important; }
            thead { display: table-header-group; }
            tr { break-inside: avoid; page-break-inside: avoid; }
            th, td { border: 1px solid #999 !important; color: #111 !important; padding: 5px !important; }
            .error, .message { display: none !important; }
            canvas, svg { max-width: 100% !important; }
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
                ← Kembali ke Ringkasan Laporan
            </a>
        </div>

        <div class="header">

            <h1>Laporan Pengguna</h1>

            <p>
                Monitoring data anggota yang terdaftar di perpustakaan.
            </p>

        </div>

        <button type="button" class="pdf-download-btn" onclick="window.print()">⬇ Cetak / Simpan PDF</button>


        <div id="message" class="message"></div>


        <!-- =========================
             FILTER
        ========================= -->

        <div class="filter-card">

            <h3>Filter Laporan</h3>

            <div class="filter-form">

                <div class="form-group">

                    <label for="search">
                        Cari Pengguna
                    </label>

                    <input
                        type="text"
                        id="search"
                        placeholder="Nama / NIK / Member Code"
                    >

                </div>


                <div class="form-group">

                    <label for="status">
                        Status
                    </label>

                    <select id="status">

                        <option value="">
                            Semua Status
                        </option>

                        <option value="aktif">
                            Aktif
                        </option>

                        <option value="nonaktif">
                            Nonaktif
                        </option>

                    </select>

                </div>


                <button
                    type="button"
                    class="btn btn-primary"
                    id="filterButton"
                >
                    Terapkan
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
                    Total Pengguna
                </div>

                <div
                    class="value"
                    id="totalUsers"
                >
                    0
                </div>

            </div>


            <div class="summary-card">

                <div class="label">
                    Pengguna Aktif
                </div>

                <div
                    class="value"
                    id="activeUsers"
                >
                    0
                </div>

            </div>


            <div class="summary-card">

                <div class="label">
                    Pengguna Nonaktif
                </div>

                <div
                    class="value"
                    id="inactiveUsers"
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
                    Data Pengguna
                </h3>

            </div>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>No</th>

                            <th>ID User</th>

                            <th>Member Code</th>

                            <th>Nama</th>

                            <th>NIK</th>

                            <th>Email</th>

                            <th>No. Telepon</th>

                            <th>Status</th>

                            <th>Tanggal Daftar</th>

                        </tr>

                    </thead>

                    <tbody id="usersTable">

                        <tr>

                            <td
                                colspan="9"
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
        "../../../api/reports/users.php";


    let currentPage = 1;

    const limit = 10;


    const usersTable =
        document.getElementById("usersTable");

    const paginationInfo =
        document.getElementById("paginationInfo");

    const prevButton =
        document.getElementById("prevButton");

    const nextButton =
        document.getElementById("nextButton");

    const message =
        document.getElementById("message");


    const searchInput =
        document.getElementById("search");

    const statusInput =
        document.getElementById("status");


    const totalUsers =
        document.getElementById("totalUsers");

    const activeUsers =
        document.getElementById("activeUsers");

    const inactiveUsers =
        document.getElementById("inactiveUsers");


    function showError(text) {

        message.className =
            "message error";

        message.textContent =
            text;

        message.style.display =
            "block";

    }


    function hideMessage() {

        message.style.display =
            "none";

        message.textContent =
            "";

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


    function getUserData(item) {

        return {

            idUser:
                item.id_user ??
                item.id ??
                "-",

            memberCode:
                item.member_code ??
                "-",

            nama:
                item.nama ??
                "-",

            nik:
                item.nik ??
                "-",

            email:
                item.email ??
                "-",

            noTelp:
                item.no_telp ??
                item.no_telepon ??
                "-",

            status:
                item.status ??
                "-",

            createdAt:
                item.created_at ??
                item.tanggal_daftar ??
                "-"

        };

    }


    function statusBadge(status) {

        const normalized =
            String(status)
                .toLowerCase()
                .trim();


        let className =
            "status status-lain";


        if (
            normalized === "aktif" ||
            normalized === "active"
        ) {

            className =
                "status status-aktif";

        } else if (
            normalized === "nonaktif" ||
            normalized === "inactive"
        ) {

            className =
                "status status-nonaktif";

        }


        return `
            <span class="${className}">
                ${escapeHtml(status)}
            </span>
        `;

    }


    function renderTable(data) {

        if (
            !Array.isArray(data) ||
            data.length === 0
        ) {

            usersTable.innerHTML = `
                <tr>

                    <td
                        colspan="9"
                        class="empty"
                    >
                        Tidak ada data pengguna.
                    </td>

                </tr>
            `;

            return;

        }


        usersTable.innerHTML =
            data.map((item, index) => {

                const user =
                    getUserData(item);


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
                                user.idUser
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                user.memberCode
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                user.nama
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                user.nik
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                user.email
                            )}
                        </td>

                        <td>
                            ${escapeHtml(
                                user.noTelp
                            )}
                        </td>

                        <td>
                            ${statusBadge(
                                user.status
                            )}
                        </td>

                        <td>
                            ${formatDate(
                                user.createdAt
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
            Number(
                pagination?.total ??
                pagination?.total_data ??
                rows.length
            );


        let active = 0;

        let inactive = 0;


        rows.forEach(item => {

            const user =
                getUserData(item);


            const status =
                String(user.status)
                    .toLowerCase()
                    .trim();


            if (
                status === "aktif" ||
                status === "active"
            ) {

                active++;

            } else if (
                status === "nonaktif" ||
                status === "inactive"
            ) {

                inactive++;

            }

        });


        totalUsers.textContent =
            total;

        activeUsers.textContent =
            active;

        inactiveUsers.textContent =
            inactive;

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
                Math.ceil(
                    total / perPage
                )
            );


        currentPage =
            page;


        paginationInfo.textContent =
            total > 0
                ? `Halaman ${page} dari ${totalPages} • Total ${total} data`
                : "Tidak ada data";


        prevButton.disabled =
            currentPage <= 1;


        nextButton.disabled =
            currentPage >= totalPages;

    }


    async function loadUsers() {

        hideMessage();


        usersTable.innerHTML = `
            <tr>

                <td
                    colspan="9"
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
                searchInput.value.trim()
            ) {

                params.set(
                    "search",
                    searchInput.value.trim()
                );

            }


            if (
                statusInput.value
            ) {

                params.set(
                    "status",
                    statusInput.value
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
                    "API laporan pengguna tidak mengembalikan JSON. Periksa URL API."
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
                    "Gagal mengambil laporan pengguna."
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


            usersTable.innerHTML = `
                <tr>

                    <td
                        colspan="9"
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

                currentPage = 1;

                loadUsers();

            }
        );


    document
        .getElementById("resetButton")
        .addEventListener(
            "click",
            () => {

                searchInput.value =
                    "";

                statusInput.value =
                    "";

                currentPage = 1;

                loadUsers();

            }
        );


    searchInput.addEventListener(
        "keydown",
        event => {

            if (
                event.key === "Enter"
            ) {

                currentPage = 1;

                loadUsers();

            }

        }
    );


    prevButton.addEventListener(
        "click",
        () => {

            if (
                currentPage > 1
            ) {

                currentPage--;

                loadUsers();

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

                loadUsers();

            }

        }
    );


    loadUsers();

</script>

</body>
</html>