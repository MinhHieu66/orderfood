<?php
require_once "Database.php";

class Order_Detail_Database extends Database
{
    public function save($order_id, $product_id, $price, $num)
    {
        $sql = self::$connection->prepare("INSERT INTO order_detail(order_id, product_id, price, num) VALUES (?, ?, ?, ?)");
        $sql->bind_param("iiii", $order_id, $product_id, $price, $num);
        return $sql->execute();
    }

}
