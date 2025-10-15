<?php
$host = "localhost";
$user = "root";     // default XAMPP MySQL username
$pass = "";         // default XAMPP password is blank
$dbname = "recipe_book_db";

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// echo "Database connected successfully!";
?>
