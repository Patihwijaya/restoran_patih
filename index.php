<?php
require 'config.php';

// Ambil data menu dari database
$stmt = $pdo->query("SELECT * FROM menus");
$menus = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Kelompokkan menu berdasarkan kategori agar tampilannya rapi
$menu_kategori = [
    'Food' => [],
    'Beverage' => [],
    'Dessert' => []
];

foreach ($menus as $item) {
    $menu_kategori[$item['kategori']][] = $item;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Le Restauration | Digital Menu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    <style>
        .font-serif { font-family: 'Playfair Display', serif; }
        .font-sans { font-family: 'Lato', sans-serif; }
    </style>
</head>
<body class="bg-[#faf9f6] text-gray-800 font-sans antialiased">

    <nav class="absolute top-0 left-0 w-full z-10 px-8 py-6 flex justify-between items-center text-white">
        <div class="text-2xl font-serif font-bold tracking-widest uppercase">Le Restauration</div>
        <div>
            <a href="login.php" class="text-sm tracking-widest uppercase hover:text-amber-400 transition">Admin Login</a>
        </div>
    </nav>

    <header class="relative h-[60vh] bg-black flex items-center justify-center text-center">
        <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1514362545857-3bc16c4c7d1b?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center opacity-40"></div>
        
        <div class="relative z-10 px-4">
            <h2 class="text-amber-400 text-sm md:text-base tracking-[0.3em] uppercase mb-4">Pengalaman Kuliner Terbaik</h2>
            <h1 class="text-5xl md:text-7xl text-white font-serif mb-6">Menu Signature Kami</h1>
            <p class="text-gray-200 max-w-xl mx-auto font-light text-lg">Jelajahi perpaduan rasa autentik dan bahan-bahan premium yang diracik khusus oleh Executive Chef kami.</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-6 py-20 space-y-24">
        
        <?php 
        // Label untuk Kategori
        $label_kategori = [
            'Food' => ['title' => 'Main Course', 'desc' => 'Hidangan utama yang disajikan dengan keahlian kuliner tingkat tinggi.'],
            'Beverage' => ['title' => 'Beverages', 'desc' => 'Minuman segar dan koktail artisan untuk menyempurnakan hidangan Anda.'],
            'Dessert' => ['title' => 'Desserts', 'desc' => 'Penutup manis yang meleleh di mulut, dibuat dengan penuh cinta.']
        ];

        foreach ($menu_kategori as $kategori => $items): 
            if (count($items) === 0) continue; // Lewati jika kategori kosong
        ?>
        
        <section>
            <div class="text-center mb-12">
                <h2 class="text-4xl font-serif text-gray-900 mb-3"><?= $label_kategori[$kategori]['title'] ?></h2>
                <div class="w-16 h-0.5 bg-amber-500 mx-auto mb-4"></div>
                <p class="text-gray-500 italic"><?= $label_kategori[$kategori]['desc'] ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
                <?php foreach ($items as $item): ?>
                
                <div class="group bg-white rounded-none shadow-sm hover:shadow-2xl transition-all duration-500 overflow-hidden flex flex-col relative border border-gray-100">
                    
                    <?php if ($item['status'] == 'Sold Out'): ?>
                        <div class="absolute top-4 right-4 bg-black/80 text-white text-xs tracking-widest uppercase px-3 py-1 z-10 backdrop-blur-sm">
                            Sold Out
                        </div>
                    <?php endif; ?>

                    <div class="h-64 overflow-hidden relative bg-gray-200">
                        <img src="<?= htmlspecialchars($item['foto_url']) ?>" 
                             alt="<?= htmlspecialchars($item['nama_menu']) ?>" 
                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 <?= $item['status'] == 'Sold Out' ? 'grayscale opacity-70' : '' ?>">
                    </div>
                    
                    <div class="p-8 flex flex-col flex-grow text-center">
                        <h3 class="text-2xl font-serif text-gray-900 mb-2 group-hover:text-amber-600 transition-colors">
                            <?= htmlspecialchars($item['nama_menu']) ?>
                        </h3>
                        
                        <p class="text-gray-500 text-sm mb-6 flex-grow line-clamp-2">
                            <?= htmlspecialchars($item['komposisi_bahan']) ?>
                        </p>
                        
                        <div class="flex flex-col items-center gap-3">
                            <div class="flex items-center gap-1 text-xs text-gray-400 tracking-widest uppercase">
                                <span class="text-amber-500">★</span> <?= number_format($item['rating'], 1) ?>
                            </div>
                            
                            <div class="text-xl text-gray-900 font-medium">
                                Rp <?= number_format($item['harga'], 0, ',', '.') ?>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="detail.php?id=<?= $item['id'] ?>" 
                               class="inline-block w-full border border-gray-900 text-gray-900 px-6 py-3 text-sm tracking-widest uppercase hover:bg-gray-900 hover:text-white transition-colors duration-300 <?= $item['status'] == 'Sold Out' ? 'pointer-events-none opacity-50' : '' ?>">
                                <?= $item['status'] == 'Sold Out' ? 'Habis Terjual' : 'Lihat Detail & Pesan' ?>
                            </a>
                        </div>
                    </div>
                </div>

                <?php endforeach; ?>
            </div>
        </section>
        
        <?php endforeach; ?>

    </main>

    <footer class="bg-gray-900 text-gray-400 py-12 text-center mt-10">
        <div class="text-3xl font-serif text-white mb-4">Le Restauration</div>
        <p class="text-sm tracking-widest uppercase mb-6">Rasakan Kemewahan dalam Setiap Gigitan</p>
        <div class="flex justify-center gap-6 mb-8">
            <span class="hover:text-white cursor-pointer transition">Instagram</span>
            <span class="hover:text-white cursor-pointer transition">TripAdvisor</span>
            <span class="hover:text-white cursor-pointer transition">Reservasi</span>
        </div>
        <p class="text-xs">&copy; <?= date('Y') ?> Le Restauration. All rights reserved.</p>
    </footer>

</body>
</html>