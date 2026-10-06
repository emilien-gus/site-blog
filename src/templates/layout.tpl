<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$title|default:'Блог'}</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    {include file="partials/header.tpl"}

    <main class="container">
        {block name="content"}{/block}
    </main>

    {include file="partials/footer.tpl"}
</body>
</html>
