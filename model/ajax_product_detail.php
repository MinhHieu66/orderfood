<?php
require_once "Product_Database.php";
$productDb = new Product_Database();

if (isset($_GET['id'])) {
    $id      = intval($_GET['id']);
    $product = $productDb->getProductById($id);

    if ($product) {
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} else {
    echo json_encode(['error' => 'No ID provided']);
}
