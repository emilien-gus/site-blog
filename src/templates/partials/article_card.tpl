<article class="article-card">
    <a href="/article/{$article.id}" class="article-card__image-link">
        {if !empty($article.image)}
            <img src="{$article.image|escape}" alt="{$article.title|escape}" class="article-card__image" loading="lazy">
        {else}
            <div class="article-card__placeholder">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                </svg>
            </div>
        {/if}
    </a>

    <div class="article-card__body">
        {if !empty($article.categories)}
            <div class="article-card__tags">
                {foreach from=$article.categories item=cat}
                    <a href="/category/{$cat.id}" class="badge">{$cat.name|escape}</a>
                {/foreach}
            </div>
        {/if}

        <h3 class="article-card__title">
            <a href="/article/{$article.id}">{$article.title|escape}</a>
        </h3>

        {if !empty($article.description)}
            <p class="article-card__desc">
                {$article.description|escape}
            </p>
        {/if}

        <div class="article-card__footer">
            <div class="article-card__meta">
                <span class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    {$article.created_at|date_format:"%d.%m.%Y"}
                </span>
                <span class="meta-item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    </svg>
                    {$article.views}
                </span>
            </div>
            <a href="/article/{$article.id}" class="article-card__readmore">
                Читать &rarr;
            </a>
        </div>
    </div>
</article>
