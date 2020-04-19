<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\calculator\Calculator;

ini_set('display_errors', 1);
error_reporting(E_ALL);
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
        <li>Значение меди по LME: <span class="current-lme"></span> Данные актуальны на: <span class="stored-lme-date"></span></li>
        <li>Текущий курс $: <span class="current-minfin"></span> Данные актуальны на: <span class="stored-minfin-date"></span></li>
    </ul>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm">
                <form action="/controller.php">
                    <input type="text" class="datepicker" placeholder="Выбрать дату">
                </form>
            </div>

            <div class="col-sm">
                <h3>Цена</h3>
                <form class="form-inline" action="/controller.php" method="POST" id="bN">
            <div>
                <span class="open-bracket">(</span>
                <span class="average-lme"></span>
                <span class="plus-sign">+</span>
                <input type="text" value="" name="prize"/>
                <span class="close-bracket">)</span>
                <span>x</span>
                <span class="average-minfin"></span>
            </div>
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
