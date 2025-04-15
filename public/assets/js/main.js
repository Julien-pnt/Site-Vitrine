/**
 * Fichier principal qui initialise tous les modules
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Main script loaded');
    
    // Vérifier si les modules sont chargés et les initialiser si nécessaire
    if (typeof window.cartFunctions !== 'undefined' && typeof window.cartFunctions.updateCartDisplay === 'function') {
        window.cartFunctions.updateCartDisplay();
    } else {
        console.error('Module de panier non chargé');
    }
    
    // Autres initialisations générales peuvent être ajoutées ici
});