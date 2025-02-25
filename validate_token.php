<?php
session_start();
header('Content-Type: text/plain');

// ✅ Check if token is provided
if (!isset($_GET['token'])) {
    http_response_code(400);
    echo "MISSING_TOKEN";
    exit();
}

$token = $_GET['token'];

// ✅ Ensure token exists in session and matches
if (isset($_SESSION['token']) && $_SESSION['token'] === $token) {
    echo "VALID";
    exit();
}

// ❌ Token is invalid or expired
http_response_code(403);
echo "INVALID_TOKEN";
exit();
?>
