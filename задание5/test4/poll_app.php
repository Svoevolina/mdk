<?php
// Подключение к базе данных SQLite
$db = new PDO('sqlite:polls.db');

// Создание таблиц
$db->exec("CREATE TABLE IF NOT EXISTS polls (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    question TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)");

$db->exec("CREATE TABLE IF NOT EXISTS options (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    poll_id INTEGER NOT NULL,
    option_text TEXT NOT NULL,
    votes INTEGER DEFAULT 0,
    FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
)");

// Обработка создания нового опроса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_poll'])) {
    $question = $_POST['question'];
    $options = explode(',', $_POST['options']);

    // Вставка опроса
    $stmt = $db->prepare("INSERT INTO polls (question) VALUES (:question)");
    $stmt->execute(['question' => $question]);
    $poll_id = $db->lastInsertId();

    // Вставка вариантов ответов
    foreach ($options as $option) {
        $stmt = $db->prepare("INSERT INTO options (poll_id, option_text) VALUES (:poll_id, :option_text)");
        $stmt->execute(['poll_id' => $poll_id, 'option_text' => trim($option)]);
    }

    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка голосования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['vote'])) {
    $option_id = $_POST['option_id'];
    $stmt = $db->prepare("UPDATE options SET votes = votes + 1 WHERE id = :id");
    $stmt->execute(['id' => $option_id]);
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit;
}

// Получение всех опросов
$polls = $db->query("SELECT * FROM polls")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Poll App</title>
</head>
<body>
    <h1>Poll Application</h1>

    <h2>Create a Poll</h2>
    <form method="POST">
        <label for="question">Question:</label>
        <input type="text" name="question" required>
        <br>
        <label for="options">Options (comma separated):</label>
        <input type="text" name="options" required>
        <br>
        <input type="submit" name="create_poll" value="Create Poll">
    </form>

    <h2>Existing Polls</h2>
    <ul>
        <?php foreach ($polls as $poll): ?>
            <li>
                <strong><?php echo htmlspecialchars($poll['question']); ?></strong>
                <form method="POST" style="display:inline;">
                    <?php
                    $options = $db->prepare("SELECT * FROM options WHERE poll_id = :poll_id");
                    $options->execute(['poll_id' => $poll['id']]);
                    $poll_options = $options->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($poll_options as $option): ?>
                        <div>
                            <input type="radio" name="option_id" value="<?php echo $option['id']; ?>" required>
                            <?php echo htmlspecialchars($option['option_text']); ?>
                        </div>
                    <?php endforeach; ?>
                    <input type="submit" name="vote" value="Vote">
                    <a href="results.php?id=<?php echo $poll['id']; ?>">Results</a>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
