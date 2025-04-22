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

    public function addUser($fullname, $email, $phone, $address, $password, $status)
    {
        $role_id = 2;
        $sql     = self::$connection->prepare("INSERT INTO users (`user_name`, `user_email`, `user_phone`, `user_address`, `user_password`, `role_id`, `status`) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $sql->bind_param("ssissii", $fullname, $email, $phone, $address, $password, $role_id, $status);
        return $sql->execute();
    }

    public function getUserById($user_id)
    {
        $sql = self::$connection->prepare("SELECT * FROM users WHERE user_id = ?");
        $sql->bind_param("i", $user_id);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items[0];
    }

    //hàm cập nhật thông tin cá nhan
    public function updateUsers($user_id, $user_name, $user_phone, $user_email, $user_address, $new_password)
    {
        $sql = self::$connection->prepare("UPDATE users SET
        user_name = ?,
        user_phone = ?,
        user_email = ?,
        user_address = ?,
        user_password = ?
        WHERE user_id = ?");
        $sql->bind_param("sisssi", $user_name, $user_phone, $user_email, $user_address, $new_password, $user_id);
        return $sql->execute();
    }
    //hàm cập nhật mật khẩu mới
    // public function reSetPassword($user_id, $new_password)
    // {
    //     $sql = self::$connection->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
    //     $sql->bind_param("si", $new_password, $user_id);
    //     return $sql->execute();
    // }

    //hàm cập nhật số điện thoại
    public function updatePhone($user_id, $user_phone)
    {
        $sql = self::$connection->prepare("UPDATE users SET user_phone = ? WHERE user_id = ?");
        $sql->bind_param("ii", $user_phone, $user_id);
        return $sql->execute();
    }

    public function getPassword($user_id)
    {
        $sql = self::$connection->prepare("SELECT user_password FROM users WHERE user_id = ?");
        $sql->bind_param("i", $user_id);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items[0];
    }

    public function deleteUserById($user_id)
    {
        $sql = self::$connection->prepare("DELETE FROM users WHERE user_id = ?");
        $sql->bind_param("i", $user_id);
        return $sql->execute();
    }

    public function upadateUser($name, $phone, $email, $address, $password, $status, $id)
    {
        $sql = self::$connection->prepare("UPDATE users SET user_name = ?, user_phone = ?, user_email = ?, user_address = ?, user_password = ?, status = ? WHERE user_id = ?");
        $sql->bind_param("sisssii", $name, $phone, $email, $address, $password, $status, $id);
        return $sql->execute();
    }

}
