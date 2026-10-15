<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Ganti Password - Pustanka</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #1f2937;
            min-height: 100vh;
        }

        .container {
            max-width: 620px;
            margin: 0 auto;
            padding: 40px 20px;
        }

        .back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            color: #2563eb;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 24px;
        }

        .back:hover {
            color: #1d4ed8;
        }

        .card {
            background: #ffffff;
            border-radius: 18px;
            padding: 32px;
            box-shadow: 0 8px 30px rgba(15, 23, 42, 0.08);
        }

        .icon {
            width: 58px;
            height: 58px;
            border-radius: 16px;
            background: #eff6ff;
            color: #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 18px;
        }

        h1 {
            font-size: 26px;
            margin-bottom: 8px;
            color: #111827;
        }

        .subtitle {
            color: #6b7280;
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
            font-weight: 600;
            color: #374151;
        }

        .input-wrapper {
            position: relative;
        }

        input {
            width: 100%;
            height: 48px;
            padding: 0 48px 0 14px;
            border: 1px solid #d1d5db;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
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
            color: #6b7280;
            font-size: 13px;
        }

        .toggle-password:hover {
            color: #2563eb;
        }

        .password-info {
            margin-top: 6px;
            font-size: 12px;
            color: #6b7280;
        }

        .message {
            display: none;
            padding: 13px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .message.success {
            display: block;
            background: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }

        .message.error {
            display: block;
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .buttons {
            display: flex;
            gap: 12px;
            margin-top: 28px;
        }

        .btn {
            flex: 1;
            height: 48px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-cancel:hover {
            background: #e5e7eb;
        }

        .btn-save {
            background: #2563eb;
            color: white;
        }

        .btn-save:hover {
            background: #1d4ed8;
        }

        .btn-save:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        @media (max-width: 600px) {
            .container {
                padding: 24px 14px;
            }

            .card {
                padding: 24px 18px;
                border-radius: 14px;
            }

            h1 {
                font-size: 23px;
            }

            .buttons {
                flex-direction: column-reverse;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <a href="../profile/" class="back">
        ← Kembali ke Profile
    </a>

    <div class="card">

        <div class="icon">
            🔐
        </div>

        <h1>Ganti Password</h1>

        <p class="subtitle">
            Gunakan password lama untuk membuat password baru.
            Pastikan password baru minimal 8 karakter.
        </p>

        <div id="message" class="message"></div>

        <form id="passwordForm">

            <!-- PASSWORD LAMA -->
            <div class="form-group">
                <label for="old_password">
                    Password Lama
                </label>

                <div class="input-wrapper">
                    <input
                        type="password"
                        id="old_password"
                        name="old_password"
                        placeholder="Masukkan password lama"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="old_password"
                    >
                        Lihat
                    </button>
                </div>
            </div>

            <!-- PASSWORD BARU -->
            <div class="form-group">
                <label for="new_password">
                    Password Baru
                </label>

                <div class="input-wrapper">
                    <input
                        type="password"
                        id="new_password"
                        name="new_password"
                        placeholder="Masukkan password baru"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="new_password"
                    >
                        Lihat
                    </button>
                </div>

                <div class="password-info">
                    Minimal 8 karakter dan maksimal 72 karakter.
                </div>
            </div>

            <!-- KONFIRMASI -->
            <div class="form-group">
                <label for="confirm_password">
                    Konfirmasi Password Baru
                </label>

                <div class="input-wrapper">
                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Ulangi password baru"
                        minlength="8"
                        maxlength="72"
                        autocomplete="new-password"
                        required
                    >

                    <button
                        type="button"
                        class="toggle-password"
                        data-target="confirm_password"
                    >
                        Lihat
                    </button>
                </div>
            </div>

            <div class="buttons">

                <a
                    href="../profile/"
                    class="btn btn-cancel"
                >
                    Batal
                </a>

                <button
                    type="submit"
                    id="saveButton"
                    class="btn btn-save"
                >
                    Simpan Password
                </button>

            </div>

        </form>

    </div>

</div>

<script>
    const API_BASE = "../../../api";

    const form = document.getElementById("passwordForm");
    const message = document.getElementById("message");
    const saveButton = document.getElementById("saveButton");

    // ===============================
    // MESSAGE
    // ===============================

    function showMessage(text, type) {
        message.textContent = text;
        message.className = "message " + type;
    }

    function hideMessage() {
        message.textContent = "";
        message.className = "message";
    }

    // ===============================
    // TOGGLE PASSWORD
    // ===============================

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

    // ===============================
    // SUBMIT
    // ===============================

    form.addEventListener("submit", async function(event) {

        event.preventDefault();

        hideMessage();

        const oldPassword =
            document.getElementById("old_password").value;

        const newPassword =
            document.getElementById("new_password").value;

        const confirmPassword =
            document.getElementById("confirm_password").value;

        // ===============================
        // VALIDASI FRONTEND
        // ===============================

        if (newPassword.length < 8) {

            showMessage(
                "Password baru minimal 8 karakter.",
                "error"
            );

            return;
        }

        if (newPassword.length > 72) {

            showMessage(
                "Password baru maksimal 72 karakter.",
                "error"
            );

            return;
        }

        if (newPassword !== confirmPassword) {

            showMessage(
                "Konfirmasi password baru tidak sama.",
                "error"
            );

            return;
        }

        if (oldPassword === newPassword) {

            showMessage(
                "Password baru harus berbeda dari password lama.",
                "error"
            );

            return;
        }

        saveButton.disabled = true;
        saveButton.textContent = "Menyimpan...";

        try {

            const response = await fetch(
                API_BASE + "/users/change_password.php",
                {
                    method: "PUT",
                    credentials: "include",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: JSON.stringify({
                        password_lama: oldPassword,
                        password_baru: newPassword,
                        konfirmasi_password: confirmPassword
                    })
                }
            );

            const result = await response.json();

            // ===============================
            // SESSION HABIS
            // ===============================

            if (response.status === 401) {

                window.location.href = "../../auth/login.php";
                return;
            }

            // ===============================
            // ERROR
            // ===============================

            if (!response.ok || !result.status) {

                throw new Error(
                    result.message ||
                    "Gagal mengubah password."
                );
            }

            // ===============================
            // BERHASIL
            // ===============================

            showMessage(
                result.message ||
                "Password berhasil diubah.",
                "success"
            );

            form.reset();

            setTimeout(() => {
                window.location.href = "../profile/";
            }, 1000);

        } catch (error) {

            showMessage(
                error.message ||
                "Terjadi kesalahan saat mengubah password.",
                "error"
            );

        } finally {

            saveButton.disabled = false;
            saveButton.textContent = "Simpan Password";

        }

    });
</script>

</body>
</html>