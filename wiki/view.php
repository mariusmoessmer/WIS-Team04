<?php

class View {

    //Prints an overview of all existing wikipages
    public static function printListView() {
        static::printHeader('Wiki - Team 04', 'All pages');

        //Load all wikipages and print a link to their site
        $pages = WikiPage::loadAll();
        
        if($pages != null) {
            foreach($pages as $page) {
                echo '<a href="index.php?title='. $page->getTitle() . '">' . $page->getTitle() . '</a><br />';
            }
        }
        
        //Print the link to create a new site
        echo '<p><a href="create.php">Create a new page</a></p>';

        static::printFooter();
    }
    
    
    //Prints a view for editing an already existing wikipage
    public static function printShowView($wikipage) {
        static::printHeader('Wiki - Team 04', $wikipage->getTitle());
        
        $content = $wikipage->getContent();
        $content = preg_replace('/---(.*?)---/', '<h3>$1</h3>', $content);
        $content = preg_replace('/\[\[(.*?)\]\]/', '<a href="index.php?title=$1">$1</a>', $content);
        echo $content;
        
        //Print the link list
        echo '<p class="links">';
        echo '<a href="edit.php?title='. $wikipage->getTitle() . '">Edit</a> - ';
        echo '<a href="delete.php?title='. $wikipage->getTitle() . '">Delete</a> - ';
        echo '<a href="index.php">All pages</a>';
        echo '</p>';
        
        static::printFooter();
    }
    

    //Prints a view for creating a new wikipage
    public static function printCreateView() {
        static::printHeader('Wiki - Team 04', 'Create a new page');
        static::printForm('save.php', 'POST', new WikiPage('',''));
        static::printFooter();
    }
    
    
    //Prints a view for editing an already existing wikipage
    public static function printEditView($wikipage) {
        static::printHeader('Wiki - Team 04', 'Edit the page');
        static::printForm('save.php', 'POST', $wikipage);
        static::printFooter();
    }
    
    
    //Prints a view with an error message
    public static function printErrorView($wikipage) {
        static::printHeader('Wiki - Team 04', 'Error');
        echo 'Wiki page not found';
        static::printFooter();
    }
    
    
    //Prints a form for creating or editing a wikipage
    private static function printForm($action, $method, $wikipage) {

		?>
        <form action="<?php echo $action; ?>" method="<?php echo $method; ?>">
        		<input name="old" type="hidden" value="<?php echo $wikipage->getTitle(); ?>" />
        
                <label for="title">Title</label></br>
                <input name="title" type="text" value="<?php echo $wikipage->getTitle(); ?>" class="text" /></br>
                
                <label for="content">Content</label></br>
                <textarea name="content" type="textarea" class="textarea"><?php echo $wikipage->getContent(); ?></textarea></br>

                <input name="submit" type="submit" value="Save" class="submit" />
        </form>
		<?php

    }

	
    //Prints the header of the website
    private static function printHeader($title, $subtitle) {

		?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
		<head>
		    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		    <title>Team 04</title>
		    
		    <link rel='stylesheet' href='../css/normalize.css' type='text/css' media='screen' />
		    <link rel='stylesheet' href='../css/style.css' type='text/css' media='screen' />
		</head>
		
		<body>
		
		    <div id="header">
		
		            <div class="blue-box">
		                    <div class="line-box">&nbsp;</div>
		            </div>
		
		            <div class="white-box">
		                    <div class="line-box title"><?php echo $title; ?><br />
		                            <span class="subtitle"><?php echo $subtitle; ?></span>
		                    </div>
		            </div>
		
		    </div>
		
		    <div id="body">
		            <div id="content">
		                    <div class="line-box">
		<?php

    }
	
	
    //Prints the footer of the website
    private static function printFooter() {

		?>
		                    </div>
		            </div>
		
		            <div class="info">
		                    Gruppenmitglieder:
		                    <a href="mailto:alex.lanz@student.uibk.ac.at">Alex Lanz</a>
		                    <a href="mailto:marius.b.moessmer@student.uibk.ac.at">Marius M&ouml;ssmer</a>
		            </div>
		    </div>
		
		</body>
		</html>
		<?php

    }

}
