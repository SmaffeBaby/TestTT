<div class="text-center mt-4">
    Направления бизнеса ПАО "Газпром"
</div>

<img src="<?=SITE_TEMPLATE_PATH?>/img/atlas_logo_svg.svg"
     alt="Atlas Logo"
     style="height:250px; padding-left:60px; margin-right:15px;">

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