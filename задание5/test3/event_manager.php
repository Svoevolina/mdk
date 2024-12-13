<?php
session_start();

// Инициализация массива событий в сессии
if (!isset($_SESSION['events'])) {
    $_SESSION['events'] = [];
}

// Обработка добавления события
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_event'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_datetime = $event_date . ' ' . $event_time;

    // Добавляем событие в сессию
    $_SESSION['events'][] = [
        'name' => $event_name,
        'datetime' => $event_datetime,
    ];

    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$upcoming_events = [];

// Получаем текущее время
$current_time = new DateTime();

// Проверяем предстоящие события
foreach ($_SESSION['events'] as $event) {
    $event_time = new DateTime($event['datetime']);
    if ($event_time > $current_time) {
        $upcoming_events[] = $event;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Manager</title>
</head>
<body>
    <h1>Event Manager</h1>
    
    <h2>Add Event</h2>
    <form action="" method="POST">
        <input type="text" name="event_name" required placeholder="Event Name">
        <input type="date" name="event_date" required>
        <input type="time" name="event_time" required>
        <button type="submit" name="add_event">Add Event</button>
    </form>

    <h2>Upcoming Events</h2>
    <?php if (empty($upcoming_events)): ?>
        <p>No upcoming events.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($upcoming_events as $event): ?>
                <li>
                    <?= htmlspecialchars($event['name']) ?> at <?= htmlspecialchars($event['datetime']) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>
</html>
