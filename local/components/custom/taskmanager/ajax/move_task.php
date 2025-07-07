<?php
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

$response = ['success' => false];
$request  = Context::getCurrent()->getRequest();

$id        = (int)$request->getPost('id');
$newStatus = trim($request->getPost('status'));
$oldStatus = trim($request->getPost('old_status'));
$name      = trim($request->getPost('name'));
$desc      = trim($request->getPost('description'));
$datetime  = $request->getPost('datetime');

global $USER;
if (!$USER->IsAuthorized()) {
    $response['error'] = 'Вы не авторизованы';
    echo json_encode($response); exit;
}
$userId = (int)$USER->GetID();

if (!$id || !$newStatus || !$name) {
    $response['error'] = 'Недостаточно данных';
    echo json_encode($response); exit;
}

if (!Loader::includeModule('highloadblock')) {
    $response['error'] = 'Не подключен модуль HL';
    echo json_encode($response); exit;
}

function getEntityClass($table) {
    $hl = HL\HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $table]
    ])->fetch();
    return $hl ? HL\HighloadBlockTable::compileEntity($hl)->getDataClass() : null;
}

$OldEntity = getEntityClass($oldStatus);
$NewEntity = getEntityClass($newStatus);
if (!$OldEntity || !$NewEntity) {
    $response['error'] = 'HL-блок не найден';
    echo json_encode($response); exit;
}

// 1. Удалим старую задачу
$OldEntity::delete($id);

// 2. Проверим, не существует ли уже такая задача
$existing = $NewEntity::getList([
    'filter' => [
        'UF_USER_ID' => $userId,
        '=UF_NAME' => $name
    ],
    'limit' => 1
])->fetch();

if ($existing) {
    $response['success'] = true;
    $response['new_id']  = $existing['ID'];
    echo json_encode($response); exit;
}

// 3. Подготовим дату
$dt = null;
if ($datetime) {
    $datetime = str_replace('T', ' ', $datetime);
    if (strlen($datetime) === 16) $datetime .= ':00';
    $dt = DateTime::createFromTimestamp((new \DateTime($datetime))->getTimestamp());
}

// 4. Добавим новую запись
$fields = [
    'UF_USER_ID'     => $userId,
    'UF_NAME'        => $name,
    'UF_DESCRIPTION' => $desc,
];
if ($dt) $fields['UF_DATETIME'] = $dt;

$result = $NewEntity::add($fields);
if ($result->isSuccess()) {
    $response['success'] = true;
    $response['new_id']  = $result->getId();
} else {
    $response['error'] = implode('; ', $result->getErrorMessages());
}
echo json_encode($response);
