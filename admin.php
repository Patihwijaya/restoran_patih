<?php
session_start();
require 'config.php';

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// Mengambil semua data menu
$stmt = $pdo->query("SELECT * FROM menus ORDER BY id DESC");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-6xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Dashboard Admin</h1>
            <div class="flex gap-4">
                <a href="tambah_menu.php" class="bg-green-600 text-white px-4 py-2 rounded shadow hover:bg-green-700 font-semibold">+ Tambah Menu</a>
                <a href="logout.php" class="bg-red-600 text-white px-4 py-2 rounded shadow hover:bg-red-700 font-semibold">Logout</a>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-6 py-4 text-left text-sm uppercase">Nama Menu</th>
                        <th class="px-6 py-4 text-left text-sm uppercase">Kategori</th>
                        <th class="px-6 py-4 text-left text-sm uppercase">Harga</th>
                        <th class="px-6 py-4 text-left text-sm uppercase">Status</th>
                        <th class="px-6 py-4 text-center text-sm uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <?php foreach ($menus as $item): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900"><?= htmlspecialchars($item['nama_menu']) ?></td>
                        <td class="px-6 py-4"><?= htmlspecialchars($item['kategori']) ?></td>
                        <td class="px-6 py-4 text-orange-600 font-semibold">Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4">
                            <?php $statusColor = $item['status'] == 'Ready' ? 'text-green-600' : 'text-red-600'; ?>
                            <span class="font-bold <?= $statusColor ?>"><?= $item['status'] ?></span>
                        </td>
                        <td class="px-6 py-4 text-center flex justify-center gap-2">
                            <a href="edit_menu.php?id=<?= $item['id'] ?>" class="bg-blue-500 text-white px-3 py-1 rounded hover:bg-blue-600 text-sm">Edit</a>
                            
                            <a href="hapus_menu.php?id=<?= $item['id'] ?>" 
                               onclick="return confirm('Apakah Anda yakin ingin menghapus menu ini? Data yang dihapus tidak bisa dikembalikan.')" 
                               class="bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600 text-sm">
                               Hapus
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if (empty($menus)): ?>
                <div class="p-6 text-center text-gray-500">Belum ada menu yang ditambahkan.</div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>