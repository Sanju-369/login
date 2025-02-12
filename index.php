<?php
session_start();

// Fetch Database Connection URL from environment variable
$connection_url = getenv("DATABASE_URL");

if (!$connection_url) {
    die("Database connection URL not set.");
}

// Parse the connection URL
$parsed_url = parse_url($connection_url);
$host = $parsed_url["host"];
$port = $parsed_url["port"] ?? "3306";
$username = $parsed_url["user"];
$password = $parsed_url["pass"];
$dbname = ltrim($parsed_url["path"], "/");

// Connect to MySQL database using PDO
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form is submitted
$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sub_id = trim($_POST['sub_id'] ?? '');

    if (!empty($sub_id)) {
        // Check if sub_id exists in the database
        $stmt = $conn->prepare("SELECT sub_id FROM subscriptions WHERE sub_id = :sub_id");
        $stmt->bindParam(':sub_id', $sub_id, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Valid sub_id, start session and redirect to app.php
            $_SESSION['sub_id'] = $sub_id;
            header("Location: app.php");
            exit();
        } else {
            $error = "Invalid Subscription ID!";
        }
    } else {
        $error = "Please enter your Subscription ID!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }
        .login-container h2 {
            margin-bottom: 20px;
        }
        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }
        .input-group label {
            display: block;
            font-weight: bold;
        }
        .input-group input {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background: #0056b3;
        }
        .error {
            color: red;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login</h2>
    <form method="post">
        <div class="input-group">
            <label>Subscription ID</label>
            <input type="text" name="sub_id" required>
        </div>
        <button type="submit" class="btn">Login</button>
    </form>
    <?php if (!empty($error)) { echo "<p class='error'>$error</p>"; } ?>
</div>

</body>
</html>
