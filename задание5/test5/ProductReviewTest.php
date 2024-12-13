<?php

use PHPUnit\Framework\TestCase;

class ProductReviewTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        // Создаем временную базу данных SQLite для тестов
        $this->db = new PDO('sqlite::memory:');
        $this->db->exec("CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT NOT NULL,
            price DECIMAL(10, 2) NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        $this->db->exec("CREATE TABLE reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            product_id INTEGER NOT NULL,
            rating INTEGER NOT NULL CHECK (rating >= 1 AND rating <= 5),
            comment TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
        )");
    }

    public function testCreateProduct()
    {
        // Создаем новый продукт
        $stmt = $this->db->prepare("INSERT INTO products (name, description, price) VALUES (:name, :description, :price)");
        $stmt->execute(['name' => 'Test Product', 'description' => 'This is a test product.', 'price' => 19.99]);

        // Проверяем, что продукт был создан
        $stmt = $this->db->query("SELECT * FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($product);
        $this->assertEquals('Test Product', $product['name']);
        $this->assertEquals('This is a test product.', $product['description']);
        $this->assertEquals(19.99, $product['price']);
    }

    public function testReadProduct()
    {
        // Создаем продукт для тестирования
        $this->testCreateProduct();

        // Читаем продукт
        $stmt = $this->db->query("SELECT * FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals('Test Product', $product['name']);
    }

    public function testUpdateProduct()
    {
        // Создаем продукт для тестирования
        $this->testCreateProduct();

        // Обновляем продукт
        $stmt = $this->db->prepare("UPDATE products SET price = :price WHERE name = :name");
        $stmt->execute(['price' => 29.99, 'name' => 'Test Product']);

        // Проверяем, что цена обновилась
        $stmt = $this->db->query("SELECT * FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(29.99, $product['price']);
    }

    public function testDeleteProduct()
    {
        // Создаем продукт для тестирования
        $this->testCreateProduct();

        // Удаляем продукт
        $stmt = $this->db->prepare("DELETE FROM products WHERE name = :name");
        $stmt->execute(['name' => 'Test Product']);

        // Проверяем, что продукт был удален
        $stmt = $this->db->query("SELECT * FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($product);
    }

    public function testCreateReview()
    {
        // Создаем продукт для тестирования
        $this->testCreateProduct();

        // Получаем ID продукта
        $stmt = $this->db->query("SELECT id FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Создаем новый отзыв
        $stmt = $this->db->prepare("INSERT INTO reviews (product_id, rating, comment) VALUES (:product_id, :rating, :comment)");
        $stmt->execute(['product_id' => $product['id'], 'rating' => 5, 'comment' => 'Great product!']);

        // Проверяем, что отзыв был создан
        $stmt = $this->db->query("SELECT * FROM reviews WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertNotFalse($review);
        $this->assertEquals(5, $review['rating']);
        $this->assertEquals('Great product!', $review['comment']);
    }

    public function testReadReview()
    {
        // Создаем продукт и отзыв для тестирования
        $this->testCreateReview();

        // Получаем ID продукта
        $stmt = $this->db->query("SELECT id FROM products WHERE name = 'Test Product'");
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        // Читаем отзыв
        $stmt = $this->db->query("SELECT * FROM reviews WHERE product_id = :product_id");
        $stmt->bindParam(':product_id', $product['id']);
        $stmt->execute();
        $review = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(5, $review['rating']);
    }

    public function testUpdateReview()
    {
        // Создаем продукт и отзыв для тестирования
        $this->testCreateReview();

        // Получаем ID отзыва
        $stmt = $this->db->query("SELECT * FROM reviews");
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        // Обновляем отзыв
        $stmt = $this->db->prepare("UPDATE reviews SET rating = :rating WHERE id = :id");
        $stmt->execute(['rating' => 4, 'id' => $review['id']]);

        // Проверяем, что рейтинг обновился
        $stmt = $this->db->query("SELECT * FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $review['id']);
        $stmt->execute();
        $updated_review = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals(4, $updated_review['rating']);
    }

    public function testDeleteReview()
    {
        // Создаем продукт и отзыв для тестирования
        $this->testCreateReview();

        // Получаем ID отзыва
        $stmt = $this->db->query("SELECT * FROM reviews");
        $review = $stmt->fetch(PDO::FETCH_ASSOC);

        // Удаляем отзыв
        $stmt = $this->db->prepare("DELETE FROM reviews WHERE id = :id");
        $stmt->execute(['id' => $review['id']]);

        // Проверяем, что отзыв был удален
        $stmt = $this->db->query("SELECT * FROM reviews WHERE id = :id");
        $stmt->bindParam(':id', $review['id']);
        $stmt->execute();
        $deleted_review = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertFalse($deleted_review);
    }

    protected function tearDown(): void
    {
        // Очищаем базу данных
        $this->db = null;
    }
}
