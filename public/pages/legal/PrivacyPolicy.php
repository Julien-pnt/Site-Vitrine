<?php
// Configuration des variables pour le header
$relativePath = "../..";
$pageTitle = "Politique de confidentialité - Elixir du Temps";
$pageDescription = "Découvrez comment Elixir du Temps protège vos données personnelles et respecte votre vie privée.";

// CSS spécifique à la page (à inclure directement dans la variable additionalCss)
$additionalCss = '';

// Code supplémentaire dans le head
$additionalHead = '
<!-- Meta tags SEO -->
<meta name="keywords" content="politique de confidentialité, protection des données, Elixir du Temps, montres de luxe">
<meta name="author" content="Elixir du Temps">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="Politique de confidentialité - Elixir du Temps">
<meta property="og:description" content="Découvrez comment Elixir du Temps protège vos données personnelles et respecte votre vie privée.">
<meta property="og:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
<meta property="og:url" content="https://elixirdutemps.com/pages/legal/politique-de-confidentialite">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="Politique de confidentialité - Elixir du Temps">
<meta property="twitter:description" content="Découvrez comment Elixir du Temps protège vos données personnelles et respecte votre vie privée.">
<meta property="twitter:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
';

// Important: Définir la valeur correcte pour que le menu fonctionne
$currentPage = "legal/PrivacyPolicy.php";
$headerClass = "dark-header fixed-header"; // Ajout de classes pour styliser le header

// Inclusion du header
require_once "../../Includes/header.php";
?>

