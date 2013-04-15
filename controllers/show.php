<?php

class ShowController {

    /**
     * List all existing wiki pages
     * 
     * @return void
     */
    public static function listPages() {

        //Get messages
        if(isset($_GET['message'])) {
            switch($_GET['message']) {
                case 'deleted':
                    $GLOBALS['view']->message = 'The wiki page was deleted';
                    break;

                default:
                    break;
            }
        }

        //Get errors
        if(isset($_GET['error'])) {
            switch($_GET['error']) {
                case 'notfound':
                    $GLOBALS['view']->error = 'The wiki page was not found';
                    break;

                default:
                    break;
            }
        }

        //List pages
        $GLOBALS['view']->wikipages = WikiPage::loadAll();
        View::printListView();
    }

    /**
     * Show a wiki page
     * 
     * @return void
     */
    public static function showPage() {
        $wikiPage = WikiPage::load($_GET['id']);
    
        if($wikiPage != null) {
            $GLOBALS['view']->wikipage = $wikiPage;
            View::printShowView();
        } else {
            View::printErrorView();
        }
    }

    /**
     * Show the view to create a wiki page
     * 
     * @return void
     */
    public static function showCreateView() {
        $GLOBALS['view']->wikipage = new WikiPage;
        View::printCreateView();
    }

    /**
     * Show the view to edit a wiki page
     * 
     * @return void
     */
    public static function showEditView() {
        $wikiPage = WikiPage::load($_GET['id']);
    
        if($wikiPage != null) {
            $GLOBALS['view']->wikipage = $wikiPage;
            View::printEditView();
        } else {
            View::printErrorView();
        }
    }

}