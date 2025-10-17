<?php
// index.php
include 'db.php'; // include your database connection

// Fetch only approved recipes
$sql = "SELECT recipes.*, users.name AS user_name FROM recipes 
        JOIN users ON recipes.user_id = users.id 
        WHERE recipes.status = 'Approved' 
        ORDER BY recipes.created_at DESC";

$recipes = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipe Book</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fafafa;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #ff7043;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .container {
            width: 90%;
            max-width: 1100px;
            margin: 30px auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .recipe-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s ease;
        }
        .recipe-card:hover {
            transform: translateY(-5px);
        }
        .recipe-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }
        .recipe-card .content {
            padding: 15px;
        }
        .recipe-card h3 {
            margin-top: 0;
            color: #333;
        }
        .recipe-card p {
            color: #555;
        }
        .by {
            font-size: 14px;
            color: #777;
        }
        .button {
            display: inline-block;
            background: #ff7043;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            transition: 0.2s ease;
        }
        .button:hover {
            background: #ff5722;
        }
    </style>
</head>
<body>

<header>
    <h1>🍲 Recipe Book</h1>
    <p>Discover delicious recipes shared by our users!</p>
</header>

<div class="container">
    <?php
    if ($recipes->num_rows > 0) {
        while ($row = $recipes->fetch_assoc()) {
            ?>
            <div class="recipe-card">
                <?php if (!empty($row['image'])): ?>
                    <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>">
                <?php else: ?>
                    <img src="default.jpg" alt="No image available">
                <?php endif; ?>

                <div class="content">
                    <h3><?php echo htmlspecialchars($row['title']); ?></h3>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>

                    <?php
                    // Show preview of steps as description
                    $previewField = 'steps';
                    if (!empty($row[$previewField])) {
                        $previewText = htmlspecialchars(substr($row[$previewField], 0, 150));
                        echo "<p>$previewText...</p>";
                    } else {
                        echo "<p>No description available.</p>";
                    }
                    ?>

                    <p class="by">👩‍🍳 By: <?php echo htmlspecialchars($row['user_name']); ?></p>

                    <!-- View Full Recipe button -->
                    <p><a class="button" href="view_recipe.php?id=<?php echo $row['id']; ?>">View Full Recipe</a></p>
                </div>
            </div>
            <?php
        }
    } else {
        echo "<p style='text-align:center; font-size:18px;'>No recipes found.</p>";
    }
    ?>
</div>

</body>
</html>
