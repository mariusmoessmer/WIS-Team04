<?php
require_once('wikipage.php');
require_once('view.php');

session_start();


if(isset($_POST['old'])) {
    $old = $_POST['old'];
}

if(isset($_POST['title'])) {
    $title = $_POST['title'];
}

if(isset($_POST['content'])) {
	$content = $_POST['content'];
}


//Load the page and edit settings
$wikiPage = WikiPage::load($old);
        
if($wikiPage == null) {
	$wikiPage = new WikiPage($title, $content);
} else {
	$wikiPage->delete();
	$wikiPage = new WikiPage($title, $content);
}


//Save
if($wikiPage->save()) {
	header('Location: index.php?title=' . $wikiPage->getTitle());
	exit;
}
