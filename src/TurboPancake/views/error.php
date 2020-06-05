<?php
use TurboPancake\Exceptions\SystemException;

/**
 * @var SystemException[] $exceptions
 * @var string[] $details
 * @var string[] $modules
 * @var string[] $loaded_modules
 * @var string[] $middlewares
 */
?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
              content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>TurboPancake Error</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hack/0.8.1/hack.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/hack/0.8.1/dark.css">

        <style>

        </style>
    </head>
    <body class="hack dark">
        <div class="container">
            <h1>TruboPancake system Error !</h1>
            <?php foreach ($exceptions as $exception): ?>
                <div class="alert alert-error">
                    <?=$exception->getMessage();?><br>
                    <i><?=$exception->getFile()?>:<?=$exception->getLine()?></i>
                </div>
            <?php endforeach; ?>

            <div class="card">
                <header class="card-header">Modules</header>
                <div class="card-content">
                    <div class="inner">
                        <?php foreach ($modules as $module) : ?>
                            <?=$module?>
                            <?php if (in_array($module, $loaded_modules)): ?>
                                <b>[LOADED]</b>
                            <?php endif; ?>
                            <br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div style="height: 20px;"></div>
            <div class="card">
                <header class="card-header">Middlewares</header>
                <div class="card-content">
                    <div class="inner">
                        <?php foreach ($middlewares as $middleware) : ?>
                            <?=$middleware?>
                            <br>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <hr>
            <i>
                <?=$details['version']?><br>
                Container: <b><?=$details['container']?></b><br>
                Renderer: <b><?=$details['renderer']?></b>
            </i>
        </div>
    </body>
</html>