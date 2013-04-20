<?php

class ShowController {
	private static $AMOUNT_OF_ITEMS_PER_PAGE = 10;

    /**
     * List all existing articles
     * 
     * @return void
     */
    public static function listPages() {

        //Get messages
        if(isset($_GET['message'])) {
            switch($_GET['message']) {
                case 'deleted':
                    $message = 'The wiki page was deleted';
                    break;

                default:
                    $message = null;
                    break;
            }

            View::setVariable('message', $message);
        }

        //Get errors
        if(isset($_GET['error'])) {
            switch($_GET['error']) {
                case 'notfound':
                    $error = 'The wiki page was not found';
                    break;

                default:
                    $error = null;
                    break;
            }

            View::setVariable('error', $error);
        }
		
		
		$page = 1;
		if(isset($_GET['page'])) {
			$page = $_GET['page'];			
		}
		
		$from = ($page-1)*self::$AMOUNT_OF_ITEMS_PER_PAGE;
		
        //List pages
        View::setVariable('articles', Article::loadArticlesForPage($from,self::$AMOUNT_OF_ITEMS_PER_PAGE));

        //Set pagination
        $paginator = Paginator::make('index.php', Article::getCountOfArticles(), self::$AMOUNT_OF_ITEMS_PER_PAGE);
        //$appendens = array('search' => 'test');
        //$paginator->appends($appendens);
        View::setVariable('paginator', $paginator);

        View::printListView();
    }

    /**
     * Show an article
     * 
     * @return void
     */
    public static function showPage() {
        $wikiPage = Article::load($_GET['id']);
    
        if($wikiPage != null) {
            View::setVariable('article', $wikiPage);
            View::printShowView();
        } else {
            View::printErrorView();
        }
	}
	
	public static function showArticleGenerationFinished() {
		View::printArticleGenerationFinished();
	}
	
	public static function showDeleteArticlesFinished()
	{
		View::printDeleteArticlesFinished();
	}

    /**
     * Show the view to create an article
     * 
     * @return void
     */
    public static function showCreateView() {
        View::setVariable('article', new Article);
        View::printCreateView();
    }

    /**
     * Show the view to edit an article
     * 
     * @return void
     */
    public static function showEditView() {
        $article = Article::load($_GET['id']);
    
        if($article != null) {
            View::setVariable('article', $article);
            View::printEditView();
        } else {
            View::printErrorView();
        }
    }

}