<?php
require 'config.php';

if (!isset($_GET['order_id'])) {
    die("ID Order tidak ditemukan.");
}

$order_id = $_GET['order_id'];

// Ambil data order beserta nama menu dengan JOIN
$stmt = $pdo->prepare("
    SELECT orders.*, menus.nama_menu 
    FROM orders 
    JOIN menus ON orders.menu_id = menus.id 
    WHERE orders.id = ?
");
$stmt->execute([$order_id]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die("Data pesanan tidak ditemukan.");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Selesaikan Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans p-6">
    <div class="max-w-md mx-auto bg-white p-8 rounded-lg shadow-lg mt-10">
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Selesaikan Pembayaran</h2>
            <p class="text-gray-500 mt-1">Order ID: #<?= str_pad($order['id'], 5, '0', STR_PAD_LEFT) ?></p>
        </div>

        <div class="border-t border-b border-gray-200 py-4 mb-6">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Item</span>
                <span class="font-medium"><?= htmlspecialchars($order['nama_menu']) ?> (x<?= $order['jumlah'] ?>)</span>
            </div>
            <div class="flex justify-between text-lg font-bold">
                <span class="text-gray-800">Total Tagihan</span>
                <span class="text-orange-600">Rp <?= number_format($order['total_harga'], 0, ',', '.') ?></span>
            </div>
        </div>

        <button onclick="payNow()" class="w-full bg-green-600 text-white py-3 rounded-md font-bold text-lg hover:bg-green-700 transition shadow-lg">
            Bayar Rp <?= number_format($order['total_harga'], 0, ',', '.') ?>
        </button>

        <script>
            function payNow() {
                // Di sistem nyata, integrasikan Snap.js Midtrans di sini
                alert('Pembayaran berhasil disimulasikan!');
                window.location.href = "index.php"; 
            }
        </script>
    </div>
</body>
</html>