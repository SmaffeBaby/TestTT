<?php
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;

global $USER;
$response = ['success' => false];

if (!$USER->IsAuthorized()) {
    $response['error'] = 'Пользователь не авторизован';
    echo json_encode($response);
    exit;
}

$request = Context::getCurrent()->getRequest();

$statusKey = $request->get('status_key');
$newTitle = trim($request->get('status_title'));

if (!$statusKey || !$newTitle) {
    $response['error'] = 'Параметры не переданы';
    echo json_encode($response);
    exit;
}

$userId = (int)$USER->GetID();
$file = $_SERVER['DOCUMENT_ROOT'] . "/local/status_titles_{$userId}.json";
$statuses = [];

if (file_exists($file)) {
    $json = file_get_contents($file);
    $statuses = json_decode($json, true);
}

$statuses[$statusKey] = $newTitle;

if (file_put_contents($file, json_encode($statuses, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT))) {
    $response['success'] = true;
} else {
    $response['error'] = 'Не удалось сохранить новое название';
}

echo json_encode($response);
