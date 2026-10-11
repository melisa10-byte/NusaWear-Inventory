<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../helpers/auth.php'; require_once __DIR__ . '/../helpers/escape.php'; require_login();
$pageTitle='Kategori';$error='';$success='';
if($_SERVER['REQUEST_METHOD']==='POST'){require_admin();$name=trim((string)($_POST['name']??''));$description=trim((string)($_POST['description']??''));if($name==='')$error='Nama kategori wajib diisi.';else{try{$s=$pdo->prepare('INSERT INTO categories(name,description) VALUES(:n,:d)');$s->execute(['n'=>$name,'d'=>$description?:null]);$success='Kategori berhasil ditambahkan.';}catch(PDOException $ex){$error='Kategori gagal disimpan. Pastikan namanya belum digunakan.';}}}
$rows=$pdo->query('SELECT c.*,COUNT(p.id) product_count FROM categories c LEFT JOIN products p ON p.category_id=c.id GROUP BY c.id ORDER BY c.name')->fetchAll();
require __DIR__ . '/../views/header.php';?>
<div class="page-head"><div><h1>Kategori</h1><p class="sub">Pengelompokan produk dengan relasi one-to-many.</p></div></div>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="alert"><?= e($success) ?></div><?php endif; ?>
<?php if(current_user()['role']==='admin'): ?><section class="panel"><h2>Tambah Kategori</h2><form method="post" class="form-grid"><div class="field"><label>Nama kategori</label><input name="name" required maxlength="100"></div><div class="field"><label>Deskripsi</label><input name="description" maxlength="255"></div><div class="field" style="justify-content:end"><button class="btn">Simpan</button></div></form></section><?php endif; ?>
<div class="table-wrap"><table><thead><tr><th>Kategori</th><th>Deskripsi</th><th>Jumlah produk</th></tr></thead><tbody><?php foreach($rows as $r): ?><tr><td><b><?= e($r['name']) ?></b></td><td><?= e($r['description']) ?></td><td><?= (int)$r['product_count'] ?></td></tr><?php endforeach; ?></tbody></table></div>
<?php require __DIR__ . '/../views/footer.php'; ?>
