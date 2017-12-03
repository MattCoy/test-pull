<?php
namespace WF3\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class HomeController {
	/**
     * Home page controller.
     *
     * @param Application $app Silex application
     */
	public function homePageAction(Application $app){	
	    $articles = $app['dao.article']->findALLOrderByDate('DESC');
	    $users = $app['dao.user']->findALL();
	    return $app['twig']->render('index.html.twig', array(
	    												'articles' => $articles,
	    												'users' => $users
	    ));
	}

	/**
     * Articles list page controller.
     *
     * @param Application $app Silex application
     */
	public function listAction(Application $app){
		$articles = $app['dao.article']->findALLWithUser();
	    return $app['twig']->render('list.html.twig', array(
	    												'articles' => $articles
	    ));
	}

	/**
     * Author details page controller.
     *
     * @param Application $app Silex application
     * @param $id the user id
     */
	public function authorAction(Application $app, $id){		
	    $user = $app['dao.user']->find($id);
	    $articles = $app['dao.article']->findByUser($id);
	    return $app['twig']->render('author.html.twig', array(
	    												'user' => $user,
	                                                    'articles' => $articles
	    ));
	}

	/**
     * Article details page controller.
     *
     * @param Application $app Silex application
     * @param $id the article id
     */
	public function articleAction(Application $app, $id){		
	    $article = $app['dao.article']->find($id);
	    $author = $app['dao.user']->find($article->getAuthor());
	    return $app['twig']->render('article.html.twig', array(
	                                                    'article' => $article,
	                                                    'author' => $author
	    ));
	}

	/**
     * login page controller.
     *
     * @param Application $app Silex application
     * @param $request Symfony\Component\HttpFoundation\Request
     */
	public function loginAction(Application $app, Request $request){
	    return $app['twig']->render('login.html.twig', array(
	        'error'         => $app['security.last_error']($request),
	        'last_username' => $app['session']->get('_security.last_username'),
	    ));
	}
}
