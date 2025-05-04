<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Collections - Elixir du Temps | Montres de Luxe et Haute Horlogerie";
$pageDescription = "Découvrez les collections exclusives de montres de luxe par Elixir du Temps - Classic, Sport, Prestige et Éditions Limitées.";

// CSS spécifique à cette page
$additionalCss = '
<link rel="stylesheet" href="../../assets/css/collections-list.css">
<link rel="stylesheet" href="../../assets/css/collections.css">
';

// Inclure le header
include($relativePath . '/includes/header.php');
?>

<!-- Video Background -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline>
        <source src="../../assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="../../assets/img/collections/collections-hero.jpg" alt="Nos Collections" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
</div>

<!-- Hero Content -->
<section class="hero">
    <div class="hero-content">
        <h1 class="hero-title">Nos Collections</h1>
        <p class="hero-subtitle">Découvrez l'excellence horlogère à travers nos collections exclusives</p>
    </div>
</section>

<!-- Collections Grid -->
<section class="featured-collections">
    <div class="collections-grid">
        <!-- Collection Classic -->
        <div class="collection-card">
            <div class="collection-image">
                <img src="../../assets/img/products/collection_classique.JPG" alt="Collection Classic" loading="lazy">
            </div>
            <div class="collection-info">
                <h2>Collection Classic</h2>
                <p>L'élégance intemporelle dans sa forme la plus pure</p>
                <span class="price-range">À partir de 8 500 €</span>
                <a href="Collection-Classic.php" class="explore-button">Explorer la collection</a>
            </div>
        </div>

        <!-- Collection Sport -->
        <div class="collection-card">
            <div class="collection-image">
                <img src="../../assets/img/products/collection-sport.png" alt="Collection Sport" loading="lazy">
            </div>
            <div class="collection-info">
                <h2>Collection Sport</h2>
                <p>Performance et style pour les aventuriers urbains</p>
                <span class="price-range">À partir de 12 000 €</span>
                <a href="Collection-Sport.php" class="explore-button">Explorer la collection</a>
            </div>
        </div>

        <!-- Collection Prestige -->
        <div class="collection-card">
            <div class="collection-image">
                <img src="../../assets/img/products/collection-prestige.JPG" alt="Collection Prestige" loading="lazy">
            </div>
            <div class="collection-info">
                <h2>Collection Prestige</h2>
                <p>Le summum du luxe et de la sophistication</p>
                <span class="price-range">À partir de 25 000 €</span>
                <a href="Collection-Prestige.php" class="explore-button">Explorer la collection</a>
            </div>
        </div>

        <!-- Collection Limited Edition -->
        <div class="collection-card">
            <div class="collection-image">
                <img src="../../assets/img/products/edition-limit.jpg" alt="Collection Limited Edition" loading="lazy">
            </div>
            <div class="collection-info">
                <h2>Éditions Limitées</h2>
                <p>Des pièces uniques pour les collectionneurs avertis</p>
                <span class="price-range">À partir de 50 000 €</span>
                <a href="Collection-Limited-Edition.php" class="explore-button">Explorer la collection</a>
            </div>
        </div>
    </div>
</section>

<?php
// Inclure le footer
include($relativePath . '/includes/footer.php');
?>

<!-- Scripts spécifiques à la page -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fix pour le problème de fondu blanc
    document.body.classList.add('video-loaded');
    
    // Animation des cartes de collection au scroll
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.collection-card').forEach(card => {
        observer.observe(card);
        card.classList.add('fade-in');
    });
});
</script>

<!-- Importation des fichiers JS modulaires (une seule fois chacun) -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
</body>
</html>