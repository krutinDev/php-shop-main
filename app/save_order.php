<?php
require_once '../core/init.php'; // Подключаем файл инициализации 

try {
    // Проверяем метод запроса
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Извлекаем данные из отправленной формы
        $customerName = Cleaner::str($_POST['customer']); // Имя клиента
        $customerEmail = Cleaner::str($_POST['email']);   // Email клиента
        $customerPhone = Cleaner::str($_POST['phone']);   // Телефон клиента
        $customerAddress = Cleaner::str($_POST['address']); // Адрес клиента
        
        // Создаем экземпляр корзины и инициализируем ее
        $shoppingBasket = new Basket();
        $shoppingBasket->init(); // Загружаем существующие товары в корзине
        
        // Проверяем, есть ли товары в корзине
        if (empty($shoppingBasket->getItems())) {
            throw new Exception("Корзина пуста. Невозможно оформить заказ."); // Исключение если корзина пуста
        }

        // Создаем новый заказ с полученными данными и товарами из корзины
        $newOrder = new Order($customerName, $customerEmail, $customerPhone, $customerAddress, $shoppingBasket->getItems());

        // Сохраняем заказ используя Eshop
        Eshop::saveOrder($newOrder);

        // Переадресация на каталог после успешного оформления заказа
        header('Location: /catalog'); 
        exit(); // Завершаем выполнение скрипта
        
    } else {
        throw new Exception('Неверный метод запроса.'); // Исключение для неверных методов запроса
    }
} catch (Exception $e) {
    // Выводим сообщение об ошибке
    echo 'Ошибка: ' . htmlspecialchars($e->getMessage());
}