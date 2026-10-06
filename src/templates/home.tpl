{extends file="layout.tpl"}

{block name="content"}
    <section class="hero">
        <h1 class="hero__title">Добро пожаловать в наш блог</h1>
        <p class="hero__subtitle">Актуальные статьи, новости и полезные материалы по рубрикам</p>
    </section>

    {if !empty($categories)}
        <div class="category-sections">
            {foreach from=$categories item=category}
                <section class="category-section">
                    <div class="category-section__header">
                        <div class="category-section__info">
                            <h2 class="category-section__title">
                                <a href="/category/{$category.id}">{$category.name|escape}</a>
                            </h2>
                            {if !empty($category.description)}
                                <p class="category-section__desc">{$category.description|escape}</p>
                            {/if}
                        </div>
                        <a href="/category/{$category.id}" class="category-section__link">
                            Все статьи категории &rarr;
                        </a>
                    </div>

                    <div class="articles-grid">
                        {foreach from=$category.articles item=article}
                            {include file="partials/article_card.tpl" article=$article}
                        {/foreach}
                    </div>
                </section>
            {/foreach}
        </div>
    {else}
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
            </svg>
            <h2>Статей пока нет</h2>
            <p>База данных пуста. На следующих шагах мы добавим сидер для генерации тестовых статей и категорий.</p>
        </div>
    {/if}
{/block}
