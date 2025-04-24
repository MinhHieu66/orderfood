<?php
require_once "Database.php";

class Order_Database extends Database
{
    public function save($user_id, $email, $phone, $note)
    {
        $sql = self::$connection->prepare("INSERT INTO `order` (user_id, email, phone, note) VALUES (?, ?, ?, ?)");
        $sql->bind_param("isss", $user_id, $email, $phone, $note);

        if ($sql->execute()) {
            return self::$connection->insert_id;
        }

        return false;
    }

}
