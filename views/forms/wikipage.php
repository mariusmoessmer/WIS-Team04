        <form action="<?php echo $GLOBALS['view']->formAction; ?>" method="<?php echo $GLOBALS['view']->formMethod; ?>">
                <input name="old" type="hidden" value="<?php echo $GLOBALS['view']->wikipage->getTitle(); ?>" />
        
                <label for="title">Title</label></br>
                <input name="title" type="text" value="<?php echo $GLOBALS['view']->wikipage->getTitle(); ?>" class="text" /></br>
                
                <label for="content">Content</label></br>
                <textarea name="content" type="textarea" class="textarea"><?php echo $GLOBALS['view']->wikipage->getContent(); ?></textarea></br>

                <input name="submit" type="submit" value="Save" class="submit" />
        </form>