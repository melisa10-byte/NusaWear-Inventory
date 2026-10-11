<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/escape.php';
require_once __DIR__ . '/../helpers/auth.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if ($username === '' || $password === '') {
        $error = 'Username dan password wajib diisi.';
    } else {
        $stmt = $pdo->prepare('SELECT id,username,password_hash,role FROM users WHERE username=:username');
        $stmt->execute(['username'=>$username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>(int)$user['id'],'username'=>$user['username'],'role'=>$user['role']];
            header('Location: dashboard.php'); exit;
        }
        $error = 'Username atau password salah.';
    }
}
$pageTitle='Login';
require __DIR__ . '/../views/header.php';
?>
<div class="login-shell">
 <div class="login-logo"><a class="brand" href="login.php"><span class="brand-icon">N</span> NusaWear</a><p class="sub">Inventory Management System</p></div>
 <section class="panel"><h1>Selamat datang</h1><p class="sub">Masuk untuk mengelola persediaan toko.</p>
 <?php if($error): ?><div class="alert error"><?= e($error) ?></div><?php endif; ?>
 <form method="post">
  <div class="field"><label>Username</label><input name="username" required autocomplete="username"></div>
  <div class="field"><label>Password</label><input type="password" name="password" required autocomplete="current-password"></div>
  <button class="btn" type="submit" style="width:100%">Masuk</button>
 </form><p class="mini">Akun demo: admin / Admin123! atau staff / Staff123!</p>
 </section>
</div>
<?php require __DIR__ . '/../views/footer.php'; ?>
