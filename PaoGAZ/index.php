<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Направления бизнеса ПАО Газпром");

// Указываем путь к файлу шаблона
$templatePath = __DIR__ . '/local/templates/PaoGAZ/index.php';

if (file_exists($templatePath)) {
    require $templatePath;
} else {
    echo "Файл шаблона не найден: " . $templatePath;
}


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
