<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>FINORA | Smart Expense Tracker</title>

    <!-- Google Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="css/style.css">

</head>

<body>

<?php include("includes/navbar.php"); ?>


<!-- ===================================
            HERO SECTION
=================================== -->

<section class="hero" id="home">

    <div class="hero-content" data-aos="fade-right">

        <span class="hero-tag">
            💙 Smart Finance Management
        </span>

        <h1>

            Spend Smart.
            <br>

            <span>Save More.</span>

        </h1>

        <p>

            FINORA helps you manage your income, track expenses,
            analyze spending habits and generate beautiful financial
            reports—all from one modern dashboard.

        </p>

        <div class="hero-buttons">

            <a href="register.php" class="hero-btn primary-btn">
                Get Started
            </a>

            <a href="#features" class="hero-btn secondary-btn">
                Learn More
            </a>

        </div>

    </div>

    <div class="hero-image" data-aos="fade-left">

        <div class="image-glow"></div>

        <img
            src="assets/images/hero-wallet.png"
            alt="FINORA Wallet">

    </div>

</section>

<!-- ===================================
        FEATURES SECTION
=================================== -->

<section class="features" id="features">

    <div class="section-title" data-aos="fade-up">

        <span class="section-badge">
            ⭐ FEATURES
        </span>

        <h2>Everything You Need</h2>

        <p>
            Powerful features designed to help you manage your finances
            smarter, faster, and more securely.
        </p>

    </div>

    <div class="feature-container">

        <div class="feature-card" data-aos="fade-up">

            <div class="icon">📊</div>

            <h3>Dashboard</h3>

            <p>
                View your complete financial overview with beautiful charts
                and real-time summaries.
            </p>

        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="100">

            <div class="icon">💸</div>

            <h3>Expenses</h3>

            <p>
                Easily add, edit and organize your daily expenses in one place.
            </p>

        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="200">

            <div class="icon">🔍</div>

            <h3>Search</h3>

            <p>
                Instantly find transactions using powerful smart search.
            </p>

        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="300">

            <div class="icon">🌙</div>

            <h3>Dark Mode</h3>

            <p>
                Switch between light and dark themes for comfortable viewing.
            </p>

        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="400">

            <div class="icon">📄</div>

            <h3>Export PDF</h3>

            <p>
                Generate professional financial reports with a single click.
            </p>

        </div>

        <div class="feature-card" data-aos="fade-up" data-aos-delay="500">

            <div class="icon">🚨</div>

            <h3>Budget Alerts</h3>

            <p>
                Stay on budget with intelligent spending notifications.
            </p>

        </div>

    </div>

</section>


<!-- ===================================
        WHY CHOOSE FINORA
=================================== -->

<section class="why-finora" id="about">

    <div class="why-container">

        <!-- Left Side -->

        <div class="why-left" data-aos="fade-right">

            <span class="why-badge">
                ⭐ WHY CHOOSE FINORA
            </span>

            <h2>

                Smarter Finance
                <span>Starts with FINORA.</span>

            </h2>

            <p>

                FINORA helps you take complete control of your finances with
                smart expense tracking, powerful analytics, secure data
                management, and professional reports—all designed to make
                money management simple and stress-free.

            </p>

            <div class="why-feature">

                <div class="feature-box">

                    <i class="fa-solid fa-chart-line"></i>

                    <span>Smart Expense Tracking</span>

                </div>

                <div class="feature-box">

                    <i class="fa-solid fa-chart-pie"></i>

                    <span>Beautiful Analytics</span>

                </div>

                <div class="feature-box">

                    <i class="fa-solid fa-shield-halved"></i>

                    <span>100% Secure Data</span>

                </div>

                <div class="feature-box">

                    <i class="fa-solid fa-file-pdf"></i>

                    <span>One Click PDF Export</span>

                </div>

            </div>

            <a href="register.php" class="why-btn">

                Get Started

            </a>

        </div>

        <!-- Right Side -->

        <div class="why-right" data-aos="fade-left">

            <div class="image-glow"></div>

            <img src="assets/images/dashboard.png"
                alt="FINORA Dashboard">

        </div>

    </div>

</section>

<!-- ===================================
            FOOTER
=================================== -->

<footer class="footer">

    <div class="footer-container">

        <div class="footer-logo">

            <img src="assets/images/finora-logo.png" alt="FINORA Logo">

            <h2>FINORA</h2>

        </div>

        <p>

            Smart Expense Tracking • Beautiful Analytics • Secure Reports

        </p>

        <p class="footer-copy">

            Crafted by <strong>KHAN ERAM</strong>
            
        </p>

    </div>

</footer>

<!-- ===============================
        AOS Animation
================================ -->

<script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>

<script>

AOS.init({

    duration:1000,

    once:true,

    offset:120

});

</script>

</body>
</html>