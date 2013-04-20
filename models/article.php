<?php

//Describes a value-object for a Article.
//It also provides methods for persistency-management
class Article {
	private static $TABLENAME = '`article`';
    private $id;
    private $title;
    private $content;

    public function __construct($id = -1, $title = "", $content = "") {
        $this->id = $id;
        $this->title = $title;
        $this->content = $content;
    }



    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function setContent($content) {
        $this->content = $content;
    }

    public function getID() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getContent() {
        return $this->content;
    }



    //Return the content with all HTML replacements
    public function getReplacedContent() {
        $content = $this->content;
        $content = preg_replace('/---(.*?)---/', '<h3>$1</h3>', $content);		
		
        //$content = preg_replace_callback('/\[\[(.*?)\]\]/', "<a href=\"index.php?id='$1'\">strlen('$1')</a>", $content);
		$content = preg_replace_callback('/\[\[(.*?)\]\]/', function($matches){ 
			$linked_wikipage_title = $matches[1];
			$linked_wikipage = Article::findByTitle($linked_wikipage_title);

			if(is_null($linked_wikipage)) {
				return generateLinkText($linked_wikipage);
			}
			return '<a href="index.php?id=' . $linked_wikipage->getID() . '">' . $linked_wikipage->getTitle() . '</a>'; 
		}, $content);


        $content = preg_replace('/\n/', '<br />', $content);
        return $content;
    }



    //Save the wiki page
    public function save() {
        $mysqli = DatabaseManager::getDatabase();

        if(is_null($this->id)) {
            //Insert article
            
            if($stmt = $mysqli->prepare("INSERT INTO ".self::$TABLENAME." (`title`, `content`, `created_ipaddress`) VALUES (?,?,?)")) {
            	$ipAsLong = ip2long($_SERVER['REMOTE_ADDR']);           
                $stmt->bind_param("ssi", $this->title, $this->content, $ipAsLong);
                $stmt->execute();
                $this->id = $mysqli->insert_id;
                $affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $affected_rows > 0;
            }

            return false;
        } else {
            //Update wikipage

            if($stmt = $mysqli->prepare("UPDATE ".self::$TABLENAME." SET `title` = ?, `content` = ? WHERE article_id = ?")) {           
                $stmt->bind_param("ssi", $this->title, $this->content, $this->id);
                $stmt->execute();
                $affected_rows = $stmt->affected->rows;
                $stmt->close();
                return $affected_rows > 0;
            }

            return false;
        }
    }


    //Delete the wiki page
    public function delete() {
        if(!is_null($this->id)) {
            $mysqli = DatabaseManager::getDatabase();

            if($stmt = $mysqli->prepare("DELETE FROM ".self::$TABLENAME." WHERE article_id = ?")) {
                // "i" because corresponding variable $id has type integer
                $stmt->bind_param("i", $this->id);
                $stmt->execute();
                $affected_rows = $stmt->affected_rows;
                $stmt->close();
                return $affected_rows > 0;
            }

        }
        return false;
    }
    
    // deletes all articles from database
    public static function deleteAll()
    {
		$mysqli = DatabaseManager::getDatabase();

            if($stmt = $mysqli->prepare("DELETE FROM ".self::$TABLENAME)) {
                $stmt->execute();
                $stmt->close();
            }
	}

    
    //Load a wiki page with a specific id
    public static function load($id) {
        if(is_null($id)) {
            return null;
        }

        $result = null;

        $mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare("SELECT title, content FROM ".self::$TABLENAME." WHERE article_id = ?")) {
            // "i" because corresponding variable $id has type integer
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($title, $content);

            if($stmt->fetch()) {
                $result = new Article($id, $title, $content);
            }
            
            $stmt->close();
        }

