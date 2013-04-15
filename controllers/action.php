<?php

class ActionController {

    /**
     * Create a wiki page
     * 
     * @return void
     */
    public static function create() {
        $title = $_POST['title'];
        $content = $_POST['content'];

        $wikipage = new WikiPage(null, $title, $content);

        //Check if title is set
        if(!isset($title) || $title == '') {
            $GLOBALS['view']->error = 'The title must be set.';
            $GLOBALS['view']->wikipage = $wikipage;
            View::printCreateView();
            return;
        }

        //Save wikipage
        $wikipage->save();

        //Redirect to the created wiki page
        header('Location: index.php?id=' . $wikipage->getID());
        exit();
    }


    /**
     * Edit a wiki page
     * 
     * @return void
     */
    public static function edit() {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        //Check if title is set
        if(!isset($title) || $title == '') {
            $GLOBALS['view']->error = 'The title must be set.';
            $GLOBALS['view']->wikipage = $wikipage;
            View::printEditView();
            return;
        }

        //Load wiki page
        $wikipage = WikiPage::load($id);

        if(is_null($wikipage)) {
            header('Location: index.php?error=notfound');
            exit();
        }

        //Save wikipage
        $wikipage->setTitle($title);
        $wikipage->setContent($content);
        $wikipage->save();

        //Redirect to the created wiki page
        header('Location: index.php?id=' . $wikipage->getID());
        exit();
    }


    /**
     * Delete a wiki page
     * 
     * @return void
     */
    public static function delete() {
        if(!isset($_GET['id'])) {
            View::printErrorView();
            return;
        }

        //Wiki page not found
        $wikiPage = WikiPage::load($_GET['id']);
            
        if(is_null($wikiPage)) {
            header('Location: index.php?error=notfound');
            exit();
        }

        $wikiPage->delete();

        //Redirect to the wiki start page
        header('Location: index.php?message=deleted');
        exit();
    }

}