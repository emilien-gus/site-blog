{extends file="layout.tpl"}

{block name="content"}
    {* Хлебные крошки *}
    <nav class="breadcrumbs" aria-label="Навигация по разделам">
        <a href="/" class="breadcrumbs__link">Главная</a>
        <span class="breadcrumbs__separator">/</span>
        <span class="breadcrumbs__current">{$category.name|escape}</span>
    </nav>

    {* Заголовок категории *}
    <header class="category-header">
        <h1 class="category-header__title">{$category.name|escape}</h1>
        {if !empty($category.description)}
            <p class="category-header__desc">{$category.description|escape}</p>
        {/if}
    </header>

    {* Панель сортировки и количества *}
    <div class="category-toolbar">
        <div class="category-toolbar__count">
            Всего статей: <strong>{$totalArticles}</strong>
        </div>

        <form method="GET" action="/category/{$category.id}" class="sort-form">
            <label for="sort-select" class="sort-form__label">Сортировка:</label>
            <div class="select-wrapper">
                <select name="sort" id="sort-select" class="sort-form__select" onchange="this.form.submit()">
                    <option value="date_desc" {if $currentSort == 'date_desc'}selected{/if}>Сначала новые</option>
                    <option value="date_asc" {if $currentSort == 'date_asc'}selected{/if}>Сначала старые</option>
                    <option value="views_desc" {if $currentSort == 'views_desc'}selected{/if}>По просмотрам &darr;</option>
                    <option value="views_asc" {if $currentSort == 'views_asc'}selected{/if}>По просмотрам &uarr;</option>
                </select>
            </div>
            <noscript>
                <button type="submit" class="btn btn-sm">Применить</button>
            </noscript>
        </form>
    </div>

    {* Список статей *}
    {if !empty($articles)}
        <div class="articles-grid">
            {foreach from=$articles item=article}
                {include file="partials/article_card.tpl" article=$article}
            {/foreach}
        </div>

        {* Пагинация *}
        {include file="partials/pagination.tpl"}
    {else}
        <div class="empty-state">
            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <h2>В этой категории пока нет статей</h2>
            <p>Вернитесь на <a href="/" style="color: var(--color-primary); text-decoration: underline;">главную страницу</a>.</p>
        </div>
    {/if}
{/block}
