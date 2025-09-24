<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Изменения стиля карточек в зависимости от количества элементов
 * @param array $items — массив элементов
 * @return string — CSS стиль
 */
function getCardStyle(array $items): string
{
    $count = count($items);

    if ($count <= 2) {
        return "
            width: 400px;
            height: 500px;
            background-color: #ffffff;
            color: #007ac3;
            border: 2px solid #007ac3;
            text-decoration: none;
            border-radius: 12px;
            overflow: hidden;
            padding-top: 20px;
            white-space: inherit !important;
        ";
    } elseif ($count <= 4) {
        return "
            width: 300px;
            height: 400px;
            background-color: #ffffff;
            color: #007ac3;
            border: 1px solid #007ac3;
            text-decoration: none;
            border-radius: 10px;
            overflow: hidden;
            padding-top: 15px;
            white-space: inherit !important;
        ";
    } else {
        return "
            width: 200px;
            height: 300px;
            background-color: #ffffff;
            color: #007ac3;
            border: 1px solid #007ac3;
            text-decoration: none;
            border-radius: 10px;
            overflow: hidden;
            padding-top: 15px;
            white-space: inherit !important;
        ";
    }
}
