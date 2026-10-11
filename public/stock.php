<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../helpers/auth.php'; require_once __DIR__ . '/../helpers/escape.php'; require_once __DIR__ . '/../helpers/validation.php'; require_login();
$pageTitle='Transaksi Stok'; $error=''; $success='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 $productId=positive_int($_POST['product_id']??null);$quantity=positive_int($_POST['quantity']??null);$type=(string)($_POST['type']??'');$note=trim((string)($_POST['note']??''));
 if(!$productId||!$quantity||!in_array($type,['IN','OUT'],true))$error='Produk, jenis, dan jumlah harus valid. Jumlah minimal 1.';
 else{
  try{$pdo->beginTransaction();
   $lock=$pdo->prepare('SELECT stock FROM products WHERE id=:id FOR UPDATE');$lock->execute(['id'=>$productId]);$product=$lock->fetch();
   if(!$product)throw new RuntimeException('Produk tidak ditemukan.');
   $current=(int)$product['stock'];
   if($type==='OUT'&&$quantity>$current)throw new RuntimeException('Stok tidak cukup. Stok tersedia: '.$current.'.');
   $newStock=$type==='IN'?$current+$quantity:$current-$quantity;
   $upd=$pdo->prepare('UPDATE products SET stock=:stock WHERE id=:id');$upd->execute(['stock'=>$newStock,'id'=>$productId]);
   $ins=$pdo->prepare('INSERT INTO stock_movements(product_id,user_id,type,quantity,note) VALUES(:p,:u,:t,:q,:n)');
   $ins->execute(['p'=>$productId,'u'=>current_user()['id'],'t'=>$type,'q'=>$quantity,'n'=>$note!==''?$note:null]);
   $pdo->commit();$success='Transaksi berhasil. Stok terbaru: '.$newStock.'.';
  }catch(Throwable $ex){if($pdo->inTransaction())$pdo->rollBack();$error=$ex instanceof RuntimeException?$ex->getMessage():'Transaksi gagal disimpan; perubahan stok dibatalkan.';}
 }
}
$products=$pdo->query('SELECT id,sku,name,stock FROM products ORDER BY name')->fetchAll();
$history=$pdo->query('SELECT sm.*,p.name product_name,p.sku,u.username FROM stock_movements sm JOIN products p ON p.id=sm.product_id JOIN users u ON u.id=sm.user_id ORDER BY sm.created_at DESC LIMIT 100')->fetchAll();
require __DIR__ . '/../views/header.php';
?>
<div class="page-head"><div><h1>Barang Masuk & Keluar</h1><p class="sub">Stok dan histori disimpan bersama dalam satu database transaction.</p></div></div>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="alert"><?= e($success) ?></div><?php endif; ?>
<section class="panel"><h2>Catat Transaksi</h2><form method="post" class="form-grid">
<div class="field"><label>Produk</label><select name="product_id" required><option value="">Pilih produk</option><?php foreach($products as $p): ?><option value="<?= (int)$p['id'] ?>"><?= e($p['sku'].' — '.$p['name'].' (stok '.$p['stock'].')') ?></option><?php endforeach; ?></select></div>
<div class="field"><label>Jenis transaksi</label><select name="type" required><option value="IN">Barang masuk</option><option value="OUT">Barang keluar</option></select></div>
<div class="field"><label>Jumlah</label><input type="number" name="quantity" min="1" required></div><div class="field"><label>Catatan</label><input name="note" maxlength="255" placeholder="Contoh: restock pemasok"></div>
<div class="field" style="justify-content:end"><button class="btn">Simpan Transaksi</button></div></form></section>
<section class="panel" style="margin-top:18px"><h2>Histori Pergerakan Stok</h2><div class="table-wrap"><table><thead><tr><th>Waktu</th><th>SKU / Produk</th><th>Jenis</th><th>Jumlah</th><th>Pengguna</th><th>Catatan</th></tr></thead><tbody>
<?php foreach($history as $h): ?><tr><td><?= e($h['created_at']) ?></td><td><?= e($h['sku']) ?><div class="mini"><?= e($h['product_name']) ?></div></td><td><?= $h['type']==='IN'?'<span class="badge">Masuk</span>':'<span class="badge low">Keluar</span>' ?></td><td><?= (int)$h['quantity'] ?></td><td><?= e($h['username']) ?></td><td><?= e($h['note']) ?></td></tr><?php endforeach; ?>
<?php if(!$history): ?><tr><td colspan="6" class="empty">Belum ada histori.</td></tr><?php endif; ?></tbody></table></div></section>
<?php require __DIR__ . '/../views/footer.php'; ?>
