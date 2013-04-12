<?php

//Describes a value-object for a wikipage.
//It also provides methods for persistency-management
class WikiPage {

    private $title;
    private $content;
    private static $sessionVariable = 'wiki';



    public function __construct($title, $content) {
        $this->title = $title;
        $this->content = $content;
    }



    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setContent($title) {
        $this->content = $content;
    }

	public function getTitle() {
	    return $this->title;
	}

    public function getContent() {
        return $this->content;
    }


    //Return the title in a encoded format for the url
    public function getEncodedTitle() {
        return rawurlencode($this->title);
    }

    //Return the content with all HTML replacements
    public function getReplacedContent() {
        $content = $this->content;
        $content = preg_replace('/---(.*?)---/', '<h3>$1</h3>', $content);
        $content = preg_replace('/\[\[(.*?)\]\]/', '<a href="index.php?title=$1">$1</a>', $content);
        $content = preg_replace('/\n/', '<br />', $content);
        return $content;
    }



    //Save the wiki page
    public function save() {
        if(!is_null($this->title)) {
            $_SESSION[self::$sessionVariable][$this->title] = $this->content;
            return true;
        }

        return false;
    }


    //Delete the wiki page
    public function delete() {
        if(!is_null($this->title)) {
        	unset($_SESSION[self::$sessionVariable][$this->title]);
            return true;
        }

        return false;
    }

    
    
    //Load a wiki page with a specific title
    public static function load($title) {
        if(is_null($title)) {
            return null;
        }

        if(isset($_SESSION[self::$sessionVariable][$title])) {
        	$content = $_SESSION[self::$sessionVariable][$title];
        	return new WikiPage($title, $content);
        }

        return null;
    }


    //Load a wiki page with a specific encoded title
    public static function loadEncoded($encodedTitle) {
        return static::load(rawurldecode($encodedTitle));
    }


    //Load all wiki pages
    public static function loadAll() {
        if(isset($_SESSION[self::$sessionVariable])) {
        	$pages = array();
        	
        	foreach($_SESSION[self::$sessionVariable] as $key => $value) {
        		$pages[] = new WikiPage($key, $value);
        	}
        	
        	return $pages;
        } else {
	        return null;
        }
    }
}
