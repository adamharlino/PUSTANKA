<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pustanka</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }

        body {
            min-height: 100vh;
            background: #f4f7fb;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 950px;
            min-height: 560px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.10);
            display: flex;
        }

        /* =========================
           BAGIAN KIRI
        ========================= */

        .login-left {
            width: 50%;
            background: linear-gradient(135deg, #123c69, #1e6091);
            color: white;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .brand {
            font-size: 34px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .login-left h1 {
            font-size: 38px;
            line-height: 1.2;
            margin-bottom: 18px;
        }

        .login-left p {
            font-size: 16px;
            line-height: 1.7;
            opacity: 0.9;
            max-width: 380px;
        }

        /* =========================
           BAGIAN KANAN
        ========================= */

        .login-right {
            width: 50%;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-title {
            font-size: 30px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .login-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }

        .form-group input:focus {
            border-color: #1e6091;
            box-shadow: 0 0 0 3px rgba(30, 96, 145, 0.10);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 50px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #6b7280;
            font-size: 13px;
        }

        .forgot-password {
            display: block;
            text-align: right;
            margin-top: -8px;
            margin-bottom: 22px;
            color: #1e6091;
            text-decoration: none;
            font-size: 13px;
        }

        .forgot-password:hover {
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 10px;
            background: #1e6091;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
        }

        .login-button:hover {
            background: #164b72;
        }

        .login-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .message {
            display: none;
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
        }

        .message.error {
            display: block;
            background: #fee2e2;
            color: #b91c1c;
        }

        .message.success {
            display: block;
            background: #dcfce7;
            color: #166534;
        }

        .register-text {
            text-align: center;
            margin-top: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .register-text a {
            color: #1e6091;
            font-weight: 600;
            text-decoration: none;
        }

        .register-text a:hover {
            text-decoration: underline;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {
            .login-container {
                max-width: 500px;
                min-height: auto;
            }

            .login-left {
                display: none;
            }

            .login-right {
                width: 100%;
                padding: 40px 30px;
            }
        }
    </style>
</head>

<body>

<div class="login-container">

    <!-- =========================
         LEFT
    ========================= -->

    <div class="login-left">

        <div class="brand">
            Pustanka
        </div>

        <h1>
            Selamat Datang Kembali!
        </h1>

        <p>
            Masuk ke akun Pustanka untuk mengakses berbagai layanan
            perpustakaan dengan mudah dan nyaman.
        </p>

    </div>


    <!-- =========================
         RIGHT
    ========================= -->

    <div class="login-right">

        <h2 class="login-title">
            Login
        </h2>

        <p class="login-subtitle">
            Masukkan email dan password untuk melanjutkan.
        </p>

        <div id="message" class="message"></div>

        <form id="loginForm">

            <div class="form-group">
                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    placeholder="Masukkan email"
                    autocomplete="email"
                    required
                >
            </div>


            <div class="form-group">
                <label for="password">
                    Password
                </label>

                <div class="password-wrapper">

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        id="togglePassword"
                    >
                        Lihat
                    </button>

                </div>
            </div>


            <a href="forgot_password.php" class="forgot-password">
                Lupa password?
            </a>

            <button
                type="submit"
                class="login-button"
                id="loginButton"
            >
                Login
            </button>

        </form>


        <div class="register-text">
            Belum punya akun?
            <a href="../auth/register.php">
                Daftar sekarang
            </a>
        </div>

    </div>

</div>


<script>

    // =========================
    // TOGGLE PASSWORD
    // =========================

    const togglePassword =
        document.getElementById("togglePassword");

    const passwordInput =
        document.getElementById("password");

    togglePassword.addEventListener("click", function () {

        if (passwordInput.type === "password") {

            passwordInput.type = "text";
            togglePassword.textContent = "Sembunyikan";

        } else {

            passwordInput.type = "password";
            togglePassword.textContent = "Lihat";

        }

    });


    // =========================
    // LOGIN
    // =========================

    const loginForm =
        document.getElementById("loginForm");

    const loginButton =
        document.getElementById("loginButton");

    const message =
        document.getElementById("message");


    loginForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        message.className = "message";
        message.textContent = "";

        loginButton.disabled = true;
        loginButton.textContent = "Memproses...";


        const email =
            document.getElementById("email").value.trim();

        const password =
            document.getElementById("password").value;


        try {

            const response = await fetch(
                "../../api/auth/login.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    credentials: "include",

                    body: JSON.stringify({
                        email: email,
                        password: password
                    })
                }
            );


            const result = await response.json();


            if (!response.ok || !result.status) {

                message.className = "message error";
                message.textContent =
                    result.message ||
                    "Login gagal.";

                loginButton.disabled = false;
                loginButton.textContent = "Login";

                return;
            }


            message.className = "message success";
            message.textContent =
                "Login berhasil. Mengarahkan...";


            // =========================
            // REDIRECT BERDASARKAN ROLE
            // =========================

            if (result.role === "admin") {

                window.location.href =
                    "../admin/dashboard/";

            } else if (result.role === "user") {

                window.location.href =
                    "../user/dashboard/";

            } else {

                message.className = "message error";
                message.textContent =
                    "Role akun tidak dikenali.";

                loginButton.disabled = false;
                loginButton.textContent = "Login";
            }


        } catch (error) {

            console.error(error);

            message.className = "message error";
            message.textContent =
                "Terjadi kesalahan koneksi ke server.";

            loginButton.disabled = false;
            loginButton.textContent = "Login";
        }

    });

</script>

</body>
</html>