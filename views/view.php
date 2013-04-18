<?php

class View {

    private static $variables = array();

    /**
     * Sets a variable to the view class
     * @param  string $name  The name of the variable
     * @param  mixed  $value The value of the variable
     * @return void
     */
    public static function setVariable($name, $value) {
        static::$variables[$name] = $value;
    }


    /**
     * Returns a variable value of the view class
     * @param  string $name The name of the variable
     * @return mixed        The value of the variable
     */
    public static function getVariable($name) {
        return static::$variables[$name];
    }


    /**
     * Prints an overview of all existing articles
     * @return void
     */
    public static function printListView() {
        static::setTitle('Articles', 'List');
        static::printHeader();
        
        echo '<div id="list">';

        $articles = static::getVariable('articles');;
        if($articles != null) {
            foreach($articles as $article) {
                echo '<a href="index.php?id='. $article->getID() . '" class="btn span12">' . $article->getTitle() . '</a>';
            }
        }

        echo '</div>';

        //Print message
        //if(isset($GLOBALS['view']->message) && !is_null($GLOBALS['view']->message)) {
        //    echo '<p class="message">' . $GLOBALS['view']->message . '</p>';
        //}

        static::printFooter();
    }
    
    
    /**
     * Prints a view for show an article
     * @return void
     */
    public static function printShowView() {
        $article = static::getVariable('article');;

        $linkEdit = array(
            'text'  => 'Edit article',
            'link'  => 'edit.php?id='. $article->getID()
        );

        $linkDelete = array(
            'text'  => 'Delete article',
            'link'  => 'delete.php?id='. $article->getID()
        );

        $navigation = array($linkEdit, $linkDelete);

        static::setTitle($article->getTitle(), 'Show');
        static::setVariable('navigation', $navigation);
        static::printHeader();
        
        //Print wiki text
        echo $article->getReplacedContent();
        
        static::printFooter();
    }
    

    /**
     * Prints a view for creating a new article
     * @return void
     */
    public static function printCreateView() {
        static::setTitle('Article', 'Create');
        static::printHeader();
        static::printForm('create.php', 'POST');
        static::printFooter();
    }
    
    
    /**
     * Prints a view for editing an already existing article
     * @return [type] [description]
     */
    public static function printEditView() {
        $article = static::getVariable('article');
        static::setTitle($article->getTitle(), 'Edit');
        static::printHeader();
        static::printForm('edit.php', 'POST');
        static::printFooter();
    }
    
    
    /**
     * Prints a view with an error message
     * @return void
     */
    public static function printErrorView() {
        static::setTitle('Article', 'Error');
        static::printHeader();
        echo '<p class="red">Artile not found</p>';
        static::printFooter();
    }
    
    
    /**
     * Prints a form for creating or editing an article
     * @param  string $action The link to the action script
     * @param  string $method The method, that will be used for the form
     * @return void
     */
    private static function printForm($action, $method) {
        static::setVariable('formAction', $action);
        static::setVariable('formMethod', $method);
        include('forms/wikipage.php');
    }

	
    //Prints the header of the website
    private static function printHeader() {
        include('layout/header.php');
    }
	
	
    //Prints the footer of the website
    private static function printFooter() {
        include('layout/footer.php');
    }


    /**
     * Sets the title and subtitle to the view class
     * @param string $title
     * @param string $subtitle
     */
    private static function setTitle($title, $subtitle) {
        static::setVariable('title', $title);
        static::setVariable('subtitle', $subtitle);
    }

}
