<?php
$currentPage = basename($APPLICATION->GetCurPage(false));


$equivalentGroups = [
    ['1_more_dobycha.php', '1_2_tecknologii_explotacii.php'],
    ['svodka_2_3_1_podvodniy_sposob_explotacii.php'],
    ['primeneniye_v_ip_2_3_1_podvodniy_sposob_explotacii.php'],
    ['explotiruyemoe_2_3_1_podvodniy_sposob_explotacii.php'],
    ['proizvoditeli_2_3_1_podvodniy_sposob_explotacii.php'],
    ['tekushiy_status_2_3_1_podvodniy_sposob_explotacii.php'],
    ['niokrPatents_2_3_1_podvodniy_sposob_explotacii.php'],
    ['zakupki_2_3_1_podvodniy_sposob_explotacii.php'],
    ['NtDotchety_2_3_1_podvodniy_sposob_explotacii.php'],
    ['other_page.php']
];
?>

<div class="plain-links-scroll">
    <?foreach($arResult["ITEMS"] as $arItem):


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

        $link = $arItem["PROPERTIES"]["LINK"]["VALUE"];
        $linkName = basename($link);

        $isActive = false;
        foreach ($equivalentGroups as $group) {
            if (in_array($currentPage, $group) && in_array($linkName, $group)) {
                $isActive = true;
                break;
            }
        }
        ?>
        <a href="<?=$link?>"
           id="<?=$this->GetEditAreaId($arItem['ID']);?>"
           class="icon-card<?=$isActive ? ' active-card' : ''?>">
            <?=$arItem["NAME"]?>
        </a>
    <?endforeach;?>
</div>
