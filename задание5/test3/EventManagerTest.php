<?php

use PHPUnit\Framework\TestCase;

class EventManagerTest extends TestCase
{
    private $session;

    protected function setUp(): void
    {
        // Инициализация сессии
        $this->session = [];
        $_SESSION['events'] = [];
    }

    public function testAddEvent()
    {
        // Добавляем событие
        $_POST['add_event'] = true;
        $_POST['event_name'] = 'Test Event';
        $_POST['event_date'] = '2023-12-31';
        $_POST['event_time'] = '12:00';

        // Запуск кода добавления события
        $this->addEvent();

        // Проверяем, что событие добавлено
        $this->assertCount(1, $_SESSION['events']);
        $this->assertEquals('Test Event', $_SESSION['events'][0]['name']);
        $this->assertEquals('2023-12-31 12:00', $_SESSION['events'][0]['datetime']);
    }

    public function testEditEvent()
    {
        // Добавляем событие
        $_SESSION['events'][] = [
            'name' => 'Test Event',
            'datetime' => '2023-12-31 12:00',
        ];

        // Редактируем событие
        $_POST['edit_event'] = true;
        $_POST['event_index'] = 0;
        $_POST['event_name'] = 'Updated Event';
        $_POST['event_date'] = '2023-12-31';
        $_POST['event_time'] = '15:00';

        // Запуск кода редактирования события
        $this->editEvent();

        // Проверяем, что событие обновлено
        $this->assertEquals('Updated Event', $_SESSION['events'][0]['name']);
        $this->assertEquals('2023-12-31 15:00', $_SESSION['events'][0]['datetime']);
    }

    public function testDeleteEvent()
    {
        // Добавляем событие
        $_SESSION['events'][] = [
            'name' => 'Test Event',
            'datetime' => '2023-12-31 12:00',
        ];

        // Удаляем событие
        $_POST['delete_event'] = true;
        $_POST['event_index'] = 0;

        // Запуск кода удаления события
        $this->deleteEvent();

        // Проверяем, что событие удалено
        $this->assertCount(0, $_SESSION['events']);
    }

    public function testUpcomingEvents()
    {
        // Добавляем предстоящее событие
        $_SESSION['events'][] = [
            'name' => 'Future Event',
            'datetime' => (new DateTime('+1 hour'))->format('Y-m-d H:i'),
        ];

        // Получаем предстоящие события
        $upcoming_events = $this->getUpcomingEvents();

        // Проверяем, что предстоящее событие отображается
        $this->assertCount(1, $upcoming_events);
        $this->assertEquals('Future Event', $upcoming_events[0]['name']);
    }

    // Метод, который имитирует добавление события
    private function addEvent()
    {
        if (isset($_POST['add_event'])) {
            $event_name = $_POST['event_name'];
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'];
            $event_datetime = $event_date . ' ' . $event_time;

            $_SESSION['events'][] = [
                'name' => $event_name,
                'datetime' => $event_datetime,
            ];
        }
    }

    // Метод, который имитирует редактирование события
    private function editEvent()
    {
        if (isset($_POST['edit_event'])) {
            $index = $_POST['event_index'];
            $event_name = $_POST['event_name'];
            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'];
            $event_datetime = $event_date . ' ' . $event_time;

            $_SESSION['events'][$index] = [
                'name' => $event_name,
                'datetime' => $event_datetime,
            ];
        }
    }

    // Метод, который имитирует удаление события
    private function deleteEvent()
    {
        if (isset($_POST['delete_event'])) {
            $index = $_POST['event_index'];
            unset($_SESSION['events'][$index]);
            $_SESSION['events'] = array_values($_SESSION['events']); // Пересчитываем индексы
        }
    }

    // Метод для получения предстоящих событий
    private function getUpcomingEvents()
    {
        $upcoming_events = [];
        $current_time = new DateTime();

        foreach ($_SESSION['events'] as $event) {
            $event_time = new DateTime($event['datetime']);
            if ($event_time > $current_time) {
                $upcoming_events[] = $event;
            }
        }

        return $upcoming_events;
    }
}
