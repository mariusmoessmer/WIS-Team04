<?php

require_once('../autoloader.php');

//Check request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ActionController::edit();
} else {
    ShowController::showEditView();
}