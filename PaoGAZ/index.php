<?php
// Указываем путь к файлу шаблона
$templatePath = __DIR__ . '/local/templates/PaoGAZ/index.php';

if (file_exists($templatePath)) {
    require $templatePath;
} else {
    echo "Файл шаблона не найден: " . $templatePath;
}