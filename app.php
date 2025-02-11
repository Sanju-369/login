
<?php
session_start(); // Start session to track login status

// Check if the user is logged in
if (!isset($_SESSION['sub_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

$sub_id = $_SESSION['sub_id']; // Retrieve the logged-in user's sub_id
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the App</title>
</head>
<body>
    <h2>Welcome to the App</h2>
    <p>Your Subscription ID: <strong><?php echo htmlspecialchars($sub_id); ?></strong></p>
    
    <!-- Add Streamlit App Button -->
    <p>
        <a href="https://youutuberesearcher.streamlit.app/" target="_blank">
            <button>Go to Streamlit App</button>
        </a>
    </p>

    <p><a href="logout.php">Logout</a></p>
</body>
</html>
