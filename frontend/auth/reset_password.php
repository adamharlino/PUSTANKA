<?php

session_set_cookie_params([
    "httponly" => true,
    "secure" => false,
    "samesite" => "Lax"
]);

session_start();

$token = trim($_GET["token"] ?? "");
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reset Password - Pustanka</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;

            min-height: 100vh;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 20px;

            background: linear-gradient(
                135deg,
                #eff6ff,
                #dbeafe
            );
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

        .input-wrapper {
            position: relative;
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
                0 0 0 3px rgba(
                    37,
                    99,
                    235,
                    0.10
                );
        }

        .token-input {
            font-family: monospace;

            font-size: 12px;

            word-break: break-all;
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

        .password-info {
            margin-top: 8px;

            color: #64748b;

            font-size: 11px;

            line-height: 1.5;
        }

        .success-box {
            display: none;

            text-align: center;

            padding: 10px 0;
        }

        .success-icon {
            font-size: 48px;

            margin-bottom: 12px;
        }

        .success-title {
            color: #166534;

            font-size: 20px;

            font-weight: 700;

            margin-bottom: 8px;
        }

        .success-text {
            color: #64748b;

            font-size: 14px;

            line-height: 1.6;

            margin-bottom: 20px;
        }

        .login-button {
            display: block;

            width: 100%;

            padding: 13px;

            border-radius: 10px;

            background: #123b70;

            color: #ffffff;

            text-decoration: none;

            font-size: 14px;

            font-weight: 600;
        }

        .login-button:hover {
            background: #0f315d;
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
            Reset Password
        </h1>

        <p class="subtitle">
            Buat password baru untuk akun
            Pustanka Anda.
        </p>

        <div
            id="message"
            class="message"
        ></div>


        <!-- ========================= -->
        <!-- FORM RESET PASSWORD -->
        <!-- ========================= -->

        <form id="resetForm">

            <div class="form-group">

                <label for="token">
                    Token Reset
                </label>

                <input
                    type="text"
                    id="token"
                    name="token"
                    class="token-input"
                    placeholder="Masukkan token reset"
                    value="<?php echo htmlspecialchars(
                        $token,
                        ENT_QUOTES,
                        "UTF-8"
                    ); ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password_baru">
                    Password Baru
                </label>

                <input
                    type="password"
                    id="password_baru"
                    name="password_baru"
                    placeholder="Masukkan password baru"
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                    required
                >

                <div class="password-info">
                    Password harus terdiri dari 8-72 karakter.
                </div>

            </div>


            <div class="form-group">

                <label for="konfirmasi_password">
                    Konfirmasi Password
                </label>

                <input
                    type="password"
                    id="konfirmasi_password"
                    name="konfirmasi_password"
                    placeholder="Ulangi password baru"
                    minlength="8"
                    maxlength="72"
                    autocomplete="new-password"
                    required
                >

            </div>


            <button
                type="submit"
                class="btn"
                id="submitButton"
            >
                Reset Password
            </button>

        </form>


        <!-- ========================= -->
        <!-- SUCCESS -->
        <!-- ========================= -->

        <div
            id="successBox"
            class="success-box"
        >

            <div class="success-icon">
                ✅
            </div>

            <div class="success-title">
                Password Berhasil Direset
            </div>

            <div class="success-text">
                Password Anda sudah berhasil diubah.
                Silakan login menggunakan password baru.
            </div>

            <a
                href="login.php"
                class="login-button"
            >
                Kembali ke Login
            </a>

        </div>


        <a
            href="login.php"
            class="back-login"
            id="backLogin"
        >
            ← Kembali ke Login
        </a>

    </div>

</div>


<script>

const resetForm =
    document.getElementById("resetForm");

const tokenInput =
    document.getElementById("token");

const passwordInput =
    document.getElementById("password_baru");

const confirmationInput =
    document.getElementById("konfirmasi_password");

const submitButton =
    document.getElementById("submitButton");

const message =
    document.getElementById("message");

const successBox =
    document.getElementById("successBox");

const backLogin =
    document.getElementById("backLogin");


function showMessage(text, type) {

    message.textContent = text;

    message.className =
        "message " + type;
}


function hideMessage() {

    message.textContent = "";

    message.className = "message";
}


resetForm.addEventListener(
    "submit",
    async function(event) {

        event.preventDefault();

        hideMessage();


        const token =
            tokenInput.value.trim();

        const password =
            passwordInput.value;

        const confirmation =
            confirmationInput.value;


        /*
         * ==============================
         * VALIDASI FRONTEND
         * ==============================
         */

        if (!token) {

            showMessage(
                "Token reset wajib diisi.",
                "error"
            );

            return;
        }


        if (password.length < 8) {

            showMessage(
                "Password minimal 8 karakter.",
                "error"
            );

            return;
        }


        if (password.length > 72) {

            showMessage(
                "Password maksimal 72 karakter.",
                "error"
            );

            return;
        }


        if (password !== confirmation) {

            showMessage(
                "Konfirmasi password tidak cocok.",
                "error"
            );

            return;
        }


        submitButton.disabled = true;

        submitButton.textContent =
            "Memproses...";


        try {

            /*
             * =================================
             * COBA RESET USER TERLEBIH DAHULU
             * =================================
             */

            let response = await fetch(
                "../../api/users/reset_password.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type":
                            "application/json"
                    },

                    credentials: "include",

                    body: JSON.stringify({
                        token: token,
                        password_baru: password,
                        konfirmasi_password:
                            confirmation
                    })
                }
            );


            let result =
                await response.json();


            /*
             * =================================
             * JIKA BUKAN USER,
             * COBA RESET ADMIN
             * =================================
             */

            if (
                !response.ok ||
                !result.status
            ) {

                response = await fetch(
                    "../../api/admins/reset_password.php",
                    {
                        method: "POST",

                        headers: {
                            "Content-Type":
                                "application/json"
                        },

                        credentials: "include",

                        body: JSON.stringify({
                            token: token,
                            password_baru:
                                password,
                            konfirmasi_password:
                                confirmation
                        })
                    }
                );


                result =
                    await response.json();
            }


            /*
             * =================================
             * GAGAL
             * =================================
             */

            if (
                !response.ok ||
                !result.status
            ) {

                throw new Error(
                    result.message ||
                    "Gagal mereset password."
                );
            }


            /*
             * =================================
             * BERHASIL
             * =================================
             */

            showMessage(
                result.message ||
                "Password berhasil direset.",
                "success"
            );


            resetForm.style.display =
                "none";

            successBox.style.display =
                "block";

            backLogin.style.display =
                "none";


        } catch (error) {

            showMessage(
                error.message ||
                "Terjadi kesalahan.",
                "error"
            );


            submitButton.disabled =
                false;

            submitButton.textContent =
                "Reset Password";
        }

    }
);

</script>

</body>

</html>