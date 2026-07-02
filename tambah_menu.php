<?php
session_start();
require 'config.php';

// Proteksi halaman
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    $komposisi_bahan = $_POST['komposisi_bahan'];
    $kalori = $_POST['kalori'];
    
    // Logika Upload Gambar
    $foto_url = ''; 
    if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] === UPLOAD_ERR_OK) {
        $nama_file = $_FILES['foto_file']['name'];
        $tmp_name = $_FILES['foto_file']['tmp_name'];
        
        // Buat nama file unik agar tidak bentrok
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_file_baru = uniqid() . '.' . $ekstensi;
        
        // Tentukan folder tujuan (pastikan folder 'uploads' sudah dibuat)
        $tujuan = 'uploads/' . $nama_file_baru;
        
        // Pindahkan file dari penyimpanan sementara ke folder uploads
        if (move_uploaded_file($tmp_name, $tujuan)) {
            $foto_url = $tujuan; // Simpan path 'uploads/namafile.jpg' ke database
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    }

    if (!isset($error)) {
        $stmt = $pdo->prepare("INSERT INTO menus (nama_menu, kategori, harga, status, komposisi_bahan, kalori, foto_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$nama_menu, $kategori, $harga, $status, $komposisi_bahan, $kalori, $foto_url])) {
            header("Location: admin.php");
            exit;
        } else {
            $error = "Gagal menyimpan data ke database.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Menu Baru</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Tambah Menu Baru</h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-4" enctype="multipart/form-data">
            <div>
                <label class="block font-medium">Nama Menu</label>
                <input type="text" name="nama_menu" class="w-full border p-2 rounded" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Kategori</label>
                    <select name="kategori" class="w-full border p-2 rounded" required>
                        <option value="Food">Food</option>
                        <option value="Beverage">Beverage</option>
                        <option value="Dessert">Dessert</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border p-2 rounded" required>
                        <option value="Ready">Ready</option>
                        <option value="Sold Out">Sold Out</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Harga (Rp)</label>
                    <input type="number" name="harga" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label class="block font-medium">Kalori (Kkal)</label>
                    <input type="number" name="kalori" class="w-full border p-2 rounded" required>
                </div>
            </div>

            <div>
                <label class="block font-medium">Upload Foto Menu</label>
                <input type="file" name="foto_file" class="w-full border p-2 rounded bg-white" accept="image/*" required>
            </div>

            <div>
                <label class="block font-medium">Komposisi Bahan</label>
                <textarea name="komposisi_bahan" class="w-full border p-2 rounded" rows="3" required></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded hover:bg-indigo-700">Simpan Menu</button>
                <a href="admin.php" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>