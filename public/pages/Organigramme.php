<?php
// Configuration des variables pour le header
$relativePath = "..";
$pageTitle = "Organigramme - Elixir du Temps | Notre équipe d'experts";
$pageDescription = "Découvrez l'organigramme d'Elixir du Temps - Notre équipe d'experts passionnés qui créent des montres de luxe d'exception.";

// CSS spécifique à la page (si nécessaire)
$additionalCss = '';

// Code supplémentaire dans le head
$additionalHead = '
<!-- Meta tags SEO -->
<meta name="keywords" content="organigramme, équipe, Elixir du Temps, montres de luxe, horlogerie">
<meta name="author" content="Elixir du Temps">

<!-- Open Graph / Facebook -->
<meta property="og:type" content="website">
<meta property="og:title" content="Organigramme - Elixir du Temps">
<meta property="og:description" content="Découvrez l\'équipe d\'Elixir du Temps, des experts passionnés au service de l\'horlogerie de luxe.">
<meta property="og:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
<meta property="og:url" content="https://elixirdutemps.com/organigramme">

<!-- Twitter -->
<meta property="twitter:card" content="summary_large_image">
<meta property="twitter:title" content="Organigramme - Elixir du Temps">
<meta property="twitter:description" content="Découvrez l\'équipe d\'Elixir du Temps, des experts passionnés au service de l\'horlogerie de luxe.">
<meta property="twitter:image" content="' . $relativePath . '/assets/img/layout/social-share.jpg">
';

// Important: Définir la valeur correcte pour que le menu fonctionne
$currentPage = "Organigramme.php";
$headerClass = "dark-header fixed-header"; // Ajout de classes pour styliser le header

// Inclusion du header
require_once "../Includes/header.php";
?>