        return $result;
    }
	
	public static function findByTitle($title)
	{
		if(is_null($title)) {
            return null;
        }

        $result = null;

        $mysqli = DatabaseManager::getDatabase();
        if($stmt = $mysqli->prepare("SELECT article_id, content FROM ".self::$TABLENAME." WHERE title = ?")) {		
            // "s" because corresponding variable $id has type string
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $stmt->bind_result($id, $content);
            if($stmt->fetch()) {
                $result = new Article($id, $title, $content);
            }
            
            $stmt->close();
        }

        return $result;
	}
	
	public static function loadArticlesForPage($from = 0, $amountOfItems = 10)
	{
		$mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare('SELECT article_id, title, content FROM '.self::$TABLENAME.' ORDER BY article_id LIMIT '.$from.', '.$amountOfItems)) {
            $pages = array();
            
            $stmt->execute();
            $stmt->bind_result($id, $title, $content);
            
            while($stmt->fetch()) {
                $pages[] = new Article($id, $title, $content);
            }
            
            $stmt->close();

            return $pages;
        } else {
            return null;
        }
		
	}

    //Load all wiki pages
    public static function loadAll() {
        $mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare("SELECT article_id, title, content FROM ".self::$TABLENAME)) {
            $pages = array();
            
            $stmt->execute();
            $stmt->bind_result($id, $title, $content);
            
            while($stmt->fetch()) {
                $pages[] = new Article($id, $title, $content);
            }
            
            $stmt->close();

            return $pages;
        } else {
            return null;
        }
    }
    
    //generates an amount of random articles and saves them to database
    public static function generateRandomAndSave($amount = 10000) {
    	$i = 0;
        while($i < $amount) {
        	if(self::generateRandom()->save()) {
				$i++;
			}
        }
    }
    
    private static $RANDOM_TITLE_LENGTH = 10;
    private static $CONTENT_GAP_LENGTH = 10;
    private static $MAX_RANDOM_ARTICLE_LINKS = 3;
    //generates a random article
    private static function generateRandom() {	
		
		$title = self::randomString(self::$RANDOM_TITLE_LENGTH);
		
		srand((double)microtime()*1000000);
		$randomGeneratedLinkAmount = rand() % self::$MAX_RANDOM_ARTICLE_LINKS;
		
		
		$content_gap_text = 'trallalal ';
		$content = $content_gap_text = 'trallalal ';//self::randomString(self::$CONTENT_GAP_LENGTH);
		for ($i = 0; $i < $randomGeneratedLinkAmount; $i++) {
			//$content = $content . ' ' . self::generateLinkText(self::getRandomArticle());
			$content = $content . ' ' . $content_gap_text;
		}
		
		return new Article(null, $title, $content);
		
    }
    
    private static function generateLinkText($article)
    {
		if(is_null($article))
		{
			return '';
		}
		
		return '[[' . $article->title . ']]';	
	}
    
    private static function getRandomArticle()
    {		
		$result = null;

        $mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare('SELECT article_id, title, content, article_id* RAND( ) AS random_no FROM '.self::$TABLENAME.'ORDER BY random_no LIMIT 1')) {
            $stmt->execute();
            $stmt->bind_result($article_id, $title, $content, $random_no);

            if($stmt->fetch()) {
                $result = new Article($article_id, $title, $content);
            }
            
            $stmt->close();
        }

        return $result;
	}
    
    // define allowed chars for generating random strings
    public static $randomChars = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    private static function randomString($length = 6) {		
    	$generatedString = '';
    	// init rand
		srand((double)microtime()*1000000);
		for ($i = 0; $i < $length; $i++) {
			// get random number: 0 <= number < strlen($randomChars)
			$num = rand() % strlen(self::$randomChars);
			// extract random char
			$tmp = substr(self::$randomChars, $num, 1);
			$generatedString = $generatedString . $tmp;
		}
  	
		return $generatedString;
    }
	
	
	public static function getCountOfArticles()
	{
        $result = null;

        $mysqli = DatabaseManager::getDatabase();
        if($stmt = $mysqli->prepare("SELECT COUNT(*) FROM ".self::$TABLENAME)) {		
            $stmt->execute();
            $stmt->bind_result($result);
            if(! $stmt->fetch()) {
                $result = null;
            }
            
            $stmt->close();
        }

        return $result;
	}

}
