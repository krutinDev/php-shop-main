<?php
require_once '../core/init.php'; // Подключаем основной файл инициализации

try {
    // Проверяем, отправлены ли данные методом POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Очищаем данные, полученные из формы
        $bookTitle = Cleaner::str($_POST['title']); // Название книги
        $bookAuthor = Cleaner::str($_POST['author']); // Автор книги
        $publicationYear = Cleaner::uint($_POST['pubyear']); // Год публикации
        $bookPrice = Cleaner::float($_POST['price']); // Чистим цену (предполагается наличие метода для очистки float)

        // Создание экземпляра класса Book
        $newBook = new Book($bookTitle, $bookAuthor, $publicationYear, $bookPrice);

        // Добавление книги в каталог
        Eshop::addItemToCatalog($newBook); // Вызов метода добавления книги

        // Переадресация на страницу добавления товара
        header('Location: /admin/add_item_to_catalog'); // Перенаправление
        exit(); // Завершаем скрипт
    }
} catch (Exception $e) {
    // Обработка исключений
    echo "<h1>Произошла ошибка</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

<h1>Форма добавления товара в каталог</h1>
<p><a href='/admin'>Вернуться в админку</a></p>    
<form action="save_item_to_catalog" method="post">
    <div>
        <label>Введите название:</label> 
        <input type="text" name="title" size="50" required>
    </div>
    <div>
        <label>Введите автора:</label>
        <input type="text" name="author" size="50" required>
    </div>
    <div>
        <label>Год публикации:</label> 
        <input type="text" name="pubyear" size="50" maxlength="4" required>
    </div>
    <div>
        <label>Цена (руб.):</label> 
        <input type="text" name="price" size="50" maxlength="6" required>
    </div>
    <div>
        <input type="submit" value="Добавить книгу">
    </div>
</form>