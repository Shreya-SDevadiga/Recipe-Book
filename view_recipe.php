<?php
// view_recipe.php
include 'db.php';

// get recipe id from query string and sanitize
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid recipe id.");
}
$id = (int) $_GET['id'];

// fetch recipe
$stmt = $conn->prepare("SELECT recipes.*, users.name AS user_name FROM recipes JOIN users ON recipes.user_id = users.id WHERE recipes.id = ? AND recipes.status = 'Approved'");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Recipe not found or not approved.");
}

$row = $result->fetch_assoc();

// helper: split text by newlines into trimmed non-empty items
function split_lines($text) {
    $parts = preg_split('/\r\n|\r|\n/', $text);
    $out = [];
    foreach ($parts as $p) {
        $t = trim($p);
        if ($t !== '') $out[] = $t;
    }
    return $out;
}

// prepare arrays
$ingredients = split_lines($row['ingredients'] ?? '');
$steps = split_lines($row['steps'] ?? '');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?php echo htmlspecialchars($row['title']); ?> - Recipe</title>
    <style>
        body { font-family: Arial, sans-serif; background:#fafafa; margin:0; padding:20px; }
        .card { max-width:800px; margin:20px auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.08); }
        img { max-width:100%; border-radius:6px; }
        h1 { margin:0 0 10px; }
        .meta { color:#666; margin-bottom:20px; }
        .section { margin-top:20px; }
        ul { padding-left:20px; }
        ol { padding-left:20px; }
        .back { display:inline-block; margin-top:15px; padding:8px 12px; background:#007bff; color:#fff; text-decoration:none; border-radius:5px; }
    </style>
</head>
<body>
<div class="card">
    <?php if (!empty($row['image'])): ?>
        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="">
    <?php endif; ?>

    <h1><?php echo htmlspecialchars($row['title']); ?></h1>
    <div class="meta">
        <strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?> |
        <strong>By:</strong> <?php echo htmlspecialchars($row['user_name']); ?> |
        <strong>Posted:</strong> <?php echo htmlspecialchars($row['created_at']); ?>
    </div>

    <div class="section">
        <h2>Ingredients</h2>
        <?php if (count($ingredients) > 0): ?>
            <ul>
                <?php foreach ($ingredients as $ing): ?>
                    <li><?php echo htmlspecialchars($ing); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No ingredients listed.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Steps</h2>
        <?php if (count($steps) > 0): ?>
            <ol>
                <?php foreach ($steps as $s): ?>
                    <li><?php echo nl2br(htmlspecialchars($s)); ?></li>
                <?php endforeach; ?>
            </ol>
        <?php else: ?>
            <p>No steps provided.</p>
        <?php endif; ?>
    </div>

    <a class="back" href="index.php">← Back to Recipes</a>
</div>
</body>
</html>