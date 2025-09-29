<?php
/**
 * Ассистент Викки
 */


ob_start();
$APPLICATION->IncludeFile(
    SITE_TEMPLATE_PATH."/include/assistant_text.php",
    ["PAGE" => $APPLICATION->GetCurPage(false)],
    ["MODE" => "html"]
);
$assistantText = trim(ob_get_clean());
?>

<?php if ($assistantText): ?>
    <div id="assistant-wrapper">
        <img id="assistant-img"
             src="<?=SITE_TEMPLATE_PATH?>/img/Asistent_svg.svg"
             alt="Ассистент Викки">

        <div id="assistant-bubble">
            <div class="assistant-tail-border"></div>
            <div class="assistant-tail-bg"></div>

            <?= $assistantText ?>
        </div>
    </div>
<?php endif; ?>

<!-- Свернутый ассистент -->
<div id="assistant-collapsed" style="<?= $assistantText ? 'display:none;' : 'display:block;' ?>">
    <img src="<?=SITE_TEMPLATE_PATH?>/img/collapsed_asistent.svg"
         alt="Открыть ассистента">
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const wrapper = document.getElementById("assistant-wrapper");
        const img = document.getElementById("assistant-img");
        const bubble = document.getElementById("assistant-bubble");
        const collapsed = document.getElementById("assistant-collapsed");

        if (wrapper) {
            setTimeout(() => img.classList.add("show"), 300);
            setTimeout(() => bubble.classList.add("show"), 1300);

            setTimeout(() => hideAssistant(), 10000);

            wrapper.addEventListener("click", hideAssistant);
        }

        function hideAssistant() {
            if (img) img.classList.remove("show");
            if (bubble) bubble.classList.remove("show");

            setTimeout(() => {
                if (wrapper) wrapper.style.display = "none";
                if (collapsed) collapsed.style.display = "block";
            }, 1000);
        }

        if (collapsed) {
            collapsed.addEventListener("click", function () {
                collapsed.style.display = "none";
                if (wrapper) {
                    wrapper.style.display = "flex";
                    setTimeout(() => img.classList.add("show"), 100);
                    setTimeout(() => bubble.classList.add("show"), 1000);
                }
            });
        }
    });
</script>

<style>
    /* Ассистент */
    #assistant-wrapper {
        position: fixed;
        bottom: 30px;
        left: 28%; /* чуть сдвинули вправо */
        display: flex;
        flex-direction: column-reverse;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        cursor: pointer;
    }

    #assistant-img {
        height: 180px;
        opacity: 0;
        transform: translateY(50px);
        transition: opacity 1s ease, transform 1s ease;
        margin-left: -54%;
    }
    #assistant-img.show {
        opacity: 1;
        transform: translateY(0);
    }

    #assistant-bubble {
        background: #f5faff;
        border: 2px solid #007ac3;
        border-radius: 20px;
        padding: 20px;
        font-size: 18px;
        line-height: 1.4;
        position: relative;
        box-shadow: 2px 4px 10px rgba(0,0,0,0.15);
        max-width: 400px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.6s ease, transform 0.6s ease;
    }


    #assistant-bubble.show {
        opacity: 1;
        transform: translateY(0);
    }


    .assistant-tail-border {
        position: absolute;
        top: 100%;
        left: 25%;
        transform: translateX(-50%);
        width: 0; height: 0;
        border-left: 15px solid transparent;
        border-right: 15px solid transparent;
        border-top: 15px solid #007ac3;
    }
    .assistant-tail-bg {
        position: absolute;
        top: calc(100% - 2px);
        left: 25%;
        transform: translateX(-50%);
        width: 0; height: 0;
        border-left: 13px solid transparent;
        border-right: 13px solid transparent;
        border-top: 13px solid #f5faff;
    }

    /* Свернутый ассистент */
    #assistant-collapsed {
        position: fixed;
        bottom: 30px;
        right: 20px;
        z-index: 9999;
        cursor: pointer;
        display: none;
    }
    #assistant-collapsed img {
        height: 80px;
    }

</style>
