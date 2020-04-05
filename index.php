<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\calculator\Calculator;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$calculator = new Calculator();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <title>Katanka</title>

    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript" src="/src/assets/main.js"></script>
</head>
<body>
<div class="container">
    <h2>Калькулятор катанки / проволоки</h2>

    <ul>
        <li>Значение меди по LME: <?= $calculator->currentLme ?> Данные актуальны на: <?= $calculator->storedLmeDate ?></li>
        <li>Текущий курс $: <?= $calculator->currentMinfin ?> Данные актуальны на: <?= $calculator->storedMinfinDate ?></li>
    </ul>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm">
                <form action="/controller.php" class="form-inline">
                    <input type="text" class="datepicker">
                </form>
                <button id="foo-btn">click</button>
            </div>

            <div class="col-sm">
                <h3>Цена</h3>
                <form class="form-inline" action="/controller.php" method="POST" id="bN">
            <span>
                (<?= number_format($calculator->averageLme, 2, ',', '') ?> +
                <input type="text" value="" name="prize"/>)
                x <?= number_format($calculator->averageMinfin, 3, ',', '') ?>
            </span>
                    <span>&nbsp;x 1,2 = <span class="bn-value"></span></span>
                </form>
            </div>

            <div class="col-sm">
                <h3>Цена -10%</h3>
            <span class="bn-value">
            </span>
                    <span>&nbsp;- 10% = <span class="cash-value"></span></span>
            </div>
        </div>
    </div>

</div>
<div id="chart_div"></div>
</body>
</html>
