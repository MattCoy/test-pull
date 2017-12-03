
<?php

// Home page
$app->get('/', function () use ($app) {
    $articles = $app['dao.article']->findALLOrderByDate('DESC');
    $users = $app['dao.user']->findALL();
    return $app['twig']->render('index.html.twig', array(
    												'articles' => $articles,
    												'users' => $users
    ));
})->bind('homepage');

//article list with author name
$app->get('/list', function () use ($app) {
    $articles = $app['dao.article']->findALLWithUser();
    return $app['twig']->render('list.html.twig', array(
    												'articles' => $articles
    ));
})->bind('listWithAuthors');

//author details page
$app->get('/author/{id}', function ($id) use ($app) {
    $user = $app['dao.user']->find($id);
    $articles = $app['dao.article']->findByUser($id);
    return $app['twig']->render('author.html.twig', array(
    												'user' => $user,
                                                    'articles' => $articles
    ));
})->bind('author');

//article details page
$app->get('/article/{id}', function ($id) use ($app) {
    $article = $app['dao.article']->find($id);
    $author = $app['dao.user']->find($article->getAuthor());
    return $app['twig']->render('article.html.twig', array(
                                                    'article' => $article,
                                                    'author' => $author
    ));
})->bind('article');