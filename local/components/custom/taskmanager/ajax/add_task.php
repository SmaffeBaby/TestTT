<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;
use Bitrix\Main\Context;
use Bitrix\Main\Type\DateTime;

global $USER;
$response = ['success' => false];
$request = Context::getCurrent()->getRequest();

$status = $request->get('status');
$name = $request->get('name');
$desc = $request->get('description') ?? '';
$datetimeStr = $request->get('datetime');

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

if ($datetimeStr) {
    $datetimeStr = str_replace('T', ' ', $datetimeStr);
    if (strlen($datetimeStr) === 16) $datetimeStr .= ':00';

    try {
        $datetime = DateTime::createFromTimestamp((new \DateTime($datetimeStr))->getTimestamp());
    } catch (\Exception $e) {
        $response['error'] = 'Некорректный формат даты и времени: ' . $datetimeStr;
        echo json_encode($response);
        exit;
    }
} else {
    $datetime = new DateTime();
}
$result = $entityClass::add([
    'UF_NAME' => $name,
    'UF_DESCRIPTION' => $desc,
    'UF_DATETIME' => $datetime,
    'UF_USER_ID' => $USER->GetID(),
]);

$response['success'] = $result->isSuccess();
if (!$result->isSuccess()) {
    $response['error'] = implode(', ', $result->getErrorMessages());
}

echo json_encode($response);
