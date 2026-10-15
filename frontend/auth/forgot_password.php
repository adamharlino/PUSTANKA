<?php
session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Lupa Password - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: linear-gradient(135deg, #eff6ff, #dbeafe);
            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 430px;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 35px;

            box-shadow:
                0 15px 40px rgba(15, 23, 42, 0.12);
        }

        .logo {
            width: 65px;
            height: 65px;

            margin: 0 auto 18px;

            border-radius: 16px;

            background: #123b70;
            color: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 30px;
        }

        .title {
            text-align: center;
            color: #123b70;
            font-size: 25px;
            font-weight: 700;
        }

        .subtitle {
            text-align: center;
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;

            margin-top: 8px;
            margin-bottom: 28px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;

            color: #334155;
            font-size: 14px;
            font-weight: 600;

            margin-bottom: 8px;
        }

        input {
            width: 100%;

            padding: 13px 14px;

            border: 1px solid #cbd5e1;
            border-radius: 10px;

            font-size: 14px;
            outline: none;

            transition: 0.2s;
        }

        input:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37, 99, 235, 0.10);
        }

        .btn {
            width: 100%;

            border: none;
            border-radius: 10px;

            padding: 13px;

            background: #2563eb;
            color: #ffffff;

            font-size: 14px;
            font-weight: 600;

            cursor: pointer;

            transition: 0.2s;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .back-login {
            display: block;

            text-align: center;

            margin-top: 20px;

            color: #2563eb;
            text-decoration: none;

            font-size: 14px;
            font-weight: 600;
        }

        .back-login:hover {
            text-decoration: underline;
        }

        .message {
            display: none;

            padding: 12px 14px;

            border-radius: 10px;

            font-size: 13px;
            line-height: 1.5;

            margin-bottom: 18px;
        }

        .message.error {
            display: block;

            background: #fef2f2;
            color: #b91c1c;

            border: 1px solid #fecaca;
        }

        .message.success {
            display: block;

            background: #f0fdf4;
            color: #166534;

            border: 1px solid #bbf7d0;
        }

        .token-box {
            display: none;

            margin-top: 20px;

            padding: 16px;

            background: #f8fafc;

            border: 1px solid #cbd5e1;
            border-radius: 12px;
        }

        .token-title {
            color: #334155;

            font-size: 13px;
            font-weight: 700;

            margin-bottom: 8px;
        }

        .token {
            word-break: break-all;

            padding: 12px;

            background: #ffffff;

            border: 1px dashed #94a3b8;
            border-radius: 8px;

            color: #123b70;

            font-family: monospace;
            font-size: 12px;

            line-height: 1.5;
        }

        .reset-button {
            display: none;

            width: 100%;

            margin-top: 12px;

            padding: 12px;

            border: none;
            border-radius: 10px;

            background: #123b70;
            color: #ffffff;

            text-align: center;
            text-decoration: none;

            font-size: 14px;
            font-weight: 600;
        }

        .reset-button:hover {
            background: #0f315d;
        }

        .development-note {
            margin-top: 18px;

            color: #64748b;

            text-align: center;

            font-size: 11px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

<div class="container">

    <div class="card">

        <div class="logo">
            📚
        </div>

        <h1 class="title">
            Lupa Password
        </h1>

        <p class="subtitle">
            Masukkan email yang terdaftar pada akun
            Pustanka untuk mendapatkan token reset password.
        </p>

        <div id="message" class="message"></div>

        <form id="forgotForm">

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email Anda"
                    autocomplete="email"
                    required
                >

            </div>

            <button
                type="submit"
                class="btn"
                id="submitButton"
            >
                Kirim Token Reset
            </button>

        </form>

        <div id="tokenBox" class="token-box">

            <div class="token-title">
                🔑 Token Reset Password
            </div>

            <div
                id="token"
                class="token"
            ></div>

            <a
                href="#"
                id="resetButton"
                class="reset-button"
            >
                Lanjut Reset Password
            </a>

        </div>

        <p class="development-note">
            Mode development: token ditampilkan untuk keperluan
            testing. Pada production token dikirim melalui email.
        </p>

        <a
            href="login.php"
            class="back-login"
        >
            ← Kembali ke Login
        </a>

    </div>

</div>


<script>

const forgotForm = document.getElementById("forgotForm");
const emailInput = document.getElementById("email");
const submitButton = document.getElementById("submitButton");

const message = document.getElementById("message");

const tokenBox = document.getElementById("tokenBox");
const tokenElement = document.getElementById("token");
const resetButton = document.getElementById("resetButton");


function showMessage(text, type) {

    message.textContent = text;

    message.className = "message " + type;

}


function hideMessage() {

    message.textContent = "";
    message.className = "message";

}


forgotForm.addEventListener("submit", async function(event) {

    event.preventDefault();

    hideMessage();

    tokenBox.style.display = "none";
    resetButton.style.display = "none";

    const email = emailInput.value.trim();

    if (!email) {

        showMessage(
            "Email wajib diisi.",
            "error"
        );

        return;
    }


    submitButton.disabled = true;

    submitButton.textContent = "Memproses...";


    try {

        /*
         * ==========================================
         * COBA USER TERLEBIH DAHULU
         * ==========================================
         */

        let response = await fetch(
            "../../api/users/forgot_password.php",
            {
                method: "POST",

                headers: {
                    "Content-Type": "application/json"
                },

                credentials: "include",

                body: JSON.stringify({
                    email: email
                })
            }
        );


        let result = await response.json();


        /*
         * ==========================================
         * JIKA BUKAN USER, COBA ADMIN
         * ==========================================
         */

        if (!response.ok || !result.status) {

            response = await fetch(
                "../../api/admins/forgot_password.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    credentials: "include",

                    body: JSON.stringify({
                        email: email
                    })
                }
            );


            result = await response.json();

        }


        /*
         * ==========================================
         * GAGAL
         * ==========================================
         */

        if (!response.ok || !result.status) {

            throw new Error(
                result.message ||
                "Gagal membuat token reset password."
            );

        }


        /*
         * ==========================================
         * BERHASIL
         * ==========================================
         */

        const resetToken =
            result.data?.reset_token || "";

        const expiresAt =
            result.data?.expires_at || "";


        showMessage(
            result.message ||
            "Token reset password berhasil dibuat.",
            "success"
        );


        if (resetToken) {

            tokenElement.textContent = resetToken;

            tokenBox.style.display = "block";

            resetButton.href =
                "reset_password.php?token=" +
                encodeURIComponent(resetToken);

            resetButton.style.display = "block";

        }


        if (expiresAt) {

            const expiryInfo =
                document.createElement("div");

            expiryInfo.style.marginTop = "8px";
            expiryInfo.style.fontSize = "11px";
            expiryInfo.style.color = "#64748b";

            expiryInfo.textContent =
                "Token berlaku sampai: " +
                expiresAt;

            tokenBox.appendChild(expiryInfo);

        }


    } catch (error) {

        showMessage(
            error.message ||
            "Terjadi kesalahan.",
            "error"
        );

    } finally {

        submitButton.disabled = false;

        submitButton.textContent =
            "Kirim Token Reset";

    }

});

</script>

</body>
</html>