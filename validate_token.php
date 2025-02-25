<?php
session_start();

// ✅ Ensure a token is provided in the request
if (!isset($_GET['token'])) {
    http_response_code(400);
    echo "MISSING_TOKEN";
    exit();
}

$token = $_GET['token'];

// ✅ Validate token against session
if (isset($_SESSION['token']) && $_SESSION['token'] === $token) {
    echo "VALID";
} else {
    http_response_code(403);
    echo "INVALID_TOKEN";
}
exit();
?>
