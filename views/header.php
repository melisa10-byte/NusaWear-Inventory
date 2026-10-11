<?php
require_once __DIR__ . '/../helpers/escape.php';
require_once __DIR__ . '/../helpers/auth.php';
$pageTitle = $pageTitle ?? 'NusaWear';
$user = current_user();
?>
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | NusaWear</title>
<link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <a class="brand" href="dashboard.php"><span class="brand-icon">N</span> NusaWear</a>
  <?php if (!empty($user)): ?>
  <nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="products.php">Produk</a>
    <a href="categories.php">Kategori</a>
    <a href="suppliers.php">Supplier</a>
    <a href="stock.php">Stok</a>
    <?php if (($user['role'] ?? '') === 'admin'): ?><a href="users.php">Pengguna</a><?php endif; ?>
  </nav>
  <div class="userbox"><?= e($user['username'] ?? '') ?> · <?= e($user['role'] ?? '') ?> <a class="logout" href="logout.php">Keluar</a></div>
  <?php endif; ?>
</header>
<main class="container">
