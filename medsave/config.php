<?php
session_start();
date_default_timezone_set('Africa/Kampala');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'u559191231_medsaveus');
define('DB_PASS', 'Medsave@2025');
define('DB_NAME', 'u559191231_medsavedb');

// API Configuration
define('ANTHROPIC_API_KEY', 'apikey_01Rj2N8SVvo6BePZj99NhmiT'); // Add your Claude API key

// Connect to database
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
