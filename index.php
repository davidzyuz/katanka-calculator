<?php

require_once __DIR__ . '/vendor/autoload.php';

use app\renderer\Renderer;
use app\storeManager\StoreManager;

ini_set('display_errors', 1);
error_reporting(E_ALL);

$renderer = new Renderer();
$storeManager = new StoreManager();
$wasteFormulaData = $storeManager->fetchFormulaValues('waste_formula');
$bnFormulaData = $storeManager->fetchFormulaValues('bn_formula');
$cashFormulaData = $storeManager->fetchFormulaValues('cash_formula');
?>

<!doctype html>
<html lang="ru-RU">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link
        rel="stylesheet"
        href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css"
        integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh"
        crossorigin="anonymous">
    <script src="src/js/jquery.dev.js"></script>
    <script src="./src/js/main.js"></script>
    <title>Document</title>
</head>
<body>
<div class="container">
    <h2>Калькулятор катанки</h2>
    <ol>
        <li>Для указания дробной части числа используйте знак "." вместо ","
        <ul>
            <li>Корректно: 12.34</li>
            <li>Ошибка: 12,34</li>
        </ul>
        </li>
        <li>
            Чтобы изменить значения формулы нажмите на кнопку "Изменить" напротив формулы, значения которой нужно изменить.
            Для сохранения данных нажмите на кнопку "Сохранить".
        </li>
    </ol>
    <ul>
        <li>Значение меди по LME: <?= number_format($renderer->lmeData, 2); ?> Дата: <?= date('d M Y'); ?></li>
        <li>Среднее значение LME за 10 дней: Нет данных</li>
        <li>Текущий курс $: <?= number_format($renderer->minfinData, 2); ?> Дата: <?= date('d M Y'); ?></li>
        <li>Курс за 5 дней: Нет данных</li>
    </ul>

    <h3>Формула "Б/н"</h3>
    <form class="form-inline" action="" method="POST">
        <span>
            (<?= number_format($renderer->lmeData, 2) ?> + 250)
            * <?= number_format($renderer->minfinData, 2) ?></span>
        <span>*</span>
        <input type="number" value="<?= $bnFormulaData['x'] ?>" name="x" min="00.00" disabled/>
        <button id="2" type="submit" class="btn btn-primary" name="submit" value="2">Изменить</button>
    </form>

    <h3>Формула "Cash"</h3>
    <form class="form-inline" action="" method="POST">
        <span>
            (<?= number_format($renderer->lmeData, 2) ?> + 250)
            * <?= number_format($renderer->minfinData, 2) ?></span>
        <span>*</span>
        <input type="number" value="<?= $cashFormulaData['x'] ?>" name="x" min="00.00" disabled/>
        <span>-</span>
        <input type="number" value="<?= $cashFormulaData['percent'] ?>" name="percent" min="00.00" disabled/>
        <span>%</span>
        <button id="3" type="submit" class="btn btn-primary" name="submit" value="3">Изменить</button>
    </form>

    <h3>Формула "Лом"</h3>
    <form class="form-inline" action="" method="POST">
        <span>
            (<?= number_format($renderer->lmeData, 2) ?> + 250)
            * <?= number_format($renderer->minfinData, 2) ?></span>
        <span>*</span>
        <input type="number" value="<?= $wasteFormulaData['x'] ?>" name="x" min="00.00" disabled/>
        <span>*</span>
        <input type="number" value="<?= $wasteFormulaData['y'] ?>" name="y" min="00.00" disabled/>
        <span>-</span>
        <input type="number" value="<?= $wasteFormulaData['z'] ?>" name="z" min="00.00" disabled/>
        <button id="1" type="submit" class="btn btn-primary" name="submit" value="1">Изменить</button>
    </form>

</div>
</body>
</html>
