        <form action="<?php echo View::getVariable('formAction'); ?>" method="<?php echo View::getVariable('formMethod'); ?>">
                <input name="id" type="hidden" value="<?php echo View::getVariable('article')->getID(); ?>" />
        
                <label for="title">Title</label>
                <input name="title" type="text" value="<?php echo View::getVariable('article')->getTitle(); ?>" class="span12" />
                
                <label for="content">Content</label>
                <textarea name="content" rows="10" class="span12"><?php echo View::getVariable('article')->getContent(); ?></textarea>

                <button type="submit" class="btn btn-primary">Save</button>
                <button type="button" class="btn">Cancel</button>
        </form>