<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="/css/global.css?ver=<?= time() ?>">
    <?php if (!empty($cssFile)): ?>
        <link rel="stylesheet" href="<?= htmlspecialchars($cssFile) ?>">
    <?php endif; ?>
    <title>Document</title>
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">RoiStat</div>
            <nav>
                <ul class="main-menu">
                    <!--                <li><a href="#about">О нас</a></li>-->
                    <li><a href="/about">Описание</a></li>
                    <li><a href="/create">Создать сделку</a></li>



                </ul>
            </nav>
        </div>

    </header>