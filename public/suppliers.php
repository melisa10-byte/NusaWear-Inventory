<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../helpers/auth.php'; require_once __DIR__ . '/../helpers/escape.php'; require_login();
$pageTitle='Supplier';$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_admin();$name=trim((string)($_POST['name']??''));$phone=trim((string)($_POST['phone']??''));$address=trim((string)($_POST['address']??''));if($name==='')$error='Nama supplier wajib diisi.';else{$s=$pdo->prepare('INSERT INTO suppliers(name,phone,address) VALUES(:n,:p,:a)');$s->execute(['n'=>$name,'p'=>$phone?:null,'a'=>$address?:null]);$success='Supplier berhasil ditambahkan.';}}
$rows=$pdo->query('SELECT s.*,COUNT(ps.product_id) product_count FROM suppliers s LEFT JOIN product_suppliers ps ON ps.supplier_id=s.id GROUP BY s.id ORDER BY s.name')->fetchAll();
require __DIR__ . '/../views/header.php';?>
<div class="page-head"><div><h1>Supplier</h1><p class="sub">Data pemasok produk NusaWear.</p></div></div>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="alert"><?= e($success) ?></div><?php endif; ?>
<?php if(current_user()['role']==='admin'): ?><section class="panel"><h2>Tambah Supplier</h2><form method="post" class="form-grid"><div class="field"><label>Nama supplier</label><input name="name" required maxlength="120"></div><div class="field"><label>Telepon</label><input name="phone" maxlength="30"></div><div class="field"><label>Alamat</label><input name="address" maxlength="255"></div><div class="field" style="justify-content:end"><button class="btn">Simpan</button></div></form></section><?php endif; ?>
<div class="table-wrap"><table><thead><tr><th>Supplier</th><th>Telepon</th><th>Alamat</th><th>Produk terkait</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['phone']) ?></td><td><?= e($r['address']) ?></td><td><?= (int)$r['product_count'] ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../views/footer.php'; ?>
