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
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password Admin - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1e293b;
        }

        .container {
            max-width: 650px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 28px;
            color: #0f172a;
        }

        .back-btn {
            text-decoration: none;
            background: #0f172a;
            color: white;
            padding: 11px 18px;
            border-radius: 10px;
            font-size: 14px;
        }

        .card {
            background: white;
            border-radius: 18px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        .icon {
            width: 70px;
            height: 70px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #dbeafe;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
        }

        .description {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #334155;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            padding: 13px 48px 13px 14px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 18px;
            padding: 3px;
        }

        .hint {
            margin-top: 7px;
            color: #64748b;
            font-size: 12px;
        }

        .actions {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        button,
        .cancel-btn {
            flex: 1;
            border: none;
            padding: 13px 18px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
        }

        .cancel-btn {
            background: #e2e8f0;
            color: #334155;
        }

        .save-btn {
            background: #2563eb;
            color: white;
        }

        .save-btn:hover {
            background: #1d4ed8;
        }

        .save-btn:disabled {
            background: #93c5fd;
            cursor: not-allowed;
        }

        .message {
            display: none;
            padding: 14px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        .message.error {
            display: block;
            background: #fee2e2;
            color: #991b1b;
        }

        @media (max-width: 600px) {

            .topbar {
                flex-direction: column;
                align-items: stretch;
            }

            .back-btn {
                text-align: center;
            }

            .actions {
                flex-direction: column-reverse;
            }

            button,
            .cancel-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <div class="topbar">

        <h1>Ganti Password</h1>

        <a href="../profile/index.php" class="back-btn">
            ← Kembali
        </a>

    </div>

    <div id="message" class="message"></div>

    <div class="card">

        <div class="icon">
            🔐
        </div>

        <div class="description">
            Gunakan password yang kuat dan jangan membagikannya
            kepada orang lain.
        </div>

        <form id="passwordForm">

            <!-- Password Lama -->
            <div class="form-group">

                <label for="password_lama">
                    Password Lama
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="password_lama"
                        name="password_lama"
                        placeholder="Masukkan password lama"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password_lama"
                    >
                        👁️
                    </button>

                </div>

            </div>


            <!-- Password Baru -->
            <div class="form-group">

                <label for="password_baru">
                    Password Baru
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="password_baru"
                        name="password_baru"
                        placeholder="Masukkan password baru"
                        autocomplete="new-password"
                        minlength="8"
                        maxlength="72"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="password_baru"
                    >
                        👁️
                    </button>

                </div>

                <div class="hint">
                    Password harus terdiri dari 8–72 karakter.
                </div>

            </div>


            <!-- Konfirmasi Password -->
            <div class="form-group">

                <label for="konfirmasi_password">
                    Konfirmasi Password Baru
                </label>

                <div class="input-wrapper">

                    <input
                        type="password"
                        id="konfirmasi_password"
                        name="konfirmasi_password"
                        placeholder="Ulangi password baru"
                        autocomplete="new-password"
                        minlength="8"
                        maxlength="72"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="konfirmasi_password"
                    >
                        👁️
                    </button>

                </div>

            </div>


            <div class="actions">

                <a
                    href="../profile/index.php"
                    class="cancel-btn"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    class="save-btn"
                    id="saveButton"
                >
                    🔒 Simpan Password
                </button>

            </div>

        </form>

    </div>

</div>


<script>

const API_URL = "../../../api/admins/change_password.php";

const form = document.getElementById("passwordForm");
const message = document.getElementById("message");
const saveButton = document.getElementById("saveButton");


function showMessage(text, type) {

    message.textContent = text;
    message.className = "message " + type;

    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
}


// Toggle password visibility
document.querySelectorAll(".toggle-password").forEach(button => {

    button.addEventListener("click", function () {

        const targetId = this.dataset.target;
        const input = document.getElementById(targetId);

        if (input.type === "password") {

            input.type = "text";
            this.textContent = "🙈";

        } else {

            input.type = "password";
            this.textContent = "👁️";
        }

    });

});


form.addEventListener("submit", async function(event) {

    event.preventDefault();

    message.className = "message";
    message.textContent = "";

    const passwordLama =
        document.getElementById("password_lama").value;

    const passwordBaru =
        document.getElementById("password_baru").value;

    const konfirmasiPassword =
        document.getElementById("konfirmasi_password").value;


    // Validasi frontend
    if (passwordBaru.length < 8) {

        showMessage(
            "Password baru minimal 8 karakter.",
            "error"
        );

        return;
    }


    if (passwordBaru.length > 72) {

        showMessage(
            "Password baru maksimal 72 karakter.",
            "error"
        );

        return;
    }


    if (passwordBaru !== konfirmasiPassword) {

        showMessage(
            "Konfirmasi password baru tidak cocok.",
            "error"
        );

        return;
    }


    if (passwordLama === passwordBaru) {

        showMessage(
            "Password baru harus berbeda dari password lama.",
            "error"
        );

        return;
    }


    saveButton.disabled = true;
    saveButton.textContent = "Menyimpan...";


    try {

        const response = await fetch(API_URL, {

            method: "PUT",

            credentials: "include",

            headers: {
                "Content-Type": "application/json"
            },

            body: JSON.stringify({

                password_lama: passwordLama,

                password_baru: passwordBaru,

                konfirmasi_password: konfirmasiPassword

            })

        });


        const result = await response.json();


        if (!response.ok || !result.status) {

            throw new Error(
                result.message ||
                "Gagal mengganti password."
            );
        }


        showMessage(
            result.message ||
            "Password berhasil diubah.",
            "success"
        );


        form.reset();


        setTimeout(() => {

            window.location.href =
                "../profile/index.php";

        }, 1200);


    } catch (error) {

        showMessage(
            error.message ||
            "Terjadi kesalahan.",
            "error"
        );

        saveButton.disabled = false;

        saveButton.textContent =
            "🔒 Simpan Password";
    }

});

</script>

</body>
</html>