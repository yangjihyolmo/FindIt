<?php

session_start();

require_once __DIR__ . "/config/db.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: register.html");
    exit();
}

/*
  Receive form values
*/

$fullName = trim($_POST["full_name"] ?? "");
$email = strtolower(trim($_POST["email"] ?? ""));
$phone = trim($_POST["phone"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";


if (
    $fullName === "" ||
    $email === "" ||
    $password === "" ||
    $confirmPassword === ""
) {
    die("Please complete all required fields.");
}

if (strlen($fullName) < 2) {
    die("Full name must contain at least 2 characters.");
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}

if (strlen($password) < 6) {
    die("Password must contain at least 6 characters.");
}

if ($password !== $confirmPassword) {
    die("Passwords do not match.");
}

/*
   Check whether email already exists
*/

try {
    $checkUser = $connection->prepare(
        "SELECT user_id FROM users WHERE LOWER(email) = LOWER(:email)"
    );

    $checkUser->execute([
        ":email" => $email
    ]);

    if ($checkUser->fetch(PDO::FETCH_ASSOC)) {
        die("An account with this email already exists.");
    }

    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    $insertUser = $connection->prepare(
        "
        INSERT INTO users (
            full_name,
            email,
            phone,
            password
        )
        VALUES (
            :full_name,
            :email,
            :phone,
            :password
        )
        "
    );

    $insertUser->execute([
        ":full_name" => $fullName,
        ":email" => $email,
        ":phone" => $phone !== "" ? $phone : null,
        ":password" => $hashedPassword
    ]);

    $_SESSION["login_success"] =
        "Registration successful. You can now log in.";

    header("Location: login.html");
    exit();

} catch (PDOException $error) {
    error_log($error->getMessage());

    die("Registration failed. Please try again.");
}
?>