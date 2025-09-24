<?php
$currentPage = basename($APPLICATION->GetCurPage(false)); // текущий файл
?>

<div class="plain-links-scroll">
    <?foreach($arResult["ITEMS"] as $arItem):
        $link = $arItem["PROPERTIES"]["LINK"]["VALUE"];
        $linkName = basename($link);


        $equivalentPages = [
            '1_more_dobycha.php' => ['1_more_dobycha.php', '1_2_tecknologii_explotacii.php'],
            'svodka_2_3_1_podvodniy_sposob_explotacii.php' => ['svodka_2_3_1_podvodniy_sposob_explotacii.php'],
            'primeneniye_v_ip_2_3_1_podvodniy_sposob_explotacii.php' => ['primeneniye_v_ip_2_3_1_podvodniy_sposob_explotacii.php'],
            'explotiruyemoe_2_3_1_podvodniy_sposob_explotacii.php' => ['explotiruyemoe_2_3_1_podvodniy_sposob_explotacii.php'],
            'proizvoditeli_2_3_1_podvodniy_sposob_explotacii.php' => ['proizvoditeli_2_3_1_podvodniy_sposob_explotacii.php'],
            'tekushiy_status_2_3_1_podvodniy_sposob_explotacii.php' => ['tekushiy_status_2_3_1_podvodniy_sposob_explotacii.php'],
            'niokrPatents_2_3_1_podvodniy_sposob_explotacii.php' => ['niokrPatents_2_3_1_podvodniy_sposob_explotacii.php'],
            'zakupki_2_3_1_podvodniy_sposob_explotacii.php' => ['zakupki_2_3_1_podvodniy_sposob_explotacii.php'],
            'NtDotchety_2_3_1_podvodniy_sposob_explotacii.php' => ['NtDotchety_2_3_1_podvodniy_sposob_explotacii.php'],
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
