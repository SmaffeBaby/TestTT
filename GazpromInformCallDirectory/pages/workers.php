<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Работники");
?>
<div class="custom-block">
    <?php
    $APPLICATION->IncludeFile(
        SITE_TEMPLATE_PATH."/include/search.php",
        array(),
        array("MODE" => "html")
    );
    ?>

    <!-- Шапка таблицы -->
    <div class="table-header">
        <div class="table-cell">ФИО</div>
        <div class="table-cell">Телефон</div>
        <div class="table-cell">Email</div>
        <div class="table-cell">Должность</div>
        <div class="table-cell">Подразделение</div>
        <div class="table-cell">Местоположение</div>
    </div>

    <!-- Пример строки -->
    <div class="table-row">
        <div class="table-cell">Иванов Иван Иванович</div>
        <div class="table-cell">+7 (900) 123-45-67</div>
        <div class="table-cell">ivanov@example.com</div>
        <div class="table-cell">Инженер</div>
        <div class="table-cell">Отдел ИТ</div>
        <div class="table-cell">Москва</div>
    </div>

    <div class="organization-list">
        <!-- тут будут динамические строки -->
    </div>
</div>

<style>
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #ffffff;
        color: #667085;
        font-family: 'Montserrat', sans-serif;
        font-weight: 400;
        font-size: 16px;
        line-height: 1.4;
        border-radius: 12px;
        padding: 20px 20px 20px 40px;
        margin-bottom: 10px;
        gap: 8px;
    }

    .table-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #ddd;
        border-radius: 12px;
        padding: 15px 20px 15px 40px;
        margin-bottom: 8px;
        font-family: 'Montserrat', sans-serif;
        font-size: 16px;
        line-height: 1.4;
    }

    .table-cell {
        flex: 1; /* все колонки занимают равное место */
        padding-left: 10px;
        word-break: break-word;
    }

</style>
<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

