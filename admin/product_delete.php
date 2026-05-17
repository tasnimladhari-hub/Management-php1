<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$id  = (int)($_GET['id'] ?? 0);
$pdo = getPDO();

$stmt = $pdo->prepare('SELECT * FROM products WHERE id=?');
$stmt->execute([$id]);
$product = $stmt->fetch();

if ($product) {
    if ($product['image'] && str_starts_with($product['image'], 'uploads/') && file_exists('../'.$product['image'])) {
        unlink('../' . $product['image']);
    }
    $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
    flashSet('success', "Product \"{$product['name']}\" deleted.");
} else {
    flashSet('error', 'Product not found.');
}

header('Location: products.php');
exit;
