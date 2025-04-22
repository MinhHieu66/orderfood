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

    public function addCategory($name)
    {
        $sql = self::$connection->prepare("INSERT INTO category(name) VALUES (?)");
        $sql->bind_param("s", $name);
        return $sql->execute();
    }

    public function deleteCategory($id)
    {
        $sql = self::$connection->prepare("DELETE FROM category WHERE id = ?");
        $sql->bind_param("i", $id);
        return $sql->execute();
    }

    public function upadteCategory($name, $id)
    {
        $sql = self::$connection->prepare("UPDATE category SET name = ? WHERE id = ?");
        $sql->bind_param("si", $name, $id);
        return $sql->execute();
    }
}
