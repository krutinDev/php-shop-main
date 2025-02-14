<?php
// Подключаем необходимые файлы для работы
require_once '../core/init.php'; 

try {
    // Проверяем метод запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Извлекаем данные из POST-запроса
        $userLogin = Cleaner::str($_POST['login']); // Логин пользователя
        $userPassword = Cleaner::str($_POST['password']); // Пароль пользователя
        $userEmail = Cleaner::str($_POST['email']); // Email пользователя
        
        // Генерируем хэш для пароля
        $passwordHash = Eshop::createHash($userPassword);
        // Создаем новый объект User
        $newUser = new User($userLogin, $passwordHash, $userEmail);

        // Добавляем нового пользователя в систему
        Eshop::userAdd($newUser);

        // Перенаправляем на страницу администрирования после успешного добавления
        header('Location: /admin'); // Переадресация на админскую панель
        exit();

    } else {
        // Выбрасываем исключение, если метод не POST
        throw new Exception('Неверный метод запроса.');
    }
} catch (Exception $exception) {
    // Выводим сообщение об ошибке с безопасной обработкой
    echo 'Ошибка: ' . htmlspecialchars($exception->getMessage());
}