<style>
    /* Styles spécifiques à la page organigramme */
    .org-section {
        padding: 60px 0;
        position: relative;
        z-index: 1;
    }
    
    .org-container {
        max-width: 1200px;
        margin: 0 auto;
        background-color: rgba(255, 255, 255, 0.95);
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        position: relative;
    }
    
    .org-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #d4af37, #f5e7ba, #d4af37);
        border-radius: 12px 12px 0 0;
    }
    
    .org-header {
        text-align: center;
        margin-bottom: 50px;
    }
    
    .org-header h1 {
        color: #1a1a1a;
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-family: 'Playfair Display', serif;
        position: relative;
        padding-bottom: 15px;
    }
    
    .org-header h1::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 3px;
        background-color: #d4af37;
    }
    
    .org-header p {
        max-width: 700px;
        margin: 0 auto;
        color: #666;
        font-size: 1.1rem;
        line-height: 1.6;
    }
    
    .organigramme {
        position: relative;
        padding: 30px 0;
    }
    
    .level {
        display: flex;
        justify-content: center;
        margin-bottom: 40px;
        position: relative;
        z-index: 2;
    }
    
    .connector {
        position: relative;
        height: 30px;
        margin-bottom: 10px;
    }
    
    .connector::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        height: 100%;
        width: 2px;
        background: linear-gradient(to bottom, #d4af37, rgba(212, 175, 55, 0.3));
        transform: translateX(-50%);
    }
    
    .node {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.9), rgba(248, 248, 248, 0.9));
        border: 1px solid rgba(212, 175, 55, 0.3);
        border-radius: 10px;
        padding: 20px;
        margin: 0 15px;
        width: 280px;
        text-align: center;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .node:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        border-color: rgba(212, 175, 55, 0.6);
    }
    
    .node::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        height: 3px;
        width: 100%;
        background: linear-gradient(90deg, #d4af37, #f5e7ba);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .node:hover::before {
        opacity: 1;
    }
    
    .node p:first-child {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
        margin-top: 0;
        margin-bottom: 10px;
        font-family: 'Playfair Display', serif;
        position: relative;
        padding-bottom: 10px;
    }
    
    .node p:first-child::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 40px;
        height: 1px;
        background-color: rgba(212, 175, 55, 0.5);
    }
    
    .node .name {
        font-size: 0.95rem;
        color: #666;
        margin: 5px 0;
    }
    
    .directeur {
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.95), rgba(250, 250, 250, 0.95));
        border-color: rgba(212, 175, 55, 0.5);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
        position: relative;
    }
    
    .directeur::before {
        opacity: 1;
        height: 4px;
    }
    
    .sub-node {
        width: 220px;
        padding: 15px;
    }
    
    .fichePoste {
        color: #d4af37;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
        position: relative;
    }
    
    .fichePoste::after {
        content: '\f15c';
        font-family: 'Font Awesome 5 Free';
        margin-left: 5px;
        font-size: 0.8em;
        opacity: 0.8;
    }
    
    .fichePoste:hover {
        color: #b7941e;
        text-decoration: underline;
    }
    
    .org-legend {
        background-color: rgba(212, 175, 55, 0.08);
        border-radius: 8px;
        padding: 15px 20px;
        margin-top: 40px;
        text-align: center;
        border-left: 4px solid #d4af37;
    }
    
    .org-legend p {
        margin: 5px 0;
        font-size: 0.9rem;
        color: #666;
    }
    
    .org-legend strong {
        color: #333;
    }
    
    /* Responsive */
    @media (max-width: 1200px) {
        .org-container {
            max-width: 95%;
            padding: 30px;
        }
    }
    
    @media (max-width: 992px) {
        .level {
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .node {
            margin-bottom: 20px;
        }
    }
    
    @media (max-width: 768px) {
        .org-container {
            padding: 20px;
        }
        
        .org-header h1 {
            font-size: 2rem;
        }
        
        .node {
            width: 100%;
            max-width: 280px;
        }
        
        .sub-node {
            width: 100%;
            max-width: 280px;
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

<!-- Organigramme Section -->
<section class="org-section">
    <div class="org-container">
        <div class="org-header">
            <h1>Organigramme</h1>
            <p>Découvrez l'équipe d'Elixir du Temps, des experts passionnés qui œuvrent chaque jour à la création de montres d'exception. Notre structure organisationnelle reflète notre engagement envers l'excellence et la tradition horlogère suisse.</p>
        </div>

        <div class="organigramme">
            <!-- Directeur Général -->
            <div class="level">
                <div class="node directeur">
                    <p><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Directeur-General.pdf" target="_blank" class="fichePoste">Directeur Général</a></p>
                    <p class="name">Julien Pinot</p>
                </div>
            </div>

            <!-- Ligne de connexion -->
            <div class="connector"></div>

            <!-- Niveau 1 : Assistant Directeur et Administration -->
            <div class="level">
                <div class="node">
                    <p><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Assistant-Directeur.pdf" target="_blank" class="fichePoste">Assistant Directeur</a></p>
                    <p class="name">Erica Romaguera</p>
                </div>
                <div class="node">
                    <p><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Administratif.pdf" target="_blank" class="fichePoste">Administration</a></p>
                    <p class="name">Russell Ross</p>
                </div>
            </div>

            <!-- Ligne de connexion -->
            <div class="connector"></div>

            <!-- Niveau 2 : Ventes, Marketing, Finances, Création, Production -->
            <div class="level">
                <div class="node">
                    <p>Ventes</p>
                    <p class="name">Département commercial</p>
                </div>
                <div class="node">
                    <p>Marketing</p>
                    <p class="name">Département marketing</p>
                </div>
                <div class="node">
                    <p>Finances</p>
                    <p class="name">Département financier</p>
                </div>
                <div class="node">
                    <p>Création</p>
                    <p class="name">Département de design</p>
                </div>
                <div class="node">
                    <p>Production</p>
                    <p class="name">Département technique</p>
                </div>
            </div>

            <!-- Ligne de connexion -->
            <div class="connector"></div>

            <!-- Niveau 3 : Équipes -->
            <div class="level">
                <!-- Équipe Ventes -->
                <div class="node sub-node">
                    <p>Équipe des Ventes</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Equipe-des-ventes.pdf" target="_blank" class="fichePoste">Vendeur</a> : Léa Caron</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Responsable-Regional.pdf" target="_blank" class="fichePoste">Responsable Régional </a> : Paul Faure</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Representant-Commercial.pdf" target="_blank" class="fichePoste">Représentants Commerciaux</a> : Lucien Lefèvre</p>
                </div>

                <!-- Équipe Marketing -->
                <div class="node sub-node">
                    <p>Équipe Marketing</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Membre-Equipe-Marketing.pdf" target="_blank" class="fichePoste">Membre</a> : Nina Dubois</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Responsable-Publicité.pdf" target="_blank" class="fichePoste">Responsable Publicité</a> : Alice Moreau</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Responsable-Digital.pdf" target="_blank" class="fichePoste">Responsable Digital</a> : Isabelle Richard</p>
                </div>

                <!-- Équipe Finances -->
                <div class="node sub-node">
                    <p>Équipe Comptable</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Membre-de-l-Equipe-Comptable.pdf" target="_blank" class="fichePoste">Membre</a> : Hugo Fontaine</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Comptable-Senior.pdf" target="_blank" class="fichePoste">Comptable Senior</a> : Léa Caron</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Comptable-Junior.pdf" target="_blank" class="fichePoste">Comptable Junior</a> : Paul Faure</p>
                </div>

                <!-- Équipe Création -->
                <div class="node sub-node">
                    <p>Équipe Créative</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Directeur-Creatif.pdf" target="_blank" class="fichePoste">Directeur Créatif</a> : Julie Marchand</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Responsable-Design.pdf" target="_blank" class="fichePoste">Responsable Design</a> : Alice Moreau</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Responsable-Produit.pdf" target="_blank" class="fichePoste">Responsable Produit</a> : Isabelle Richard</p>
                </div>

                <!-- Équipe Production -->
                <div class="node sub-node">
                    <p>Équipe de Production</p>
                    <p class="name"><a href="<?php echo $relativePath; ?>/assets/Fiches-Postes/fiche-de-poste-Membre-de-l-Equipe-de-Production.pdf" target="_blank" class="fichePoste">Membre</a> : Lucien Lefèvre</p>
                </div>
            </div>
        </div>
        
        <div class="org-legend">
            <p><strong>Note :</strong> Cliquez sur les intitulés de poste pour consulter les fiches de poste détaillées.</p>
            <p>Dernière mise à jour de l'organigramme : avril 2025</p>
        </div>
    </div>
</section>

<?php
// Inclusion du footer
require_once "../Includes/footer.php";
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
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Chercher la vidéo de fond
    const video = document.querySelector('.video-bg');
    
    // Si la vidéo existe
    if (video) {
      // Fonction pour marquer le chargement
      const markAsLoaded = () => {
        document.body.classList.add('video-loaded');
      };
      
      // Event listeners pour différents cas
      video.addEventListener('loadeddata', markAsLoaded);
      video.addEventListener('canplay', markAsLoaded);
      
      // Backup: si la vidéo prend trop de temps, afficher quand même la page après 1.5s
      setTimeout(markAsLoaded, 1500);
    } else {
      // Pas de vidéo trouvée, afficher la page quand même
      document.body.classList.add('video-loaded');
    }
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