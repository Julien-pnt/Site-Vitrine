<?php
// Configuration des variables pour le header
$relativePath = "..";
$pageTitle = "Elixir du Temps | À propos de notre maison horlogère";
$pageDescription = "Découvrez l'histoire, les valeurs et l'engagement d'Elixir du Temps, marque horlogère de luxe fondée en 1985 à Genève.";

// CSS spécifique à la page
$additionalCss = '
<link rel="stylesheet" href="'.$relativePath.'/assets/css/about.css">
';

// Code supplémentaire dans le head
$additionalHead = '
<!-- Meta tags SEO -->
<meta name="keywords" content="Elixir du Temps, horlogerie, montres de luxe, histoire horlogère, Genève, montres suisses">
<meta name="author" content="Elixir du Temps">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="Elixir du Temps - À propos de notre maison horlogère">
<meta property="og:description" content="Découvrez l\'histoire, les valeurs et l\'engagement d\'Elixir du Temps, marque horlogère de luxe fondée en 1985 à Genève.">
<meta property="og:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
<meta property="og:url" content="https://elixirdutemps.com/a-propos">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="Elixir du Temps - À propos">
<meta property="twitter:description" content="Découvrez l\'histoire, les valeurs et l\'engagement d\'Elixir du Temps, marque horlogère de luxe fondée en 1985 à Genève.">
<meta property="twitter:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
';

// Important: Définir la valeur correcte pour que le menu fonctionne
$currentPage = "APropos.php";
$headerClass = "dark-header fixed-header"; // Ajout de classes pour styliser le header directement

// Inclusion du header
require_once "../Includes/header.php";
?>

