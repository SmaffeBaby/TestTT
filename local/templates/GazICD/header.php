<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Page\Asset;
?><!DOCTYPE html>
<html lang="ru">
<head>
    <?php $APPLICATION->ShowHead(); ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>
    <?php $APPLICATION->ShowPanel(); ?>

    <?php
    Asset::getInstance()->addCss(SITE_TEMPLATE_PATH . "/css/style.css");
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="custom-header-block">
    <div class="logo">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/gazprom-inform 50.svg" alt="Газпром Информ">
    </div>
    <nav class="nav">
        <a href="#">Телефонный справочник</a>
        <a href="#">Газпром</a>
        <a href="#">Газпром информ</a>
    </nav>
</div>
