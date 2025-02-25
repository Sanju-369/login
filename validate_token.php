<?php
session_start();
$tokenFile = "valid_token.txt"; // File to store the current token

if (isset($_GET['store_token'])) {
    $token = $_GET['store_token'];
    file_put_contents($tokenFile, $token); // Save token
    echo "TOKEN STORED";
    exit();
}

if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete token on logout
    }
    echo "TOKEN EXPIRED";
    exit();
}

// Validate token request
if (isset($_GET['get_token'])) {
    if (file_exists($tokenFile)) {
        echo trim(file_get_contents($tokenFile)); // Return latest token
    } else {
        echo "INVALID";
    }
    exit();
}
?>
