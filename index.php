<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\renderer\Renderer;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$renderer = new Renderer();
?>

<!doctype html>
<html lang="ru-RU">
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
        crossorigin="anonymous">
    <script src="./src/js/jquery.min.js"></script>
    <script src="./src/js/main.js"></script>
    <title>Document</title>
</head>
<body>
<div class="container">
    <h2>Калькулятор катанки / проволоки</h2>

    <ul>
        <li>Значение меди по LME: <?= $renderer->currentLme ?></li>
        <li>Текущий курс $: <?= $renderer->currentMinfin ?></li>
    </ul>

    <h3>Формула "Б/н"</h3>
    <form class="form-inline" action="" method="POST">
        <span>
            (<?= number_format($renderer->averageLme, 3) ?> + 250)
            * <?= number_format($renderer->averageMinfin, 3) ?></span>
        <span>* 1.2 = <?= number_format(round($renderer->bnFormula($renderer->averageLme, $renderer->averageMinfin)), 0, '.', ' ') ?></span>
    </form>

    <h3>Формула "Cash"</h3>
    <form class="form-inline" action="" method="POST">
        <span>
            (<?= number_format($renderer->averageLme, 3) ?> + 250)
            * <?= number_format($renderer->averageMinfin, 3) ?></span>
        <span>* 1.2 * 0.9 = <?= number_format(round($renderer->cashFormula($renderer->averageLme, $renderer->averageMinfin)), 0, '.', ' ') ?></span>
    </form>

</div>
</body>
</html>
