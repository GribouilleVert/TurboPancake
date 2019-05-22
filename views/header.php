<!doctype html>
<html lang="fr">
<head>
    <title>Haifunime</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/logo.png">
</head>

<body>
<div class="off-canvas off-canvas-sidebar-show">
    <header class="navbar">
        <!-- off-screen toggle button -->
        <a class="off-canvas-toggle btn btn-link btn-action" href="#sidebar">
            <i class="icon icon-menu"></i>
        </a>
        <section class="navbar-section links">
            <div class="dropdown dropdown-right">
                <a href="#" class="btn btn-link">Connexion</a>
                <a href="#" class="btn btn-link">Inscription</a>
        </section>
    </header>

    <div id="sidebar" class="off-canvas-sidebar">
        <div class="brand">
            <a href="/" class="logo">
                <img src="/assets/images/logo.png" alt="">
                <h2>Haifunime <small class="label label-secondary">DEV</small></h2>
            </a>
        </div>
        <div class="content">
            <ul class="nav">
                <li class="nav-item active">
                    <a href="/" class="text-light">Accueil</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="text-light">Les dernières sorties</a>
                </li>
                <li class="nav-item">
                    <a href="#" class="text-light">Catégories</a>
                    <ul class="nav">
                        <li class="nav-item">
                            <a href="#" class="text-light">Science fiction</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="text-light">Romantique</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="text-light">Slice of life</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="text-light">Magie / Mystique</a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="text-light">Etc...</a>
                        </li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a href="#" class="text-light">A propos</a>
                </li>
            </ul>
        </div>

        <div class="input-group search">
            <input class="form-input" type="text" placeholder="Rechercher">
            <button class="btn btn-primary input-group-btn"><i class="icon icon-search"></i></button>
        </div>
    </div>

    <a class="off-canvas-overlay" href="#close"></a>

    <div class="off-canvas-content">

        <main>
            <div class="container block">
                <h1><?=$title??'Sans nom'?></h1>
                <div class="card mt-1">
                    <div class="card-body py-0">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="/" class="tooltip" data-tooltip="Haifunime Distribution Network">HDN <small class="label label-secondary">DEV</small></a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="#" class="tooltip" data-tooltip="Accueil">Accueil</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>