<?php

namespace App\Core;

class View
{
    /**
     * Подключает файл представления и передаёт в него данные.
     *
     * @param string $view Путь к файлу представления (без расширения)
     * @param array $data Массив данных для передачи в представление
     */
    public static function render(string $view, array $data = [])
    {
        $file = dirname(__DIR__) . "/Views/{$view}.php";



        if (file_exists($file)) {
            extract($data, EXTR_SKIP);
            require $file;
        } else {
            throw new \Exception("View file '{$file}' not found.");
        }

    }

}
