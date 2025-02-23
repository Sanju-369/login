<?php
session_start();
header('Content-Type: application/json');

// ✅ Session Timeout Check (Same as index.php, 15 minutes)
$sessionTimeout = 15 * 60; // 15 minutes in seconds
if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time'] > $sessionTimeout)) {
    session_destroy();
    echo json_encode(["valid" => false, "message" => "Session expired. Please log in again."]);
    exit();
}

// ✅ Ensure user is logged in
if (!isset($_SESSION['token']) || !isset($_SESSION['sub_id'])) {
    echo json_encode(["valid" => false, "message" => "Unauthorized access. Please log in."]);
    exit();
}

// ✅ Validate token
$token = $_GET['token'] ?? '';
if ($_SESSION['token'] !== $token) {
    echo json_encode(["valid" => false, "message" => "Invalid session token."]);
    exit();
}

// ✅ Update session time (Keep session active if valid)
$_SESSION['login_time'] = time();

// ✅ Token is valid, return user ID
echo json_encode(["valid" => true, "sub_id" => $_SESSION['sub_id']]);
exit();
?>
