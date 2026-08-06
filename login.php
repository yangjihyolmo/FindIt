<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/db.php";

$errorMessage = "";
$successMessage = $_SESSION["login_success"] ?? "";
$email = "";

unset($_SESSION["login_success"]);

/*
Redirect users who are already logged in
*/

if (isset($_SESSION["user_id"])) {
    if (
        isset($_SESSION["role"]) &&
        strtolower($_SESSION["role"]) === "admin"
    ) {
        header("Location: admin_dashboard.php");
        exit();
    }

    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | FindIt</title>
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
            transition: color 0.2s;
        }

        .navLinks a:hover,
        .navLinks a.active {
            color: #1769aa;
        }

        .navLinks a.active {
            font-weight: 700;
        }

        /*
          Login page
        */

        .loginPage {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 72px);
            padding: 48px 20px;
        }

        .loginCard {
            width: 100%;
            max-width: 460px;
            padding: 34px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 5px 22px rgba(0, 0, 0, 0.09);
        }

        .loginHeader {
            margin-bottom: 26px;
            text-align: center;
        }

        .loginHeader h1 {
            margin: 0 0 10px;
            color: #1f2937;
            font-size: 32px;
        }

        .loginHeader p {
            margin: 0;
            color: #666666;
            line-height: 1.6;
        }

        .formMessage {
            padding: 13px 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 14px;
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

        .formGroup input {
            width: 100%;
            min-height: 48px;
            padding: 12px 14px;
            color: #1f2937;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            outline: none;
            font-size: 15px;
            transition:
                border-color 0.2s,
                box-shadow 0.2s;
        }

        .formGroup input:focus {
            border-color: #1769aa;
            box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.14);
        }

        .loginButton {
            width: 100%;
            min-height: 48px;
            padding: 12px 18px;
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

        .loginButton:hover {
            background: #12558a;
        }

        .bottomText {
            margin: 22px 0 0;
            color: #666666;
            text-align: center;
            line-height: 1.6;
        }

        .bottomText a {
            color: #1769aa;
            font-weight: 700;
            text-decoration: none;
        }

        .bottomText a:hover {
            text-decoration: underline;
        }

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

            .loginPage {
                min-height: calc(100vh - 130px);
            }
        }

        @media (max-width: 560px) {
            .loginPage {
                padding: 30px 15px;
            }

            .loginCard {
                padding: 26px 20px;
            }

            .loginHeader h1 {
                font-size: 28px;
            }

            .navLinks {
                font-size: 14px;
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

                <a href="browse.php">
                    Browse Items
                </a>

                <a href="report.php">
                    Report Item
                </a>

                <a href="contact.php">
                    Contact
                </a>

                <a href="login.php" class="active">
                    Login
                </a>

                <a href="register.php" class="registerLink">
                    Register
                </a>
            </div>

        </nav>
    </header>

    <main class="loginPage">

        <section class="loginCard">

            <div class="loginHeader">
                <h1>Welcome Back</h1>

                <p>
                    Login to manage your lost and found reports.
                </p>
            </div>

            <?php if ($successMessage !== ""): ?>

                <div class="formMessage successMessage" role="status">
                    <?php echo htmlspecialchars($successMessage); ?>
                </div>
            <?php endif; ?>

            <?php if ($errorMessage !== ""): ?>

                <div class="formMessage errorMessage" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>

            <?php endif; ?>

            <form action="login.php" method="POST">

                <div class="formGroup">
                    <label for="email">
                        Email Address
                    </label>

                    <input type="email" id="email" name="email" placeholder="Enter your email" autocomplete="email"
                        value="<?php echo htmlspecialchars($email); ?>" required>
                </div>

                <div class="formGroup">
                    <label for="password">
                        Password
                    </label>

                    <input type="password" id="password" name="password" placeholder="Enter your password"
                        autocomplete="current-password" required>
                </div>

                <button type="submit" class="loginButton">
                    Login
                </button>

            </form>

            <p class="bottomText">
                Do not have an account?

                <a href="register.php">
                    Create an account
                </a>
            </p>

        </section>

    </main>

    <script src="js/script.js"></script>

</body>

</html>
</body>

</html>