<?php
$firstRowCount = 5;
$counter = 0;
?>

<div class="cards-row" style="display:flex; flex-wrap:wrap; justify-content:center; max-width:1700px; margin:0 auto;">
    <?foreach($arResult["ITEMS"] as $arItem):?>
        <?php
        $imgSrc = '';
        if (!empty($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"])) {
            if(is_array($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"])) {
                $imgSrc = CFile::GetPath($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"][0]);
            } else {
                $imgSrc = CFile::GetPath($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"]);
            }
        }
        ?>
        <a href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>"
           class="icon-card m-3 d-flex flex-column align-items-center justify-content-start"
           style="
                width: 200px;
                height: 300px;
                background-color: #ffffff;
                color: #007ac3;
                border: 1px solid #007ac3;
                text-decoration: none;
                border-radius: 10px;
                overflow: hidden;
                padding-top: 15px;
           ">

            <span style="font-size:18px; text-align:center; padding:0 10px; margin-bottom:10px;"><?=$arItem["NAME"]?></span>

            <?php if($imgSrc): ?>
                <img src="<?=$imgSrc?>" alt="<?=$arItem["NAME"]?>"
                     style="max-width:90%; max-height:70%; object-fit:contain;">
            <?php else: ?>
                <span style="width:90%; height:70%; display:inline-block; background:#ffffff;"></span>
            <?php endif; ?>
        </a>

        <?php
        $counter++;
        if($counter == $firstRowCount) echo '<div style="flex-basis:100%; height:0;"></div>';
        ?>
    <?endforeach;?>
</div>
