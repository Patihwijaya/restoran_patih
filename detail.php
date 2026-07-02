<?php
require 'config.php';

if (!isset($_GET['id'])) {
    die("ID Menu tidak ditemukan.");
}
$id = $_GET['id'];

// Proses Submit Ulasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_ulasan'])) {
    $rating_baru = (int)$_POST['rating'];
    $komentar_baru = $_POST['komentar'];

    // Ambil data ulasan yang sudah ada
    $stmt = $pdo->prepare("SELECT ulasan_pelanggan FROM menus WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $ulasan = json_decode($row['ulasan_pelanggan'], true) ?? [];
    
    // Proses konversi jika ada data ulasan lama (format string murni) ke format array
    $total_rating = 0;
    foreach ($ulasan as $key => $item) {
        if (is_string($item)) {
            $ulasan[$key] = ['rating' => 5, 'komentar' => $item, 'tanggal' => date('Y-m-d')];
            $total_rating += 5;
        } else {
            $total_rating += $item['rating'];
        }
    }

    // Tambahkan ulasan baru ke dalam array
    $ulasan[] = [
        'rating' => $rating_baru,
        'komentar' => $komentar_baru,
        'tanggal' => date('Y-m-d H:i')
    ];
    $total_rating += $rating_baru;
    
    // Hitung rata-rata rating baru
    $rata_rata = $total_rating / count($ulasan);

    // Update database dengan JSON baru dan Rating Rata-rata yang baru
    $updateStmt = $pdo->prepare("UPDATE menus SET rating = ?, ulasan_pelanggan = ? WHERE id = ?");
    $updateStmt->execute([$rata_rata, json_encode($ulasan), $id]);
    
    // Refresh halaman agar ulasan baru langsung muncul
    header("Location: detail.php?id=" . $id);
    exit;
}

// Fetch data menu untuk ditampilkan
$stmt = $pdo->prepare("SELECT * FROM menus WHERE id = ?");
$stmt->execute([$id]);
$menu = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$menu) {
    die("Menu tidak ada di database.");
}

$ulasan_ditampilkan = json_decode($menu['ulasan_pelanggan'], true) ?? [];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail - <?= htmlspecialchars($menu['nama_menu']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans p-6">
    <div class="max-w-5xl mx-auto">
        <div class="mb-4">
            <a href="index.php" class="text-indigo-600 hover:underline font-medium">&larr; Kembali ke Daftar Menu</a>
        </div>

        <div class="bg-white rounded-lg shadow-lg overflow-hidden flex flex-col md:flex-row mb-8">
            <div class="md:w-1/2">
                <img src="<?= htmlspecialchars($menu['foto_url']) ?>" alt="<?= htmlspecialchars($menu['nama_menu']) ?>" class="w-full h-full object-cover">
            </div>
            
            <div class="md:w-1/2 p-8 flex flex-col">
                <h2 class="text-3xl font-bold text-gray-900"><?= htmlspecialchars($menu['nama_menu']) ?></h2>
                <p class="text-2xl font-bold text-orange-600 mt-2">Rp <?= number_format($menu['harga'], 0, ',', '.') ?></p>
                
                <div class="mt-6">
                    <h3 class="text-lg font-bold text-gray-800">Komposisi Bahan:</h3>
                    <p class="text-gray-600 mt-1"><?= nl2br(htmlspecialchars($menu['komposisi_bahan'])) ?></p>
                </div>
                
                <div class="mt-4 flex items-center gap-4">
                    <div class="bg-gray-100 px-3 py-1 rounded-md text-sm font-medium text-gray-700">🔥 <?= $menu['kalori'] ?> Kalori</div>
                    <div class="bg-yellow-50 px-3 py-1 rounded-md text-sm font-bold text-yellow-700">⭐ <?= number_format($menu['rating'], 1) ?> / 5</div>
                </div>

                <hr class="my-6">

                <form action="checkout.php" method="POST" class="mt-auto">
                    <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">
                    <input type="hidden" name="harga_satuan" value="<?= $menu['harga'] ?>">
                    
                    <div class="flex items-center gap-4">
                        <input type="number" name="jumlah" value="1" min="1" class="border border-gray-300 rounded-md p-2 w-20 text-center" required>
                        <button type="submit" 
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md font-semibold hover:bg-indigo-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            <?= $menu['status'] == 'Sold Out' ? 'disabled' : '' ?>>
                            <?= $menu['status'] == 'Sold Out' ? 'Sold Out' : 'Checkout Sekarang' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-8">
            <h3 class="text-2xl font-bold text-gray-900 mb-6">Ulasan Pelanggan</h3>
            
            <ul class="space-y-4 mb-8">
                <?php if (count($ulasan_ditampilkan) > 0): ?>
                    <?php foreach ($ulasan_ditampilkan as $u): ?>
                        <?php 
                            // Proteksi untuk membaca data array atau string lama
                            $teks = is_string($u) ? $u : $u['komentar']; 
                            $rate = is_array($u) && isset($u['rating']) ? $u['rating'] : 5;
                            $tanggal = is_array($u) && isset($u['tanggal']) ? $u['tanggal'] : '';
                        ?>
                        <li class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="flex items-center mb-2 gap-2">
                                <span class="text-yellow-500 text-sm">
                                    <?= str_repeat('⭐', $rate) ?>
                                </span>
                                <?php if($tanggal): ?>
                                    <span class="text-xs text-gray-400 ml-auto"><?= $tanggal ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-gray-700 italic">"<?= htmlspecialchars($teks) ?>"</p>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-500 italic">Belum ada ulasan untuk menu ini. Jadilah yang pertama!</p>
                <?php endif; ?>
            </ul>

            <div class="bg-blue-50/50 p-6 rounded-lg border border-blue-100">
                <h4 class="text-lg font-bold text-gray-800 mb-4">Tinggalkan Ulasan Anda</h4>
                <form action="" method="POST">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Beri Rating</label>
                        <select name="rating" class="border border-gray-300 rounded-md p-2 w-full md:w-1/3 outline-none focus:border-indigo-500" required>
                            <option value="5">⭐⭐⭐⭐⭐ (5) Sangat Bagus</option>
                            <option value="4">⭐⭐⭐⭐ (4) Bagus</option>
                            <option value="3">⭐⭐⭐ (3) Biasa Saja</option>
                            <option value="2">⭐⭐ (2) Kurang</option>
                            <option value="1">⭐ (1) Sangat Buruk</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                        <textarea name="komentar" rows="3" class="border border-gray-300 rounded-md p-3 w-full outline-none focus:border-indigo-500" placeholder="Bagaimana rasa dan porsinya?" required></textarea>
                    </div>
                    <button type="submit" name="submit_ulasan" class="bg-yellow-500 text-white px-6 py-2 rounded font-bold hover:bg-yellow-600 transition shadow">
                        Kirim Ulasan
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>