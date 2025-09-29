<?php
/**
 * Свёрнутый ассистент Викки (для всех страниц кроме главной)
 */

// Получаем текст ассистента
ob_start();
$APPLICATION->IncludeFile(
    SITE_TEMPLATE_PATH."/include/assistant_text.php",
    ["PAGE" => $APPLICATION->GetCurPage(false)],
    ["MODE" => "html"]
);
$assistantText = trim(ob_get_clean());
?>

<div id="assistant-collapsed">
    <img src="<?=SITE_TEMPLATE_PATH?>/img/collapsed_asistent.svg"
         alt="Ассистент Викки">
</div>

<?php if ($assistantText): ?>
    <div id="assistant-bubble-collapsed" class="assistant-bubble-hidden">
        <div class="assistant-tail-border-bottom"></div>
        <div class="assistant-tail-bg-bottom"></div>
        <?= $assistantText ?>
    </div>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const collapsed = document.getElementById("assistant-collapsed");
        const bubbleCollapsed = document.getElementById("assistant-bubble-collapsed");

        if (!collapsed) return;

        if (bubbleCollapsed) {
            // Автоматически открыть при загрузке
            setTimeout(() => {
                bubbleCollapsed.classList.remove("assistant-bubble-hidden");
                bubbleCollapsed.classList.add("assistant-bubble-show");
            }, 500);

            // Автоматически закрыть через 10 секунд
            setTimeout(() => {
                bubbleCollapsed.classList.remove("assistant-bubble-show");
                bubbleCollapsed.classList.add("assistant-bubble-hidden");
            }, 10500);
        }

        collapsed.addEventListener("click", function () {
            if (!bubbleCollapsed) return;

            if (bubbleCollapsed.classList.contains("assistant-bubble-hidden")) {
                bubbleCollapsed.classList.remove("assistant-bubble-hidden");
                bubbleCollapsed.classList.add("assistant-bubble-show");

                setTimeout(() => {
                    bubbleCollapsed.classList.remove("assistant-bubble-show");
                    bubbleCollapsed.classList.add("assistant-bubble-hidden");
                }, 10000);
            } else {
                bubbleCollapsed.classList.remove("assistant-bubble-show");
                bubbleCollapsed.classList.add("assistant-bubble-hidden");
            }
        });
    });
</script>

<style>
    /* Свёрнутый ассистент */
    #assistant-collapsed {
        position: fixed;
        bottom: 30px;
        right: 20px;
        z-index: 9999;
        cursor: pointer;
    }
    #assistant-collapsed img {
        height: 80px;
    }

    /* Бабл ассистента */
    #assistant-bubble-collapsed {
        background: #f5faff;
        border: 2px solid #007ac3;
        border-radius: 20px;
        padding: 20px;
        font-size: 18px;
        line-height: 1.4;
        position: fixed;
        bottom: 136px;
        right: 20px;
        box-shadow: 2px 4px 10px rgba(0,0,0,0.15);
        max-width: 400px;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.4s ease, transform 0.4s ease;
        pointer-events: none;
    }
    .assistant-bubble-show {
        opacity: 1 !important;
        transform: translateY(0) !important;
        pointer-events: auto;
    }
    .assistant-bubble-hidden {
        opacity: 0;
        transform: translateY(20px);
        pointer-events: none;
    }

    /* Хвостик облачка снизу справа */
    .assistant-tail-border-bottom {
        position: absolute;
        bottom: -20px;
        right: 20px;
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-top: 20px solid #007ac3;
        border-right: 20px solid transparent;
    }
    .assistant-tail-bg-bottom {
        position: absolute;
        bottom: -18px;
        right: 21px;
        width: 0;
        height: 0;
        border-left: 18px solid transparent;
        border-top: 18px solid #f5faff;
        border-right: 18px solid transparent;
    }
</style>
