<?php
require_once "Database.php";
class Item_Database extends Database
{
    public function getAllItems()
    {
        $sql = self::$connection->prepare("SELECT  items.*, authors.name  AS author_name, categories.name AS category_name FROM items
        JOIN authors ON items.author = authors.id
        JOIN categories ON categories.id = items.category ORDER BY created_at DESC");
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function quantityItemsByAuthor($author_id)
    {
        $sql = self::$connection->prepare("SELECT count(id) as total FROM items WHERE author = ?");
        $sql->bind_param("i", $author_id);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_assoc();
        return $items["total"];
    }

    public function search($keyword)
    {
        $keyword1 = "%$keyword%";
        $sql      = self::$connection->prepare("SELECT  items.*, authors.name  AS author_name, categories.name AS category_name FROM items
        JOIN authors ON items.author = authors.id
        JOIN categories ON categories.id = items.category WHERE excerpt LIKE ?");
        $sql->bind_param("s", $keyword1);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function search_pagination($keyword, $page, $perPage)
    {
        $keyword1    = "%$keyword%";
        $startRecord = ($page - 1) * $perPage;
        $sql         = self::$connection->prepare("SELECT  items.*, authors.name  AS author_name, categories.name AS category_name FROM items
        JOIN authors ON items.author = authors.id
        JOIN categories ON categories.id = items.category WHERE excerpt LIKE ? LIMIT ?, ?");
        $sql->bind_param("sii", $keyword1, $startRecord, $perPage);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function search_pagination_total($keyword)
    {
        $keyword1 = "%$keyword%";
        $sql      = self::$connection->prepare("SELECT count(*) as total FROM items
        JOIN authors ON items.author = authors.id
        JOIN categories ON categories.id = items.category WHERE excerpt LIKE ?");
        $sql->bind_param("s", $keyword1);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_assoc();
        return $items["total"];
    }

    public function nagivationBar($url, $page, $perPage, $total)
    {
        $links   = "";
        $maxPage = ceil($total / $perPage);
        for ($i = 1; $i <= $maxPage; $i++) {
            if ($page == $i) {
                $links .= "<li class='active'><a href='$url&page=$i'>$i</a></li>";
            } else {
                $links .= "<li><a href='$url&page=$i'>$i</a></li>";
            }
        }
        return $links;
    }
}
