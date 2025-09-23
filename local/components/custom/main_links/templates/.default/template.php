<?php if(!empty($arResult)): ?>
    <?php foreach($arResult as $item): ?>
        <?php
        $link = isset($item["LINK"]) && !empty($item["LINK"]) ? $item["LINK"] : "#";
        $name = isset($item["NAME"]) && !empty($item["NAME"]) ? $item["NAME"] : "Без названия";
        ?>
        <a href="<?=$link?>"
           style="
                display:block;
                text-decoration:none;
                color:#ffffff;
                background-color:#007ac3;
                text-align:center;
                padding:10px 20px;
                border-radius:6px;
                font-weight:bold;
                transition: background-color 0.2s;
                margin-bottom:10px;
           "
           onmouseover="this.style.backgroundColor='#005ea3';"
           onmouseout="this.style.backgroundColor='#007ac3';"
        >
            <?=$name?>
        </a>
    <?php endforeach; ?>
<?php else: ?>
    <span style="color:#999;">Ссылки не найдены</span>
<?php endif; ?>
