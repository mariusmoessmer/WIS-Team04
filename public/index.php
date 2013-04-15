<?php

require_once('../autoloader.php');


if(isset($_GET['id'])) {
    ShowController::showPage();
} else {
    ShowController::listPages();
}