<?php
// Start session safely (prevents duplicate start warnings)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';

$message = "";

// Approve recipe
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE recipes SET status='Approved' WHERE id='$id'");
    $message = "✅ Recipe approved!";
}

// Reject recipe
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
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
            font-family: Arial, sans-serif;
            background: #f2f2f2;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 90%;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th {
            background: #007bff;
            color: white;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        a {
            padding: 6px 12px;
            background: #007bff;
            color: white;
            border-radius: 5px;
            text-decoration: none;
            margin-right: 5px;
            display: inline-block;
        }
        a.reject {
            background: red;
        }
        a:hover {
            opacity: 0.85;
        }
        .message {
            color: green;
            font-weight: bold;
            margin: 15px 0;
        }
        .logout {
            float: right;
            background: #e84118;
            color: #fff;
            padding: 8px 15px;
            border-radius: 6px;
            text-decoration: none;
        }
        .logout:hover {
            background: #c23616;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>👩‍💼 Welcome, Admin <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
    <a href="logout.php" class="logout">Logout</a>

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
            <td><?php echo htmlspecialchars($row['user_name']); ?></td>
            <td><?php echo htmlspecialchars($row['title']); ?></td>
            <td><?php echo htmlspecialchars($row['category']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
            <td>
                <?php if (!empty($row['image'])) { ?>
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="Recipe Image">
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
