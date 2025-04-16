// Scripts interactifs pour la page
document.addEventListener('DOMContentLoaded', function() {
    // Gestion du header sticky
    const header = document.querySelector('.site-header');
    window.addEventListener('scroll', function() {
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
    
    // Gestion des filtres de genre
    const genderToggles = document.querySelectorAll('.gender-toggle');
    genderToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            genderToggles.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            // Code de filtrage ici...
        });
    });
    
    // Gestion du modal d'aperçu rapide
    const quickViewBtns = document.querySelectorAll('.quick-view-btn');
    const modal = document.getElementById('quickViewModal');
    const closeModalBtn = document.querySelector('.close-modal');
    
    quickViewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = this.getAttribute('data-product');
            // Simuler le chargement des données du produit (à remplacer par une vraie API)
            
            // Produit 1
            if(productId === '1') {
                document.getElementById('modalProductTitle').textContent = 'Élégance Éternelle';
                document.getElementById('modalProductCollection').textContent = 'Collection Classic';
                document.getElementById('modalProductPrice').textContent = '18 500 €';
                document.getElementById('modalProductImage').src = '../../assets/img/products/elegance-eternelle.jpg';
                document.getElementById('modalProductDescription').textContent = 'Cette montre d\'exception incarne l\'élégance à l\'état pur. Son boîtier en or rose 18 carats et son cadran guilloché à la main en font une pièce unique. Le mouvement mécanique à remontage automatique offre une réserve de marche de 70 heures. Étanche à 50 mètres.';
                document.getElementById('modalProductLink').href = 'product-detail.php?id=101';
            }
            
            // Produit 2
            if(productId === '2') {
                document.getElementById('modalProductTitle').textContent = 'Force Titan';
                document.getElementById('modalProductCollection').textContent = 'Collection Sport';
                document.getElementById('modalProductPrice').textContent = '14 500 €';
                document.getElementById('modalProductImage').src = '../../assets/img/products/force-titan.jpg';
                document.getElementById('modalProductDescription').textContent = 'La Force Titan est une montre sportive ultra-robuste conçue pour les aventuriers. Son boîtier en titane de grade 5 lui confère légèreté et résistance exceptionnelles. Étanche à 300 mètres, elle est équipée d\'une valve à hélium pour la plongée professionnelle. Son mouvement chronographe automatique certifié chronomètre garantit une précision parfaite en toutes conditions.';
                document.getElementById('modalProductLink').href = 'product-detail.php?id=301';
            }
            
            // Afficher le modal
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });
    });
    
    // Fermer le modal
    closeModalBtn.addEventListener('click', function() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });
    
    // Fermer le modal en cliquant à l'extérieur
    window.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });
    
    // Gestion des favoris
    const wishlistBtns = document.querySelectorAll('.add-to-wishlist-btn');
    wishlistBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('active');
            const icon = this.querySelector('i');
            if (this.classList.contains('active')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
            }
        });
    });
});