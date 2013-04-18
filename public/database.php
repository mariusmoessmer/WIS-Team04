<?php

require_once('../autoloader.php');

if(isset($_GET['action'])) {
	if($_GET['action'] == 'generate_random') {
		Article::generateRandomAndSave();
		ShowController::showArticleGenerationFinished();
	}
	else if($_GET['action'] == 'delete_all') {
    	Article::deleteAll();
		ShowController::showDeleteArticlesFinished();
	}
}
else
{
	ShowController::listPages();
}
