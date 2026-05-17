<?php

if (!defined('PAGE_TITLE')) define('PAGE_TITLE', 'HERFA');
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= htmlspecialchars(PAGE_TITLE) ?> — HERFA</title>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">

    <?php

    ?>
    <link rel="stylesheet" href="<?= $cssRoot ?? '' ?>style.css">

    <?php

    if (isset($extraCss)) echo $extraCss;
    ?>
</head>
<body>
