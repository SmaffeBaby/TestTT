<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;

$response = ['success' => false];
$request = Context::getCurrent()->getRequest();

$id = $request->get('id');
$newStatus = $request->get('status');
$name = $request->get('name');
$desc = $request->get('description');
$datetime = $request->get('datetime');
$oldStatus = $request->get('old_status');



if (!$id || !$newStatus || !$name) {
    $response['error'] = 'Обязательные поля не заполнены';
    echo json_encode($response);
    exit;
}

if (!Loader::includeModule('highloadblock')) {
    $response['error'] = 'Модуль highloadblock не подключен';
    echo json_encode($response);
    exit;
}

function prepareDateTime($datetime) {
    if (!$datetime) {
        return null;
    }
    try {
        if (strpos($datetime, 'T') !== false) {
            $datetime = str_replace('T', ' ', $datetime);
        }
        if (strlen($datetime) === 16) {
            $datetime .= ':00';
        }
        $phpDate = new \DateTime($datetime);
        return \Bitrix\Main\Type\DateTime::createFromTimestamp($phpDate->getTimestamp());
    } catch (Exception $e) {
        return false;
    }
}

// Получаем HL-блок для нового статуса
$hlNew = HL\HighloadBlockTable::getList([
    'filter' => ['=TABLE_NAME' => $newStatus]
])->fetch();

if (!$hlNew) {
    $response['error'] = 'HL-блок для нового статуса не найден: ' . htmlspecialcharsbx($newStatus);
    echo json_encode($response);
    exit;
}

$entityNew = HL\HighloadBlockTable::compileEntity($hlNew);
$entityClassNew = $entityNew->getDataClass();

if ($oldStatus && $oldStatus !== $newStatus) {
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/edit_task_log.txt', "Статус изменился, переносим задачу\n", FILE_APPEND);

    // Получаем HL-блок старого статуса
    $hlOld = HL\HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $oldStatus]
    ])->fetch();

    if (!$hlOld) {
        $response['error'] = 'HL-блок для старого статуса не найден: ' . htmlspecialcharsbx($oldStatus);
        echo json_encode($response);
        exit;
    }

    $entityOld = HL\HighloadBlockTable::compileEntity($hlOld);
    $entityClassOld = $entityOld->getDataClass();

    // Проверяем, что задача существует в старом HL-блоке
    $taskOld = $entityClassOld::getById($id)->fetch();
    if (!$taskOld) {
        $response['error'] = 'Задача не найдена в старом HL-блоке';
        echo json_encode($response);
        exit;
    }

    // Удаляем запись из старого HL-блока
    $deleteResult = $entityClassOld::delete($id);
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/edit_task_log.txt', "Удаление старой записи: " . ($deleteResult->isSuccess() ? "успешно" : "ошибка") . "\n", FILE_APPEND);

    if (!$deleteResult->isSuccess()) {
        $response['error'] = 'Ошибка при удалении задачи из старого HL-блока: ' . implode(', ', $deleteResult->getErrorMessages());
        echo json_encode($response);
        exit;
    }

    $fieldsToAdd = [
        'UF_NAME' => $name,
        'UF_DESCRIPTION' => $desc,
    ];

    $dt = prepareDateTime($datetime);
    if ($dt === false) {
        $response['error'] = 'Ошибка обработки даты';
        echo json_encode($response);
        exit;
    } elseif ($dt !== null) {
        $fieldsToAdd['UF_DATETIME'] = $dt;
    }

    $addResult = $entityClassNew::add($fieldsToAdd);

    if ($addResult->isSuccess()) {
        $response['success'] = true;
    } else {
        $response['error'] = 'Ошибка при добавлении задачи в новый HL-блок: ' . implode(', ', $addResult->getErrorMessages());
    }

    echo json_encode($response);
    exit;
} else {

    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/edit_task_log.txt', "Статус не изменился, обновляем задачу\n", FILE_APPEND);

    $fieldsToUpdate = [
        'UF_NAME' => $name,
        'UF_DESCRIPTION' => $desc,
    ];

    $dt = prepareDateTime($datetime);
    if ($dt === false) {
        $response['error'] = 'Ошибка обработки даты';
        echo json_encode($response);
        exit;
    } elseif ($dt !== null) {
        $fieldsToUpdate['UF_DATETIME'] = $dt;
    }

    $updateResult = $entityClassNew::update($id, $fieldsToUpdate);
    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/edit_task_log.txt', "Обновление записи: " . ($updateResult->isSuccess() ? "успешно" : "ошибка") . "\n", FILE_APPEND);

    if ($updateResult->isSuccess()) {
        $response['success'] = true;
    } else {
        $response['error'] = implode(', ', $updateResult->getErrorMessages());
    }

    echo json_encode($response);
    exit;
}
