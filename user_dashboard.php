<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// handle recipe delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM recipes WHERE id='$id' AND user_id='$user_id'");
    $message = "❌ Recipe deleted successfully!";
}

// fetch user's recipes
$recipes = $conn->query("SELECT * FROM recipes WHERE user_id='$user_id' ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Recipe Book</title>
    <style>
        body {font-family: Arial; background: #f2f2f2;}
        .container {width: 90%; margin: 30px auto; background: white; padding: 20px; border-radius: 10px;}
        h2 {color: #333;}
        a, button {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
        }
        a.delete {
            background: red;
        }
        a.edit {
            background: orange;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        img {
            width: 80px;
        }
        .message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👋 Welcome, <?php echo $_SESSION['name']; ?></h2>
    <p><a href="logout.php">Logout</a> | <a href="add_recipe.php">➕ Add New Recipe</a></p>

    <div class="message"><?php echo $message; ?></div>

    <h3>📋 Your Recipes</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php if ($recipes->num_rows > 0) { ?>
            <?php while ($row = $recipes->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['title']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['status']; ?></td>
                    <td>
                        <?php if ($row['image']) { ?>
                            <img src="images/<?php echo $row['image']; ?>" alt="">
                        <?php } else { echo "No Image"; } ?>
                    </td>
                    <td>
                        <a href="edit_recipe.php?id=<?php echo $row['id']; ?>" class="edit">✏ Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this recipe?')">🗑 Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="6">No recipes added yet.</td></tr>
        <?php } ?>
    </table>
</div>
</body>
</html>