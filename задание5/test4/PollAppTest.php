<?php

use PHPUnit\Framework\TestCase;

class PollAppTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Создаем временную базу данных SQLite для тестов
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE polls (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            question TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->exec("CREATE TABLE options (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            poll_id INTEGER NOT NULL,
            option_text TEXT NOT NULL,
            votes INTEGER DEFAULT 0,
            FOREIGN KEY (poll_id) REFERENCES polls(id) ON DELETE CASCADE
        )");
    }

    public function testCreatePoll()
    {
        // Создаем опрос
        $question = "What is your favorite color?";
        $options = ["Red", "Green", "Blue"];
        
        // Вставка опроса
        $stmt = $this->db->prepare("INSERT INTO polls (question) VALUES (:question)");
        $stmt->execute(['question' => $question]);
        $poll_id = $this->db->lastInsertId();

        // Вставка вариантов ответов
        foreach ($options as $option) {
            $stmt = $this->db->prepare("INSERT INTO options (poll_id, option_text) VALUES (:poll_id, :option_text)");
            $stmt->execute(['poll_id' => $poll_id, 'option_text' => $option]);
        }

        // Проверяем, что опрос был создан
        $stmt = $this->db->query("SELECT * FROM polls WHERE id = $poll_id");
        $poll = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($question, $poll['question']);
    }

    public function testVote()
    {
        // Создаем опрос и варианты ответов
        $this->testCreatePoll();

        // Получаем poll_id
        $poll_id = $this->db->lastInsertId();
        $stmt = $this->db->query("SELECT * FROM options WHERE poll_id = $poll_id");
        $option = $stmt->fetch(PDO::FETCH_ASSOC);

        // Голосуем за вариант
        $option_id = $option['id'];
        $stmt = $this->db->prepare("UPDATE options SET votes = votes + 1 WHERE id = :id");
        $stmt->execute(['id' => $option_id]);

        // Проверяем, что голос был учтен
        $stmt = $this->db->query("SELECT * FROM options WHERE id = $option_id");
        $updated_option = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $updated_option['votes']);
    }

    public function testGetResults()
    {
        // Создаем опрос и варианты ответов
        $this->testCreatePoll();

        // Получаем poll_id
        $poll_id = $this->db->lastInsertId();
        $stmt = $this->db->query("SELECT * FROM options WHERE poll_id = $poll_id");
        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Проверяем, что мы можем получить результаты
        $this->assertCount(3, $options); // Должно быть 3 варианта
    }

    protected function tearDown(): void
    {
        // Очищаем базу данных
        $this->db = null;
    }
}
