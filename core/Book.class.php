<?php
class Book {
    // Заголовок книги
    private $title;
    // Автор книги
    private $author;
    // Год публикации
    private $pubyear;
    // Цена книги
    private $price;

    // Конструктор класса, инициализирующий основные параметры книги
    public function __construct($title, $author, $pubyear, $price) {
        $this->title = $title;        // Установка заголовка
        $this->author = $author;      // Установка автора
        $this->pubyear = $pubyear;    // Установка года публикации
        $this->price = $price;        // Установка цены
    }

    // Метод для получения заголовка книги
    public function getTitle() {
        return $this->title; // Возврат заголовка
    }

    // Метод для получения автора книги
    public function getAuthor() {
        return $this->author; // Возврат автора
    }

    // Метод для получения года публикации книги
    public function getPubyear() {
        return $this->pubyear; // Возврат года публикации
    }

    // Метод для получения цены книги
    public function getPrice() {
        return $this->price; // Возврат цены
    }
}