<?php
$host = 'localhost';
$db = 'library_manager';
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Обработка добавления книги
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $stmt = $pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
    $stmt->execute(['title' => $title, 'author' => $author]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка регистрации читателя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_reader'])) {
    $name = $_POST['name'];
    $stmt = $pdo->prepare("INSERT INTO readers (name) VALUES (:name)");
    $stmt->execute(['name' => $name]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка выдачи книги читателю
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_book'])) {
    $book_id = $_POST['book_id'];
    $reader_id = $_POST['reader_id'];
    $stmt = $pdo->prepare("INSERT INTO borrows (book_id, reader_id) VALUES (:book_id, :reader_id)");
    $stmt->execute(['book_id' => $book_id, 'reader_id' => $reader_id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Получаем все книги
$stmt = $pdo->query("SELECT * FROM books");
$books = $stmt->fetchAll();

// Получаем всех читателей
$stmt = $pdo->query("SELECT * FROM readers");
$readers = $stmt->fetchAll();

// Получаем все заимствования
$stmt = $pdo->query("SELECT borrows.*, books.title, readers.name FROM borrows JOIN books ON borrows.book_id = books.id JOIN readers ON borrows.reader_id = readers.id");
$borrows = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library Manager</title>
</head>
<body>
    <h1>Library Manager</h1>
    
    <h2>Add Book</h2>
    <form action="" method="POST">
        <input type="text" name="title" required placeholder="Book Title">
        <input type="text" name="author" required placeholder="Author">
        <button type="submit" name="add_book">Add Book</button>
    </form>

    <h2>Register Reader</h2>
    <form action="" method="POST">
        <input type="text" name="name" required placeholder="Reader Name">
        <button type="submit" name="register_reader">Register Reader</button>
    </form>

    <h2>Borrow Book</h2>
    <form action="" method="POST">
        <select name="book_id" required>
            <option value="">Select Book</option>
            <?php foreach ($books as $book): ?>
                <option value="<?= $book['id'] ?>"><?= htmlspecialchars($book['title']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="reader_id" required>
            <option value="">Select Reader</option>
            <?php foreach ($readers as $reader): ?>
                <option value="<?= $reader['id'] ?>"><?= htmlspecialchars($reader['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" name="borrow_book">Borrow Book</button>
    </form>

    <h2>Borrowed Books</h2>
    <ul>
        <?php foreach ($borrows as $borrow): ?>
            <li>
                <?= htmlspecialchars($borrow['name']) ?> borrowed "<?= htmlspecialchars($borrow['title']) ?>" 
                <em>(ID: <?= $borrow['id'] ?>)</em>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
