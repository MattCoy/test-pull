<?php

use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Silex\Provider;

// Register global error and exception handlers
ErrorHandler::register();
ExceptionHandler::register();

// Register service providers.
$app->register(new Silex\Provider\DoctrineServiceProvider());

$app->register(new Provider\HttpFragmentServiceProvider());
$app->register(new Provider\ServiceControllerServiceProvider());

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
));
$app->register(new Silex\Provider\AssetServiceProvider(), array(
    'assets.version' => 'v1'
));

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'secured' => array(
            'pattern' => '^/',
            'anonymous' => true,
            'logout' => true,
            'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
            'users' => function () use ($app) {
                return new WF3\DAO\UserDAO($app['db'], 'users', 'WF3\Domain\User');
            },
            'logout' => array('logout_path' => '/logout', 'invalidate_session' => true)
        ),
    ),
));

$app->register(new Provider\WebProfilerServiceProvider(), array(
    'profiler.cache_dir' => __DIR__.'/../cache/profiler',
    'profiler.mount_prefix' => '/_profiler', // this is the default
));

$app->register(new Sorien\Provider\DoctrineProfilerServiceProvider());

// Register services.
$app['dao.user'] = function ($app) {
    return new WF3\DAO\UserDAO($app['db'], 'users', 'WF3\Domain\User');
};

$app['dao.article'] = function ($app) {
    $articleDAO =  new WF3\DAO\ArticleDAO($app['db'], 'articles', 'WF3\Domain\Article');
    $articleDAO->setUserDAO($app['dao.user']);
    return $articleDAO;
};

