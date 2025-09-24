<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Технологии эксплотации скважин");
?>

<!-- Новый блок с плашками сверху -->
<div class="component-block flex-grow-1" style="display:flex; flex-wrap:wrap; gap:15px; align-items:flex-start;">

    <?$APPLICATION->IncludeComponent(
        "bitrix:news.list",
        "main_categories_pages", // новый шаблон без картинок
        [
            "IBLOCK_ID" => 6,
            "NEWS_COUNT" => 8,
            "FIELD_CODE" => [],
            "PROPERTY_CODE" => ["LINK"],
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "DISPLAY_PANEL" => "N",
            "SET_TITLE" => "N",
            "PARENT_SECTION_CODE" => "in-main"
        ]
    );?>


</div>

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

<a href="/pages/1_more_dobycha.php"
   class="gray-btn">
    Вернуться к УВ на море
</a>

<div style="font-size: 38px; font-weight:bold; color:#007ac3; margin-top: 80px; padding-right: 40px; text-align: right;">
    Технологии эксплотации скважин
</div>

<div class="d-flex align-items-start mt-4" style="gap:30px;">

    <div class="logo-block d-flex flex-column align-items-center" style="margin-top: -5%;">
        <a href="<?=SITE_DIR?>" style="display:block;">
            <img src="<?=SITE_TEMPLATE_PATH?>/img/atlas_logo_svg.svg"
                 alt="Atlas Logo"
                 style="height:250px; display:block; padding-left: 30px;">
        </a>

        <div class="logo-links d-flex flex-column mt-3" style="gap:10px; padding-left: 30px;">
            <?$APPLICATION->IncludeComponent(
                "custom:main_links",
                "",
                [
                    "IBLOCK_ID" => 7,
                    "FIELD_CODE" => ["NAME"],
                    "PROPERTY_CODE" => ["LINK_URL","LINK_NAME"],
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => 3600
                ]
            );?>
        </div>
    </div>


    <div class="component-block flex-grow-1">
        <?$APPLICATION->IncludeComponent(
            "bitrix:news.list",
            "main_categores",
            [
                "IBLOCK_ID" => 6,
                "NEWS_COUNT" => 8,
                "FIELD_CODE" => [],
                "PROPERTY_CODE" => ["LINK", "IMAGE_MAIN"],
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "3600",
                "DISPLAY_PANEL" => "N",
                "SET_TITLE" => "N",
                "PARENT_SECTION_CODE" => "teckno-explotacii",
                "INCLUDE_SUBSECTIONS" => "N"
            ]
        );?>
    </div>

</div>

<style>
    /* Стили для плашек */
    .icon-card {
        background-color: #007ac3;
        color: #fff;
        font-weight: bold;
        border-radius: 6px;
        padding: 10px 20px;
        text-decoration: none;
        text-align: center;
        display: inline-block;
        min-width: 150px;
    }
    .icon-card:hover {
        background-color: #005999;
    }
</style>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
