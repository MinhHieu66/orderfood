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

    public function addUser($fullname, $email, $phone, $address, $password, $status = 1)
    {
        $role_id = 2;
        $sql     = self::$connection->prepare("INSERT INTO users (`user_name`, `user_email`, `user_phone`, `user_address`, `user_password`, `role_id`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssissii", $fullname, $email, $phone, $address, $password, $role_id, $status);
        return $sql->execute();
    }
}
