<?php

$host = "127.0.0.1";
$port = "5431";
$dbname = "lostfound_db";
$username = "yangjihyolmo";
$password = "yangjihyolmo13@#";

try {
    $connection = new PDO(
        "pgsql:host=$host;port=$port;dbname=$dbname",
        $username,
        $password
    );

    $connection->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

    //echo "Database connected successfully.";

} catch (PDOException $error) {
    die("Database connection failed: " . $error->getMessage());
}
?>
