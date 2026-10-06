{extends file="layout.tpl"}

{block name="content"}
    {* Хлебные крошки *}
    <nav class="breadcrumbs" aria-label="Навигация по разделам">
        <a href="/" class="breadcrumbs__link">Главная</a>
        {if !empty($article.categories)}
            <span class="breadcrumbs__separator">/</span>
            <a href="/category/{$article.categories[0].id}" class="breadcrumbs__link">{$article.categories[0].name|escape}</a>
        {/if}
        <span class="breadcrumbs__separator">/</span>
        <span class="breadcrumbs__current">{$article.title|escape}</span>
    </nav>

    {* Полная статья *}
    <article class="article-single">
        <header class="article-single__header">
            {if !empty($article.categories)}
                <div class="article-single__tags">
                    {foreach from=$article.categories item=cat}
                        <a href="/category/{$cat.id}" class="badge">{$cat.name|escape}</a>
                    {/foreach}
                </div>
            {/if}

            <h1 class="article-single__title">{$article.title|escape}</h1>

            <div class="article-single__meta">
                <span class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {$article.created_at|date_format:"%d.%m.%Y"}
                </span>
                <span class="meta-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    {$article.views} просмотров
                </span>
            </div>
        </header>

        {if !empty($article.image)}
            <div class="article-single__image-wrapper">
                <img src="{$article.image|escape}" alt="{$article.title|escape}" class="article-single__image">
            </div>
        {/if}

        {if !empty($article.description)}
            <div class="article-single__lead">
                {$article.description|escape}
            </div>
        {/if}

        <div class="article-single__content">
            {$article.content|nl2br}
        </div>

        <footer class="article-single__footer">
            {if !empty($article.categories)}
                <div class="article-single__footer-cats">
                    <span class="footer-label">Категории:</span>
                    <div class="article-card__tags">
                        {foreach from=$article.categories item=cat}
                            <a href="/category/{$cat.id}" class="badge">{$cat.name|escape}</a>
                        {/foreach}
                    </div>
                </div>
            {/if}

            <div class="article-single__footer-actions">
                <a href="/" class="btn btn-outline">&larr; На главную</a>
                {if !empty($article.categories)}
                    <a href="/category/{$article.categories[0].id}" class="btn btn-primary">
                        Ко всем статьям «{$article.categories[0].name|escape}»
                    </a>
                {/if}
            </div>
        </footer>
    </article>

    {* Блок «Похожие статьи» *}
    {if !empty($similarArticles)}
        <section class="similar-articles">
            <div class="similar-articles__header">
                <h2 class="similar-articles__title">Похожие статьи</h2>
                <span class="similar-articles__subtitle">Материалы из тех же категорий</span>
            </div>

            <div class="articles-grid">
                {foreach from=$similarArticles item=similar}
                    {include file="partials/article_card.tpl" article=$similar}
                {/foreach}
            </div>
        </section>
    {/if}
{/block}
