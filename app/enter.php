<?php
// Подключаем начальную конфигурацию системы
require_once '../core/init.php'; 

try {
   // Проверяем метод запроса
   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
       // Извлекаем данные из POST-запроса
       $username = Cleaner::str($_POST['login']); // Получаем логин
       $passwd = Cleaner::str($_POST['password']); // Получаем пароль
       
       // Создаем экземпляр класса User для валидации учетных данных
       $user = new User($username, password_hash('', PASSWORD_DEFAULT)); 
       // Проверяем, удается ли войти в систему
       if (Eshop::logIn($user)) { 
           header('Location: /admin'); // Перенаправление на административную панель после успешной аутентификации 
           exit(); 
       }
   }
} catch (Exception $e) { 
   // Обработка исключений и вывод сообщения об ошибке
   echo 'Ошибка: ' . htmlspecialchars($e->getMessage()); 
}

// HTML часть для формы входа
?>
<h1>Вход в админку</h1>
<form action="login.php" method="post">
   <div>
       <label>Логин:</label>
       <input type="text" name="login" required>
   </div>
   <div>
       <label>Пароль:</label>
       <input type="password" name="password" required>
   </div>
   <div>
       <input type="submit" value="Войти">
   </div>
</form>