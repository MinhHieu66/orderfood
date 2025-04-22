<?php
require_once "Database.php";
class Author_Database extends Database
{
    public function getAllAuthors()
    {
        $sql = self::$connection->prepare("SELECT * FROM authors");
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function addAuthor($name, $image)
    {
        $sql = self::$connection->prepare("INSERT INTO authors(name, image) VALUES (?, ?)");
        $sql->bind_param("ss", $name, $image);
        return $sql->execute();
    }

    public function deleteAuthorById($id)
    {
        $sql = self::$connection->prepare("DELETE FROM authors WHERE id = ?");
        $sql->bind_param("i", $id);
        return $sql->execute();
    }
}
