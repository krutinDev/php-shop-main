<?php
class Basket {
    private $items = []; // Инициализация массива для хранения товаров в корзине
    private const COOKIE_NAME = 'eshop'; // Название cookie для сохранения состояния корзины

    public function init() {
        // Проверяем, существует ли cookie с данными корзины
        if (isset($_COOKIE[self::COOKIE_NAME])) {
            $this->read(); // Загружаем данные корзины из cookie
        } else {
            $this->create(); // Если cookie нет, создаем новую корзину
        }
    }

    public function getItems() {
        return $this->items; // Возвращаем список товаров в корзине
    }

    public function add($itemId, $quantity) {
        // Метод для добавления товара в корзину
        if (isset($this->items[$itemId])) {
            $this->items[$itemId] += $quantity; // Увеличиваем количество, если товар уже есть
        } else {
            $this->items[$itemId] = $quantity; // Добавляем новый товар, если его нет
        }
        $this->save(); // Обновляем cookie с изменениями
    }

    public function remove($itemId) {
        // Удаляем товар из корзины, если он присутствует
        if (isset($this->items[$itemId])) {
            unset($this->items[$itemId]); // Удаляем товар из массива
            $this->save(); // Обновляем cookie после удаления
        }
    }

    public function create() {
        // Метод для инициализации пустой корзины
        $this->items = []; // Обнуляем массив товаров
        $this->save(); // Сохраняем пустую корзину в cookie
    }

    public function save() {
        // Метод для сохранения текущего состояния корзины в cookie
        setcookie(self::COOKIE_NAME, json_encode($this->items), time() + 86400, '/'); // Держим cookie в течение 1 дня
    }

    public function read() {
        // Метод для загрузки данных корзины из cookie
        $this->items = json_decode($_COOKIE[self::COOKIE_NAME], true); // Декодируем JSON в массив
    }
}