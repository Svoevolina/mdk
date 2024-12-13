<?php
$host = 'localhost';
$db = 'task_manager';
$user = 'root'; 
$pass = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Обработка добавления задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task = $_POST['task'];
    $stmt = $pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
    $stmt->execute(['task' => $task]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка удаления задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_task'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка редактирования задачи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_task'])) {
    $task = $_POST['task'];
    $id = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE tasks SET task = :task WHERE id = :id");
    $stmt->execute(['task' => $task, 'id' => $id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Обработка пометки задачи как выполненной
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_completed'])) {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = :id");
    $stmt->execute(['id' => $id]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Получаем все задачи
$stmt = $pdo->query("SELECT * FROM tasks");
$tasks = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task Manager</title>
</head>
<body>
    <h1>Task Manager</h1>
    <form action="" method="POST">
        <input type="text" name="task" required placeholder="Enter a new task">
        <button type="submit" name="add_task">Add Task</button>
    </form>

    <h2>Tasks</h2>
    <ul>
        <?php foreach ($tasks as $task): ?>
            <li>
                <form action="" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <button type="submit" name="mark_completed" <?= $task['is_completed'] ? 'disabled' : '' ?>><?= $task['is_completed'] ? 'Completed' : 'Mark as Completed' ?></button>
                </form>
                <?= htmlspecialchars($task['task']) ?>
                <a href="#" onclick="document.getElementById('edit-form-<?= $task['id'] ?>').style.display='block';">Edit</a>
                <form action="" method="POST" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $task['id'] ?>">
                    <button type="submit" name="delete_task">Delete</button>
                </form>
                <div id="edit-form-<?= $task['id'] ?>" style="display:none;">
                    <form action="" method="POST">
                        <input type="text" name="task" value="<?= htmlspecialchars($task['task']) ?>" required>
                        <input type="hidden" name="id" value="<?= $task['id'] ?>">
                        <button type="submit" name="edit_task">Update Task</button>
                    </form>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>
