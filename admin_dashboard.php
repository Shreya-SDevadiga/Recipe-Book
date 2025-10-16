<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$message = "";

// Approve recipe
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $conn->query("UPDATE recipes SET status='Approved' WHERE id='$id'");
    $message = "✅ Recipe approved!";
}

// Reject recipe
if (isset($_GET['reject'])) {
    $id = $_GET['reject'];
    $conn->query("UPDATE recipes SET status='Rejected' WHERE id='$id'");
    $message = "❌ Recipe rejected!";
}

// Fetch all recipes
$recipes = $conn->query("SELECT recipes.*, users.name AS user_name FROM recipes JOIN users ON recipes.user_id = users.id ORDER BY recipes.id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Recipe Book</title>
    <style>
        body {
            font-family: Arial;
            background: #f2f2f2;
        }
        .container {
            width: 90%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
        button, a {
            padding: 5px 10px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            text-decoration: none;
        }
        a.reject {
            background: red;
        }
        .message {
            color: green;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👩‍💼 Welcome, Admin <?php echo $_SESSION['name']; ?></h2>
    <p><a href="logout.php">Logout</a></p>

    <div class="message"><?php echo $message; ?></div>

    <h3>All Submitted Recipes</h3>
    <table>
        <tr>
            <th>Recipe ID</th>
            <th>User</th>
            <th>Title</th>
            <th>Category</th>
            <th>Status</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $recipes->fetch_assoc()) { ?>
        <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo $row['user_name']; ?></td>
            <td><?php echo $row['title']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td><?php echo $row['status']; ?></td>
            <td>
                <?php if ($row['image']) { ?>
                    <img src="images/<?php echo $row['image']; ?>" alt="">
                <?php } else { echo "No Image"; } ?>
            </td>
            <td>
                <?php if ($row['status'] == 'Pending') { ?>
                    <a href="?approve=<?php echo $row['id']; ?>">Approve</a>
                    <a href="?reject=<?php echo $row['id']; ?>" class="reject">Reject</a>
                <?php } else { echo "—"; } ?>
            </td>
        </tr>
        <?php } ?>
    </table>
</div>
</body>
</html>
