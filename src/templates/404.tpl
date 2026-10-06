{extends file="layout.tpl"}

{block name="content"}
    <div class="not-found">
        <div class="not-found__code">404</div>
        <h1 class="not-found__title">Страница не найдена</h1>
        <p class="not-found__message">{$message|default:'Запрашиваемая страница не существует или была перемещена.'}</p>
        <a href="/" class="btn btn-primary">&larr; Вернуться на главную</a>
    </div>
{/block}
