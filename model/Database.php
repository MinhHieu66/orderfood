<?php
require_once "config.php";
class Database
{
    public static $connection;

    public function __construct()
    {
        if (!self::$connection) {
            self::$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            self::$connection->set_charset(DB_CHARSET);
        }
    }
}
