<?php
require_once('wikipage.php');
require_once('view.php');

session_start();


if(isset($_GET['title'])) {
	$wikiPage = WikiPage::load($_GET['title']);
	
	if($wikiPage != null) {
	        $wikiPage->delete();
	}
}

header('Location: index.php');
exit;
