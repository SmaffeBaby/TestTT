<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");


$APPLICATION->IncludeComponent(
    "custom:taskmanager",
    "",
    []
);

?>



<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>