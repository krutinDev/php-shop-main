<?php
class Eshop {
    // Статическая переменная для подключения к базе данных
    private static $db;

    public static function init(array $dbConfig) {
        // Проверяем, указаны ли все необходимые параметры для подключения
        if (empty($dbConfig['HOST']) || empty($dbConfig['USER']) || empty($dbConfig['PASS']) || empty($dbConfig['NAME'])) {
            throw new Exception("Недостаточно данных для подключения к базе данных.");
        }

        // Устанавливаем соединение с базой данных
        self::$db = new mysqli($dbConfig['HOST'], $dbConfig['USER'], $dbConfig['PASS'], $dbConfig['NAME']);

        // Проверяем наличие ошибок подключения
        if (self::$db->connect_error) {
            throw new Exception("Ошибка подключения: " . self::$db->connect_error);
        }

        // Устанавливаем кодировку соединения
        self::$db->set_charset("utf8");
    }

    public static function getDb() {
        return self::$db; // Возвращаем объект подключения к БД
    }

    // Метод для добавления книги в каталог
    public static function addItemToCatalog(Book $book) {
        // Извлекаем данные о книге
        $title = $book->getTitle();
        $author = $book->getAuthor();
        $pubyear = $book->getPubyear();
        $price = $book->getPrice();

        // Подготовка к добавлению товара в каталог с использованием хранимой процедуры
        $stmt = self::getDb()->prepare("CALL spAddItemToCatalog(?, ?, ?, ?)");

        if ($stmt) {
            // Привязываем параметры к подготовленному запросу
            $stmt->bind_param("ssds", $title, $author, $pubyear, $price);
            
            // Выполняем запрос
            if ($stmt->execute()) {
                return true; // Возвращаем успех
            } else {
                throw new Exception("Ошибка при добавлении товара: " . self::getDb()->error);
            }
            
            // Закрываем подготовленное выражение
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }
    }

    // Получение всех товаров из каталога
    public static function getItemsFromCatalog() {
        $books = []; // Массив для хранения книг
        
        // Подготовка запроса для получения всех книг
        $stmt = self::getDb()->prepare("CALL spGetCatalog()");
        
        if ($stmt) {
            $stmt->execute();
            $result = $stmt->get_result();

            // Создаем объекты Book на основе полученных данных
            while ($row = $result->fetch_assoc()) {
                $books[] = new Book($row['title'], $row['author'], $row['pubyear'], $row['price']);
            }

            // Закрываем подготовленное выражение
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }

        // Возвращаем итератор для массива книг
        return new IteratorIterator(new ArrayIterator($books));
    }

    // Добавление товара в корзину
    public static function addItemToBasket($itemId, $quantity) {
        // Создаем и инициализируем корзину
        $basket = new Basket();
        $basket->init(); 

        // Добавление товара в корзину
        $basket->add($itemId, $quantity);
        
        echo 'Добавление товара в корзину покупателя';
    }

    // Удаление товара из корзины
    public static function removeItemFromBasket($itemId) {
        // Инициализация корзины
        $basket = new Basket();
        $basket->init(); 

        // Удаляем товар из корзины
        $basket->remove($itemId);
        
        echo 'Удаление товара из корзины покупателя';
    }

    // Получение всех товаров из корзины
    public static function getItemsFromBasket() {
        // Инициализация корзины
        $basket = new Basket();
        $basket->init(); 

        return $basket->getItems(); // Возвращаем товары из корзины
    }

    // Сохранение заказа
    public static function saveOrder(Order $order) {
        // Получаем данные о заказе
        $customer = $order->getCustomer();
        $email = $order->getEmail();
        $phone = $order->getPhone();
        $address = $order->getAddress();
        
        // Начинаем транзакцию для сохранения заказа
        self::getDb()->begin_transaction(); 
        try {
            // Подготовка запроса для сохранения заказа
            $stmt = self::getDb()->prepare("CALL spSaveOrder(?, ?, ?, ?, ?)");
            if ($stmt) {
                // Привязываем параметры
                $stmt->bind_param("sssss", $customer, $email, $phone, $address);
                if (!$stmt->execute()) {
                    throw new Exception("Ошибка при сохранении заказа: " . self::getDb()->error);
                }
                // Получаем ID последнего сохраненного заказа
                $orderId = self::getDb()->insert_id;
                // Закрываем подготовленное выражение
                $stmt->close();
                
                // Сохраняем товары в заказе
                foreach ($order->getItems() as $itemId => $quantity) {
                    self::saveOrderedItems($orderId, (int)$itemId, (int)$quantity);
                }
                
                // Очищаем корзину после успешного сохранения заказа
                (new Basket())->create(); // Создаем новую корзину (очищаем)
                
                self::getDb()->commit(); // Подтверждаем транзакцию
                return true; 
            } else {
                throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
            }
            
        } catch (Exception $e) {
            self::getDb()->rollback(); // Откатываем транзакцию в случае ошибки
            throw new Exception("Ошибка при сохранении заказа: " . htmlspecialchars($e->getMessage()));
        }
    }

