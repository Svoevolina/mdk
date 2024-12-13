<?php
// Подключение к базе данных SQLite
$db = new PDO('sqlite:products.db');

// Создание таблиц
$db->exec("CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    product_id INTEGER NOT NULL,
    rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)");

// Обработка добавления нового товара
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Вставка товара
    $stmt = $db->prepare("INSERT INTO products (name, description, price) VALUES (:name, :description, :price)");
    $stmt->execute(['name' => $name, 'description' => $description, 'price' => $price]);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка добавления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $product_id = $_POST['product_id'];
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];

    // Вставка отзыва
    $stmt = $db->prepare("INSERT INTO reviews (product_id, rating, comment) VALUES (:product_id, :rating, :comment)");
    $stmt->execute(['product_id' => $product_id, 'rating' => $rating, 'comment' => $comment]);

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Получение всех товаров
$products = $db->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Listing</title>
</head>
<body>
    <h1>Product Listings</h1>

    <h2>Add a Product</h2>
    <form method="POST">
        <label for="name">Product Name:</label>
        <input type="text" name="name" required>
        <br>
        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <br>
        <label for="price">Price:</label>
        <input type="number" name="price" step="0.01" required>
        <br>
        <input type="submit" name="add_product" value="Add Product">
    </form>

    <h2>Available Products</h2>
    <ul>
        <?php foreach ($products as $product): ?>
            <li>
                <strong><?php echo htmlspecialchars($product['name']); ?></strong> - $<?php echo htmlspecialchars($product['price']); ?>
                <p><?php echo htmlspecialchars($product['description']); ?></p>

                <h3>Reviews</h3>
                <form method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <label for="rating">Rating (1-5):</label>
                    <input type="number" name="rating" min="1" max="5" required>
                    <br>
                    <label for="comment">Comment:</label>
                    <textarea name="comment" required></textarea>
                    <br>
                    <input type="submit" name="add_review" value="Add Review">
                </form>

                <h4>Existing Reviews:</h4>
                <ul>
                    <?php
                    $reviews = $db->prepare("SELECT * FROM reviews WHERE product_id = :product_id");
                    $reviews->execute(['product_id' => $product['id']]);
                    $product_reviews = $reviews->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($product_reviews as $review): ?>
                        <li>
                            Rating: <?php echo htmlspecialchars($review['rating']); ?>
                            <p><?php echo htmlspecialchars($review['comment']); ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
