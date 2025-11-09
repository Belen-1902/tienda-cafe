<?php
session_start();
if(empty($_SESSION['admin'])){ header('Location: login.php'); exit; }
require '../db.php';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if($id){
    $stmt = $pdo->prepare('SELECT image_path FROM products WHERE id = ?');
    $stmt->execute([$id]);
    $p = $stmt->fetch();
    if($p && !empty($p['image_path'])){
        $path = __DIR__ . '/../' . $p['image_path'];
        if(file_exists($path)) @unlink($path);
    }
    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
    $stmt->execute([$id]);
}
header('Location: dashboard.php');
