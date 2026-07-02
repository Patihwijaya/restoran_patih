<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $menu_id = $_POST['menu_id'];
    $jumlah = $_POST['jumlah'];
    $harga_satuan = $_POST['harga_satuan'];
    
    // Validasi sederhana
    if (!is_numeric($jumlah) || $jumlah < 1) {
        die("Jumlah tidak valid.");
    }

    $total_harga = $jumlah * $harga_satuan;

    // Simpan pesanan ke tabel orders
    $stmt = $pdo->prepare("INSERT INTO orders (menu_id, jumlah, total_harga, status_pembayaran) VALUES (?, ?, ?, 'Pending')");
    if ($stmt->execute([$menu_id, $jumlah, $total_harga])) {
        // Ambil ID order yang baru saja dibuat
        $order_id = $pdo->lastInsertId();
        
        // Redirect ke halaman pembayaran
        header("Location: payment.php?order_id=" . $order_id);
        exit;
    } else {
        die("Gagal memproses pesanan.");
    }
} else {
    header("Location: index.php");
    exit;
}
?>