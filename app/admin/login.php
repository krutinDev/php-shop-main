<?php
require_once '../core/init.php'; // Подключение к основным файлам приложения

try {
    // Проверяем, был ли отправлен POST запрос
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Получаем и очищаем данные из формы для входа
        $userLogin = Cleaner::str($_POST['login']); 
        $userPassword = Cleaner::str($_POST['password']);
        
        // Создаем экземпляр класса User для проверки учетных данных
        $currentUser = new User($userLogin, password_hash('', PASSWORD_DEFAULT)); 

        // Проверяем, прошла ли аутентификация
        if (Eshop::logIn($currentUser)) {
            header('Location: /admin'); // Перенаправление на админскую панель при успешной аутентификации
            exit(); 
        }
    }
} catch (Exception $error) { 
    // Вывод сообщения об ошибке
    echo 'Ошибка: ' . htmlspecialchars($error->getMessage()); 
}
?>


<h1>Вход в админку</h1>
<form action="login.php" method="post">
    <div>
        <label for="login">Логин:</label>
        <input type="text" id="login" name="login" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <input type="submit" value="Войти">
    </div>
</form>