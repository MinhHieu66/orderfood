<?php
session_start();
header('Content-Type: application/json');

$id     = $_POST['id'] ?? null;
$action = $_POST['action'] ?? null;

if (! $id || ! isset($_SESSION['cart'][$id])) {
    echo json_encode(['success' => false]);
    exit;
}

if ($action == 'increase') {
    $_SESSION['cart'][$id]['quantity']++;
} elseif ($action == 'decrease') {
    $_SESSION['cart'][$id]['quantity'] = max(1, $_SESSION['cart'][$id]['quantity'] - 1);
}

$quantity = $_SESSION['cart'][$id]['quantity'];
$total    = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['quantity'] * $item['price'];
}

echo json_encode([
    'success'  => true,
    'quantity' => $quantity,
    'total'    => $total,
]);