<style>
    /* Styles globaux améliorés */
    :root {
        --gold-primary: #d4af37;
        --gold-light: #e6c863;
        --gold-dark: #b7922a;
        --dark-1: #111111;
        --dark-2: #222222;
        --dark-3: #333333;
        --gray-1: #666666;
        --gray-2: #999999;
        --light-1: #f9f9f9;
        --light-2: #ffffff;
        --shadow-sm: 0 5px 15px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 10px 25px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 15px 35px rgba(0, 0, 0, 0.15);
        --transition-fast: 0.3s ease;
        --transition-med: 0.5s ease;
        --font-playfair: 'Playfair Display', serif;
        --font-raleway: 'Raleway', sans-serif;
        --radius-sm: 4px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --spacing-xs: 5px;
        --spacing-sm: 10px;
        --spacing-md: 20px;
        --spacing-lg: 40px;
        --spacing-xl: 80px;
    }

    /* Animation keyframes */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    @keyframes slideInRight {
        from { opacity: 0; transform: translateX(50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes slideInLeft {
        from { opacity: 0; transform: translateX(-50px); }
        to { opacity: 1; transform: translateX(0); }
    }
    
    @keyframes scaleIn {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    
    @keyframes shimmer {
        0% { background-position: -100% 0; }
        100% { background-position: 200% 0; }
    }
    
    /* Hero section améliorée */
    .video-background {
        position: relative;
        height: 85vh;
        overflow: hidden;
        margin-top: 0;
    }
    
    .video-bg {
        position: absolute;
        top: 50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        transform: translateX(-50%) translateY(-50%);
        z-index: -1;
        object-fit: cover;
    }
    
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(to bottom, 
            rgba(0,0,0,0.4) 0%, 
            rgba(0,0,0,0.6) 50%, 
            rgba(17,17,17,0.9) 100%);
        z-index: 0;
    }
    
    .hero {
        position: relative;
        height: 85vh;
        display: flex;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: white;
        z-index: 1;
        padding-top: 60px;
    }
    
    .hero-content {
        max-width: 900px;
        padding: 0 var(--spacing-md);
        z-index: 2;
        animation: fadeIn 1.5s ease;
    }
    
    .hero-title {
        font-family: var(--font-playfair);
        font-size: 4rem;
        font-weight: 700;
        margin-bottom: var(--spacing-md);
        line-height: 1.2;
        text-shadow: 0 4px 12px rgba(0, 0, 0, 0.5);
        background: linear-gradient(120deg, #ffffff, var(--gold-light), #ffffff);
        background-size: 200% auto;
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: shimmer 5s infinite linear;
    }
    
    .hero-subtitle {
        font-size: 1.5rem;
        font-weight: 300;
        margin-bottom: 0;
        line-height: 1.5;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.5);
        max-width: 700px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Contenu principal amélioré */
    .about-content {
        background-color: var(--light-2);
        position: relative;
        z-index: 2;
    }
    
    .about-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: var(--spacing-xl) var(--spacing-md);
    }
    
    /* Sections améliorées */
    .about-section, 
    .mission-section, 
    .history-section, 
    .values-section, 
    .collection-section, 
    .sustainability-section {
        margin-bottom: var(--spacing-xl);
        opacity: 0;
        transform: translateY(30px);
        transition: opacity 1s ease, transform 1s ease;
    }
    
    .section-visible {
        opacity: 1;
        transform: translateY(0);
    }
    
    .section-header {
        text-align: center;
        margin-bottom: var(--spacing-lg);
    }
    
    .section-header h2 {
        font-family: var(--font-playfair);
        font-size: 3rem;
        color: var(--dark-2);
        margin-bottom: var(--spacing-md);
        position: relative;
        display: inline-block;
        padding-bottom: var(--spacing-md);
    }
    
    .section-header h2:after {
        content: "";
        position: absolute;
        left: 50%;
        bottom: 0;
        width: 100px;
        height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold-primary), transparent);
        transform: translateX(-50%);
    }
    
    .section-header p {
        color: var(--gray-1);
        font-size: 1.2rem;
        max-width: 800px;
        margin: 0 auto;
        line-height: 1.6;
    }
    
    .about-section p, 
    .mission-section p, 
    .collection-section p, 
    .sustainability-section p {
        color: var(--gray-1);
        font-size: 1.1rem;
        line-height: 1.8;
        margin-bottom: var(--spacing-md);
        max-width: 900px;
        margin-left: auto;
        margin-right: auto;
    }
    
    /* Images améliorées */
    .about-image, .sustainability-image {
        margin: var(--spacing-lg) 0;
        border-radius: var(--radius-md);
        overflow: hidden;
        box-shadow: var(--shadow-lg);
        position: relative;
    }
    
    .about-image:before, .sustainability-image:before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(to right, rgba(212, 175, 55, 0.2), transparent, rgba(212, 175, 55, 0.2));
        opacity: 0;
        z-index: 1;
        transition: opacity var(--transition-med);
    }
    
    .about-image:hover:before, .sustainability-image:hover:before {
        opacity: 1;
    }
    
    .about-image img, .sustainability-image img {
        width: 100%;
        height: auto;
        display: block;
        transition: transform 1s cubic-bezier(0.165, 0.84, 0.44, 1);
        max-height: 600px;
        object-fit: cover;
    }
    
    .about-image:hover img, .sustainability-image:hover img {
        transform: scale(1.05);
    }
    
    /* Timeline complètement refaite */
    .timeline {
        position: relative;
        max-width: 1000px;
        margin: var(--spacing-xl) auto;
        padding: 0;
    }
    
    .timeline::before {
        content: '';
        position: absolute;
        width: 4px;
        background: linear-gradient(to bottom, var(--gold-primary), var(--gold-primary));
        top: 40px;
        bottom: 40px;
        left: 50%;
        margin-left: -2px;
        border-radius: 4px;
        z-index: 1;
    }
    
    .timeline-item {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: var(--spacing-xl);
        position: relative;
    }
    
    .timeline-item.left {
        flex-direction: row;
    }
    
    .timeline-item.right {
        flex-direction: row-reverse;
    }
    
    .timeline-date {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, var(--gold-primary), var(--gold-dark));
        color: var(--light-2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.3rem;
        box-shadow: 0 0 0 4px white, 0 0 0 8px rgba(212, 175, 55, 0.3), var(--shadow-md);
    }
    
    .timeline-content {
        width: 45%;
        background: var(--light-1);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-md);
        border-radius: var(--radius-md);
        transition: transform var(--transition-fast), box-shadow var(--transition-fast);
    }
    
    .timeline-content:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }
    
    /* Media queries pour le responsive */
    @media (max-width: 992px) {
        .timeline-item {
            flex-direction: row-reverse;
        }
        
        .timeline-item.left {
            flex-direction: row-reverse;
        }
        
        .timeline::before {
            left: 40px;
        }
        
        .timeline-date {
            width: 70px;
            height: 70px;
            font-size: 1.1rem;
            margin-right: 20px;
        }
        
        .timeline-content {
            width: calc(100% - 110px);
            margin-left: 0;
        }
    }
    
    @media (max-width: 576px) {
        .timeline-date {
            width: 60px;
            height: 60px;
            font-size: 1rem;
        }
        
        .timeline-content {
            width: calc(100% - 90px);
            padding: var(--spacing-md);
        }
    }
</style>

