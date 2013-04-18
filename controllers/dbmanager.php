<?php

/**
 * Implements the Singleton pattern to prevent multiple instantiations and connections
 * to the application database.
 *
 */
class DatabaseManager
{
    private static $instance;
    public $db;

    /**
     * Constructor function is declared private to prevent instantiation.
     *
     */
    protected function __construct() {}

    /**
     * Returns an instance of a Database_manager.
     *
     * @return object DatabaseManager object
     */
    public static function getInstance() {
        if (self::$instance == null) {
            $className = __CLASS__;
            self::$instance = new $className();
        }
        return self::$instance;
    }
    /**
     * Returns an instance of a Database_manager.
     *
     * @return the mysqli-database
     */
    public static function getDatabase() {
        $instance = self::getInstance();
        if ($instance->db == null) {
            //$instance->db = mysqli_connect("localhost", "wikiuser", "wikiuser", "wis04");
            $instance->db = mysqli_connect("localhost", "root", "root", "wiki");
        }
        return $instance->db;
    }
}
