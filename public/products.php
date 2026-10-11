<?php
require_once __DIR__ . '/../config/database.php'; require_once __DIR__ . '/../helpers/auth.php'; require_once __DIR__ . '/../helpers/escape.php'; require_login();
$pageTitle='Produk'; $error=''; $success='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    require_admin();
    $categoryId=filter_var($_POST['category_id']??null,FILTER_VALIDATE_INT);
    $sku=strtoupper(trim((string)($_POST['sku']??''))); $name=trim((string)($_POST['name']??''));
    $size=trim((string)($_POST['size']??'All Size')); $color=trim((string)($_POST['color']??'Campuran'));
    $price=filter_var($_POST['price']??null,FILTER_VALIDATE_FLOAT);
    $minimum=filter_var($_POST['minimum_stock']??0,FILTER_VALIDATE_INT);
    if (!$categoryId || $name==='' || $sku==='' || $price===false || $price<0 || $minimum===false || $minimum<0) $error='Periksa kembali data produk. Harga dan stok minimum tidak boleh negatif.';
    else {
      try { $stmt=$pdo->prepare('INSERT INTO products(category_id,sku,name,size,color,price,minimum_stock) VALUES(:c,:s,:n,:z,:co,:p,:m)');
        $stmt->execute(['c'=>$categoryId,'s'=>$sku,'n'=>$name,'z'=>$size,'co'=>$color,'p'=>$price,'m'=>$minimum]); $success='Produk berhasil ditambahkan.';
      } catch(PDOException $ex) { $error=$ex->getCode()==='23000'?'SKU sudah digunakan atau kategori tidak valid.':'Produk gagal disimpan.'; }
    }
}
$cats=$pdo->query('SELECT id,name FROM categories ORDER BY name')->fetchAll();
$q=trim((string)($_GET['q']??'')); $category=filter_var($_GET['category']??'',FILTER_VALIDATE_INT); $sort=(string)($_GET['sort']??'name'); $dir=strtoupper((string)($_GET['dir']??'ASC'));
$allowed=['name'=>'p.name','price'=>'p.price','stock'=>'p.stock','sku'=>'p.sku']; if(!isset($allowed[$sort]))$sort='name'; if(!in_array($dir,['ASC','DESC'],true))$dir='ASC';
$where=[];$params=[]; if($q!==''){$where[]='(p.name LIKE :q OR p.sku LIKE :q)';$params['q']="%$q%";} if($category && $category>0){$where[]='p.category_id=:cat';$params['cat']=$category;}
$sqlWhere=$where?' WHERE '.implode(' AND ',$where):'';
$count=$pdo->prepare("SELECT COUNT(*) FROM products p$sqlWhere");$count->execute($params);$total=(int)$count->fetchColumn();
$page=max(1,(int)($_GET['page']??1));$perPage=8;$pages=max(1,(int)ceil($total/$perPage));$page=min($page,$pages);$offset=($page-1)*$perPage;
$sql="SELECT p.*,c.name category_name FROM products p JOIN categories c ON c.id=p.category_id$sqlWhere ORDER BY {$allowed[$sort]} $dir LIMIT :lim OFFSET :off";
$stmt=$pdo->prepare($sql);foreach($params as $k=>$v)$stmt->bindValue(':'.$k,$v,is_int($v)?PDO::PARAM_INT:PDO::PARAM_STR);$stmt->bindValue(':lim',$perPage,PDO::PARAM_INT);$stmt->bindValue(':off',$offset,PDO::PARAM_INT);$stmt->execute();$products=$stmt->fetchAll();
require __DIR__ . '/../views/header.php';
?>
<div class="page-head"><div><h1>Produk</h1><p class="sub">Kelola katalog, SKU unik, harga, dan batas stok minimum.</p></div></div>
<?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?><?php if($success): ?><div class="alert"><?= e($success) ?></div><?php endif; ?>
<?php if(current_user()['role']==='admin'): ?><section class="panel"><h2>Tambah Produk</h2><form method="post" class="form-grid">
<div class="field"><label>Kategori</label><select name="category_id" required><?php foreach($cats as $c): ?><option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option><?php endforeach; ?></select></div>
<div class="field"><label>SKU unik</label><input name="sku" required maxlength="50" placeholder="NW-TS-006"></div><div class="field"><label>Nama produk</label><input name="name" required maxlength="150"></div>
<div class="field"><label>Ukuran</label><input name="size" value="All Size"></div><div class="field"><label>Warna</label><input name="color" value="Campuran"></div>
<div class="field"><label>Harga (Rp)</label><input type="number" name="price" min="0" step="1000" required></div><div class="field"><label>Stok minimum</label><input type="number" name="minimum_stock" min="0" value="5" required></div>
<div class="field" style="justify-content:end"><button class="btn">Simpan Produk</button></div></form></section><?php endif; ?>
<section class="panel" style="margin-top:18px"><form class="filter" method="get"><input name="q" value="<?= e($q) ?>" placeholder="Cari nama atau SKU..."><select name="category"><option value="">Semua kategori</option><?php foreach($cats as $c): ?><option value="<?= (int)$c['id'] ?>" <?= (string)$category===(string)$c['id']?'selected':'' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select><select name="sort"><option value="name" <?= $sort==='name'?'selected':'' ?>>Nama</option><option value="price" <?= $sort==='price'?'selected':'' ?>>Harga</option><option value="stock" <?= $sort==='stock'?'selected':'' ?>>Stok</option><option value="sku" <?= $sort==='sku'?'selected':'' ?>>SKU</option></select><select name="dir"><option <?= $dir==='ASC'?'selected':'' ?>>ASC</option><option <?= $dir==='DESC'?'selected':'' ?>>DESC</option></select><button class="btn">Terapkan</button><a class="btn secondary" href="products.php">Reset</a></form>
<div class="table-wrap"><table><thead><tr><th>SKU / Produk</th><th>Kategori</th><th>Ukuran / Warna</th><th>Harga</th><th>Stok</th><th>Status</th></tr></thead><tbody>
<?php foreach($products as $p): ?><tr><td><b><?= e($p['name']) ?></b><div class="mini"><?= e($p['sku']) ?></div></td><td><?= e($p['category_name']) ?></td><td><?= e($p['size']) ?> / <?= e($p['color']) ?></td><td>Rp <?= number_format((float)$p['price'],0,',','.') ?></td><td><?= (int)$p['stock'] ?></td><td><?= (int)$p['stock'] <= (int)$p['minimum_stock']?'<span class="badge low">Stok rendah</span>':'<span class="badge">Tersedia</span>' ?></td></tr><?php endforeach; ?>
<?php if(!$products): ?><tr><td colspan="6" class="empty">Produk tidak ditemukan.</td></tr><?php endif; ?></tbody></table></div>
<p class="mini">Menampilkan <?= count($products) ?> dari <?= $total ?> produk · <?= $perPage ?> data per halaman</p>
<div class="pagination"><?php for($i=1;$i<=$pages;$i++): $paramsPage=$_GET;$paramsPage['page']=$i;$url='?'.http_build_query($paramsPage); ?><a class="<?= $i===$page?'active':'' ?>" href="<?= e($url) ?>"><?= $i ?></a><?php endfor; ?></div>
</section>
<?php require __DIR__ . '/../views/footer.php'; ?>