    // Сохранение позиций в заказе
    private static function saveOrderedItems($orderId, int $itemId, int $quantity) {
        // Подготовка запроса для сохранения позиций заказа
        $stmt = self::getDb()->prepare("CALL spSaveOrderedItems(?, ?, ?)");
        
        if ($stmt) {
            // Привязываем параметры
            $stmt->bind_param("iii", $orderId, $itemId, $quantity);
            
            if (!$stmt->execute()) {
                throw new Exception("Ошибка при сохранении позиций заказа: " . self::getDb()->error);
            }
            
            // Закрытие подготовленного выражения
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }
    }

    // Получение всех заказов
    public static function getOrders() {
        // Получаем все существующие заказы
        return new IteratorIterator(new ArrayIterator(self::fetchOrders()));
    }

    // Извлечение всех заказов из базы данных
    private static function fetchOrders() {
        $orders = []; // Массив для хранения заказов

        // Подготовка запроса для получения всех заказов
        if ($stmt = self::getDb()->prepare("CALL spGetOrders()")) {
            
            if ($stmt->execute()) {
                $result_set = $stmt->get_result();

                while ($row = $result_set->fetch_assoc()) {
                    // Создаем объект Order и добавляем его в массив заказов
                    if (!isset($orders[$row['id']])) { 
                        $orders[$row['id']] = new Order(
                            $row['customer'], 
                            $row['email'], 
                            $row['phone'], 
                            $row['address']
                        );
                        $orders[$row['id']]->setId($row['id']); 
                    }
                    // Добавляем позицию товара к заказу
                    $orders[$row['id']]->addItem($row['item_id'], $row['quantity']); 
                    // Устанавливаем дату создания заказа
                    $orders[$row['id']]->setCreated($row['created']); 
                }
                
                $stmt->close(); // Закрываем подготовленное выражение
                
                return $orders; // Возвращаем массив заказов
                
            } else { 
                throw new Exception("Ошибка при получении заказов: " . self::getDb()->error); 
            } 
            
        } else { 
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error); 
        } 
    }

    // Добавление пользователя в систему
    public static function userAdd(User $user) {
        // Подготовка запроса для добавления пользователя
        $stmt = self::getDb()->prepare("CALL spSaveAdmin(?, ?, ?)");
        
        if ($stmt) {
            // Привязываем параметры
            $stmt->bind_param("sss", $user->getLogin(), $user->getPassword(), $user->getEmail());
            
            if (!$stmt->execute()) {
                throw new Exception("Ошибка при добавлении пользователя: " . self::getDb()->error);
            }

            // Закрытие подготовленного выражения
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }
    }

    // Проверка существования пользователя в базе данных
    public static function userCheck(User $user): bool {
        // Подготовка запроса для проверки пользователя
        if ($stmt = self::getDb()->prepare("CALL spGetAdmin(?)")) {
            $stmt->bind_param("s", $user->getLogin());
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                return ($result->num_rows > 0); // Если есть хотя бы одна запись, пользователь существует
            } else {
                throw new Exception("Ошибка при проверке пользователя: " . self::getDb()->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }
    }

    // Получение данных пользователя из базы данных
    public static function userGet(User $user): User {
        // Подготовка запроса для получения пользователя
        if ($stmt = self::getDb()->prepare("CALL spGetAdmin(?)")) {
            $stmt->bind_param("s", $user->getLogin());
            
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    // Создаем объект User с данными из базы
                    $fetchedUser = new User($row['login'], $row['password'], $row['email']);
                    $fetchedUser->setId($row['id']);
                    return $fetchedUser;
                }
                throw new Exception("Пользователь не найден.");
            } else {
                throw new Exception("Ошибка при получении пользователя: " . self::getDb()->error);
            }
            
            $stmt->close();
        } else {
            throw new Exception("Ошибка подготовки запроса: " . self::getDb()->error);
        }
    }

    // Хэширование пароля
    public static function createHash(string $password): string {
        return password_hash($password, PASSWORD_DEFAULT); // Используем хеширование для пароля
    }

    // Проверяем, является ли текущий пользователь администратором
    public static function isAdmin(): bool {
        return isset($_SESSION['admin']); // Проверяем, авторизован ли администратор
    }

    // Логин для администратора
    public static function logIn(User $user): bool {
        // Проверяем, существует ли пользователь и совпадает ли пароль
        if (self::userCheck($user)) {
            // Получаем данные о пользователе из базы данных
            $fetchedUser = self::userGet($user);
            
            if (password_verify($user->getPassword(), $fetchedUser->getPassword())) { 
                $_SESSION['admin'] = true; // Устанавливаем сессию для администратора
                return true; 
            }
            
            throw new Exception("Неверный пароль.");
        }
        
        throw new Exception("Пользователь не найден.");
    }

    // Выход из системы
    public static function logOut() {
        unset($_SESSION['admin']); // Удаляем сессию администратора
    }
}
