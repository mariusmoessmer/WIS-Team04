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

        $wikipage = new WikiPage($title, $content);

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
        header('Location: index.php?title=' . $wikipage->getEncodedTitle());
        exit();
    }


    /**
     * Edit a wiki page
     * 
     * @return void
     */
    public static function edit() {
        $old = $_POST['old'];
        $title = $_POST['title'];
        $content = $_POST['content'];

        $wikipage = new WikiPage($title, $content);

        //Check if title is set
        if(!isset($title) || $title == '') {
            $GLOBALS['view']->error = 'The title must be set.';
            $GLOBALS['view']->wikipage = $wikipage;
            View::printCreateView();
            return;
        }

        //Wiki page not found
        $oldPage = WikiPage::load($old);

        if(is_null($oldPage)) {
            header('Location: index.php?error=notfound');
            exit();
        }

        //Save wikipage
        $oldPage->delete();
        $wikipage->save();

        //Redirect to the created wiki page
        header('Location: index.php?title=' . $wikipage->getEncodedTitle());
        exit();
    }


    /**
     * Delete a wiki page
     * 
     * @return void
     */
    public static function delete() {
        if(!isset($_GET['title'])) {
            View::printErrorView();
            return;
        }

        //Wiki page not found
        $wikiPage = WikiPage::loadEncoded($_GET['title']);
            
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