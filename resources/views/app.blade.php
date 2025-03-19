<!DOCTYPE html>
<html class="h-full {{ request()->is('/') ? '' : 'bg-gray-100' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, width=device-width">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&amp;display=swap" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#ffffff">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite('resources/js/app.js')
    @inertiaHead
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(98232814, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true,
            trackHash:true
        });
    </script>
    <!-- /Yandex.Metrika counter -->
</head>
<body class="template-base">
<main class="page-content">
    @inertia
</main>
<noscript><div><img src="https://mc.yandex.ru/watch/98232814" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
</body>

</html>

<style>
    input,
    textarea {
        border-radius: 0; /* Убираем скругление углов */
        outline: none;    /* Убираем рамку при фокусе */
        box-shadow: none; /* Убираем тень, если она есть */
    }
    #share-block .ya-share2__item {
        /*width: 5px;  !* Ширина иконок *!*/
        /*height: 5px; !* Высота иконок *!*/
    }

    /* Опционально: изменение размера самого содержимого иконок */
    #share-block .ya-share2__item .ya-share2__icon {
        height: 36px;
        width: 36px;
        background-size: 36px 36px;
    }

    /* Пример увеличения области клика */
    #share-block .ya-share2__item:hover {
        /*transform: scale(1.1); !* Увеличение при наведении для лучшего взаимодействия *!*/
    }

    .superadmin-tips__date {
        font-size: 13px;
    }
    /* Стили для уменьшения шрифта опций и выбранного значения */
    .v-select .vs__dropdown-toggle {
        font-size: 14px; /* Размер текста для выбранной опции */
    }

    .v-select .vs__dropdown .vs__list {
        font-size: 14px; /* Размер текста для опций в выпадающем списке */
    }

    .superadmin-tips__date {
        text-decoration: none;
    }

    .v-select .vs__selected {
        font-size: 14px;
    }

    .superadmin-tips .form {
        max-width: 1000px;
        margin-left: auto;
    }

    .client__icon img {
        max-width: 2.75rem;
        height: auto;
        max-height: 2.75rem;
        border-radius: 0.6rem;
    }

    .list-standard__image img {
        max-width: 42px;
        height: auto;
        max-height: 42px;
        border-radius: 0.6rem;
    }
</style>

