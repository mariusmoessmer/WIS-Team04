<?php

error_reporting(E_ALL);

session_start();


//Create view element
$GLOBALS['view'] = new stdClass;


//Models
require_once('models/wikipage.php');

//Views
require_once('views/view.php');

//Controllers
require_once('controllers/dbmanager.php');
require_once('controllers/show.php');
require_once('controllers/action.php');