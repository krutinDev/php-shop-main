<?php
class User {
    private $id; // Уникальный идентификатор пользователя
    private $login; // Логин пользователя
    private $password; // Хранилище для хэша пароля
    private $email; // Электронная почта пользователя
    
    // Конструктор класса User, принимает логин, хэш пароля и email
    public function __construct($login, $password, $email) {
        $this->login = $login; // Установка логина
        $this->password = $password; // Установка хэша пароля
        $this->email = $email; // Установка электронной почты
    }

    // Получение идентификатора пользователя
    public function getId() {
        return $this->id; // Возвращает id
    }

    // Установка идентификатора пользователя
    public function setId($id) {
        $this->id = $id; // Сохраняет id
    }

    // Получение логина пользователя
    public function getLogin() {
        return $this->login; // Возвращает логин
    }

    // Получение пароля пользователя
    public function getPassword() {
        return $this->password; // Возвращает хэш пароля
    }

    // Получение электронной почты пользователя
    public function getEmail() {
        return $this->email; // Возвращает email
    }
}