<!-- Video Background -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline>
        <source src="<?php echo $relativePath; ?>/assets/video/background.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo.
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1 class="hero-title">Notre histoire et notre passion</h1>
            <p class="hero-subtitle">Découvrez l'univers d'Elixir du Temps</p>
        </div>
    </section>
</div>

<!-- Historic Content -->
<main class="about-content">
    <div class="about-container">
        <section class="about-section">
            <div class="section-header">
                <h2>Chez Elixir du Temps, le Luxe à travers les Âges</h2>
                <p>L'excellence horlogère depuis 1985</p>
            </div>
            <p>Fondée en 1985 à Genève, Elixir du Temps a vu le jour dans une ville reconnue mondialement pour son héritage horloger. Depuis ses modestes débuts dans un petit atelier niché au cœur de cette ville historique, nous avons redéfini le luxe et l'élégance à travers une collection de montres exceptionnelles. Notre marque incarne l'essence même du raffinement et du savoir-faire horloger.</p>
            
            <div class="about-image">
                <img src="<?php echo $relativePath; ?>/assets/img/layout/atelier1985.png" alt="Notre atelier à Genève" loading="lazy">
            </div>
        </section>

        <section class="mission-section">
            <div class="section-header">
                <h2>Notre Mission</h2>
            </div>
            <p>Elixir du Temps a pour mission de célébrer l'art de l'horlogerie en offrant des montres qui allient technologie de pointe, design innovant et matériaux de la plus haute qualité. Chaque montre que nous créons est une œuvre d'art, conçue pour être à la fois intemporelle et avant-gardiste. Nous croyons que chaque montre doit être un symbole de sophistication et de précision, reflétant la personnalité unique de son propriétaire.</p>
        </section>

        <section class="history-section">
            <div class="section-header">
                <h2>Notre Histoire</h2>
            </div>
            <div class="timeline">
                <div class="timeline-item left">
                    <div class="timeline-date">1985</div>
                    <div class="timeline-content">
                        <h3>Fondation</h3>
                        <p>Création d'Elixir du Temps dans un petit atelier à Genève par nos fondateurs passionnés.</p>
                    </div>
                </div>
                
                <div class="timeline-item right">
                    <div class="timeline-date">1995</div>
                    <div class="timeline-content">
                        <h3>Première Collection Iconique</h3>
                        <p>Lancement de notre collection "Chronos" qui établit notre réputation d'excellence horlogère.</p>
                    </div>
                </div>
                
                <div class="timeline-item left">
                    <div class="timeline-date">2005</div>
                    <div class="timeline-content">
                        <h3>Expansion Internationale</h3>
                        <p>Ouverture de nos premières boutiques à Paris et New York, marquant le début de notre présence mondiale.</p>
                    </div>
                </div>
                
                <div class="timeline-item right">
                    <div class="timeline-date">2015</div>
                    <div class="timeline-content">
                        <h3>Innovation Technologique</h3>
                        <p>Introduction de notre mouvement breveté "Tempus Precision" combinant tradition et technologie de pointe.</p>
                    </div>
                </div>
                
                <div class="timeline-item left">
                    <div class="timeline-date">2025</div>
                    <div class="timeline-content">
                        <h3>Aujourd'hui</h3>
                        <p>Elixir du Temps est présent dans 25 pays avec une gamme de collections qui allient artisanat traditionnel et design contemporain.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="values-section">
            <div class="section-header">
                <h2>Nos Valeurs</h2>
            </div>
            <div class="values-cards">
                <div class="value-card">
                    <div class="value-icon">
                        <!-- SVG Excellence inline -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="8" r="7"></circle>
                          <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                    </div>
                    <h3>Excellence</h3>
                    <p>Nous nous engageons à offrir des montres d'une qualité irréprochable, en utilisant les meilleurs matériaux et en appliquant des standards de fabrication rigoureux.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <!-- SVG Innovation inline -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M9 18v3"></path>
                          <path d="M15 18v3"></path>
                          <path d="M12 22v-4"></path>
                          <path d="M10 2v4"></path>
                          <path d="M14 2v4"></path>
                          <path d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"></path>
                          <path d="M2.27 13.73a10.97 10.97 0 0 1 0-15.46"></path>
                          <path d="M21.73 13.73a10.97 10.97 0 0 0 0-15.46"></path>
                        </svg>
                    </div>
                    <h3>Innovation</h3>
                    <p>En fusionnant tradition et modernité, nous repoussons les limites de la créativité et de la technologie horlogère.</p>
                </div>
                <div class="value-card">
                    <div class="value-icon">
                        <!-- SVG Élégance inline -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#d4af37" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"></circle>
                          <circle cx="12" cy="12" r="6"></circle>
                          <circle cx="12" cy="12" r="2"></circle>
                          <line x1="12" y1="2" x2="12" y2="4"></line>
                          <line x1="12" y1="20" x2="12" y2="22"></line>
                          <line x1="2" y1="12" x2="4" y2="12"></line>
                          <line x1="20" y1="12" x2="22" y2="12"></line>
                        </svg>
                    </div>
                    <h3>Élégance</h3>
                    <p>Chaque montre est conçue pour être une expression de l'élégance intemporelle, avec un souci du détail qui en fait une pièce unique.</p>
                </div>
            </div>
        </section>

        <section class="collection-section">
            <div class="section-header">
                <h2>Notre Collection</h2>
            </div>
            <p>La collection Elixir du Temps se distingue par ses designs épurés et ses mécanismes sophistiqués. Des montres classiques aux créations contemporaines, chaque modèle est une invitation à découvrir un univers où le temps devient une œuvre d'art. Chaque création est un hommage à notre héritage et à notre engagement envers l'excellence.</p>
            <div class="section-footer">
                <a href="<?php echo $relativePath; ?>/pages/Collections.php" class="btn-outline">Découvrir nos collections</a>
            </div>
        </section>

        <section class="sustainability-section">
            <div class="section-header">
                <h2>Engagement Durable</h2>
            </div>
            <p>Nous sommes également engagés dans une démarche de développement durable, en utilisant des matériaux responsables et en privilégiant des procédés de fabrication respectueux de l'environnement. Découvrez l'univers d'Elixir du Temps et laissez-vous séduire par l'essence de l'élégance.</p>
            <div class="sustainability-image">
                <img src="<?php echo $relativePath; ?>/assets/img/layout/devdurable2.png" alt="Notre engagement durable" loading="lazy">
            </div>
        </section>
    </div>
