<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Технологии эксплотации скважин");
?>

<style>
    .gray-btn {
        display: inline-block;
        margin-top: 20px;
        margin-left: 10px;
        padding: 10px 20px;
        font-size: 16px;
        border-radius: 8px;
        background-color: #6c757d;
        color: #fff;
        text-decoration: none;
        transition: background-color 0.3s ease;
    }

    .gray-btn:hover {
        background-color: #5a6268; /* чуть темнее */
    }
</style>

<a href="/pages/1_2_tecknologii_explotacii.php"
   class="gray-btn">
    Вернуться к Технологиям эксплуатации скважин
</a>



<div class="component-block flex-grow-1" style="display:flex; flex-wrap:wrap; gap:15px; align-items:flex-start;">

    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_categories_pages",
        [
            "IBLOCK_ID" => 6,
            "NEWS_COUNT" => 100,
            "FIELD_CODE" => [],
            "PROPERTY_CODE" => ["LINK"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "DISPLAY_PANEL" => "N",
            "SET_TITLE" => "N",
            "PARENT_SECTION_CODE" => "podvodniy_sposob_explotacii"
        ]
    );?>

</div>