<style>
    /* Styles spécifiques à la page de politique de confidentialité */
    .privacy-policy {
        padding: 60px 0;
        position: relative;
        z-index: 1;
    }
    
    .privacy-container {
        max-width: 1000px;
        margin: 0 auto;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        position: relative;
    }
    
    .privacy-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #d4af37, #f5e7ba, #d4af37);
        border-radius: 12px 12px 0 0;
    }
    
    .privacy-container h1 {
        color: #1a1a1a;
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-family: 'Playfair Display', serif;
        text-align: center;
        position: relative;
        padding-bottom: 15px;
    }
    
    .privacy-container h1::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background-color: #d4af37;
    }
    
    .privacy-container p strong {
        color: #666;
        font-weight: 600;
    }
    
    .privacy-container > p:nth-child(2),
    .privacy-container > p:nth-child(3) {
        text-align: center;
        margin-bottom: 5px;
    }
    
    .privacy-container > p:nth-child(4) {
        margin-top: 30px;
        margin-bottom: 40px;
        font-size: 1.1rem;
        line-height: 1.6;
        color: #555;
        border-left: 3px solid #d4af37;
        padding-left: 20px;
        font-style: italic;
    }
    
    .privacy-nav {
        background-color: rgba(212, 175, 55, 0.08);
        padding: 20px 30px;
        border-radius: 8px;
        margin: 30px 0;
        border-left: 4px solid #d4af37;
    }
    
    .privacy-nav h3 {
        margin-top: 0;
        color: #333;
        font-size: 1.2rem;
    }
    
    .privacy-nav ul {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        list-style: none;
        padding: 0;
        margin: 15px 0 0;
    }
    
    .privacy-nav li a {
        display: inline-block;
        text-decoration: none;
        color: #333;
        font-size: 0.95rem;
        padding: 8px 15px;
        border-radius: 20px;
        background: rgba(212, 175, 55, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(212, 175, 55, 0.3);
    }
    
    .privacy-nav li a:hover {
        background-color: #d4af37;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(212, 175, 55, 0.25);
    }
    
    .privacy-container h2 {
        color: #333;
        font-size: 1.5rem;
        margin-top: 40px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        font-family: 'Playfair Display', serif;
        border-bottom: 1px solid #eee;
        padding-bottom: 10px;
    }
    
    .privacy-container h2 i {
        margin-right: 12px;
        color: #d4af37;
        font-size: 1.6rem;
    }
    
    .privacy-container ul {
        padding-left: 20px;
    }
    
    .privacy-container ul li {
        margin-bottom: 10px;
        position: relative;
        padding-left: 5px;
    }
    
    .privacy-container ul li::marker {
        color: #d4af37;
        font-size: 1.1em;
    }
    
    .privacy-container strong {
        color: #333;
    }
    
    .contact-cta {
        background: rgba(212, 175, 55, 0.08);
        border-radius: 8px;
        padding: 25px;
        margin-top: 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .contact-cta::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('<?php echo $relativePath; ?>/assets/img/layout/pattern.png');
        opacity: 0.05;
        z-index: -1;
    }
    
    .contact-cta p {
        margin-bottom: 20px;
        font-size: 1.1rem;
    }
    
    .contact-button {
        display: inline-block;
        background: linear-gradient(135deg, #d4af37 0%, #f5e7ba 50%, #d4af37 100%);
        background-size: 200% auto;
        color: white;
        font-weight: 600;
        padding: 12px 25px;
        border-radius: 50px;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
        border: none;
        cursor: pointer;
    }
    
    .contact-button:hover {
        background-position: right center;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }
    
    .section-divider {
        display: flex;
        align-items: center;
        margin: 40px 0;
    }
    
    .divider-line {
        flex-grow: 1;
        height: 1px;
        background-color: #eee;
    }
    
    .divider-icon {
        margin: 0 20px;
        color: #d4af37;
        font-size: 1.2rem;
    }
    
    @media (max-width: 768px) {
        .privacy-policy {
            padding: 30px 15px;
        }
        
        .privacy-container {
            padding: 30px 20px;
        }
        
        .privacy-nav ul {
            flex-direction: column;
            gap: 8px;
        }
        
        .privacy-nav li a {
            display: block;
        }
        
        .privacy-container h1 {
            font-size: 1.8rem;
        }
        
        .privacy-container h2 {
            font-size: 1.3rem;
        }
    }
</style>

<!-- Video Background -->
<div class="video-background">
    <video class="video-bg" autoplay muted loop playsinline>
        <source src="<?php echo $relativePath; ?>/assets/video/background.mp4" type="video/mp4">
        <img src="<?php echo $relativePath; ?>/assets/img/layout/fallback-bg.jpg" alt="Arrière-plan" class="fallback-img">
    </video>
    <div class="video-overlay"></div>
</div>

<!-- Privacy Policy Content -->
<section class="privacy-policy">
    <div class="privacy-container">
        <h1>Politique de Confidentialité</h1>
        <p><strong>Elixir du Temps</strong></p>
        <p><strong>Dernière mise à jour : 01/02/2025</strong></p>

        <p>Chez Elixir du Temps, nous accordons une grande importance à la protection de vos données personnelles. Cette politique de confidentialité décrit comment nous collectons, utilisons, partageons et protégeons vos informations lorsque vous utilisez nos services et interagissez avec notre site web.</p>

        <div class="privacy-nav">
            <h3>Sommaire</h3>
            <ul>
                <li><a href="#collecte"><i class="fas fa-clipboard-list"></i> Collecte des Informations</a></li>
                <li><a href="#utilisation"><i class="fas fa-tasks"></i> Utilisation des Informations</a></li>
                <li><a href="#partage"><i class="fas fa-share-alt"></i> Partage des Informations</a></li>
                <li><a href="#securite"><i class="fas fa-shield-alt"></i> Sécurité des Informations</a></li>
                <li><a href="#droits"><i class="fas fa-user-shield"></i> Vos Droits</a></li>
                <li><a href="#modifications"><i class="fas fa-edit"></i> Modifications</a></li>
                <li><a href="#contact"><i class="fas fa-envelope"></i> Contact</a></li>
            </ul>
        </div>

        <div id="collecte">
            <h2><i class="fas fa-clipboard-list"></i> 1. Collecte des Informations</h2>
            <p>Nous collectons les informations suivantes :</p>
            <br>
            <ul>
                <li><strong>Informations personnelles</strong> : nom, adresse, adresse e-mail, numéro de téléphone, date de naissance.</li>
                <li><strong>Informations de paiement</strong> : détails de la carte de crédit ou autres informations de paiement.</li>
                <li><strong>Informations de navigation</strong> : adresse IP, type de navigateur, pages visitées, durée de la visite.</li>
            </ul>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-watch"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="utilisation">
            <h2><i class="fas fa-tasks"></i> 2. Utilisation des Informations</h2>
            <p>Nous utilisons vos informations pour :</p>
            <br>
            <ul>
                <li>Traiter vos commandes et gérer vos achats.</li>
                <li>Vous fournir un service client de qualité.</li>
                <li>Améliorer notre site web et nos services.</li>
                <li>Vous envoyer des communications marketing, avec votre consentement.</li>
            </ul>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-gem"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="partage">
            <h2><i class="fas fa-share-alt"></i> 3. Partage des Informations</h2>
            <p>Nous ne partageons vos informations personnelles qu'avec :</p>
            <br>
            <ul>
                <li>Les prestataires de services tiers qui nous aident à traiter les paiements et à livrer les produits.</li>
                <li>Les autorités légales, si nécessaire pour se conformer à la loi.</li>
            </ul>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-clock"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="securite">
            <h2><i class="fas fa-shield-alt"></i> 4. Sécurité des Informations</h2>
            <p>Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles pour protéger vos informations contre tout accès non autorisé, perte ou destruction. Notre équipe informatique surveille constamment nos systèmes pour détecter d'éventuelles vulnérabilités et attaques.</p>
            <p>Toutes vos données sensibles sont cryptées selon les standards de l'industrie pour garantir leur protection optimale.</p>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-crown"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="droits">
            <h2><i class="fas fa-user-shield"></i> 5. Vos Droits</h2>
            <p>Conformément au Règlement Général sur la Protection des Données (RGPD), vous avez le droit de :</p>
            <br>
            <ul>
                <li>Accéder à vos informations personnelles.</li>
                <li>Demander la correction de vos informations.</li>
                <li>Demander la suppression de vos informations.</li>
                <li>Vous opposer au traitement de vos informations à des fins de marketing.</li>
                <li>Demander la limitation du traitement de vos données.</li>
                <li>Recevoir vos données dans un format structuré (portabilité).</li>
            </ul>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-file-signature"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="modifications">
            <h2><i class="fas fa-edit"></i> 6. Modifications de la Politique de Confidentialité</h2>
            <p>Nous nous réservons le droit de modifier cette politique de confidentialité à tout moment. Toute modification sera publiée sur notre site web avec la date de mise à jour. Nous vous encourageons à consulter régulièrement cette politique pour rester informé de nos pratiques en matière de protection des données.</p>
        </div>

        <div class="section-divider">
            <div class="divider-line"></div>
            <div class="divider-icon"><i class="fas fa-envelope"></i></div>
            <div class="divider-line"></div>
        </div>

        <div id="contact">
            <h2><i class="fas fa-envelope"></i> 7. Contact</h2>
            <p>Pour toute question concernant cette politique de confidentialité ou pour exercer vos droits, veuillez nous contacter à : <a href="mailto:contact@elixirdutemps.com">contact@elixirdutemps.com</a></p>
        </div>

        <div class="contact-cta">
            <p>Une question sur vos données personnelles ou nos pratiques de confidentialité ?</p>
            <a href="<?php echo $relativePath; ?>/pages/Contact.php" class="contact-button">Contactez notre équipe</a>
        </div>
    </div>
</section>

<?php
// Inclusion du footer
require_once "../../Includes/footer.php";
?>

<!-- Scripts -->
<!-- Scripts chargés à la fin pour optimiser le chargement -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/collection-sorting.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-detail.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

<!-- Scripts -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Animation de défilement fluide pour les ancres
    document.querySelectorAll('.privacy-nav a').forEach(anchor => {
      anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);
        
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
          });
        }
      });
    });
  });
