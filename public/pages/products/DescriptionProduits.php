<?php
// Définir les variables pour le header
$relativePath = "../..";
$pageTitle = "Processus de Fabrication - Elixir du Temps";
$pageDescription = "Découvrez l'expertise artisanale derrière chaque montre Elixir du Temps. Notre processus de fabrication alliant tradition horlogère et innovation technologique.";

// CSS spécifique à cette page - en utilisant des sélecteurs plus spécifiques pour éviter les conflits
$additionalCss = '
<link rel="stylesheet" href="../../assets/css/collections.css">
<style>
    /* Styles spécifiques à la page de description */
    .fabrication-description-section {
        padding: 80px 0;
        position: relative;
        z-index: 1;
    }
    
    .fabrication-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px;
        background-color: rgba(255, 255, 255, 0.95);
        border-radius: 8px;
        box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
    }
    
    .fabrication-container h1 {
        font-size: 2.2rem;
        color: #1a1a1a;
        margin-bottom: 40px;
        text-align: center;
        border-bottom: 2px solid #d4af37;
        padding-bottom: 20px;
    }
    
    .fabrication-container h2 {
        font-size: 1.8rem;
        color: #333;
        margin-top: 40px;
        margin-bottom: 15px;
        position: relative;
        padding-left: 20px;
        cursor: pointer;
    }
    
    .fabrication-container h2:before {
        content: "";
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 8px;
        height: 25px;
        background-color: #d4af37;
    }
    
    .fabrication-container p {
        font-size: 1.1rem;
        line-height: 1.8;
        color: #444;
        margin-bottom: 20px;
        text-align: justify;
    }
    
    /* Styles pour les logos SVG */
    .process-logo {
        width: 100%;
        max-width: 200px;
        height: 200px;
        margin: 30px auto;
        display: block;
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.5s ease, transform 0.5s ease;
    }
    
    .process-logo-container {
        text-align: center;
        padding: 20px;
        background-color: #f9f9f9;
        border-radius: 8px;
        margin: 30px 0;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    
    .process-logo-container svg {
        fill: none;
        stroke: #d4af37;
        stroke-width: 1.5;
        stroke-linecap: round;
        stroke-linejoin: round;
    }
    
    .process-logo-container svg circle,
    .process-logo-container svg rect,
    .process-logo-container svg path,
    .process-logo-container svg line,
    .process-logo-container svg polyline {
        transition: all 0.3s ease;
    }
    
    .process-logo-container:hover svg {
        stroke: #9f8427;
    }
    
    .process-logo-title {
        font-size: 1.2rem;
        color: #1a1a1a;
        margin-top: 15px;
        font-weight: 600;
    }
    
    @media (max-width: 768px) {
        .fabrication-container {
            padding: 25px;
        }
        
        .fabrication-container h1 {
            font-size: 1.8rem;
        }
        
        .fabrication-container h2 {
            font-size: 1.5rem;
        }
        
        .fabrication-container p {
            font-size: 1rem;
        }
        
        .process-logo {
            height: 150px;
        }
    }
</style>
';

// Inclure le header
include($relativePath . '/includes/header.php');
?>

<!-- Video Background -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline preload="auto">
        <source src="<?php echo $relativePath; ?>/assets/video/background.mp4" type="video/mp4">
        <!-- Fallback image si la vidéo ne se charge pas -->
        <img src="<?php echo $relativePath; ?>/assets/img/fabrication-bg.jpg" alt="Fabrication des montres" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
    
    <!-- Hero content sur la vidéo -->
    <div class="collection-hero">
        <div class="collection-hero-content">
            <h1 class="collection-title">L'Art de la Haute Horlogerie</h1>
            <p class="collection-description">Découvrez l'expertise et la passion qui sont au cœur de chaque création Elixir du Temps.</p>
        </div>
    </div>
</div>

<!-- Description Content - Classes renommées pour éviter les conflits -->
<section class="fabrication-description-section">
    <div class="fabrication-container">
        <h1>Le fruit d'un savoir-faire horloger exceptionnel</h1>

        <h2>1. Conception et Design</h2>
        <p>Le processus commence par une phase de conception approfondie. Nos designers travaillent en étroite collaboration avec nos maîtres horlogers pour créer des modèles uniques qui allient esthétique et fonctionnalité. Chaque design est dessiné avec précision, prenant en compte les moindres détails et les proportions parfaites.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="35" stroke-width="2" />
                <circle cx="50" cy="50" r="5" stroke-width="2" />
                <line x1="50" y1="15" x2="50" y2="25" stroke-width="2" />
                <line x1="50" y1="75" x2="50" y2="85" stroke-width="2" />
                <line x1="15" y1="50" x2="25" y2="50" stroke-width="2" />
                <line x1="75" y1="50" x2="85" y2="50" stroke-width="2" />
                <line x1="26" y1="26" x2="33" y2="33" stroke-width="2" />
                <line x1="26" y1="74" x2="33" y2="67" stroke-width="2" />
                <line x1="74" y1="26" x2="67" y2="33" stroke-width="2" />
                <line x1="74" y1="74" x2="67" y2="67" stroke-width="2" />
            </svg>
            <div class="process-logo-title">Conception et Design</div>
        </div>

        <h2>2. Sélection des Matériaux</h2>
        <p>La qualité des matériaux est primordiale pour nous. Nous sélectionnons les meilleurs matériaux disponibles, tels que l'or 18 carats, le platine, l'acier inoxydable et le titane pour les boîtiers. Les bracelets sont confectionnés à partir de cuir véritable ou de matériaux résistants comme le caoutchouc renforcé.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <rect x="25" y="30" width="50" height="40" rx="3" stroke-width="2" />
                <path d="M30 30 L30 20 L70 20 L70 30" stroke-width="2" />
                <rect x="35" y="40" width="30" height="20" rx="2" stroke-width="2" />
                <circle cx="40" cy="25" r="3" stroke-width="2" />
                <circle cx="50" cy="25" r="3" stroke-width="2" />
                <circle cx="60" cy="25" r="3" stroke-width="2" />
                <path d="M35 80 L65 80" stroke-width="2" />
                <path d="M40 70 L40 80" stroke-width="2" />
                <path d="M50 70 L50 80" stroke-width="2" />
                <path d="M60 70 L60 80" stroke-width="2" />
            </svg>
            <div class="process-logo-title">Sélection des Matériaux</div>
        </div>

        <h2>3. Fabrication des Composants</h2>
        <p>Une fois le design finalisé, la fabrication des composants peut commencer. Cela inclut la création des boîtiers, des cadrans, des aiguilles et des mouvements. Nos artisans utilisent des techniques de précision pour usiner et assembler chaque pièce avec une exactitude parfaite.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="30" stroke-width="2" />
                <circle cx="50" cy="50" r="3" stroke-width="2" />
                <path d="M50 25 L50 50" stroke-width="2" />
                <path d="M50 50 L65 40" stroke-width="2" />
                <path d="M30 30 L35 35" stroke-width="2" stroke-dasharray="2 2" />
                <path d="M30 70 L35 65" stroke-width="2" stroke-dasharray="2 2" />
                <path d="M70 30 L65 35" stroke-width="2" stroke-dasharray="2 2" />
                <path d="M70 70 L65 65" stroke-width="2" stroke-dasharray="2 2" />
                <circle cx="50" cy="50" r="35" stroke-width="1" stroke-dasharray="2 2" />
            </svg>
            <div class="process-logo-title">Fabrication des Composants</div>
        </div>

        <h2>4. Assemblage du Mouvement</h2>
        <p>Le cœur de chaque montre est son mouvement. Nos maîtres horlogers assemblent chaque mouvement avec soin, en utilisant des techniques traditionnelles et modernes. Chaque mouvement est réglé et testé pour garantir une précision optimale.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="25" stroke-width="2" />
                <circle cx="50" cy="50" r="3" stroke-width="2" />
                <path d="M40 40 L60 60" stroke-width="2" />
                <path d="M40 60 L60 40" stroke-width="2" />
                <circle cx="35" cy="50" r="5" stroke-width="2" />
                <circle cx="65" cy="50" r="5" stroke-width="2" />
                <circle cx="50" cy="35" r="5" stroke-width="2" />
                <circle cx="50" cy="65" r="5" stroke-width="2" />
                <path d="M30 20 C40 10, 60 10, 70 20" stroke-width="1.5" />
                <path d="M30 80 C40 90, 60 90, 70 80" stroke-width="1.5" />
            </svg>
            <div class="process-logo-title">Assemblage du Mouvement</div>
        </div>

        <h2>5. Montage et Assemblage Final</h2>
        <p>Les différents composants de la montre sont ensuite montés et assemblés. Le cadran est fixé au mouvement, les aiguilles sont installées, et le tout est placé dans le boîtier. Cette étape nécessite une attention minutieuse pour assurer que chaque montre fonctionne parfaitement.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <rect x="30" y="35" width="40" height="50" rx="5" stroke-width="2" />
                <circle cx="50" cy="50" r="15" stroke-width="2" />
                <path d="M50 40 L50 50" stroke-width="2" />
                <path d="M50 50 L60 50" stroke-width="2" />
                <path d="M30 25 L70 25" stroke-width="2" />
                <path d="M35 25 L35 35" stroke-width="2" />
                <path d="M65 25 L65 35" stroke-width="2" />
                <path d="M35 85 L65 85" stroke-width="2" />
            </svg>
            <div class="process-logo-title">Montage et Assemblage Final</div>
        </div>

        <h2>6. Contrôle de Qualité</h2>
        <p>Avant d'être mise sur le marché, chaque montre passe par une série de tests rigoureux. Nous vérifions la précision du mouvement, la résistance à l'eau, et la durabilité des matériaux. Chaque montre est inspectée pour garantir qu'elle répond à nos standards de qualité les plus élevés.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="25" stroke-width="2" />
                <polyline points="35,50 45,60 65,40" stroke-width="3" />
                <path d="M25 25 L35 35" stroke-width="2" />
                <path d="M25 75 L35 65" stroke-width="2" />
                <path d="M75 25 L65 35" stroke-width="2" />
                <path d="M75 75 L65 65" stroke-width="2" />
                <path d="M20 50 L30 50" stroke-width="2" />
                <path d="M70 50 L80 50" stroke-width="2" />
                <path d="M50 20 L50 30" stroke-width="2" />
                <path d="M50 70 L50 80" stroke-width="2" />
            </svg>
            <div class="process-logo-title">Contrôle de Qualité</div>
        </div>

        <h2>7. Personnalisation et Finition</h2>
        <p>Pour les clients qui souhaitent des montres personnalisées, nous offrons des options de gravure et de personnalisation. Chaque montre est polie et nettoyée pour garantir une finition impeccable avant d'être emballée dans un écrin de luxe.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="30" stroke-width="2" />
                <path d="M30 40 L70 40" stroke-width="1.5" />
                <path d="M30 50 L70 50" stroke-width="1.5" />
                <path d="M30 60 L70 60" stroke-width="1.5" />
                <path d="M35 30 L65 30" stroke-width="1" />
                <path d="M35 70 L65 70" stroke-width="1" />
                <path d="M30 40 Q40 35, 50 40 Q60 45, 70 40" stroke-width="1.5" />
                <path d="M30 50 Q40 55, 50 50 Q60 45, 70 50" stroke-width="1.5" />
                <path d="M30 60 Q40 55, 50 60 Q60 65, 70 60" stroke-width="1.5" />
                <circle cx="50" cy="50" r="5" stroke-width="2" />
            </svg>
            <div class="process-logo-title">Personnalisation et Finition</div>
        </div>

        <h2>8. Engagement Durable</h2>
        <p>Dans notre démarche de développement durable, nous nous assurons que tous les procédés de fabrication respectent l'environnement. Nous utilisons des matériaux responsables et privilégions des techniques de fabrication écologiques.</p>
        
        <div class="process-logo-container">
            <svg class="process-logo" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
                <circle cx="50" cy="50" r="20" stroke-width="2" />
                <path d="M50 20 C65 25, 75 40, 70 55 C60 75, 30 80, 20 60 C10 35, 35 15, 50 20" stroke-width="2" />
                <path d="M50 30 L50 50" stroke-width="2" />
                <path d="M50 50 L60 40" stroke-width="2" />
                <path d="M30 70 Q40 80, 50 75 Q60 70, 70 75" stroke-width="1.5" />
                <path d="M25 50 L75 50" stroke-width="1" stroke-dasharray="2 2" />
                <path d="M50 25 L50 75" stroke-width="1" stroke-dasharray="2 2" />
            </svg>
            <div class="process-logo-title">Engagement Durable</div>
        </div>
    </div>
</section>

<?php
// Inclure le footer
include($relativePath . '/includes/footer.php');
?>

<!-- Importation des fichiers JS modulaires (une seule fois chacun) -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation de défilement fluide pour les titres de section
    const sections = document.querySelectorAll('.fabrication-container h2');
    
    sections.forEach(section => {
        section.addEventListener('click', function() {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
    
    // Animation d'apparition des logos au défilement
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = 1;
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.process-logo').forEach(logo => {
        observer.observe(logo);
    });
});
</script>