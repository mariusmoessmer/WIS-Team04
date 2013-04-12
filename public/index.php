<?php

require_once('../autoloader.php');


if(isset($_GET['title'])) {
    ShowController::showPage();
} else {
    ShowController::listPages();
}