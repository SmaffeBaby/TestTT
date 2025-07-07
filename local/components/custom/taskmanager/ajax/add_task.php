<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

global $USER;
$response = ['success' => false];
$request = Context::getCurrent()->getRequest();

$status = $request['status'];
$name = $request['name'];
$desc = $request['description'] ?? '';

if (!$USER->IsAuthorized()) {
    $response['error'] = 'Пользователь не авторизован';
    echo json_encode($response);
    exit;
}

if (!$status || !$name) {
    $response['error'] = 'Не переданы обязательные поля';
    echo json_encode($response);
    exit;
}

if (!Loader::includeModule('highloadblock')) {
    $response['error'] = 'Модуль HL не подключен';
    echo json_encode($response);
    exit;
}

$hlblock = HL\HighloadBlockTable::getList([
    'filter' => ['=TABLE_NAME' => $status]
])->fetch();

if (!$hlblock) {
    $response['error'] = 'HL-блок не найден';
    echo json_encode($response);
    exit;
}

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();

$result = $entityClass::add([
    'UF_NAME' => $name,
    'UF_DESCRIPTION' => $desc,
    'UF_DATETIME' => new DateTime(),
    'UF_USER_ID' => $USER->GetID(),
]);

$response['success'] = $result->isSuccess();
if (!$result->isSuccess()) {
    $response['error'] = implode(', ', $result->getErrorMessages());
}

echo json_encode($response);

