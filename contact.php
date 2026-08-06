<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/db.php";

$fullName = "";
$email = "";
$subject = "";
$message = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");

    if (
        $fullName === "" ||
        $email === "" ||
        $subject === "" ||
        $message === ""
    ) {
        $errorMessage = "Please complete all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = "Please enter a valid email address.";

    } elseif (strlen($fullName) > 100) {
        $errorMessage = "Full name must not exceed 100 characters.";

    } elseif (strlen($email) > 120) {
        $errorMessage = "Email must not exceed 120 characters.";

    } elseif (strlen($subject) > 150) {
        $errorMessage = "Subject must not exceed 150 characters.";

    } elseif (strlen($message) > 3000) {
        $errorMessage = "Message must not exceed 3000 characters.";

    } else {

        try {

            $sql = "
                INSERT INTO contact_messages
                (
                    full_name,
                    email,
                    subject,
                    message
                )
                VALUES
                (
                    :full_name,
                    :email,
                    :subject,
                    :message
                )
            ";

            $statement = $connection->prepare($sql);

            $statement->execute([
                ":full_name" => $fullName,
                ":email" => $email,
                ":subject" => $subject,
                ":message" => $message
            ]);

            $successMessage =
                "Your message has been saved successfully.";

            $fullName = "";
            $email = "";
            $subject = "";
            $message = "";

        } catch (PDOException $exception) {

            $errorMessage =
                "Database error: " . $exception->getMessage();
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact | FindIt</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #1f2937;
            background: #f5f7fa;
            font-family: Arial, Helvetica, sans-serif;
        }

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        .header {
            position: sticky;
            top: 0;
            z-index: 1000;
            width: 100%;
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            min-height: 72px;
            padding: 0 20px;
            margin: 0 auto;
        }

        .logo {
            color: #1f2937;
            font-size: 28px;
            font-weight: 700;
            text-decoration: none;
        }

        .navLinks {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .navLinks a {
            color: #1f2937;
            font-size: 16px;
            font-weight: 500;
            text-decoration: none;
            transition:
                color 0.2s,
                background 0.2s;
        }

        .navLinks a:hover,
        .navLinks a.active {
            color: #1769aa;
        }

        .navLinks a.active {
            font-weight: 700;
        }

        /*
        |--------------------------------------------------------------------------
        | Page title
        |--------------------------------------------------------------------------
        */

        .pageHero {
            padding: 52px 20px 30px;
            text-align: center;
        }

        .pageHeroInner {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
        }

        .smallTitle {
            margin: 0 0 10px;
            color: #1769aa;
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        .pageHero h1 {
            margin: 0 0 12px;
            color: #1f2937;
            font-size: 38px;
        }

        .pageHero p:last-child {
            margin: 0;
            color: #667085;
            font-size: 17px;
            line-height: 1.6;
        }

        /*
        |--------------------------------------------------------------------------
        | Contact section
        |--------------------------------------------------------------------------
        */

        .contactSection {
            padding: 10px 20px 70px;
        }

        .contactContainer {
            display: grid;
            grid-template-columns: 0.9fr 1.1fr;
            gap: 30px;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .contactInfo,
        .contactForm {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 5px 22px rgba(0, 0, 0, 0.08);
        }

        .contactInfo {
            padding: 32px;
        }

        .contactInfo h2 {
            margin: 0 0 12px;
            font-size: 26px;
        }

        .contactIntro {
            margin: 0 0 26px;
            color: #667085;
            line-height: 1.7;
        }

        .contactBox {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            padding: 18px;
            margin-bottom: 14px;
            background: #f8fafc;
            border: 1px solid #e7edf3;
            border-radius: 10px;
        }

        .contactBox:last-child {
            margin-bottom: 0;
        }

        .contactIcon {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 44px;
            height: 44px;
            background: #eaf4fc;
            border-radius: 50%;
            font-size: 21px;
        }

        .contactBox h3 {
            margin: 0 0 5px;
            color: #1f2937;
            font-size: 17px;
        }

        .contactBox p {
            margin: 0;
            color: #667085;
            line-height: 1.5;
        }

        .contactForm {
            padding: 32px;
        }

        .formMessage {
            padding: 14px 16px;
            margin-bottom: 22px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .errorMessage {
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fca5a5;
        }

        .successMessage {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #86efac;
        }

        .formGroup {
            margin-bottom: 19px;
        }

        .formGroup label {
            display: block;
            margin-bottom: 8px;
            color: #333333;
            font-size: 14px;
            font-weight: 700;
        }

        .formGroup input,
        .formGroup textarea {
            width: 100%;
            padding: 12px 14px;
            color: #1f2937;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-family: inherit;
            font-size: 15px;
            transition:
                border-color 0.2s,
                box-shadow 0.2s;
        }

        .formGroup input {
            min-height: 48px;
        }

        .formGroup textarea {
            min-height: 150px;
            resize: vertical;
        }

        .formGroup input:focus,
        .formGroup textarea:focus {
            border-color: #1769aa;
            box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.14);
        }

        .submitButton {
            width: 100%;
            min-height: 50px;
            padding: 13px 18px;
            color: #ffffff;
            background: #1769aa;
            border: none;
            border-radius: 8px;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .submitButton:hover {
            background: #12558a;
        }

        /*
        |--------------------------------------------------------------------------
        | Footer
        |--------------------------------------------------------------------------
        */

        .footer {
            padding: 48px 20px 22px;
            color: #dbe5ef;
            background: #172033;
        }

        .footerGrid {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 40px;
            width: 100%;
            max-width: 1180px;
            margin: 0 auto;
        }

        .footer h2,
        .footer h3 {
            margin-top: 0;
            color: #ffffff;
        }

        .footer p,
        .footer a {
            color: #c4cfda;
            line-height: 1.7;
        }

        .footer a {
            display: block;
            margin-bottom: 8px;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ffffff;
        }

        .copyright {
            padding-top: 24px;
            margin-top: 30px;
            text-align: center;
            border-top: 1px solid #344054;
        }

        .copyright p {
            margin: 0;
            color: #aeb9c5;
        }

        /*
        |--------------------------------------------------------------------------
        | Responsive
        |--------------------------------------------------------------------------
        */

        @media (max-width: 900px) {
            .nav {
                flex-direction: column;
                justify-content: center;
                gap: 14px;
                padding-top: 16px;
                padding-bottom: 16px;
            }

            .navLinks {
                flex-wrap: wrap;
                justify-content: center;
                gap: 14px 18px;
            }

            .contactContainer {
                grid-template-columns: 1fr;
            }

            .footerGrid {
                grid-template-columns: 1fr 1fr;
            }
        }

        @media (max-width: 600px) {
            .pageHero h1 {
                font-size: 31px;
            }

            .contactSection {
                padding-right: 15px;
                padding-left: 15px;
            }

            .contactInfo,
            .contactForm {
                padding: 24px 20px;
            }

            .footerGrid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <header class="header">
        <nav class="nav">

            <a href="index.php" class="logo">
                FindIt
            </a>

            <div class="navLinks">
                <a href="index.php">Home</a>
                <a href="browse.php">Browse Items</a>
                <a href="report.php">Report Item</a>

                <?php if (isset($_SESSION["user_id"])): ?>

                    <a href="dashboard.php">Dashboard</a>
                    <a href="my_reports.php">My Reports</a>
                    <a href="contact.php" class="active">Contact</a>

                    <a href="logout.php" class="logoutLink">
                        Logout
                    </a>

                <?php else: ?>

                    <a href="contact.php" class="active">Contact</a>
                    <a href="login.php">Login</a>

                    <a href="register.php" class="registerLink">
                        Register
                    </a>

                <?php endif; ?>
            </div>

        </nav>
    </header>

    <main>

        <section class="pageHero">

            <div class="pageHeroInner">

                <p class="smallTitle">
                    CONTACT US
                </p>

                <h1>Get in Touch</h1>

                <p>
                    Send us a message if you need help with reports,
                    claims or your FindIt account.
                </p>

            </div>

        </section>

        <section class="contactSection">

            <div class="contactContainer">

                <aside class="contactInfo">

                    <h2>Contact Information</h2>

                    <p class="contactIntro">
                        Contact our support team for assistance with
                        lost-item reports, found-item reports or account issues.
                    </p>

                    <div class="contactBox">

                        <span class="contactIcon" aria-hidden="true">
                            ✉️
                        </span>

                        <div>
                            <h3>Email</h3>
                            <p>support@findit.com</p>
                        </div>

                    </div>

                    <div class="contactBox">

                        <span class="contactIcon" aria-hidden="true">
                            📞
                        </span>

                        <div>
                            <h3>Phone</h3>
                            <p>+977 9800000000</p>
                        </div>

                    </div>

                    <div class="contactBox">

                        <span class="contactIcon" aria-hidden="true">
                            📍
                        </span>

                        <div>
                            <h3>Address</h3>
                            <p>Kathmandu, Nepal</p>
                        </div>

                    </div>

                </aside>

                <form id="contactForm" class="contactForm" action="contact.php" method="POST">

                    <?php if ($errorMessage !== ""): ?>

                        <div class="formMessage errorMessage" role="alert">
                            <?php
                            echo htmlspecialchars($errorMessage);
                            ?>
                        </div>

                    <?php endif; ?>

                    <?php if ($successMessage !== ""): ?>

                        <div class="formMessage successMessage" role="status">
                            <?php
                            echo htmlspecialchars($successMessage);
                            ?>
                        </div>

                    <?php endif; ?>

                    <div class="formGroup">

                        <label for="contactName">
                            Full Name *
                        </label>

                        <input type="text" id="contactName" name="full_name" maxlength="100" value="<?php
                        echo htmlspecialchars($fullName);
                        ?>" autocomplete="name" required>

                    </div>

                    <div class="formGroup">

                        <label for="contactEmail">
                            Email *
                        </label>

                        <input type="email" id="contactEmail" name="email" maxlength="150" value="<?php
                        echo htmlspecialchars($email);
                        ?>" autocomplete="email" required>

                    </div>

                    <div class="formGroup">

                        <label for="subject">
                            Subject *
                        </label>

                        <input type="text" id="subject" name="subject" maxlength="150" value="<?php
                        echo htmlspecialchars($subject);
                        ?>" required>

                    </div>

                    <div class="formGroup">

                        <label for="contactMessage">
                            Message *
                        </label>

                        <textarea id="contactMessage" name="message" rows="6" maxlength="3000" required><?php
                        echo htmlspecialchars($message);
                        ?></textarea>

                    </div>

                    <button type="submit" class="submitButton">
                        Send Message
                    </button>

                </form>

            </div>

        </section>

    </main>

    <footer class="footer">

        <div class="footerGrid">

            <div>
                <h2>FindIt</h2>

                <p>
                    A simple lost and found management system
                    that helps people recover their belongings.
                </p>
            </div>

            <div>
                <h3>Pages</h3>

                <a href="browse.php">Browse Items</a>
                <a href="report.php">Report Item</a>
                <a href="contact.php">Contact</a>
            </div>

            <div>
                <h3>Contact</h3>

                <p>support@findit.com</p>
                <p>+977 9800000000</p>
            </div>

        </div>

        <div class="copyright">

            <p>
                © 2026 FindIt Management System
            </p>

        </div>

    </footer>

</body>

</html>