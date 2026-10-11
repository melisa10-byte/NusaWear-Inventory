<?php
require_once __DIR__ . '/../config/database.php';
$accounts = [
    ['admin', 'Admin123!', 'admin', 'Administrator NusaWear'],
    ['staff', 'Staff123!', 'staff', 'Staff Toko NusaWear'],
];
$created = [];
foreach ($accounts as [$username, $password, $role, $fullName]) {
    $check = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $check->execute(['username' => $username]);
    if (!$check->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users(username,password_hash,role) VALUES(:u,:p,:r)');
        $stmt->execute(['u'=>$username,'p'=>password_hash($password, PASSWORD_DEFAULT),'r'=>$role]);
        $id = (int)$pdo->lastInsertId();
        $profile = $pdo->prepare('INSERT INTO user_profiles(user_id,full_name) VALUES(:id,:name)');
        $profile->execute(['id'=>$id,'name'=>$fullName]);
        $created[] = $username;
    }
}
?><!doctype html><html lang="id"><meta charset="utf-8"><title>Seed akun</title><body style="font-family:Arial;max-width:700px;margin:50px auto;line-height:1.7"><h1>Setup akun demo NusaWear</h1><?php if ($created): ?><p>Akun berhasil dibuat: <?= htmlspecialchars(implode(', ', $created)) ?></p><?php else: ?><p>Akun demo sudah tersedia sebelumnya.</p><?php endif; ?><p><b>Admin:</b> admin / Admin123!</p><p><b>Staff:</b> staff / Staff123!</p><p>Untuk keamanan, hapus file <code>seed_admin.php</code> setelah setup.</p><p><a href="login.php">Ke halaman login</a></p></body></html>
