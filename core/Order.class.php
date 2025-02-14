<?php
class Order {
    private $id; // Уникальный идентификатор заказа
    private $customer; // Имя клиента
    private $email; // Электронная почта клиента
    private $phone; // Телефон клиента
    private $address; // Адрес клиента
    private $created; // Дата и время создания заказа
    private $items; // Ассоциативный массив с товарами (item_id => quantity)

    // Конструктор класса Order, инициализирует свойства
    public function __construct($customer, $email, $phone, $address, $items = []) {
        $this->items = $items; // Инициализация массива товаров
        $this->address = $address; // Установка адреса
        $this->phone = $phone; // Установка телефона
        $this->email = $email; // Установка электронной почты
        $this->customer = $customer; // Установка имени клиента
        $this->created = date('Y-m-d H:i:s'); // Устанавливаем дату создания заказа
    }

    // Получение имени клиента
    public function getCustomer() {
        return $this->customer;
    }

    // Получение электронной почты клиента
    public function getEmail() {
        return $this->email;
    }

    // Получение телефона клиента
    public function getPhone() {
        return $this->phone;
    }

    // Получение адреса клиента
    public function getAddress() {
        return $this->address;
    }

    // Получение даты создания заказа
    public function getCreated() {
        return $this->created;
    }

    // Метод для добавления товара в заказ
    public function addItem($itemId, $quantity) {
        // Проверка, если товар уже существует в заказе
        if (isset($this->items[$itemId])) {
            $this->items[$itemId] += $quantity; // Увеличиваем количество, если товар уже есть
        } else {
            $this->items[$itemId] = $quantity; // Иначе добавляем новый товар
        }
    }

    // Получение идентификатора заказа
    public function getId() {
        return $this->id; // ID будет установлен после сохранения
    }

    // Установка идентификатора заказа
    public function setId($id) {
        $this->id = $id; // Устанавливаем ID заказа
    }
}