</main>

<?php
// Inclusion du footer
require_once "../Includes/footer.php";
?>

<!-- Scripts optimisés -->
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js" defer></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js" defer></script>

<!-- Script pour supprimer le loader -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Supprimer le loader
        const loader = document.querySelector('.loader-container');
        if (loader) {
            loader.style.display = 'none';
            loader.remove();
        }
    });
</script>

<!-- Ajouter ce script juste avant la fermeture </body> -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Animation au scroll pour les sections et la timeline
        const sections = document.querySelectorAll('.about-section, .mission-section, .history-section, .values-section, .collection-section, .sustainability-section');
        const timelineItems = document.querySelectorAll('.timeline-item');
        
        // Observer pour les sections
        const sectionObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('section-visible');
                    sectionObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.15
        });
        
        // Observer pour la timeline
        const timelineObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate');
                    timelineObserver.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.2
        });
        
        // Observer chaque section
        sections.forEach(section => {
            sectionObserver.observe(section);
        });
        
        // Observer chaque item de la timeline
        timelineItems.forEach(item => {
            timelineObserver.observe(item);
        });
        
        // Fix pour le menu utilisateur (code existant amélioré)
        const userIconWrapper = document.querySelector('.user-icon-wrapper');
        const userDropdown = document.querySelector('.user-dropdown');
        
        if (userIconWrapper && userDropdown) {
            // Styles pour s'assurer que le menu est visible
            if (userDropdown.parentElement) {
                userDropdown.parentElement.style.zIndex = '9999';
                userDropdown.parentElement.style.position = 'relative';
            }
            
            // Ajuster le style du dropdown
            userDropdown.style.position = 'absolute';
            userDropdown.style.zIndex = '9999';
            
            // Écouteur d'événement pour le clic
            userIconWrapper.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Afficher/masquer avec manipulation directe du style
                if (userDropdown.classList.contains('show')) {
                    userDropdown.classList.remove('show');
                    setTimeout(() => {
                        userDropdown.style.display = 'none';
                    }, 150);
                } else {
                    userDropdown.style.display = 'block';
                    setTimeout(() => {
                        userDropdown.classList.add('show');
                    }, 10);
                }
            });
            
            // Fermer le menu si on clique ailleurs
            document.addEventListener('click', function(e) {
                if (userDropdown.classList.contains('show') && 
                    !userDropdown.contains(e.target) && 
                    !userIconWrapper.contains(e.target)) {
                    userDropdown.classList.remove('show');
                    setTimeout(() => {
                        userDropdown.style.display = 'none';
                    }, 150);
                }
            });
        }
    });
</script>