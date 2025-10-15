<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Add Recipe
if (isset($_POST['add_recipe'])) {
    $title = $_POST['title'];
    $ingredients = $_POST['ingredients'];
    $steps = $_POST['steps'];
    $category = $_POST['category'];

    // Handle image upload
    $image_name = "";
    if (!empty($_FILES['image']['name'])) {
        $image_name = time() . "_" . basename($_FILES['image']['name']);
        $target = "images/" . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
    }

    $sql = "INSERT INTO recipes (user_id, title, ingredients, steps, category, image, status)
            VALUES ('$user_id', '$title', '$ingredients', '$steps', '$category', '$image_name', 'Pending')";
    if ($conn->query($sql)) {
        $message = "Recipe added successfully! Waiting for admin approval.";
    } else {
        $message = "Error: " . $conn->error;
    }
}

// Fetch all user recipes
$recipes = $conn->query("SELECT * FROM recipes WHERE user_id='$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Recipe Book</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }
        .container {
            width: 80%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
        }
        button {
            background: #007bff;
            color: white;
            padding: 10px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .message {
            color: green;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            width: 80px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Welcome, <?php echo $_SESSION['name']; ?> 👋</h2>
    <p><a href="logout.php">Logout</a></p>
    <h3>Add New Recipe</h3>

    <form method="POST" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Recipe Title" required>
        <textarea name="ingredients" placeholder="Ingredients" required></textarea>
        <textarea name="steps" placeholder="Cooking Steps" required></textarea>
        <select name="category">
            <option value="Breakfast">Breakfast</option>
            <option value="Lunch">Lunch</option>
            <option value="Snacks">Snacks</option>
            <option value="Dessert">Dessert</option>
        </select>
        <input type="file" name="image">
        <button type="submit" name="add_recipe">Add Recipe</button>
    </form>

    <div class="message"><?php echo $message; ?></div>

    <h3>Your Recipes</h3>
    <table>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Image</th>
        </tr>
        <?php while ($row = $recipes->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['image']) { ?>
                    <img src="images/<?php echo $row['image']; ?>" alt="">
                <?php } else { echo "No Image"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>