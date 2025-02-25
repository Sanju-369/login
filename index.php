<?php
session_start();
require 'vendor/autoload.php'; // Google API Client Library

use Google\Client;
use Google\Service\Sheets;

// Read the JSON content from the environment variable
$serviceAccountJson = getenv('GOOGLE_APPLICATION_CREDENTIALS');

if (!$serviceAccountJson) {
    die("Error: GOOGLE_APPLICATION_CREDENTIALS environment variable is not set.");
}

// Decode JSON from the environment variable
$serviceAccountArray = json_decode($serviceAccountJson, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    die("Error: Invalid JSON format in GOOGLE_APPLICATION_CREDENTIALS.");
}

// Initialize Google Client
$client = new Client();
$client->setAuthConfig($serviceAccountArray);
$client->setScopes([Sheets::SPREADSHEETS]);

$service = new Sheets($client);
$spreadsheetId = "1e7rZcQ93-KweIxpFv5xEy21544-r1-VYOK-QxGco_zM"; // Replace with your Google Sheet ID
$range = "Sheet1!A:B"; // Adjust columns as needed

$error = "";

// Logout Logic: Destroy session immediately and invalidate token
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Login Process
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sub_id = trim($_POST['sub_id'] ?? '');

    if (!empty($sub_id)) {
        // Read data from Google Sheet
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $isValid = false;
        foreach ($values as $row) {
            if ($row[0] == $sub_id) {
                $isValid = true;
                break;
            }
        }

        if ($isValid) {
            // Generate a unique session token for validation
            $_SESSION['sub_id'] = $sub_id;
            $_SESSION['logged_in'] = true;
            $_SESSION['token'] = bin2hex(random_bytes(32)); // Secure 64-character token

            // Redirect to Streamlit app with token
            header("Location: https://youutuberesearcher.streamlit.app/?token=" . $_SESSION['token']);
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
            background-image: linear-gradient(to right, #1a2a6c, #b21f1f, #fdbb2d);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }
        .login-container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
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
