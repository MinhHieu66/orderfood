<?php
require_once "Database.php";

class Product_Database extends Database
{

    public function getAllProducts()
    {
        $sql = self::$connection->prepare("SELECT * FROM product");
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function getAllProductsByCategoryId($category_id)
    {
        $sql = self::$connection->prepare("SELECT * FROM product WHERE category_id LIKE ?");
        $sql->bind_param("i", $category_id);
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function addProduct($title, $price, $image = "nam-dui-ga-chay-toi.jpeg", $description, $category_id, $status = 1)
    {
        $sql = self::$connection->prepare("INSERT INTO product(title, price, image, description, category_id, status) VALUES (?, ?, ?, ?, ?, ?)");
        $sql->bind_param("sissii", $title, $price, $image, $description, $category_id, $status);
        return $sql->execute();
    }

    public function upadteProduct($title, $price, $image = "nam-dui-ga-chay-toi.jpeg", $description, $category_id, $status = 1, $id)
    {
        $sql = self::$connection->prepare("UPDATE product SET title = ?, price = ?, image = ?, description = ?, category_id = ?, status = ? WHERE id = ?");
        $sql->bind_param("sissiii", $title, $price, $image, $description, $category_id, $status, $id);
        return $sql->execute();
    }

    public function deleteProduct($id)
    {
        $sql = self::$connection->prepare("DELETE FROM product WHERE id = ?");
        $sql->bind_param("i", $id);
        return $sql->execute();
    }

    public function getCategoriesPagination($page, $perPage, $category_id = "")
    {
        $startRecord = ($page - 1) * $perPage;
        if ($category_id != "") {
            $sql = self::$connection->prepare("SELECT * FROM product WHERE category_id = ? LIMIT ?, ?");
            $sql->bind_param("iii", $category_id, $startRecord, $perPage);
        } else {
            $sql = self::$connection->prepare("SELECT * FROM product LIMIT ?, ?");
            $sql->bind_param("ii", $startRecord, $perPage);
        }
        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_all(MYSQLI_ASSOC);
        return $items;
    }

    public function paginationTotal($category_id = "")
    {
        if ($category_id != "") {
            $sql = self::$connection->prepare("SELECT count(*) as total FROM product WHERE category_id = ?");
            $sql->bind_param("i", $category_id);
        } else {
            $sql = self::$connection->prepare("SELECT count(*) as total FROM product");
        }

        $sql->execute();
        $items = [];
        $items = $sql->get_result()->fetch_assoc();
        return $items["total"];
    }

    public function nagivationBar($url, $page, $perPage, $total, $category_id = "")
    {
        $links   = "";
        $maxPage = ceil($total / $perPage);
        if ($category_id == "") {
            for ($i = 1; $i <= $maxPage; $i++) {

                if ($page == $i) {
                    $links .= "<li class='page-nav-item active'><a href='$url&page=$i'>$i</a></li>";
                } else {
                    $links .= "<li class='page-nav-item'><a href='$url&page=$i'>$i</a></li>";
                }
            }
        } else {
            for ($i = 1; $i <= $maxPage; $i++) {

                if ($page == $i) {
                    $links .= "<li class='page-nav-item active'><a href='$url&page=$i&category_id=$category_id'>$i</a></li>";
                } else {
                    $links .= "<li class='page-nav-item'><a href='$url&page=$i&category_id=$category_id'>$i</a></li>";
                }
            }
        }

        return $links;
    }
}
