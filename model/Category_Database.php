<?php
require_once "Database.php";

class Category_Database extends Database
{
    public function getAllCategories()
    {
        $sql = self::$connection->prepare("SELECT * FROM category");
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function getCategoryById($id)
    {
        $sql = self::$connection->prepare("SELECT * FROM category WHERE id = ?");
        $sql->bind_param("i", $id);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items[0];
    }
}
