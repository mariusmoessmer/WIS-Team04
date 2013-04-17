<?php

//Describes a value-object for a wikipage.
//It also provides methods for persistency-management
class WikiPage {

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
			$linked_wikipage = WikiPage::findByTitle($linked_wikipage_title);		
				if(is_null($linked_wikipage))
				{
					return "!!!LINKED WIKIPAGE \'".$linked_wikipage_title."\' DOES NOT EXIST!!!";
				}
			return "<a href=\"index.php?id={$linked_wikipage->id}\">".$linked_wikipage->title."</a>"; 
		}, $content);


        $content = preg_replace('/\n/', '<br />', $content);
        return $content;
    }



    //Save the wiki page
    public function save() {
        $mysqli = DatabaseManager::getDatabase();

        if(is_null($this->id)) {
            //Insert wikipage
            
            if($stmt = $mysqli->prepare("INSERT INTO `wikipage` (`title`, `content`, `created_ipaddress`) VALUES (?,?,?)")) {           
                $stmt->bind_param("ssi", $this->title, $this->content, ip2long($_SERVER['REMOTE_ADDR']));
                $stmt->execute();
                $this->id = $mysqli->insert_id;
                $affected_rows = $stmt->affected->rows;
                $stmt->close();
                return $affected_rows > 0;
            }

            return false;
        } else {
            //Update wikipage

            if($stmt = $mysqli->prepare("UPDATE `wikipage` SET `title` = ?, `content` = ? WHERE wikipage_id = ?")) {           
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

            if($stmt = $mysqli->prepare("DELETE FROM `wikipage` WHERE wikipage_id = ?")) {
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

    
    //Load a wiki page with a specific id
    public static function load($id) {
        if(is_null($id)) {
            return null;
        }

        $result = null;

        $mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare("SELECT title, content FROM `wikipage` WHERE wikipage_id = ?")) {
            // "i" because corresponding variable $id has type integer
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->bind_result($title, $content);

            if($stmt->fetch()) {
                $result = new WikiPage($id, $title, $content);
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
        if($stmt = $mysqli->prepare("SELECT wikipage_id, content FROM `wikipage` WHERE title = ?")) {		
            // "s" because corresponding variable $id has type string
            $stmt->bind_param("s", $title);
            $stmt->execute();
            $stmt->bind_result($id, $content);
            if($stmt->fetch()) {
                $result = new WikiPage($id, $title, $content);
            }
            
            $stmt->close();
        }

        return $result;
	}


    //Load all wiki pages
    public static function loadAll() {
        $mysqli = DatabaseManager::getDatabase();

        if($stmt = $mysqli->prepare("SELECT wikipage_id, title, content FROM `wikipage`")) {
            $pages = array();
            
            $stmt->execute();
            $stmt->bind_result($id, $title, $content);
            
            while($stmt->fetch()) {
                $pages[] = new WikiPage($id, $title, $content);
            }
            
            $stmt->close();

            return $pages;
        } else {
            return null;
        }
    }
}