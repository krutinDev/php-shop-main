<?php
require_once '../core/init.php'; 

// Извлекаем все заказы через Eshop
$ordersCollection = Eshop::getOrders(); // Переименовал переменную для уникальности

?>

<h1>Поступившие заказы:</h1>
<a href='/admin'>Вернуться в админку</a> 
<hr>

<?php foreach ($ordersCollection as $orderItem): ?> 
    <h2>Номер заказа: <?php echo htmlspecialchars($orderItem->getId()); ?></h2> 
    <p><b>Заказчик</b>: <?php echo htmlspecialchars($orderItem->getCustomer()); ?></p>
    <p><b>Email</b>: <?php echo htmlspecialchars($orderItem->getEmail()); ?></p>
    <p><b>Телефон</b>: <?php echo htmlspecialchars($orderItem->getPhone()); ?></p>
    <p><b>Адрес для доставки</b>: <?php echo htmlspecialchars($orderItem->getAddress()); ?></p> 
    <p><b>Дата оформления заказа</b>: <?php echo htmlspecialchars($orderItem->getCreated()); ?></p> 

    <h3>Купленные товары:</h3>
    <table>
        <tr>
            <th>N п/п</th>
            <th>Название</th>
            <th>Автор</th>
            <th>Год издания</th>
            <th>Цена (руб.)</th> 
            <th>Количество</th>
        </tr>

        <?php 
        $orderItems = $orderItem->getItems(); // Изменил переменную для уникальности
        $totalCost = 0; // Переименовал переменную для суммы
        foreach ($orderItems as $productId => $amount): // Переименовал переменные в цикле
            // Извлекаем информацию о товаре из каталога (предполагаем наличие метода для получения книги по ID)
            $product = Eshop::getBookById($productId); // Переименовал переменную для уникальности

            if ($product): // Проверяем наличие книги
                $totalCost += $product->getPrice() * $amount; // Считаем общую стоимость
        ?>
            <tr>
                <td><?php echo htmlspecialchars($productId); ?></td>
                <td><?php echo htmlspecialchars($product->getTitle()); ?></td>
                <td><?php echo htmlspecialchars($product->getAuthor()); ?></td>
                <td><?php echo htmlspecialchars($product->getPubyear()); ?></td>
                <td><?php echo htmlspecialchars(number_format($product->getPrice(), 2, '.', ' ')); ?> руб.</td>
                <td><?php echo htmlspecialchars($amount); ?></td>
            </tr>
        <?php endif; endforeach; ?>
    </table>

    <p>Общее количество товаров в заказе на сумму: <?php echo htmlspecialchars(number_format($totalCost, 2, '.', ' ')); ?> руб.</p> 
    
<?php endforeach; ?>