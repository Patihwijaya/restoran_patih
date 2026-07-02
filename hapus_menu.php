<?php
session_start();
require 'config.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $pdo->prepare("DELETE FROM menus WHERE id = ?");
    
    if ($stmt->execute([$id])) {
        // Redirect kembali ke admin jika berhasil dihapus
        header("Location: admin.php");
        exit;
    } else {
        die("Gagal menghapus menu.");
    }
} else {
    // Jika tidak ada ID di URL, kembalikan ke admin
    header("Location: admin.php");
    exit;
}
?>