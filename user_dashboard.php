<?php
session_start();
include 'db.php';

// 🔒 Check if user is logged in and is a normal user
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// 🗑 Handle recipe delete safely using prepared statement
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']); // prevent SQL injection
    $stmt = $conn->prepare("DELETE FROM recipes WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message = "❌ Recipe deleted successfully!";
    } else {
        $message = "⚠️ Could not delete recipe. Please try again.";
    }
    $stmt->close();
}

// 📋 Fetch user’s recipes
$stmt = $conn->prepare("SELECT * FROM recipes WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$recipes = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard - Recipe Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f2f2f2;
        }
        .container {
            width: 90%;
            margin: 30px auto;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        a, button {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }
        a.delete {
            background: #dc3545;
        }
        a.edit {
            background: #ff9800;
        }
        a:hover {
            opacity: 0.9;
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
            height: 60px;
            object-fit: cover;
            border-radius: 5px;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .top-links {
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👋 Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h2>

    <div class="top-links">
        <a href="logout.php">Logout</a> |
        <a href="add_recipe.php">➕ Add New Recipe</a>
    </div>

    <?php if (!empty($message)) { ?>
        <div class="message"><?php echo htmlspecialchars($message); ?></div>
    <?php } ?>

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
                    <td><?php echo htmlspecialchars($row['title']); ?></td>
                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <?php if (!empty($row['image'])) { ?>
                            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="Recipe image">
                        <?php } else { ?>
                            No Image
                        <?php } ?>
                    </td>
                    <td>
                        <a href="edit_recipe.php?id=<?php echo $row['id']; ?>" class="edit">✏ Edit</a>
                        <a href="?delete=<?php echo $row['id']; ?>" class="delete" onclick="return confirm('Are you sure you want to delete this recipe?')">🗑 Delete</a>
                    </td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr><td colspan="6" style="text-align:center;">No recipes added yet.</td></tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
