<?php
require __DIR__ . '/mutator/styleMutator.php';
$cardStyle = getCardStyle($arResult["ITEMS"]);

$counter = 0;


$firstRowCount = (strpos($_SERVER["REQUEST_URI"], "1_more_dobycha.php") !== false) ? 4 : 5;
?>

<div class="cards-row" style="display:flex; flex-wrap:wrap; justify-content:center; max-width:1700px; margin:0 auto; padding-top:30px;">
    <?php foreach($arResult["ITEMS"] as $arItem):


        $this->AddEditAction(
            $arItem['ID'],
            $arItem['EDIT_LINK'],
            CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT")
        );
        $this->AddDeleteAction(
            $arItem['ID'],
            $arItem['DELETE_LINK'],
            CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"),
            ["CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]
        );


        $imgSrc = '';
        if (!empty($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"])) {
            if (is_array($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"])) {
                $imgSrc = CFile::GetPath($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"][0]);
            } else {
                $imgSrc = CFile::GetPath($arItem["PROPERTIES"]["IMAGE_MAIN"]["VALUE"]);
            }
        }
        ?>

        <a href="<?=$arItem["PROPERTIES"]["LINK"]["VALUE"]?>"
           id="<?=$this->GetEditAreaId($arItem['ID']);?>"
           class="icon-card m-3 d-flex flex-column align-items-center justify-content-start"
           style="<?=$cardStyle;?>"
        >

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
        if ($counter == $firstRowCount) {
            echo '<div style="flex-basis:100%; height:0;"></div>';
        }
    endforeach; ?>
</div>
