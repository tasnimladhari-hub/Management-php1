<?php

require_once '../includes/config.php';

require_once '../includes/auth.php';

requireAdmin();

$id  = (int)($_GET['id'] ?? 0);
$pdo = getPDO();

$brand = $pdo->prepare('SELECT * FROM brands WHERE id=?');
$brand->execute([$id]);
$brand = $brand->fetch();

if ($brand) {

    if ($brand['image'] && str_starts_with($brand['image'], 'uploads/') && file_exists('../' . $brand['image'])) {
        unlink('../' . $brand['image']);
    }
    $pdo->prepare('DELETE FROM brands WHERE id=?')->execute([$id]);
    flashSet('success', "Brand \"{$brand['name']}\" deleted.");
} else {
    flashSet('error', 'Brand not found.');
}

header('Location: brands.php');
exit;
