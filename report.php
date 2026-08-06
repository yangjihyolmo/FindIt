<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config/db.php";

/*
|--------------------------------------------------------------------------
| Require login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$userId = (int) $_SESSION["user_id"];

$errorMessage = "";

$itemType = "";
$categoryId = "";
$itemName = "";
$color = "";
$itemDate = "";
$location = "";
$description = "";

$categories = [];

/*
|--------------------------------------------------------------------------
| Load categories
|--------------------------------------------------------------------------
*/

try {
    $categoryStatement = $connection->query(
        "
        SELECT
            category_id,
            category_name
        FROM categories
        ORDER BY category_name ASC
        "
    );

    $categories = $categoryStatement->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $exception) {
    error_log($exception->getMessage());

    $errorMessage =
        "Categories could not be loaded. Please refresh the page.";
}

/*
|--------------------------------------------------------------------------
| Process report form
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /*
    |--------------------------------------------------------------------------
    | Collect form data
    |--------------------------------------------------------------------------
    */

    $itemType = strtolower(
        trim($_POST["item_type"] ?? "")
    );

    $submittedCategoryId = filter_input(
        INPUT_POST,
        "category_id",
        FILTER_VALIDATE_INT
    );

    $categoryId = $submittedCategoryId ?: "";

    $itemName = trim(
        $_POST["item_name"] ?? ""
    );

    $color = trim(
        $_POST["color"] ?? ""
    );

    $itemDate = trim(
        $_POST["item_date"] ?? ""
    );

    $location = trim(
        $_POST["location"] ?? ""
    );

    $description = trim(
        $_POST["description"] ?? ""
    );

    /*
    |--------------------------------------------------------------------------
    | Validate required fields
    |--------------------------------------------------------------------------
    */

    if (
        $itemType === "" ||
        !$submittedCategoryId ||
        $itemName === "" ||
        $itemDate === "" ||
        $location === "" ||
        $description === ""
    ) {
        $errorMessage = "Please complete all required fields.";
    } elseif (
        !in_array(
            $itemType,
            ["lost", "found"],
            true
        )
    ) {
        $errorMessage = "Please select a valid report type.";
    } elseif (strlen($itemName) > 150) {
        $errorMessage =
            "Item name must not exceed 150 characters.";
    } elseif (strlen($color) > 80) {
        $errorMessage =
            "Color must not exceed 80 characters.";
    } elseif (strlen($location) > 200) {
        $errorMessage =
            "Location must not exceed 200 characters.";
    } elseif (strlen($description) > 3000) {
        $errorMessage =
            "Description must not exceed 3000 characters.";
    }

    /*
    |--------------------------------------------------------------------------
    | Validate date
    |--------------------------------------------------------------------------
    */

    if ($errorMessage === "") {
        $dateObject = DateTime::createFromFormat(
            "Y-m-d",
            $itemDate
        );

        $dateErrors = DateTime::getLastErrors();

        if (
            !$dateObject ||
            (
                is_array($dateErrors) &&
                (
                    $dateErrors["warning_count"] > 0 ||
                    $dateErrors["error_count"] > 0
                )
            ) ||
            $dateObject->format("Y-m-d") !== $itemDate
        ) {
            $errorMessage = "Please enter a valid item date.";
        } elseif ($itemDate > date("Y-m-d")) {
            $errorMessage =
                "The item date cannot be in the future.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Confirm category exists
    |--------------------------------------------------------------------------
    */

    if ($errorMessage === "") {
        try {
            $checkCategoryStatement = $connection->prepare(
                "
                SELECT category_id
                FROM categories
                WHERE category_id = :category_id
                LIMIT 1
                "
            );

            $checkCategoryStatement->execute([
                ":category_id" => $submittedCategoryId
            ]);

            if (!$checkCategoryStatement->fetch()) {
                $errorMessage =
                    "Please select a valid category.";
            }
        } catch (PDOException $exception) {
            error_log($exception->getMessage());

            $errorMessage =
                "The selected category could not be verified.";
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Image upload
    |--------------------------------------------------------------------------
    */

    $imageName = null;
    $uploadPath = null;

    $maximumImageSize = 2 * 1024 * 1024;

    $allowedTypes = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (
        $errorMessage === "" &&
        isset($_FILES["image"]) &&
        $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE
    ) {
        $uploadedImage = $_FILES["image"];

        if ($uploadedImage["error"] !== UPLOAD_ERR_OK) {
            $errorMessage =
                "The image upload failed. Please try again.";
        } elseif ($uploadedImage["size"] > $maximumImageSize) {
            $errorMessage =
                "The image must not exceed 2 MB.";
        } elseif (
            !is_uploaded_file($uploadedImage["tmp_name"])
        ) {
            $errorMessage = "The uploaded image is invalid.";
        } else {
            try {
                $fileInformation = new finfo(
                    FILEINFO_MIME_TYPE
                );

                $fileType = $fileInformation->file(
                    $uploadedImage["tmp_name"]
                );
            } catch (Throwable $exception) {
                error_log($exception->getMessage());
                $fileType = false;
            }

            if (
                !$fileType ||
                !isset($allowedTypes[$fileType])
            ) {
                $errorMessage =
                    "Only JPG, PNG and WEBP images are allowed.";
            } else {
                try {
                    $imageName =
                        "item_" .
                        bin2hex(random_bytes(16)) .
                        "." .
                        $allowedTypes[$fileType];
                } catch (Exception $exception) {
                    error_log($exception->getMessage());

                    $errorMessage =
                        "The image filename could not be created.";
                }
            }

            if ($errorMessage === "") {
                $uploadFolder = __DIR__ . "/uploads/";

                if (
                    !is_dir($uploadFolder) &&
                    !mkdir($uploadFolder, 0775, true)
                ) {
                    $errorMessage =
                        "The upload folder could not be created.";
                } else {
                    $uploadPath =
                        $uploadFolder . $imageName;

                    if (
                        !move_uploaded_file(
                            $uploadedImage["tmp_name"],
                            $uploadPath
                        )
                    ) {
                        $errorMessage =
                            "The image could not be saved.";
                    }
                }
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save report
    |--------------------------------------------------------------------------
    */

    if ($errorMessage === "") {
        try {
            $sql = "
                INSERT INTO items (
                    user_id,
                    category_id,
                    item_name,
                    item_type,
                    color,
                    item_date,
                    location,
                    description,
                    image_name,
                    status,
                    created_at
                )
                VALUES (
                    :user_id,
                    :category_id,
                    :item_name,
                    :item_type,
                    :color,
                    :item_date,
                    :location,
                    :description,
                    :image_name,
                    'open',
                    CURRENT_TIMESTAMP
                )
            ";

            $statement = $connection->prepare($sql);

            $statement->execute([
                ":user_id" => $userId,
                ":category_id" => $submittedCategoryId,
                ":item_name" => $itemName,
                ":item_type" => $itemType,
                ":color" => $color !== "" ? $color : null,
                ":item_date" => $itemDate,
                ":location" => $location,
                ":description" => $description,
                ":image_name" => $imageName
            ]);

            header("Location: my_reports.php?reported=1");
            exit();
        } catch (PDOException $exception) {
            error_log($exception->getMessage());

            if (
                $uploadPath !== null &&
                is_file($uploadPath)
            ) {
                unlink($uploadPath);
            }

            $errorMessage =
                "The report could not be saved. Please try again.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Report Item | FindIt</title>

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


        .pageHero {
            padding: 48px 20px 28px;
            text-align: center;
        }

        .pageHeroInner {
            width: 100%;
            max-width: 900px;
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
            font-size: 36px;
        }

        .pageHero p:last-child {
            margin: 0;
            color: #666666;
            font-size: 17px;
            line-height: 1.6;
        }

        .reportSection {
            padding: 10px 20px 60px;
        }

        .reportContainer {
            display: grid;
            grid-template-columns: 320px minmax(0, 1fr);
            gap: 30px;
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .infoCard,
        .reportForm {
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 5px 22px rgba(0, 0, 0, 0.08);
        }

        .infoCard {
            align-self: start;
            padding: 28px;
        }

        .infoCard h2 {
            margin: 0 0 12px;
            color: #1f2937;
            font-size: 24px;
        }

        .infoCard>p {
            margin: 0;
            color: #666666;
            line-height: 1.7;
        }

        .tipBox {
            padding: 20px;
            margin-top: 24px;
            background: #eaf4fc;
            border: 1px solid #c9e2f5;
            border-radius: 10px;
        }

        .tipBox h3 {
            margin: 0 0 12px;
            color: #1769aa;
            font-size: 18px;
        }

        .tipBox ul {
            padding-left: 20px;
            margin: 0;
            color: #4b5563;
        }

        .tipBox li {
            margin-bottom: 10px;
            line-height: 1.5;
        }

        .tipBox li:last-child {
            margin-bottom: 0;
        }

        .reportForm {
            padding: 32px;
        }

        .errorMessage {
            padding: 14px 16px;
            margin-bottom: 22px;
            color: #991b1b;
            background: #fee2e2;
            border: 1px solid #fca5a5;
            border-radius: 8px;
            line-height: 1.5;
        }

        .formRow {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
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
        .formGroup select,
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

        .formGroup input,
        .formGroup select {
            min-height: 48px;
        }

        .formGroup textarea {
            min-height: 135px;
            resize: vertical;
        }

        .formGroup input:focus,
        .formGroup select:focus,
        .formGroup textarea:focus {
            border-color: #1769aa;
            box-shadow: 0 0 0 3px rgba(23, 105, 170, 0.14);
        }

        .formGroup input[type="file"] {
            min-height: auto;
            padding: 11px;
            background: #f9fafb;
        }

        .formGroup small {
            display: block;
            margin-top: 7px;
            color: #667085;
            font-size: 12px;
            line-height: 1.5;
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

            .reportContainer {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 620px) {
            .pageHero h1 {
                font-size: 30px;
            }

            .reportSection {
                padding-right: 15px;
                padding-left: 15px;
            }

            .reportForm,
            .infoCard {
                padding: 24px 20px;
            }

            .formRow {
                grid-template-columns: 1fr;
                gap: 0;
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
                <a href="report.php" class="active">Report Item</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="my_reports.php">My Reports</a>
                <a href="contact.php">Contact</a>
                <a href="logout.php" class="logoutLink">Logout</a>
            </div>

        </nav>
    </header>

    <main>

        <section class="pageHero">
            <div class="pageHeroInner">

                <p class="smallTitle">
                    REPORT ITEM
                </p>

                <h1>
                    Report Lost or Found Item
                </h1>

                <p>
                    Enter accurate details to help people identify the item.
                </p>

            </div>
        </section>

        <section class="reportSection">

            <div class="reportContainer">

                <aside class="infoCard">

                    <h2>Before You Submit</h2>

                    <p>
                        Provide clear and accurate information so other people
                        can recognize and verify the item.
                    </p>

                    <div class="tipBox">

                        <h3>Helpful Tips</h3>

                        <ul>
                            <li>Use a simple and clear item name.</li>
                            <li>Add the correct date and location.</li>
                            <li>Mention the color and special marks.</li>
                            <li>Do not include private information.</li>
                        </ul>

                    </div>

                </aside>

                <form id="reportForm" class="reportForm" action="report.php" method="POST"
                    enctype="multipart/form-data">

                    <?php if ($errorMessage !== ""): ?>

                        <div class="errorMessage" role="alert">
                            <?php echo htmlspecialchars($errorMessage); ?>
                        </div>

                    <?php endif; ?>

                    <div class="formRow">

                        <div class="formGroup">
                            <label for="reportType">
                                Report Type *
                            </label>

                            <select id="reportType" name="item_type" required>
                                <option value="">Select Type</option>

                                <option value="lost" <?php
                                echo $itemType === "lost"
                                    ? "selected"
                                    : "";
                                ?>
       >
                                    Lost Item
                                </option>

                                <option value="found" <?php
                                echo $itemType === "found"
                                    ? "selected"
                                    : "";
                                ?>
    >
                                    Found Item
                                </option>
                            </select>
                        </div>

                        <div class="formGroup">
                            <label for="category">
                                Category *
                            </label>

                            <select id="category" name="category_id" required>
                                <option value="">Select Category</option>

                                <?php foreach ($categories as $category): ?>

                                    <option value="<?php
                                    echo (int) $category["category_id"];
                                    ?>"
                                        <?php
                                        echo (string) $categoryId ===
                                            (string) $category["category_id"]
                                            ? "selected"
                                            : "";
                                        ?>
                                        >
                                        <?php
                                        echo htmlspecialchars(
                                            $category["category_name"]
                                        );
                                        ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>
                        </div>

                    </div>

                    <div class="formGroup">
                        <label for="itemName">
                            Item Name *
                        </label>

                        <input type="text" id="itemName" name="item_name" placeholder="Example: Black backpack"
                            maxlength="150" value="<?php
                            echo htmlspecialchars($itemName);
                            ?>" required>
                    </div>

                    <div class="formRow">

                        <div class="formGroup">
                            <label for="color">
                                Color
                            </label>

                            <input type="text" id="color" name="color" placeholder="Example: Black" maxlength="80"
                                value="<?php
                                echo htmlspecialchars($color);
                                ?>">
                        </div>

                        <div class="formGroup">
                            <label for="date">
                                Date *
                            </label>

                            <input type="date" id="date" name="item_date" max="<?php echo date("Y-m-d"); ?>"
                            value="
                            <?php
                            echo htmlspecialchars($itemDate);
                            ?>"
                            required
                            >
                        </div>

                    </div>

                    <div class="formGroup">
                        <label for="location">
                            Location *
                        </label>

                        <input type="text" id="location" name="location" placeholder="Example: Library" maxlength="200"
                            value="<?php
                            echo htmlspecialchars($location);
                            ?>" required>
                    </div>

                    <div class="formGroup">
                        <label for="description">
                            Description *
                        </label>

                        <textarea id="description" name="description" rows="5"
                            placeholder="Write details about the item" maxlength="3000"
                            required><?php echo htmlspecialchars($description); ?></textarea>
                    </div>

                    <div class="formGroup">
                        <label for="image">
                            Item Image
                        </label>

                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">

                        <small>
                            Allowed files: JPG, PNG and WEBP. Maximum size: 2 MB.
                        </small>
                    </div>

                    <button type="submit" class="submitButton">
                        Submit Report
                    </button>

                </form>

            </div>

        </section>

    </main>

    <script src="js/script.js"></script>

</body>

</html>