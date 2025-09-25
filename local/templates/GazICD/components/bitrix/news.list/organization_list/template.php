<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>

<div class="organization-list">
    <?foreach($arResult["ITEMS"] as $item):?>
        <?
        $this->AddEditAction($item['ID'], $item['EDIT_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_EDIT"));
        $this->AddDeleteAction($item['ID'], $item['DELETE_LINK'], CIBlock::GetArrayByID($item["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));
        ?>
        <div class="organization-item" id="<?=$this->GetEditAreaId($item['ID']);?>">
            <strong><?=$item["NAME"]?></strong>
            <p><?=$item["PROPERTIES"]["FILIAL"]["VALUE"]?></p>
        </div>
    <?endforeach;?>
</div>
