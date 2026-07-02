<?php
session_start();
require 'config.php';

// Proteksi halaman admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$id = $_GET['id'];

// Ambil data menu yang akan diedit
$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    die("Menu tidak ditemukan.");
}

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_menu = $_POST['nama_menu'];
    $kategori = $_POST['kategori'];
    $harga = $_POST['harga'];
    $status = $_POST['status'];
    $komposisi_bahan = $_POST['komposisi_bahan'];
    $kalori = $_POST['kalori'];
    
    // Secara default, gunakan URL/path foto yang lama
    $foto_url = $menu['foto_url']; 

    // Cek apakah ada file foto baru yang diunggah
    if (isset($_FILES['foto_file']) && $_FILES['foto_file']['error'] === UPLOAD_ERR_OK) {
        $nama_file = $_FILES['foto_file']['name'];
        $tmp_name = $_FILES['foto_file']['tmp_name'];
        
        // Buat nama file unik
        $ekstensi = pathinfo($nama_file, PATHINFO_EXTENSION);
        $nama_file_baru = uniqid() . '.' . $ekstensi;
        $tujuan = 'uploads/' . $nama_file_baru;
        
        // Pindahkan file baru
        if (move_uploaded_file($tmp_name, $tujuan)) {
            $foto_url = $tujuan; // Update dengan path file baru
            
            // Hapus file foto lama dari server agar tidak memenuhi penyimpanan
            // Pastikan file lama benar-benar ada di folder 'uploads' (bukan URL eksternal bawaan dummy)
            if (file_exists($menu['foto_url']) && strpos($menu['foto_url'], 'http') === false) {
                unlink($menu['foto_url']);
            }
        } else {
            $error = "Gagal mengunggah gambar baru.";
        }
    }

    if (!isset($error)) {
        $updateStmt = $pdo->prepare("
            UPDATE menus 
            SET nama_menu = ?, kategori = ?, harga = ?, status = ?, komposisi_bahan = ?, kalori = ?, foto_url = ? 
            WHERE id = ?
        ");
        
        if ($updateStmt->execute([$nama_menu, $kategori, $harga, $status, $komposisi_bahan, $kalori, $foto_url, $id])) {
            header("Location: admin.php");
            exit;
        } else {
            $error = "Gagal memperbarui menu.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-lg shadow">
        <h2 class="text-2xl font-bold mb-6">Edit Menu: <?= htmlspecialchars($menu['nama_menu']) ?></h2>
        
        <?php if (isset($error)): ?>
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data" class="space-y-4">
            <div>
                <label class="block font-medium">Nama Menu</label>
                <input type="text" name="nama_menu" value="<?= htmlspecialchars($menu['nama_menu']) ?>" class="w-full border p-2 rounded" required>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Kategori</label>
                    <select name="kategori" class="w-full border p-2 rounded" required>
                        <option value="Food" <?= $menu['kategori'] == 'Food' ? 'selected' : '' ?>>Food</option>
                        <option value="Beverage" <?= $menu['kategori'] == 'Beverage' ? 'selected' : '' ?>>Beverage</option>
                        <option value="Dessert" <?= $menu['kategori'] == 'Dessert' ? 'selected' : '' ?>>Dessert</option>
                    </select>
                </div>
                <div>
                    <label class="block font-medium">Status</label>
                    <select name="status" class="w-full border p-2 rounded" required>
                        <option value="Ready" <?= $menu['status'] == 'Ready' ? 'selected' : '' ?>>Ready</option>
                        <option value="Sold Out" <?= $menu['status'] == 'Sold Out' ? 'selected' : '' ?>>Sold Out</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-medium">Harga (Rp)</label>
                    <input type="number" name="harga" value="<?= htmlspecialchars($menu['harga']) ?>" class="w-full border p-2 rounded" required>
                </div>
                <div>
                    <label class="block font-medium">Kalori (Kkal)</label>
                    <input type="number" name="kalori" value="<?= htmlspecialchars($menu['kalori']) ?>" class="w-full border p-2 rounded" required>
                </div>
            </div>

            <div>
                <label class="block font-medium mb-1">Upload Foto Menu Baru</label>
                <p class="text-xs text-gray-500 mb-2">Biarkan kosong jika tidak ingin mengubah foto.</p>
                <input type="file" name="foto_file" class="w-full border p-2 rounded bg-white" accept="image/*">
                
                <div class="mt-3">
                    <p class="text-sm font-medium text-gray-700 mb-1">Foto Saat Ini:</p>
                    <img src="<?= htmlspecialchars($menu['foto_url']) ?>" alt="Foto Menu Saat Ini" class="h-24 w-auto rounded border border-gray-200 object-cover">
                </div>
            </div>

            <div>
                <label class="block font-medium">Komposisi Bahan</label>
                <textarea name="komposisi_bahan" class="w-full border p-2 rounded" rows="3" required><?= htmlspecialchars($menu['komposisi_bahan']) ?></textarea>
            </div>

            <div class="flex gap-4 mt-6">
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700 font-semibold shadow">Update Menu</button>
                <a href="admin.php" class="bg-gray-400 text-white px-6 py-2 rounded hover:bg-gray-500 font-semibold shadow">Batal</a>
            </div>
        </form>
    </div>
</body>
</html>