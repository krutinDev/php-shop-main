<?php
require_once '../core/init.php'; // Подключаем необходимый файл инициализации

try {
    // Убеждаемся, что запрос был отправлен с помощью метода POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productId = (int)$_POST['item_id']; // Извлекаем ID товара

        // Создаем экземпляр корзины
        $shoppingCart = new Basket();
        $shoppingCart->init(); // Загружаем текущую корзину пользователя

        // Процесс удаления товара из корзины
        $shoppingCart->remove($productId);

        echo 'Товар успешно удален из корзины покупателя';
    } else {
        throw new Exception('Некорректный метод запроса.');
    }
} catch (Exception $exception) {
    echo 'Ошибка: ' . htmlspecialchars($exception->getMessage());
}