</script>

<script>
    // Script de diagnostic pour le panier
    document.addEventListener('DOMContentLoaded', function() {
        console.log("Diagnostic du panier : chargement de la page");
        
        const cartIcon = document.querySelector('.cart-icon');
        const cartDropdown = document.querySelector('.cart-dropdown');
        
        console.log("Éléments trouvés:", {
            cartIcon: !!cartIcon,
            cartDropdown: !!cartDropdown
        });
        
        if (cartIcon && cartDropdown) {
            // Ajouter un attribut data pour indiquer que le diagnostic est actif
            cartIcon.setAttribute('data-diagnostic', 'active');
            
            // Gestionnaire d'événement direct
            cartIcon.addEventListener('click', function(e) {
                console.log("CLIC SUR LE PANIER DÉTECTÉ!");
                e.preventDefault();
                e.stopPropagation();
                
                // Force l'affichage en ajoutant un style inline
                if (cartDropdown.classList.contains('show')) {
                    console.log("Masquage du dropdown");
                    cartDropdown.classList.remove('show');
                    cartDropdown.style.display = 'none';
                } else {
                    console.log("Affichage du dropdown");
                    cartDropdown.classList.add('show');
                    cartDropdown.style.display = 'block';
                }
            });
            
            console.log("Gestionnaire d'événement ajouté au panier");
        }
    });
</script>