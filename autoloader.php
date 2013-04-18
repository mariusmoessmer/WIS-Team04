<?php

error_reporting(E_ALL);

session_start();


//Create view element
$GLOBALS['view'] = new stdClass;


//Tools
require_once('tools/paginator.php');

//Models
require_once('models/article.php');

//Views
require_once('views/view.php');

//Controllers
require_once('controllers/dbmanager.php');
require_once('controllers/show.php');
require_once('controllers/action.php');