<?php

use PHPUnit\Framework\TestCase;

class LibraryManagerTest extends TestCase
{
    private $pdo;

    protected function setUp(): void
    {
        // Настройка подключения к базе данных
        $this->pdo = new PDO('mysql:host=localhost;dbname=library_manager', 'root', '');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Очистка таблиц перед каждым тестом
        $this->pdo->exec("DELETE FROM borrows");
        $this->pdo->exec("DELETE FROM books");
        $this->pdo->exec("DELETE FROM readers");
    }

    public function testAddBook()
    {
        // Добавляем книгу
        $stmt = $this->pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
        $stmt->execute(['title' => 'Test Book', 'author' => 'Test Author']);

        // Проверяем, что книга добавлена
        $stmt = $this->pdo->query("SELECT * FROM books");
        $books = $stmt->fetchAll();
        $this->assertCount(1, $books);
        $this->assertEquals('Test Book', $books[0]['title']);
        $this->assertEquals('Test Author', $books[0]['author']);
    }

    public function testDeleteBook()
    {
        // Добавляем книгу
        $stmt = $this->pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
        $stmt->execute(['title' => 'Test Book', 'author' => 'Test Author']);

        // Удаляем книгу
        $stmt = $this->pdo->prepare("DELETE FROM books WHERE id = 1");
        $stmt->execute();

        // Проверяем, что книга удалена
        $stmt = $this->pdo->query("SELECT * FROM books");
        $books = $stmt->fetchAll();
        $this->assertCount(0, $books);
    }

    public function testBorrowBook()
    {
        // Добавляем книгу и читателя
        $stmt = $this->pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
        $stmt->execute(['title' => 'Test Book', 'author' => 'Test Author']);
        
        $stmt = $this->pdo->prepare("INSERT INTO readers (name) VALUES (:name)");
        $stmt->execute(['name' => 'Test Reader']);

        // Выдаём книгу читателю
        $stmt = $this->pdo->prepare("INSERT INTO borrows (book_id, reader_id) VALUES (:book_id, :reader_id)");
        $stmt->execute(['book_id' => 1, 'reader_id' => 1]);

        // Проверяем, что книга выдана
        $stmt = $this->pdo->query("SELECT * FROM borrows");
        $borrows = $stmt->fetchAll();
        $this->assertCount(1, $borrows);
        $this->assertEquals(1, $borrows[0]['book_id']);
        $this->assertEquals(1, $borrows[0]['reader_id']);
    }

    public function testReturnBook()
    {
        // Добавляем книгу и читателя
        $stmt = $this->pdo->prepare("INSERT INTO books (title, author) VALUES (:title, :author)");
        $stmt->execute(['title' => 'Test Book', 'author' => 'Test Author']);
        
        $stmt = $this->pdo->prepare("INSERT INTO readers (name) VALUES (:name)");
        $stmt->execute(['name' => 'Test Reader']);

        // Выдаём книгу читателю
        $stmt = $this->pdo->prepare("INSERT INTO borrows (book_id, reader_id) VALUES (:book_id, :reader_id)");
        $stmt->execute(['book_id' => 1, 'reader_id' => 1]);

        // Возвращаем книгу
        $stmt = $this->pdo->prepare("DELETE FROM borrows WHERE book_id = 1 AND reader_id = 1");
        $stmt->execute();

        // Проверяем, что книга возвращена
        $stmt = $this->pdo->query("SELECT * FROM borrows");
        $borrows = $stmt->fetchAll();
        $this->assertCount(0, $borrows);
    }

    protected function tearDown(): void
    {
        // Очистка базы данных после тестов
        $this->pdo->exec("DELETE FROM borrows");
        $this->pdo->exec("DELETE FROM books");
        $this->pdo->exec("DELETE FROM readers");
    }
}
