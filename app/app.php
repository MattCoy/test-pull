<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));

// Register services.
$app['dao.user'] = function ($app) {
    return new WF3\DAO\UserDAO($app['db'], 'users', 'WF3\Domain\User');
};

$app['dao.article'] = function ($app) {
    $articleDAO =  new WF3\DAO\ArticleDAO($app['db'], 'articles', 'WF3\Domain\Article');
    $articleDAO->setUserDAO($app['dao.user']);
    return $articleDAO;
};

