
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
?>

<div class="organization-list">
    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "organization_list",
        Array(
            "IBLOCK_TYPE" => "organizations",
            "IBLOCK_ID" => "8",
            "NEWS_COUNT" => "20",
            "SORT_BY1" => "ACTIVE_FROM",
            "SORT_ORDER1" => "ASC",
            "PROPERTY_CODE" => array("FILIAL"),
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_FILTER" => "Y",
            "CACHE_GROUPS" => "Y",
        )
    );?>
</div>
