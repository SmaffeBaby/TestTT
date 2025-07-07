<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', $_SERVER['DOCUMENT_ROOT'] . '/local/logs/php_errors.log');

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

global $USER;
$response = ['success' => false];

$request = Context::getCurrent()->getRequest();
$id = (int)$request->get('id');
$newStatus = trim($request->get('status') ?? '');
$name = trim($request->get('name') ?? '');
$desc = trim($request->get('description') ?? '');
$datetimeRaw = $request->get('datetime');
$oldStatus = trim($request->get('old_status') ?? '');

function getEntityByTable(string $tableName)
{
    $hlblock = HL\HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $tableName]
    ])->fetch();

    if (!$hlblock) return null;

    return HL\HighloadBlockTable::compileEntity($hlblock)->getDataClass();
}

function formatDatetime($datetime)
{
    if (!$datetime) return null;

    $datetime = str_replace('T', ' ', $datetime);
    if (strlen($datetime) === 16) $datetime .= ':00';

    try {
        return DateTime::createFromTimestamp((new \DateTime($datetime))->getTimestamp());
    } catch (\Exception $e) {
        return null;
    }
}

try {
    if (!$USER->IsAuthorized()) {
        $response['error'] = 'Авторизуйтесь';
        return;
    }

    if (!$id || !$newStatus || !$name) {
        $response['error'] = 'Обязательные поля не заполнены';
        return;
    }

    if (!Loader::includeModule('highloadblock')) {
        $response['error'] = 'Модуль highloadblock не подключен';
        return;
    }

    $dt = formatDatetime($datetimeRaw);
    if ($datetimeRaw && !$dt) {
        $response['error'] = 'Неверная дата: ' . htmlspecialcharsbx($datetimeRaw);
        return;
    }

    $NewEntity = getEntityByTable($newStatus);
    if (!$NewEntity) {
        $response['error'] = "HL-блок «" . htmlspecialcharsbx($newStatus) . "» не найден";
        return;
    }

    $userId = $USER->GetID();

    // Перемещение в другой статус
    if ($oldStatus && $oldStatus !== $newStatus) {
        $OldEntity = getEntityByTable($oldStatus);
        if (!$OldEntity) {
            $response['error'] = "HL-блок «" . htmlspecialcharsbx($oldStatus) . "» не найден";
            return;
        }

        $task = $OldEntity::getById($id)->fetch();
        if (!$task || $task['UF_USER_ID'] != $userId) {
            $response['error'] = 'Задача не найдена или принадлежит другому пользователю';
            return;
        }

        $OldEntity::delete($id);

        $fields = [
            'UF_USER_ID' => $userId,
            'UF_NAME' => $name,
            'UF_DESCRIPTION' => $desc,
        ];
        if ($dt) $fields['UF_DATETIME'] = $dt;

        $addRes = $NewEntity::add($fields);

        if ($addRes->isSuccess()) {
            $response['success'] = true;
            $response['task'] = [
                'id' => $addRes->getId(),
                'old_id' => $id,
                'status' => $newStatus,
                'name' => $name,
                'description' => $desc,
                'datetime' => $dt ? $dt->format('Y-m-d H:i:s') : ''
            ];
        } else {
            $response['error'] = implode(', ', $addRes->getErrorMessages());
        }

    } else {
        // Обновление задачи в текущем статусе
        $task = $NewEntity::getById($id)->fetch();
        if (!$task || $task['UF_USER_ID'] != $userId) {
            $response['error'] = 'Задача не найдена или принадлежит другому пользователю';
            return;
        }

        $fields = [
            'UF_NAME' => $name,
            'UF_DESCRIPTION' => $desc,
            'UF_USER_ID' => $userId,
        ];
        if ($dt) $fields['UF_DATETIME'] = $dt;

        $updRes = $NewEntity::update($id, $fields);

        if ($updRes->isSuccess()) {
            $response['success'] = true;
            $response['task'] = [
                'id' => $id,
                'old_id' => $id,
                'status' => $newStatus,
                'name' => $name,
                'description' => $desc,
                'datetime' => $dt ? $dt->format('Y-m-d H:i:s') : ''
            ];
        } else {
            $response['error'] = implode(', ', $updRes->getErrorMessages());
        }
    }

} catch (\Exception $e) {
    $response['error'] = 'Внутренняя ошибка сервера: ' . htmlspecialcharsbx($e->getMessage());
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response);
