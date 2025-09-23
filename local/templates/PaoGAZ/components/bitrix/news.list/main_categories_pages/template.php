<?php
$currentPage = basename($APPLICATION->GetCurPage(false)); // текущий файл
?>

<div class="plain-links-scroll">
    <?foreach($arResult["ITEMS"] as $arItem):
        $link = $arItem["PROPERTIES"]["LINK"]["VALUE"];
        $linkName = basename($link);


        $equivalentPages = [
            '1_more_dobycha.php' => ['1_more_dobycha.php', '1_2_tecknologii_explotacii.php'],
            'other_page.php' => ['other_page.php']
        ];

        $isActive = false;
        if (isset($equivalentPages[$linkName])) {
            $isActive = in_array($currentPage, $equivalentPages[$linkName]);
        }
        ?>
        <a href="<?=$link?>"
           class="icon-card<?=$isActive ? ' active-card' : ''?>">
            <?=$arItem["NAME"]?>
        </a>
    <?endforeach;?>
</div>
