<div class="text-center mt-4">
    Направления бизнеса ПАО "Газпром"
</div>

<div class="d-flex align-items-start mt-4" style="gap:30px;">
    <div class="logo-block">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/atlas_logo_svg.svg"
             alt="Atlas Logo"
             style="height:250px; display:block; padding-left: 20px; margin-top: -25%;">
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