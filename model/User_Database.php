<?php
require_once "Database.php";

class User_Database extends Database
{
    public function login($phone, $password)
    {
        $sql = self::$connection->prepare("SELECT * FROM users WHERE `user_phone` LIKE ? AND `user_password` LIKE ?");
        $sql->bind_param("is", $phone, $password);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return isset($items[0]) ? $items[0] : null;
    }

    public function getAllUsers()
    {
        $sql = self::$connection->prepare("SELECT * FROM users");
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }
}
