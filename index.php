<?php
session_start();
require 'vendor/autoload.php'; // Google API Client Library

use Google\Client;
use Google\Service\Sheets;

// Load Google Service Account JSON from Environment Variable
$serviceAccountJson = getenv('SERVICE_ACCOUNT_JSON');

if (!$serviceAccountJson) {
    die("Error: GOOGLE_APPLICATION_CREDENTIALS environment variable is not set.");
}

// Save JSON to a temporary file (because Google Client requires a file path)
$serviceAccountFile = sys_get_temp_dir() . '/service-account.json';
file_put_contents($serviceAccountFile, $serviceAccountJson);

// Initialize Google Client
$client = new Client();
$client->setAuthConfig($serviceAccountFile); // Use temporary file
$client->setScopes([Sheets::SPREADSHEETS]);

$service = new Sheets($client);
$spreadsheetId = "1e7rZcQ93-KweIxpFv5xEy21544-r1-VYOK-QxGco_zM"; // Replace with your Google Sheet ID
$range = "Sheet1!A:B"; // Adjust columns as needed

$error = "";

// Set Session Timeout (e.g., 15 minutes)
$sessionTimeout = 15 * 60; // 15 minutes in seconds

// If the user is already logged in, check for inactivity
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $sessionTimeout)) {
        // Session expired, logout the user
        session_destroy();
        header("Location: index.php?timeout=true");
        exit();
    } else {
        // Update session time to keep it active
        $_SESSION['login_time'] = time();
        
        // Redirect to Streamlit app with token
        header("Location: https://youutuberesearcher.streamlit.app/?token=" . $_SESSION['token']);
        exit();
    }
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
            $_SESSION['sub_id'] = $sub_id;
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time(); // Track session time

            // Generate a unique session token for validation
            $_SESSION['token'] = bin2hex(random_bytes(32)); // 64-character secure token

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

// Logout Logic
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* Simple styling for the login form */
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
    <?php if (isset($_GET['timeout'])) { echo "<p class='error'>Session expired. Please log in again.</p>"; } ?>
</div>

</body>
</html>
