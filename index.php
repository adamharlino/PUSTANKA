<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Pustanka - Sistem Informasi Perpustakaan</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #f8fafc;
            color: #0f172a;
        }

        .container {
            width: min(1180px, calc(100% - 32px));
            margin: 0 auto;
        }

        /* =========================
           NAVBAR (Disamakan dengan katalog publik)
        ========================== */

        .topbar {
            background: #ffffff;
            border-bottom: 1px solid #e7ebf2;
            padding: 18px 0;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .topbar-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand {
            color: #164e8a;
            font-size: 22px;
            font-weight: 800;
            text-decoration: none;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .nav-links a {
            text-decoration: none;
            color: #536273;
            font-size: 14px;
            font-weight: 600;
            padding: 9px 13px;
            border-radius: 9px;
            transition: .2s;
        }

        .nav-links a:hover {
            background: #f1f6fc;
            color: #164e8a;
        }

        .login-btn {
            background: #164e8a !important;
            color: white !important;
        }

        .login-btn:hover {
            background: #123e6e !important;
        }

        /* =========================
           HERO
        ========================== */

        .hero {
            min-height: calc(100vh - 75px);
            padding: 80px 7%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
            background:
                radial-gradient(
                    circle at top right,
                    rgba(59, 130, 246, 0.12),
                    transparent 35%
                ),
                #f8fafc;
        }

        .hero-content {
            max-width: 600px;
        }

        .badge {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 30px;
            background: #e8f1ff;
            color: #123b70;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .hero h1 {
            font-size: 54px;
            line-height: 1.1;
            margin-bottom: 22px;
            color: #0f172a;
        }

        .hero h1 span {
            color: #2563eb;
        }

        .hero p {
            font-size: 17px;
            line-height: 1.8;
            color: #64748b;
            margin-bottom: 30px;
        }

        .hero-buttons {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .primary-btn {
            display: inline-block;
            text-decoration: none;
            background: #123b70;
            color: white;
            padding: 14px 25px;
            border-radius: 10px;
            font-weight: 600;
            transition: 0.3s;
        }

        .primary-btn:hover {
            background: #0c2c54;
            transform: translateY(-2px);
        }

        .secondary-btn {
            display: inline-block;
            text-decoration: none;
            color: #123b70;
            padding: 14px 25px;
            border: 1px solid #cbd5e1;
            border-radius: 10px;
            font-weight: 600;
            background: white;
            transition: 0.3s;
        }

        .secondary-btn:hover {
            border-color: #123b70;
            transform: translateY(-2px);
        }

        /* =========================
           HERO ILLUSTRATION
        ========================== */

        .hero-visual {
            width: 430px;
            height: 430px;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .circle-bg {
            width: 350px;
            height: 350px;
            background: #e7f0ff;
            border-radius: 50%;
            position: absolute;
        }

        .book-card {
            width: 250px;
            height: 290px;
            background: white;
            border-radius: 20px;
            position: relative;
            z-index: 2;
            box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
            padding: 30px;
            transform: rotate(-5deg);
        }

        .book-cover {
            height: 160px;
            background: #123b70;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 55px;
            margin-bottom: 20px;
        }

        .book-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .book-line {
            width: 70%;
            height: 8px;
            border-radius: 10px;
            background: #dbeafe;
            margin-bottom: 8px;
        }

        .floating-card {
            position: absolute;
            z-index: 3;
            background: white;
            border-radius: 15px;
            padding: 15px 18px;
            box-shadow: 0 12px 30px rgba(15, 23, 42, 0.12);
            font-size: 14px;
        }

        .floating-card.one {
            top: 50px;
            right: 0;
        }

        .floating-card.two {
            bottom: 60px;
            left: 0;
        }

        .floating-number {
            font-size: 22px;
            font-weight: bold;
            color: #123b70;
        }

        .floating-text {
            color: #64748b;
            margin-top: 3px;
        }

        /* =========================
           FEATURES
        ========================== */

        .features {
            padding: 90px 7%;
            background: white;
        }

        .section-title {
            text-align: center;
            max-width: 650px;
            margin: 0 auto 50px;
        }

        .section-title h2 {
            font-size: 34px;
            margin-bottom: 15px;
        }

        .section-title p {
            color: #64748b;
            line-height: 1.7;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .feature-card {
            background: #f8fafc;
            padding: 30px;
            border-radius: 18px;
            border: 1px solid #e2e8f0;
            transition: 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(15, 23, 42, 0.08);
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #e8f1ff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .feature-card h3 {
            margin-bottom: 10px;
            font-size: 19px;
        }

        .feature-card p {
            color: #64748b;
            line-height: 1.7;
            font-size: 14px;
        }

        /* =========================
           CTA
        ========================== */

        .cta {
            margin: 0 7% 80px;
            padding: 55px;
            border-radius: 25px;
            background: #123b70;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .cta h2 {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .cta p {
            color: #dbeafe;
        }

        .cta a {
            display: inline-block;
            background: white;
            color: #123b70;
            text-decoration: none;
            padding: 14px 25px;
            border-radius: 10px;
            font-weight: bold;
            white-space: nowrap;
        }

        /* =========================
           FOOTER
        ========================== */

        footer {
            background: #0f172a;
            color: white;
            padding: 35px 7%;
            text-align: center;
        }

        footer p {
            color: #94a3b8;
            font-size: 14px;
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 900px) {

            .topbar-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links {
                width: 100%;
                overflow-x: auto;
            }

            .hero {
                flex-direction: column;
                text-align: center;
                padding-top: 60px;
            }

            .hero-buttons {
                justify-content: center;
            }

            .hero-visual {
                width: 100%;
                max-width: 430px;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .cta {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 550px) {

            .hero {
                padding-left: 5%;
                padding-right: 5%;
            }

            .hero h1 {
                font-size: 40px;
            }

            .hero p {
                font-size: 15px;
            }

            .hero-buttons {
                flex-direction: column;
            }

            .primary-btn,
            .secondary-btn {
                width: 100%;
            }

            .hero-visual {
                transform: scale(0.8);
                margin-top: -30px;
            }

            .features {
                padding-left: 5%;
                padding-right: 5%;
            }

            .cta {
                margin-left: 5%;
                margin-right: 5%;
                padding: 35px 25px;
            }
        }
    </style>
</head>

<body>

    <!-- =========================
         NAVBAR (Identik dengan Katalog Buku)
    ========================== -->

    <header class="topbar">
        <div class="container topbar-inner">

            <a href="index.php" class="brand">
                PUSTANKA
            </a>

            <nav class="nav-links">
                <a href="#beranda">
                    Beranda
                </a>

                <a href="#fitur">
                    Fitur
                </a>

                <a href="#tentang">
                    Tentang
                </a>

                <a href="frontend/public/books/index.php">
                    Katalog Buku
                </a>

                <a href="frontend/auth/login.php" class="login-btn">
                    Login
                </a>

                <a href="frontend/auth/register.php" class="register-btn">
                    Register
                </a>
            </nav>

        </div>
    </header>


    <!-- =========================
         HERO
    ========================== -->

    <section class="hero" id="beranda">

        <div class="hero-content">

            <div class="badge">
                Sistem Informasi Perpustakaan
            </div>

            <h1>
                Selamat Datang di
                <span>Pustanka</span>
            </h1>

            <p>
                Pustanka adalah sistem informasi perpustakaan yang
                membantu pengguna dalam mencari buku, melakukan
                peminjaman, pengembalian, pembayaran denda,
                serta memantau aktivitas perpustakaan dengan lebih mudah.
            </p>

            <div class="hero-buttons">

                <a
                    href="frontend/auth/login.php"
                    class="primary-btn"
                >
                    Mulai Sekarang →
                </a>

                <a
                    href="#fitur"
                    class="secondary-btn"
                >
                    Lihat Fitur
                </a>

            </div>

        </div>


        <!-- VISUAL -->

        <div class="hero-visual">

            <div class="circle-bg"></div>

            <div class="book-card">

                <div class="book-cover">
                    📖
                </div>

                <div class="book-title">
                    Koleksi Buku
                </div>

                <div class="book-line"></div>
                <div class="book-line"></div>
                <div class="book-line"></div>

            </div>

            <div class="floating-card one">

                <div class="floating-number">
                    📚
                </div>

                <div class="floating-text">
                    Koleksi Buku
                </div>

            </div>

            <div class="floating-card two">

                <div class="floating-number">
                    ⚡
                </div>

                <div class="floating-text">
                    Mudah & Cepat
                </div>

            </div>

        </div>

    </section>


    <!-- =========================
         FEATURES
    ========================== -->

    <section class="features" id="fitur">

        <div class="section-title">

            <h2>
                Semua Kebutuhan Perpustakaan
            </h2>

            <p>
                Pustanka menyediakan berbagai fitur untuk membantu
                pengguna dan pengelola perpustakaan.
            </p>

        </div>


        <div class="feature-grid">

            <div class="feature-card">

                <div class="feature-icon">
                    📚
                </div>

                <h3>
                    Koleksi Buku
                </h3>

                <p>
                    Cari dan lihat informasi buku yang tersedia
                    di perpustakaan dengan mudah.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    📖
                </div>

                <h3>
                    Peminjaman
                </h3>

                <p>
                    Kelola proses peminjaman dan pengembalian
                    buku secara lebih terorganisir.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    💳
                </div>

                <h3>
                    Pembayaran Denda
                </h3>

                <p>
                    Pantau denda dan riwayat pembayaran dengan
                    sistem yang lebih terstruktur.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    👤
                </div>

                <h3>
                    Akun Pengguna
                </h3>

                <p>
                    Pengguna dapat melihat informasi akun,
                    peminjaman, denda, dan aktivitas mereka.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    🕐
                </div>

                <h3>
                    Kehadiran
                </h3>

                <p>
                    Catat kehadiran pengguna perpustakaan melalui
                    fitur check-in dan check-out.
                </p>

            </div>


            <div class="feature-card">

                <div class="feature-icon">
                    📊
                </div>

                <h3>
                    Dashboard
                </h3>

                <p>
                    Dapatkan informasi aktivitas perpustakaan
                    melalui dashboard yang informatif.
                </p>

            </div>

        </div>

    </section>


    <!-- =========================
         ABOUT / CTA
    ========================== -->

    <section class="cta" id="tentang">

        <div>

            <h2>
                Siap menggunakan Pustanka?
            </h2>

            <p>
                Login untuk mengakses fitur perpustakaan sesuai
                dengan akun Anda.
            </p>

        </div>

        <a href="frontend/auth/login.php">
            Login Sekarang →
        </a>

    </section>


    <!-- =========================
         FOOTER
    ========================== -->

    <footer>

        <p>
            © 2026 Pustanka. Sistem Informasi Perpustakaan.
        </p>

    </footer>

</body>

</html>