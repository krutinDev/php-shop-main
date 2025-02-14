<?php
const CORE_DIR = 'core/';
const APP_DIR = 'app/';
const ADMIN_DIR = APP_DIR . 'admin/';

// Добавляем пути к директориям в путь включения
set_include_path(get_include_path() . PATH_SEPARATOR . CORE_DIR . PATH_SEPARATOR . APP_DIR . PATH_SEPARATOR . ADMIN_DIR);
// Устанавливаем расширение для автоматической загрузки классов
spl_autoload_extensions('.class.php');
// Регистрация функции автозагрузки
spl_autoload_register();

// Настройки для логирования ошибок
const ERROR_LOG = ADMIN_DIR . 'error.log';
const ERROR_MSG = 'Возникли неожиданные проблемки..';

// Функция для записи сообщений об ошибках в лог
function log_errors($msg, $file, $line) {
    $dateTime = date('d-m-Y H:i:s');
    $logMessage = "$dateTime - $msg in $file:$line\n";
    error_log($logMessage, 3, ERROR_LOG);
    echo ERROR_MSG; // Вывод сообщения для пользователя
}

// Обработчик ошибок
function handle_error($no, $msg, $file, $line) {
    log_errors($msg, $file, $line);
}

// Установка обработчика ошибок
set_error_handler('handle_error');

// Обработчик исключений
function handle_exception($e) {
    log_errors($e->getMessage(), $e->getFile(), $e->getLine());
}

// Установка обработчика исключений
set_exception_handler('handle_exception');

// Конфигурация подключения к базе данных
const DATABASE_CONFIG = [
    'HOST' => 'localhost',
    'USER' => 'kkru_tin',
    'PASS' => 'qwerty123',
    'NAME' => 'php-shop',
];

try {
    Eshop::init(DATABASE_CONFIG); // Инициализация приложения с настройками БД
    
    // Создаем экземпляр корзины
    $basket = new Basket();
    $basket->init(); // Инициализация корзины

    session_start(); // Запускаем сессию

    // Проверка, авторизован ли администратор
    if (!isset($_SESSION['admin'])) {
       header('Location: /enter.php'); // Перенаправление на страницу входа при отсутствии авторизации
       exit(); // Завершаем выполнение скрипта
    }

} catch (Exception $e) {
    log_errors("Ошибка инициализации базы данных: " . $e->getMessage(), __FILE__, __LINE__);
}
