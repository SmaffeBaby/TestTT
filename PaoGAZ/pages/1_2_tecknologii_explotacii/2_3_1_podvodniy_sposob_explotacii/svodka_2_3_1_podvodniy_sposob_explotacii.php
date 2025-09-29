<?php
require(__DIR__ . "/header.php");
?>

<img src="/pages/1_2_tecknologii_explotacii/2_3_1_podvodniy_sposob_explotacii/img/podvodniy_sposob_explotacii_svodka.png"
     alt="podvodniy_sposob_explotacii_svodka"
     style="max-height:94vh; width:auto; display:block; padding-left:10px; object-fit:contain;">


<!--Асистент-->
<?$APPLICATION->IncludeFile(SITE_TEMPLATE_PATH."/include/assistent_more.php", [], ["MODE" => "php"]);?>

<div class="d-flex align-items-start mt-4" style="gap:30px;">
</div>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
