<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../helpers/auth.php'; require_login();
$pageTitle='Dashboard';
$totalProducts=(int)$pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
$totalStock=(int)$pdo->query('SELECT COALESCE(SUM(stock),0) FROM products')->fetchColumn();
$inventoryValue=(float)$pdo->query('SELECT COALESCE(SUM(stock*price),0) FROM products')->fetchColumn();
$lowStock=(int)$pdo->query('SELECT COUNT(*) FROM products WHERE stock <= minimum_stock')->fetchColumn();
$totalIn=(int)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_movements WHERE type='IN'")->fetchColumn();
$totalOut=(int)$pdo->query("SELECT COALESCE(SUM(quantity),0) FROM stock_movements WHERE type='OUT'")->fetchColumn();
$low=$pdo->query('SELECT p.sku,p.name,p.stock,p.minimum_stock,c.name category FROM products p JOIN categories c ON c.id=p.category_id WHERE p.stock<=p.minimum_stock ORDER BY p.stock ASC LIMIT 8')->fetchAll();
$recent=$pdo->query('SELECT sm.*,p.name product_name,u.username FROM stock_movements sm JOIN products p ON p.id=sm.product_id JOIN users u ON u.id=sm.user_id ORDER BY sm.created_at DESC LIMIT 8')->fetchAll();
require __DIR__ . '/../views/header.php';
?>
<div class="page-head"><div><h1>Dashboard</h1><p class="sub">Ringkasan persediaan toko NusaWear.</p></div><a class="btn" href="stock.php">+ Catat pergerakan stok</a></div>
<div class="grid">
 <div class="card"><div class="stat-label">Total Produk</div><div class="stat-number"><?= $totalProducts ?></div><div class="stat-foot">SKU terdaftar</div></div>
 <div class="card"><div class="stat-label">Total Stok</div><div class="stat-number"><?= number_format($totalStock) ?></div><div class="stat-foot">Unit tersedia</div></div>
 <div class="card"><div class="stat-label">Nilai Inventory</div><div class="stat-number" style="font-size:24px">Rp <?= number_format($inventoryValue,0,',','.') ?></div><div class="stat-foot">Total stok × harga jual</div></div>
 <div class="card"><div class="stat-label">Stok Rendah</div><div class="stat-number"><?= $lowStock ?></div><div class="stat-foot">Stok ≤ batas minimum</div></div>
 <div class="card"><div class="stat-label">Barang Masuk</div><div class="stat-number"><?= number_format($totalIn) ?></div><div class="stat-foot">Total unit masuk</div></div>
 <div class="card"><div class="stat-label">Barang Keluar</div><div class="stat-number"><?= number_format($totalOut) ?></div><div class="stat-foot">Total unit keluar</div></div>
</div>
<div class="grid" style="margin-top:18px;grid-template-columns:repeat(auto-fit,minmax(320px,1fr))">
 <section class="panel"><h2>Perlu Restock</h2><div class="table-wrap"><table><thead><tr><th>Produk</th><th>Stok</th><th>Min.</th></tr></thead><tbody>
 <?php foreach($low as $p): ?><tr><td><?= e($p['name']) ?><div class="mini"><?= e($p['sku']) ?></div></td><td><span class="badge low"><?= (int)$p['stock'] ?></span></td><td><?= (int)$p['minimum_stock'] ?></td></tr><?php endforeach; ?>
 <?php if(!$low): ?><tr><td colspan="3" class="empty">Tidak ada stok rendah.</td></tr><?php endif; ?>
 </tbody></table></div></section>
 <section class="panel"><h2>Aktivitas Stok Terbaru</h2><div class="table-wrap"><table><thead><tr><th>Produk</th><th>Jenis</th><th>Qty</th></tr></thead><tbody>
 <?php foreach($recent as $r): ?><tr><td><?= e($r['product_name']) ?><div class="mini"><?= e($r['username']) ?> · <?= e($r['created_at']) ?></div></td><td><?= $r['type']==='IN'?'Masuk':'Keluar' ?></td><td><?= (int)$r['quantity'] ?></td></tr><?php endforeach; ?>
 <?php if(!$recent): ?><tr><td colspan="3" class="empty">Belum ada transaksi stok.</td></tr><?php endif; ?>
 </tbody></table></div></section>
</div>
<?php require __DIR__ . '/../views/footer.php'; ?>
