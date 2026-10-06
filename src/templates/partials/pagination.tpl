{if $totalPages > 1}
    <nav class="pagination" aria-label="Пагинация статей">
        <ul class="pagination__list">
            {* Кнопка «Назад» *}
            {if $currentPage > 1}
                <li class="pagination__item">
                    <a href="/category/{$category.id}?sort={$currentSort}&page={$currentPage - 1}" class="pagination__link" aria-label="Предыдущая страница">
                        &larr; Назад
                    </a>
                </li>
            {/if}

            {* Номера страниц *}
            {for $p = 1 to $totalPages}
                {if $p == $currentPage}
                    <li class="pagination__item pagination__item--active">
                        <span class="pagination__link" aria-current="page">{$p}</span>
                    </li>
                {else}
                    <li class="pagination__item">
                        <a href="/category/{$category.id}?sort={$currentSort}&page={$p}" class="pagination__link">
                            {$p}
                        </a>
                    </li>
                {/if}
            {/for}

            {* Кнопка «Вперёд» *}
            {if $currentPage < $totalPages}
                <li class="pagination__item">
                    <a href="/category/{$category.id}?sort={$currentSort}&page={$currentPage + 1}" class="pagination__link" aria-label="Следующая страница">
                        Вперёд &rarr;
                    </a>
                </li>
            {/if}
        </ul>
    </nav>
{/if}
