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

$status = trim($request->get('status'));
$name = trim($request->get('name'));
$desc = trim($request->get('description') ?? '');
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
    $response['error'] = 'Модуль highloadblock не подключен';
    echo json_encode($response);
    exit;
}

$hlblock = HL\HighloadBlockTable::getList([
    'filter' => ['=TABLE_NAME' => $status]
])->fetch();

if (!$hlblock) {
    $response['error'] = 'HL-блок не найден: ' . htmlspecialcharsbx($status);
    echo json_encode($response);
    exit;
}

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();

$datetime = null;
if ($datetimeStr) {
    $datetimeStr = str_replace('T', ' ', $datetimeStr);
    if (strlen($datetimeStr) === 16) $datetimeStr .= ':00';
    try {
        $datetime = DateTime::createFromTimestamp((new \DateTime($datetimeStr))->getTimestamp());
    } catch (\Exception $e) {
        $response['error'] = 'Некорректный формат даты и времени: ' . htmlspecialcharsbx($datetimeStr);
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

if ($result->isSuccess()) {
    $response['success'] = true;
    $response['task'] = [
        'id' => $result->getId(),
        'status' => $status,
        'name' => $name,
        'description' => $desc,
        'datetime' => $datetime->format('Y-m-d H:i:s')
    ];
} else {
    $response['error'] = implode(', ', $result->getErrorMessages());
}

header('Content-Type: application/json; charset=UTF-8');
echo json_encode($response);
?>