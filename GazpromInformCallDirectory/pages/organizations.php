<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Филиалы и организации");
?>
<div class="custom-block">
    <?php
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH."/include/search.php",
        array(),
        array("MODE" => "html")
    );
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

</div>


<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
