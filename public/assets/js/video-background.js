/**
 * Gestion de la vidéo d'arrière-plan et du loader
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Video background module loaded');
    
    // Fix pour le problème de fondu blanc
    document.body.classList.add('video-loaded');
    
    // Gestion de la vidéo d'arrière-plan
    const video = document.querySelector('.video-bg');
    const fallbackImg = document.querySelector('.fallback-img');
    
    if (video) {
        // En cas d'erreur de chargement de la vidéo, afficher l'image de secours
        video.addEventListener('error', function() {
            if (fallbackImg) {
                fallbackImg.style.display = 'block';
            }
        });
        
        // Si la vidéo ne démarre pas après 3 secondes, afficher l'image de secours
        setTimeout(function() {
            if (video.paused || video.readyState < 2) {
                if (fallbackImg) {
                    fallbackImg.style.display = 'block';
                }
            }
        }, 3000);
    }
    
    // Masquer le loader
    const loader = document.querySelector('.loader-container');
    if (loader) {
        setTimeout(function() {
            loader.style.opacity = '0';
            setTimeout(function() {
                loader.style.display = 'none';
            }, 300);
        }, 500);
    }
});

// Fonction pour s'assurer que la page est toujours visible
function ensureVisibility() {
    document.body.classList.add('video-loaded');
}

// Backup au cas où DOMContentLoaded ne se déclencherait pas
setTimeout(ensureVisibility, 100);