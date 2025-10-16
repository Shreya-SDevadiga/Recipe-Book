<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_GET['id'])) {
    die("Recipe not found!");
}

$id = $_GET['id'];

// fetch recipe data
$result = $conn->query("SELECT * FROM recipes WHERE id='$id' AND user_id='$user_id'");
if ($result->num_rows == 0) {
    die("Recipe not found or access denied!");
}
$row = $result->fetch_assoc();

$message = "";

// update recipe
if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $conn->query("UPDATE recipes SET title='$title', category='$category', description='$description', status='Pending' WHERE id='$id' AND user_id='$user_id'");
    $message = "✅ Recipe updated! Waiting for admin approval again.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Recipe - Recipe Book</title>
    <style>
        body {font-family: Arial; background: #f4f4f4;}
        .container {width: 500px; margin: 50px auto; background: white; padding: 20px; border-radius: 10px;}
        input, textarea {width: 100%; padding: 10px; margin: 10px 0;}
        button {background: #007bff; color: white; padding: 10px; border: none; border-radius: 5px;}
        .message {color: green; text-align: center;}
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Recipe</h2>
    <div class="message"><?php echo $message; ?></div>
    <form method="POST">
        <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
        <input type="text" name="category" value="<?php echo $row['category']; ?>" required>
        <textarea name="description" rows="5" required><?php echo $row['description']; ?></textarea>
        <button type="submit" name="update">Update Recipe</button>
    </form>
    <p><a href="user_dashboard.php">← Back to Dashboard</a></p>
</div>
</body>
</html>
