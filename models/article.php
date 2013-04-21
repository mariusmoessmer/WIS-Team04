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
	
	private static $LINKED_ARTICLE_PATTERN ='/\[\[(.*?)\]\]/'; 
	
    //Return the content with all HTML replacements
    public function getReplacedContent() {
        $content = $this->content;
        $content = preg_replace('/---(.*?)---/', '<h3>$1</h3>', $content);		
		
        //$content = preg_replace_callback('/\[\[(.*?)\]\]/', "<a href=\"index.php?id='$1'\">strlen('$1')</a>", $content);
		$content = preg_replace_callback(self::$LINKED_ARTICLE_PATTERN, function($matches){ 
			$linked_wikipage_title = $matches[1];
			$linked_wikipage = Article::findByTitle($linked_wikipage_title);

			if(is_null($linked_wikipage)) {
				return ' -- invalid link -- ';
			}
			return '<a href="index.php?id=' . $linked_wikipage->getID() . '">' . $linked_wikipage->getTitle() . '</a>'; 
		}, $content);


        $content = preg_replace('/\n/', '<br />', $content);
        return $content;
    }
	
	public function isNew()
	{
		return is_null($this->id);
	}


    //Save the wiki page
    public function save() {
        $mysqli = DatabaseManager::getDatabase();

        if($this->isNew()) {
            //Insert article
            
            if($stmt = $mysqli->prepare("INSERT INTO ".self::$TABLENAME." (`title`, `content`) VALUES (?,?)")) {
                $stmt->bind_param("ss", $this->title, $this->content);
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
	
	public function findLinkingArticles()
	{
		$referencingArticles = array();
		
		if($this->isNew())
		{
			return $referencingArticles;
		}
		
		foreach (self::loadAll() as $article) {
			if($article->id != $this->id)
			{
				foreach($article->getLinkedArticlesFromContent() as $linkedArticle)
				{
					if($linkedArticle->id == $this->id){
						$referencingArticles[] = $article;
						break; 
					}
				}
			}
		}
		
		return $referencingArticles;
	}
	
	public function getLinkedArticlesFromContent()
	{
		$linkedArticles = array();
		preg_match_all(self::$LINKED_ARTICLE_PATTERN, $this->content, $matches);
		
		foreach($matches[1] as $match){
			$article = Article::findByTitle($match);
			if(!is_null($article))
			{
				$linkedArticles[] = $article;
			}
		}
		
		return $linkedArticles;
	}
	
	public static function loadArticlesForPage($from = 0, $amountOfItems = 10, $searchText = null)
	{
		$mysqli = DatabaseManager::getDatabase();
		
		$query = 'SELECT article_id, title, content FROM '.self::$TABLENAME;
		
		if(!is_null($searchText)){
			$query .= ' WHERE title LIKE \'%'.$searchText.'%\'';
		}
		
		$query.=' ORDER BY article_id LIMIT '.$from.', '.$amountOfItems;

        if($stmt = $mysqli->prepare($query)) {
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
    
	private static $MAX_INSERT_INTOS_PER_CALL = 100;
    //generates an amount of random articles and saves them to database
    public static function generateRandomAndSave($amount = 10000) {		
		$mysqli = DatabaseManager::getDatabase();
		
		$created_articles = 0;
		
		$i = 0;
		while($created_articles < $amount)
		{
			$sqlQuery = 'INSERT INTO '.self::$TABLENAME.' (`title`, `content`) VALUES';
			for($j = 0; $j < self::$MAX_INSERT_INTOS_PER_CALL && $created_articles < $amount; $j++)
			{
				$title = ''.($i++);
				$content = self::generateRandomContentText($i);
  				$sqlQuery .= ('(\''.$title.'\', \''.$content.'\'),');
			}
			// replace last comma with semicolon
			$sqlQuery = substr_replace($sqlQuery ,';',-1);
			if($mysqli->query($sqlQuery))
			{
					$created_articles = ($created_articles+$mysqli->affected_rows);
			}else{
				// articles with title "$i" already exist... increment i by $amount (performance-speedup)
				$i = $i + $amount;
			}
		}
		
		echo 'created: '.$created_articles;
	}
	
    private static $MAX_RANDOM_ARTICLE_LINKS = 3;
    //generates a random article
    private static function generateRandomContentText() {	
		
		$content = $content_gap_text = 'lorem ipsum ';
		$randomArticles = self::getRandomArticles();
		
		if(is_null($randomArticles) || count($randomArticles) == 0)
		{
			return $content;
		}
		
		$randomGeneratedLinkAmount = rand() % self::$MAX_RANDOM_ARTICLE_LINKS;
		for ($i = 0; $i < $randomGeneratedLinkAmount; $i++) {
			$randomArticle = $randomArticles[array_rand($randomArticles)];
			$content = $content . ' ' . self::generateLinkText($randomArticle);
			$content = $content . ' ' . $content_gap_text;
		}
		
		// fill up words
		for ($i = 0; $i < 250; $i++) {
			$content .= $content_gap_text;
		}
		
		
		return $content;
    }
    
    private static function generateLinkText($article)
    {
		if(is_null($article))
		{
			return '';
		}
		
		return '[[' . $article->title . ']]';	
	}
    
    private static function getRandomArticles($amount = 100)
    {		
		$articles = array();

        $mysqli = DatabaseManager::getDatabase();
		
        if($stmt = $mysqli->prepare('SELECT article_id, title, content FROM '.self::$TABLENAME.' LIMIT '.$amount)) {
            $stmt->execute();
            $stmt->bind_result($article_id, $title, $content);


            while($stmt->fetch()) {
                $articles[] = new Article($article_id, $title, $content);
            }
            
            $stmt->close();
        }
        return $articles;
	}
    
    // define allowed chars for generating random strings
    public static $randomChars = " abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    private static function randomString($length = 6) {		
		 $randomString = '';
    	for ($i = 0; $i < $length; $i++) {
        	$randomString .= self::$randomChars[rand(0, strlen(self::$randomChars) - 1)];
    	}
    	return $randomString;
  	
    }
	
	
	public static function getCountOfArticles($searchText = null)
	{
        $result = null;

        $mysqli = DatabaseManager::getDatabase();
		
		$query = 'SELECT COUNT(*) FROM '.self::$TABLENAME;
		
		if(!is_null($searchText))
		{
			$query.=' WHERE title LIKE \'%'.$searchText.'%\'';
		}
		
        if($stmt = $mysqli->prepare($query)) {		
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
