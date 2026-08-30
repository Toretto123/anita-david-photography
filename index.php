<?php
/**
 * Anita & Dávid Photography - Főoldal
 */
session_start();
require_once 'config.php';

// Albumok lekérése
$album_filter = isset($_GET['album']) ? (int)$_GET['album'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$query = 'SELECT * FROM albums WHERE is_visible = TRUE ORDER BY display_order ASC';
$stmt = $pdo->prepare($query);
$stmt->execute();
$albums = $stmt->fetchAll();

// Fotók lekérése
$photo_query = 'SELECT p.*, a.name as album_name FROM photos p 
                LEFT JOIN albums a ON p.album_id = a.id 
                WHERE a.is_visible = TRUE';

if ($album_filter > 0) {
    $photo_query .= ' AND p.album_id = ' . $album_filter;
}

if (!empty($search)) {
    $photo_query .= ' AND (p.title LIKE :search OR p.location LIKE :search)';
}

$photo_query .= ' ORDER BY p.display_order ASC, p.created_at DESC LIMIT 1000';

$stmt = $pdo->prepare($photo_query);
if (!empty($search)) {
    $stmt->execute([':search' => '%' . $search . '%']);
} else {
    $stmt->execute();
}
$photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h(SITE_TITLE); ?> - Esküvői, portré és eseményfotózás</title>
    <meta name="description" content="Anita & Dávid Photography - Profi fotózási szolgáltatások esküvőkhöz, portrékhoz és eseményekhez.">
    <meta name="keywords" content="fotózás, esküvői fotó, portré, eseményfotózás, Budapest">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/lightbox.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navigáció -->
    <nav class="navbar">
        <div class="container">
            <div class="logo">
                <h1><?php echo h(SITE_TITLE); ?></h1>
            </div>
            <div class="nav-search">
                <form method="GET" class="search-form">
                    <input type="text" name="search" placeholder="Keresés..." value="<?php echo h($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
            <div class="nav-links">
                <a href="index.php" class="active">Galeria</a>
                <a href="#kapcsolat">Kapcsolat</a>
                <a href="admin/login.php" class="admin-link">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Hero banner -->
    <section class="hero">
        <div class="hero-content">
            <h1>Anita & Dávid Photography</h1>
            <p>Az élet szép pillanatai megörökítve</p>
        </div>
    </section>

    <!-- Albumok szűrő -->
    <section class="filters">
        <div class="container">
            <h2>Albumok</h2>
            <div class="album-filter">
                <a href="index.php" class="filter-btn <?php echo $album_filter === 0 ? 'active' : ''; ?>">
                    Összes
                </a>
                <?php foreach ($albums as $album): ?>
                    <a href="?album=<?php echo $album['id']; ?>" 
                       class="filter-btn <?php echo $album_filter === $album['id'] ? 'active' : ''; ?>">
                        <?php echo h($album['name']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Fotók galeria -->
    <section class="gallery">
        <div class="container">
            <?php if (empty($photos)): ?>
                <div class="no-photos">
                    <p>Nincsenek fotók a kiválasztott album-hoz.</p>
                </div>
            <?php else: ?>
                <div class="photo-grid">
                    <?php foreach ($photos as $photo): ?>
                        <div class="photo-card">
                            <a href="<?php echo UPLOAD_URL . h($photo['filename']); ?>" 
                               class="lightbox" 
                               data-lightbox="gallery"
                               data-title="<?php echo h($photo['title']); ?>">
                                <img src="<?php echo UPLOAD_URL . 'thumbnails/' . h($photo['thumbnail_filename']); ?>" 
                                     alt="<?php echo h($photo['title']); ?>" 
                                     loading="lazy">
                                <div class="photo-overlay">
                                    <div class="photo-info">
                                        <h3><?php echo h($photo['title']); ?></h3>
                                        <?php if (!empty($photo['location'])): ?>
                                            <p class="location"><i class="fas fa-map-marker-alt"></i> <?php echo h($photo['location']); ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($photo['photo_date'])): ?>
                                            <p class="date"><i class="fas fa-calendar"></i> <?php echo date('Y. m. d.', strtotime($photo['photo_date'])); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Kapcsolat -->
    <section id="kapcsolat" class="contact">
        <div class="container">
            <h2>Kapcsolat</h2>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <p>Email: <a href="mailto:info@anita-david.photography">info@anita-david.photography</a></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <p>Telefon: <a href="tel:+36201234567">+36 20 123 4567</a></p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Város: Budapest, Magyarország</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Anita & Dávid Photography. Minden jog fenntartva.</p>
            <div class="social-links">
                <a href="#" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </footer>

    <script src="assets/js/lightbox.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
