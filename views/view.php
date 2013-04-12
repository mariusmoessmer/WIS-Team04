<?php

class View {

    //Prints an overview of all existing wikipages
    public static function printListView() {
        $wikipages = $GLOBALS['view']->wikipages;
        $GLOBALS['view']->subtitle = 'All pages';

        static::printHeader();
        
        echo '<ul>';

        if($wikipages != null) {
            foreach($wikipages as $wikipage) {
                echo '<li><a href="index.php?title='. $wikipage->getEncodedTitle() . '">' . $wikipage->getTitle() . '</a></li>';
            }
        }

        echo '</ul>';
        
        //Print the link to create a new site
        echo '<p class="links"><a href="create.php">Create a new page</a></p>';


        //Print message
        if(!is_null($message)) {
            echo '<p class="message">' . $message . '</p>';
        }

        static::printFooter();
    }
    
    
    //Prints a view for editing an already existing wikipage
    public static function printShowView() {
        $wikipage = $GLOBALS['view']->wikipage;
        $GLOBALS['view']->subtitle = $wikipage->getTitle();

        static::printHeader();
        
        //Print wiki text
        echo $wikipage->getReplacedContent();
        
        //Print the link list
        echo '<p class="links">';
        echo '<a href="edit.php?title='. $wikipage->getEncodedTitle() . '">Edit</a> - ';
        echo '<a href="delete.php?title='. $wikipage->getEncodedTitle() . '">Delete</a> - ';
        echo '<a href="index.php">All pages</a>';
        echo '</p>';
        
        static::printFooter();
    }
    

    //Prints a view for creating a new wikipage
    public static function printCreateView() {
        $view = $GLOBALS['view'];
        $view->subtitle = 'Create a page';
        $GLOBALS['view'] = $view;

        static::printHeader();
        static::printForm('create.php', 'POST');
        static::printFooter();
    }
    
    
    //Prints a view for editing an already existing wikipage
    public static function printEditView() {
        $GLOBALS['view']->subtitle = 'Edit the page';

        static::printHeader();
        static::printForm('edit.php', 'POST');
        static::printFooter();
    }
    
    
    //Prints a view with an error message
    public static function printErrorView() {
        $GLOBALS['view']->subtitle = 'Error';

        static::printHeader();
        echo 'Wiki page not found';
        static::printFooter();
    }
    
    
    //Prints a form for creating or editing a wikipage
    private static function printForm($action, $method) {
        $GLOBALS['view']->formMethod = $method;
        $GLOBALS['view']->formAction = $action;

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

}
