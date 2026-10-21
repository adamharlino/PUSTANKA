<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Pustanka</title>

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
            padding: 30px 20px;
        }

        .register-container {
            width: 100%;
            max-width: 950px;
            background: #ffffff;
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.10);
            display: flex;
        }

        /* =========================
           BAGIAN KIRI
        ========================= */

        .register-left {
            width: 40%;
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

        .register-left h1 {
            font-size: 36px;
            line-height: 1.2;
            margin-bottom: 18px;
        }

        .register-left p {
            font-size: 15px;
            line-height: 1.7;
            opacity: 0.9;
        }

        /* =========================
           BAGIAN KANAN
        ========================= */

        .register-right {
            width: 60%;
            padding: 45px 50px;
        }

        .register-title {
            font-size: 30px;
            color: #1f2937;
            margin-bottom: 8px;
        }

        .register-subtitle {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .message {
            display: none;
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 8px;
            font-size: 14px;
            line-height: 1.5;
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

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-group {
            flex: 1;
            margin-bottom: 17px;
        }

        .form-group label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 9px;
            font-size: 14px;
            outline: none;
            transition: 0.2s;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #1e6091;
            box-shadow: 0 0 0 3px rgba(30, 96, 145, 0.10);
        }

        .password-wrapper {
            position: relative;
        }

        .password-wrapper input {
            padding-right: 65px;
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #6b7280;
            font-size: 12px;
        }

        .register-button {
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
            margin-top: 5px;
        }

        .register-button:hover {
            background: #164b72;
        }

        .register-button:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }

        .login-text {
            text-align: center;
            margin-top: 20px;
            color: #6b7280;
            font-size: 14px;
        }

        .login-text a {
            color: #1e6091;
            font-weight: 600;
            text-decoration: none;
        }

        .login-text a:hover {
            text-decoration: underline;
        }

        .required {
            color: #dc2626;
        }

        /* =========================
           RESPONSIVE
        ========================= */

        @media (max-width: 768px) {

            .register-container {
                max-width: 550px;
            }

            .register-left {
                display: none;
            }

            .register-right {
                width: 100%;
                padding: 35px 25px;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>

<div class="register-container">

    <!-- =========================
         LEFT
    ========================= -->

    <div class="register-left">

        <div class="brand">
            Pustanka
        </div>

        <h1>
            Bergabung Bersama Kami
        </h1>

        <p>
            Buat akun Pustanka dan nikmati berbagai layanan
            perpustakaan dengan lebih mudah.
        </p>

    </div>


    <!-- =========================
         RIGHT
    ========================= -->

    <div class="register-right">

        <h2 class="register-title">
            Buat Akun
        </h2>

        <p class="register-subtitle">
            Lengkapi data berikut untuk membuat akun pengguna.
        </p>

        <div id="message" class="message"></div>


        <form id="registerForm">

            <!-- NAMA + NIK -->

            <div class="form-row">

                <div class="form-group">
                    <label for="nama">
                        Nama Lengkap <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        placeholder="Masukkan nama lengkap"
                        maxlength="100"
                        required
                    >
                </div>


                <div class="form-group">
                    <label for="nik">
                        NIK <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="nik"
                        name="nik"
                        placeholder="16 digit NIK"
                        maxlength="16"
                        inputmode="numeric"
                        required
                    >
                </div>

            </div>


            <!-- ALAMAT -->

            <div class="form-group">

                <label for="alamat">
                    Alamat <span class="required">*</span>
                </label>

                <textarea
                    id="alamat"
                    name="alamat"
                    placeholder="Masukkan alamat lengkap"
                    maxlength="255"
                    required
                ></textarea>

            </div>


            <!-- TELEPON + EMAIL -->

            <div class="form-row">

                <div class="form-group">

                    <label for="no_telp">
                        No. Telepon <span class="required">*</span>
                    </label>

                    <input
                        type="tel"
                        id="no_telp"
                        name="no_telp"
                        placeholder="Contoh: 081234567890"
                        maxlength="15"
                        inputmode="tel"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="email">
                        Email <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="nama@email.com"
                        maxlength="100"
                        autocomplete="email"
                        required
                    >

                </div>

            </div>


            <!-- PASSWORD -->

            <div class="form-row">

                <div class="form-group">

                    <label for="password">
                        Password <span class="required">*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Minimal 8 karakter"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="password"
                        >
                            Lihat
                        </button>

                    </div>

                </div>


                <div class="form-group">

                    <label for="konfirmasi_password">
                        Konfirmasi Password <span class="required">*</span>
                    </label>

                    <div class="password-wrapper">

                        <input
                            type="password"
                            id="konfirmasi_password"
                            name="konfirmasi_password"
                            placeholder="Ulangi password"
                            maxlength="72"
                            autocomplete="new-password"
                            required
                        >

                        <button
                            type="button"
                            class="toggle-password"
                            data-target="konfirmasi_password"
                        >
                            Lihat
                        </button>

                    </div>

                </div>

            </div>


            <button
                type="submit"
                class="register-button"
                id="registerButton"
            >
                Daftar
            </button>

        </form>


        <div class="login-text">
            Sudah punya akun?
            <a href="login.php">
                Login sekarang
            </a>
        </div>

    </div>

</div>


<script>

    // =========================
    // TOGGLE PASSWORD
    // =========================

    document.querySelectorAll(".toggle-password").forEach(button => {

        button.addEventListener("click", function () {

            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);

            if (input.type === "password") {

                input.type = "text";
                this.textContent = "Sembunyikan";

            } else {

                input.type = "password";
                this.textContent = "Lihat";

            }

        });

    });


    // =========================
    // REGISTER
    // =========================

    const registerForm =
        document.getElementById("registerForm");

    const registerButton =
        document.getElementById("registerButton");

    const message =
        document.getElementById("message");


    registerForm.addEventListener("submit", async function (event) {

        event.preventDefault();

        message.className = "message";
        message.textContent = "";

        registerButton.disabled = true;
        registerButton.textContent = "Mendaftarkan...";


        const nama =
            document.getElementById("nama").value.trim();

        const nik =
            document.getElementById("nik").value.trim();

        const alamat =
            document.getElementById("alamat").value.trim();

        const no_telp =
            document.getElementById("no_telp").value.trim();

        const email =
            document.getElementById("email").value.trim();

        const password =
            document.getElementById("password").value;

        const konfirmasi_password =
            document.getElementById("konfirmasi_password").value;


        // =========================
        // VALIDASI FRONTEND
        // =========================

        if (!/^\d{16}$/.test(nik)) {

            message.className = "message error";
            message.textContent =
                "NIK harus terdiri dari 16 digit angka.";

            registerButton.disabled = false;
            registerButton.textContent = "Daftar";

            return;
        }


        if (password.length < 8) {

            message.className = "message error";
            message.textContent =
                "Password minimal 8 karakter.";

            registerButton.disabled = false;
            registerButton.textContent = "Daftar";

            return;
        }


        if (password !== konfirmasi_password) {

            message.className = "message error";
            message.textContent =
                "Konfirmasi password tidak sesuai.";

            registerButton.disabled = false;
            registerButton.textContent = "Daftar";

            return;
        }


        try {

            const response = await fetch(
                "../../api/auth/register.php",
                {
                    method: "POST",

                    headers: {
                        "Content-Type": "application/json"
                    },

                    credentials: "include",

                    body: JSON.stringify({
                        nama: nama,
                        nik: nik,
                        alamat: alamat,
                        no_telp: no_telp,
                        email: email,
                        password: password,
                        konfirmasi_password: konfirmasi_password
                    })
                }
            );


            const result = await response.json();


            if (!response.ok || !result.status) {

                message.className = "message error";

                message.textContent =
                    result.message ||
                    "Pendaftaran gagal.";

                registerButton.disabled = false;
                registerButton.textContent = "Daftar";

                return;
            }


            // =========================
            // BERHASIL
            // =========================

            message.className = "message success";

            message.textContent =
                result.message ||
                "Registrasi berhasil. Silakan login.";


            registerForm.reset();


            // Tunggu sebentar agar user melihat pesan
            setTimeout(() => {

                window.location.href = "login.php";

            }, 1500);


        } catch (error) {

            console.error(error);

            message.className = "message error";

            message.textContent =
                "Terjadi kesalahan koneksi ke server.";

            registerButton.disabled = false;
            registerButton.textContent = "Daftar";

        }

    });

</script>

</body>
</html>