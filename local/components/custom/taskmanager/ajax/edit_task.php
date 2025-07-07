<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

global $USER;

$response = ['success' => false];
$request  = Context::getCurrent()->getRequest();

$id        = (int)$request->get('id');
$newStatus = trim($request->get('status'));
$name      = trim($request->get('name'));
$desc      = trim($request->get('description'));
$datetime  = $request->get('datetime');
$oldStatus = trim($request->get('old_status'));


if (!$USER->IsAuthorized()) {
    $response['error'] = 'Авторизуйтесь';
    echo json_encode($response); exit;
}
if (!$id || !$newStatus || !$name) {
    $response['error'] = 'Обязательные поля не заполнены';
    echo json_encode($response); exit;
}
if (!Loader::includeModule('highloadblock')) {
    $response['error'] = 'Модуль highloadblock не подключен';
    echo json_encode($response); exit;
}


$dt = null;
if ($datetime) {
    $datetime = str_replace('T', ' ', $datetime);
    if (strlen($datetime) === 16) $datetime .= ':00';
    try {
        $dt = DateTime::createFromTimestamp((new \DateTime($datetime))->getTimestamp());
    } catch (\Exception $e) {
        $response['error'] = 'Неверная дата';
        echo json_encode($response); exit;
    }
}


function getEntityByTable(string $table)
{
    $hl = HL\HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $table]
    ])->fetch();
    if (!$hl) return null;
    return HL\HighloadBlockTable::compileEntity($hl)->getDataClass();
}


$NewEntity = getEntityByTable($newStatus);
if (!$NewEntity) {
    $response['error'] = "HL‑блок «$newStatus» не найден";
    echo json_encode($response); exit;
}

$userId = $USER->GetID();


if ($oldStatus && $oldStatus !== $newStatus) {

    $OldEntity = getEntityByTable($oldStatus);
    if (!$OldEntity) {
        $response['error'] = "HL‑блок «$oldStatus» не найден";
        echo json_encode($response); exit;
    }


    $task = $OldEntity::getById($id)->fetch();
    if (!$task || $task['UF_USER_ID'] != $userId) {
        $response['error'] = 'Задача не найдена или принадлежит другому пользователю';
        echo json_encode($response); exit;
    }


    $OldEntity::delete($id);


    $fields = [
        'UF_USER_ID'    => $userId,
        'UF_NAME'       => $name,
        'UF_DESCRIPTION'=> $desc,
    ];
    if ($dt) $fields['UF_DATETIME'] = $dt;

    $addRes = $NewEntity::add($fields);

    $response['success'] = $addRes->isSuccess();
    if (!$addRes->isSuccess()) $response['error'] = implode(', ', $addRes->getErrorMessages());

    echo json_encode($response); exit;
}

$fields = [
    'UF_NAME'        => $name,
    'UF_DESCRIPTION' => $desc,
];
if ($dt)          $fields['UF_DATETIME'] = $dt;
$fields['UF_USER_ID'] = $userId;           // на всякий случай

$updRes = $NewEntity::update($id, $fields);

$response['success'] = $updRes->isSuccess();
if (!$updRes->isSuccess()) $response['error'] = implode(', ', $updRes->getErrorMessages());

echo json_encode($response);
