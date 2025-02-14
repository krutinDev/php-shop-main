<?php
class Cleaner {
    // Преобразует данные в целое число
    static function int($data): int {
        return (int) $data; // Приведение к типу int
    }

    // Преобразует данные в неотрицательное целое число
    static function uint($data): int {
        return abs(self::int($data)); // Возвращает абсолютное значение
    }

    // Очищает строку от HTML-тегов и пробелов
    static function str($data): string {
        return trim(strip_tags($data)); // Удаляет теги и пробелы по краям
    }

    // Экранирует строку для безопасного использования в SQL-запросах
    static function str2db($data, PDO $db): string {
        return $db->quote(self::str($data)); // Экранируем очищенную строку
    }

    // Экранирует строку для безопасного использования в SQL-запросах без предварительной очистки
    static function str2quote($data, PDO $db): string {
        return $db->quote($data); // Экранирует строку без очистки
    }
}
