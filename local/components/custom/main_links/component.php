<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Loader;
if(!Loader::includeModule("iblock")) {
    ShowError("Модуль инфоблоков не установлен");
    return;
}

$arFilter = [
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "ACTIVE" => "Y"
];

$arSelect = ["ID", "PROPERTY_LINK_NAME", "PROPERTY_LINK_URL"];
$res = CIBlockElement::GetList(["SORT"=>"ASC"], $arFilter, false, false, $arSelect);

$arResult = [];
while($arItem = $res->GetNext()) {
    $arResult[] = [
        "NAME" => $arItem["PROPERTY_LINK_NAME_VALUE"],
        "LINK" => $arItem["PROPERTY_LINK_URL_VALUE"]
    ];
}

$this->IncludeComponentTemplate();
