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
$status = $request->get('status');

if (!$id || !$status) {
    $response['error'] = 'Не указан ID или статус задачи';
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
    $response['error'] = 'HL-блок для статуса не найден: ' . htmlspecialcharsbx($status);
    echo json_encode($response);
    exit;
}

$entity = HL\HighloadBlockTable::compileEntity($hlblock);
$entityClass = $entity->getDataClass();

$deleteResult = $entityClass::delete($id);

if ($deleteResult->isSuccess()) {
    $response['success'] = true;
} else {
    $response['error'] = implode(', ', $deleteResult->getErrorMessages());
}

echo json_encode($response);
