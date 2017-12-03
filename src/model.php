
<?php
function getArticles(){
	$bdd = new PDO('mysql:host=localhost;dbname=mvc;charset=utf8', 'root', '');
	return $bdd->query('select * from articles order by id desc');
}

