<?php

use PHPUnit\Framework\TestCase;

class TaskManagerTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Настройка подключения к базе данных
        $this->pdo = new PDO('mysql:host=localhost;dbname=task_manager', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Очистка таблицы перед каждым тестом
        $this->pdo->exec("DELETE FROM tasks");
    }

    public function testAddTask()
    {
        // Добавляем задачу
        $stmt = $this->pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
        $stmt->execute(['task' => 'Test Task']);

        // Проверяем, что задача добавлена
        $stmt = $this->pdo->query("SELECT * FROM tasks");
        $tasks = $stmt->fetchAll();
        $this->assertCount(1, $tasks);
        $this->assertEquals('Test Task', $tasks[0]['task']);
    }

    public function testEditTask()
    {
        // Добавляем задачу
        $stmt = $this->pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
        $stmt->execute(['task' => 'Test Task']);

        // Редактируем задачу
        $stmt = $this->pdo->prepare("UPDATE tasks SET task = :task WHERE id = 1");
        $stmt->execute(['task' => 'Updated Task']);

        // Проверяем, что задача обновлена
        $stmt = $this->pdo->query("SELECT * FROM tasks WHERE id = 1");
        $task = $stmt->fetch();
        $this->assertEquals('Updated Task', $task['task']);
    }

    public function testDeleteTask()
    {
        // Добавляем задачу
        $stmt = $this->pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
        $stmt->execute(['task' => 'Test Task']);

        // Удаляем задачу
        $stmt = $this->pdo->prepare("DELETE FROM tasks WHERE id = 1");
        $stmt->execute();

        // Проверяем, что задача удалена
        $stmt = $this->pdo->query("SELECT * FROM tasks");
        $tasks = $stmt->fetchAll();
        $this->assertCount(0, $tasks);
    }

    public function testMarkTaskAsCompleted()
    {
        // Добавляем задачу
        $stmt = $this->pdo->prepare("INSERT INTO tasks (task) VALUES (:task)");
        $stmt->execute(['task' => 'Test Task']);

        // Помечаем задачу как выполненную
        $stmt = $this->pdo->prepare("UPDATE tasks SET is_completed = 1 WHERE id = 1");
        $stmt->execute();

        // Проверяем, что задача помечена как выполненная
        $stmt = $this->pdo->query("SELECT * FROM tasks WHERE id = 1");
        $task = $stmt->fetch();
        $this->assertEquals(1, $task['is_completed']);
    }

    protected function tearDown(): void
    {
        // Очистка базы данных после тестов
        $this->pdo->exec("DELETE FROM tasks");
    }
}
