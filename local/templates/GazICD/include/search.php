<div class="search-header">
    <form class="search-form" action="/search/" method="get">
        <div class="search-input-wrapper">
            <input type="text" name="q" class="search-input" placeholder="Поиск" />
            <button type="submit" class="search-icon-btn">
                <img src="<?=SITE_TEMPLATE_PATH?>/img/search.svg" alt="search icon" class="search-icon">
            </button>
        </div>
    </form>

    <!-- Кнопка фильтров -->
    <button type="button" class="filters-btn">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/filters.svg" alt="filters icon" class="filters-icon">
    </button>

    <!-- Кнопка избранного -->
    <button type="button" class="favorites-btn">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/star.svg" alt="favorites icon" class="favorites-icon">
    </button>

    <!-- Навигация -->
    <div class="navigation-wrapper">
        <div class="navigation-text">
            <a href="#">Работники</a>
            <a href="#">Подразделения</a>
            <a href="#">Филиалы и организации</a>
            <a href="#">Обратная связь</a>
        </div>
    </div>

    <!-- Кнопка помощи -->
    <button type="button" class="help-btn">
        <img src="<?=SITE_TEMPLATE_PATH?>/img/help.svg" alt="help icon" class="help-icon">
    </button>
</div>
