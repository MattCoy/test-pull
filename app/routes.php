
<?php

// Home page
$app->get('/', 'WF3\Controller\HomeController::homePageAction')->bind('homepage');

//article list with author name
$app->get('/list', 'WF3\Controller\HomeController::listAction')->bind('listWithAuthors');

//author details page
$app->get('/author/{id}', 'WF3\Controller\HomeController::authorAction')->bind('author');

//article details page
$app->get('/article/{id}', 'WF3\Controller\HomeController::articleAction')->bind('article');

//login page
$app->get('/login', 'WF3\Controller\HomeController::loginAction')->bind('login');