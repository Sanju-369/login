<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure App</title>
  <style>
    body { margin: 0; padding: 0; overflow: hidden; }
    iframe { width: 100vw; height: 100vh; border: none; }
    .logout-btn {
      position: absolute;
      top: 10px;
      right: 10px;
      background: red;
      color: white;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      font-weight: bold;
    }
    .logout-btn:hover {
      background: darkred;
    }
  </style>
</head>
<body>

  <a href="index.php?logout=true" class="logout-btn">Logout</a>
  <iframe src="https://youutuberesearcher.streamlit.app/" sandbox="allow-scripts allow-same-origin"></iframe>

</body>
</html>
