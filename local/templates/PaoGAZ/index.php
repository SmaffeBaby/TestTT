<div style="font-size: 38px;font-weight:bold;color:#007ac3;margin-top: 80px;padding-right: 40px;text-align: right;">
    Направления бизнеса ПАО "Газпром"
</div>

<div class="d-flex align-items-start mt-4" style="gap:30px;">
    <div class="logo-block d-flex flex-column align-items-center" style="margin-top: -5%;">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/atlas_logo_svg.svg"
             alt="Atlas Logo"
             style="height:250px; display:block;padding-left: 30px;">

        <div class="logo-links d-flex flex-column mt-3" style="gap:10px;padding-left: 30px;">
            <?$APPLICATION->IncludeComponent(
                "custom:main_links",  // имя твоего кастомного компонента
                "",
                [
                    "IBLOCK_ID" => 7, // ID инфоблока с ссылками
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
                "SET_TITLE" => "N"
            ]
        );?>
    </div>
</div>