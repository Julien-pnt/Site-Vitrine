<?php 
// Définir le chemin relatif si non défini
if (!isset($relativePath)) {
    $relativePath = ".."; // Par défaut, remontez d'un niveau
}
?>
<!-- Footer Section -->
<footer class="footer">
    <div class="footer-content">
        <div class="footer-columns">
            <div class="footer-column">
                <h3>Elixir du Temps</h3>
                <p>L'excellence horlogère depuis 1985</p>
                <div class="social-icons">
                    <a href="https://facebook.com/elixirdutemps" target="_blank" rel="noopener" aria-label="Facebook">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                        </svg>
                    </a>
                    <a href="https://instagram.com/elixirdutemps" target="_blank" rel="noopener" aria-label="Instagram">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                        </svg>
                    </a>
                    <a href="https://linkedin.com/company/elixirdutemps" target="_blank" rel="noopener" aria-label="LinkedIn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                            <rect x="2" y="9" width="4" height="12"></rect>
                            <circle cx="4" cy="4" r="2"></circle>
                        </svg>
                    </a>
                </div>
            </div>
            
            <div class="footer-column">
                <h3>Collections</h3>
                <ul>
                    <li><a href="<?php echo $relativePath; ?>/pages/collections/Collection-Classic.php">Classique</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/collections/Collection-Prestige.php">Prestige</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/collections/Collection-Sport.php">Sport</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/collections/Collection-Limited-Edition.php">Édition limitée</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Informations</h3>
                <ul>
                    <!-- Correction des chemins vers les pages -->
                    <li><a href="<?php echo $relativePath; ?>/pages/APropos.php">À propos</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/Contact.php">Contact</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/products/DescriptionProduits.php">Fabrication</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/legal/MentionsLegales.php">Mentions légales</a></li>
                    <li><a href="<?php echo $relativePath; ?>/pages/Organigramme.php">Organigramme</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h3>Contact</h3>
                <address>
                    <p>15 rue de la Paix<br>75002 Paris, France</p>
                    <p>Tél: <a href="tel:+33145887766">+33 (0)1 45 88 77 66</a></p>
                    <p>Email: <a href="mailto:contact@elixirdutemps.com">contact@elixirdutemps.com</a></p>
                </address>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Elixir du Temps. Tous droits réservés.</p>
            <div class="payment-methods">
                <!-- Visa -->
                <svg width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="4" fill="white"/>
                    <path d="M15.6 16.5H12.8L14.6 8.5H17.4L15.6 16.5Z" fill="#1434CB"/>
                    <path d="M24.6 8.7C24 8.5 23 8.2 21.8 8.2C19.2 8.2 17.4 9.5 17.4 11.3C17.4 12.7 18.8 13.4 19.8 13.9C20.8 14.4 21.1 14.7 21.1 15.1C21.1 15.7 20.4 16 19.2 16C18.1 16 17.4 15.8 16.4 15.4L16 15.2L15.6 17.8C16.3 18.1 17.6 18.4 19 18.4C21.8 18.4 23.5 17.1 23.5 15.2C23.5 14.1 22.9 13.3 21.3 12.5C20.3 12 19.7 11.7 19.7 11.3C19.7 10.9 20.1 10.5 21.1 10.5C22 10.5 22.6 10.7 23.1 10.9L23.4 11L23.8 8.5C23.1 8.5 24.6 8.7 24.6 8.7Z" fill="#1434CB"/>
                    <path d="M28.3 14C28.5 13.4 29.2 11.2 29.2 11.2C29.2 11.2 29.3 10.9 29.4 10.7L29.6 11.1C29.6 11.1 30 13.1 30.1 13.9C29.5 13.9 28.5 14 28.3 14ZM31.3 8.5H29.2C28.6 8.5 28.1 8.7 27.9 9.3L23.9 16.5H26.7C26.7 16.5 27.2 15.2 27.3 14.9C27.7 14.9 29.9 14.9 30.4 14.9C30.5 15.3 30.8 16.5 30.8 16.5H33.3L31.3 8.5Z" fill="#1434CB"/>
                    <path d="M11.3 8.5L8.7 14.1L8.4 12.8C7.9 11.2 6.5 9.6 5 8.8L7.4 16.5H10.2L14.3 8.5H11.3Z" fill="#1434CB"/>
                    <path d="M6.3 8.5H2L1.9 8.7C5.1 9.5 7.1 11.7 7.8 14.2L6.8 9.3C6.7 8.7 6.5 8.5 6.3 8.5Z" fill="#F79410"/>
                </svg>

                <!-- Mastercard -->
                <svg width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="4" fill="white"/>
                    <path d="M25 6H15V19H25V6Z" fill="#DFE0E5"/>
                    <path d="M15.8 12.5C15.8 10 17.2 7.8 19.3 6.6C18 5.6 16.4 5 14.7 5C10.5 5 7 8.4 7 12.5C7 16.6 10.5 20 14.7 20C16.4 20 18 19.4 19.3 18.4C17.2 17.2 15.8 15 15.8 12.5Z" fill="#ED0006"/>
                    <path d="M33 12.5C33 16.6 29.5 20 25.3 20C23.6 20 22 19.4 20.7 18.4C22.8 17.2 24.2 15 24.2 12.5C24.2 10 22.8 7.8 20.7 6.6C22 5.6 23.6 5 25.3 5C29.5 5 33 8.4 33 12.5Z" fill="#F9A000"/>
                    <path d="M20.7 6.6C22.8 7.8 24.2 10 24.2 12.5C24.2 15 22.8 17.2 20.7 18.4C18.6 17.2 17.2 15 17.2 12.5C17.2 10 18.6 7.8 20.7 6.6Z" fill="#FF5E00"/>
                </svg>

                <!-- American Express -->
                <svg width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="4" fill="#016FD0"/>
                    <path d="M6 12.5L7.5 9.5H9.5L11 12.5L12.5 9.5H14.5L11.5 14.5H9.5L8 11.5L6.5 14.5H4.5L7.5 9.5H6V12.5Z" fill="white"/>
                    <path d="M13.5 14.5V9.5H19.5V11H15.5V11.5H19V13H15.5V13.5H19.5V14.5H13.5Z" fill="white"/>
                    <path d="M24 9.5H20V14.5H24C25.5 14.5 26.5 13.5 26.5 12C26.5 10.5 25.5 9.5 24 9.5ZM24 13H22V11H24C24.5 11 24.5 13 24 13Z" fill="white"/>
                    <path d="M33 11.5C33 10.3 32 9.5 30.5 9.5H27V14.5H29V12.5H29.5L31 14.5H33.5L31.5 12C32.4 11.8 33 11.7 33 11.5ZM30 11.5H29V11H30C30.5 11 30.5 11.5 30 11.5Z" fill="white"/>
                    <path d="M33.5 9.5H35.5V14.5H33.5V9.5Z" fill="white"/>
                </svg>

                <!-- PayPal -->
                <svg width="40" height="25" viewBox="0 0 40 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <rect width="40" height="25" rx="4" fill="white"/>
                    <path d="M14.5 10C14.5 8.8 15.5 7.5 17.2 7.5H24C24.5 7.5 25 8 25 8.7C25 10.5 23.5 12.5 21.5 12.5H18.5C18 12.5 17.5 13 17.4 13.5L16.5 18.5H13L14.5 10Z" fill="#253B80"/>
                    <path d="M19.5 13.5C19.5 12.4 20.5 11 22.2 11H27C27.5 11 28 11.5 27.8 12.2C27.5 14 26 16 24 16H21C20.5 16 20 16.5 19.9 16.9L19 21.5H15.5L16.8 13.7C16.9 13.6 17 13.5 17.4 13.5H18.5C19 13.5 19.3 13.5 19.5 13.5Z" fill="#179BD7"/>
                    <path d="M27.8 12.2C27.5 14 26 16 24 16H21C20.5 16 20 16.5 19.9 16.9L19 21.5H15.5L16.8 13.7C16.9 13.6 17 13.5 17.4 13.5H18.5C19 13.5 19.5 13 19.5 13.5C19.5 12.4 20.5 11 22.2 11H27C27.5 11 28 11.5 27.8 12.2Z" fill="#179BD7"/>
                </svg>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts communs à toutes les pages -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>

<!-- Script pour assurer le fonctionnement du menu utilisateur -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Menu utilisateur - script de secours
    const userIconWrapper = document.querySelector('.user-icon-wrapper');
    const userDropdown = document.querySelector('.user-dropdown');
    
    if (userIconWrapper && userDropdown) {
        // Ajouter un écouteur d'événement pour le clic sur l'icône
        userIconWrapper.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            // Afficher/masquer le menu
            userDropdown.classList.toggle('show');
        });
        
        // Fermer le menu si on clique ailleurs
        document.addEventListener('click', function(e) {
            if (userDropdown.classList.contains('show') && 
                !userDropdown.contains(e.target) && 
                !userIconWrapper.contains(e.target)) {
                userDropdown.classList.remove('show');
            }
        });
    }
});
</script>

<!-- Ne pas ajouter d'autres scripts ici, ils doivent être chargés par chaque page individuellement -->
</body>
</html>