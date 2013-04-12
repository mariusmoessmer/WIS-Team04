                    
                    <?php
                        if(isset($GLOBALS['view']->message)) {
                            echo '<div class="message">' . $GLOBALS['view']->message . '</div>';
                        }
                    ?>

                    <?php
                        if(isset($GLOBALS['view']->error)) {
                            echo '<div class="error">' . $GLOBALS['view']->error . '</div>';
                        }
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