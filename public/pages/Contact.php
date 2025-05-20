<?php
$currentPage = 'Contact.php';
$relativePath = '/Site-Vitrine/public';
$pageTitle = "Contact - Elixir du Temps";
$pageDescription = "Contactez Elixir du Temps pour toute question concernant nos montres de luxe ou pour prendre rendez-vous dans l'une de nos boutiques.";

// Inclure les helpers si nécessaire
require_once($_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/includes/product-helpers.php');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title><?php echo $pageTitle; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?php echo $pageDescription; ?>">
  <link rel="stylesheet" href="<?= $relativePath ?>/assets/css/main.css">
  <link rel="stylesheet" href="<?= $relativePath ?>/assets/css/header.css">
  <link rel="stylesheet" href="<?= $relativePath ?>/assets/css/footer.css">
  
  <style>
    /* Hero section */
    .contact-hero {
      background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('<?= $relativePath ?>/assets/img/backgrounds/contact-background.jpg');
      background-position: center;
      background-size: cover;
      height: 400px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      position: relative;
    }

    .contact-hero-content {
      max-width: 900px;
      padding: 0 20px;
      z-index: 2;
    }

    .contact-hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 3.5rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
    }

    .contact-hero-subtitle {
      font-size: 1.3rem;
      font-weight: 300;
      line-height: 1.6;
      margin-bottom: 0;
      max-width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    /* Sections générales */
    .section {
      padding: 100px 20px;
    }

    .container {
      max-width: 1300px;
      margin: 0 auto;
    }

    .section-title {
      font-family: 'Playfair Display', serif;
      font-size: 2.6rem;
      color: #333;
      text-align: center;
      margin-bottom: 20px;
      position: relative;
      padding-bottom: 15px;
    }

    .section-title::after {
      content: '';
      position: absolute;
      width: 80px;
      height: 3px;
      background-color: #d4af37;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
    }

    .section-subtitle {
      text-align: center;
      max-width: 700px;
      margin: 0 auto 60px;
      color: #666;
      font-size: 1.2rem;
      line-height: 1.7;
    }

    /* Contact section */
    .contact-section {
      background-color: #fff;
    }

    .contact-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 50px;
    }

    /* Formulaire de contact */
    .contact-form {
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 40px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    }

    .form-group {
      margin-bottom: 25px;
    }

    .form-group label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }

    .form-control {
      width: 100%;
      padding: 14px 16px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-family: 'Raleway', sans-serif;
      font-size: 1rem;
      transition: border-color 0.3s ease;
    }

    .form-control:focus {
      border-color: #d4af37;
      outline: none;
    }

    textarea.form-control {
      min-height: 150px;
      resize: vertical;
    }

    .submit-btn {
      background-color: #d4af37;
      color: white;
      padding: 14px 32px;
      border: none;
      border-radius: 30px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      font-size: 0.95rem;
      display: inline-block;
    }

    .submit-btn:hover {
      background-color: #c49b27;
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    /* Informations de contact */
    .contact-info {
      padding: 20px 0;
    }

    .contact-info-title {
      font-family: 'Playfair Display', serif;
      font-size: 1.8rem;
      color: #333;
      margin-bottom: 25px;
      position: relative;
      padding-bottom: 15px;
    }

    .contact-info-title::after {
      content: '';
      position: absolute;
      width: 50px;
      height: 3px;
      background-color: #d4af37;
      bottom: 0;
      left: 0;
    }

    .contact-item {
      display: flex;
      margin-bottom: 30px;
    }

    .contact-icon {
      background-color: #f9f9f9;
      width: 60px;
      height: 60px;
      display: flex;
      align-items: center;
      justify-content: center;
      border-radius: 50%;
      margin-right: 20px;
      color: #d4af37;
      font-size: 1.5rem;
      flex-shrink: 0;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .contact-text h4 {
      font-size: 1.2rem;
      margin: 0 0 5px 0;
      color: #333;
    }

    .contact-text p, .contact-text a {
      margin: 0;
      color: #666;
      line-height: 1.6;
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .contact-text a:hover {
      color: #d4af37;
    }

    /* Carte */
    .map-section {
      padding: 0;
    }

    .map-container {
      height: 500px;
      width: 100%;
      position: relative;
    }

    .map-container iframe {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Boutiques */
    .stores-section {
      background-color: #f9f9f9;
    }

    .stores-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 30px;
    }

    .store-card {
      background-color: white;
      border-radius: 10px;
      overflow: hidden;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      transition: all 0.4s ease;
    }

    .store-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
    }

    .store-image {
      height: 200px;
      overflow: hidden;
    }

    .store-image img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.6s ease;
    }

    .store-card:hover .store-image img {
      transform: scale(1.1);
    }

    .store-info {
      padding: 25px 20px;
    }

    .store-info h3 {
      font-family: 'Playfair Display', serif;
      font-size: 1.6rem;
      color: #333;
      margin-bottom: 15px;
    }

    .store-info p {
      color: #666;
      margin-bottom: 15px;
      line-height: 1.6;
    }

    .store-hours {
      margin-top: 15px;
      color: #666;
    }

    .store-hours h4 {
      font-size: 1.1rem;
      margin-bottom: 8px;
      color: #333;
    }

    .hour-row {
      display: flex;
      justify-content: space-between;
      padding: 5px 0;
      border-bottom: 1px dotted #ddd;
    }

    .hour-row:last-child {
      border-bottom: none;
    }

    /* Animation classes */
    .animated-element {
      opacity: 0;
      transform: translateY(30px);
      transition: opacity 0.8s ease, transform 0.8s ease;
    }

    .animated-element.visible {
      opacity: 1;
      transform: translateY(0);
    }

    /* Styles pour l'arrière-plan vidéo */
    .video-background {
      position: relative;
      height: 100vh;
      min-height: 600px;
      width: 100%;
      overflow: hidden;
      display: flex;
      align-items: center;
      justify-content: center;
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
      z-index: -2;
      object-fit: cover;
    }

    .video-overlay {
      position: absolute;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.7);
      z-index: -1;
    }

    .hero-content {
      max-width: 900px;
      padding: 0 20px;
      z-index: 2;
      text-align: center;
      color: white;
    }

    .hero-title {
      font-family: 'Playfair Display', serif;
      font-size: 4rem;
      font-weight: 700;
      margin-bottom: 20px;
      text-shadow: 0 2px 10px rgba(0, 0, 0, 0.6);
    }

    .hero-subtitle {
      font-size: 1.4rem;
      font-weight: 300;
      line-height: 1.6;
      margin-bottom: 30px;
      max-width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    /* Responsive pour l'arrière-plan vidéo */
    @media (max-width: 768px) {
      .video-background {
        height: 80vh;
        min-height: 500px;
      }
      
      .hero-title {
        font-size: 3rem;
      }
      
      .hero-subtitle {
        font-size: 1.2rem;
      }
    }

    @media (max-width: 992px) {
      .contact-grid {
        grid-template-columns: 1fr;
      }
      
      .contact-form {
        order: 2;
      }
      
      .contact-info {
        order: 1;
      }
      
      .stores-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 768px) {
      .contact-hero-title {
        font-size: 2.5rem;
      }
      
      .contact-hero-subtitle {
        font-size: 1.1rem;
      }
      
      .section {
        padding: 70px 20px;
      }
      
      .stores-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/Includes/header.php'; ?>



<!-- Video Background avec Hero Section -->
<div class="video-background">
  <video class="video-bg" autoplay muted loop playsinline preload="auto">
    <source src="<?= $relativePath ?>/assets/video/background.mp4" type="video/mp4">
    Votre navigateur ne supporte pas la vidéo.
  </video>
  <div class="video-overlay"></div>
  
  <div class="hero-content animate__animated animate__fadeIn">
    <h1 class="hero-title">Contactez-nous</h1>
    <p class="hero-subtitle">Notre équipe est à votre disposition pour répondre à toutes vos questions et vous conseiller dans votre choix.</p>
  </div>
</div>

<!-- Contact Section -->
<section class="section contact-section">
  <div class="container">
    <h2 class="section-title animated-element">Nous sommes à votre écoute</h2>
    <p class="section-subtitle animated-element">N'hésitez pas à nous contacter pour toute demande d'information ou pour prendre rendez-vous dans l'une de nos boutiques.</p>
    
    <div class="contact-grid">
      <div class="contact-form animated-element">
        <form id="contactForm" action="<?= $relativePath ?>/php/api/contact/submit.php" method="post">
          <div class="form-group">
            <label for="name">Nom complet</label>
            <input type="text" id="name" name="name" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="email">Adresse email</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>
          
          <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="tel" id="phone" name="phone" class="form-control">
          </div>
          
          <div class="form-group">
            <label for="subject">Sujet</label>
            <select id="subject" name="subject" class="form-control" required>
              <option value="">Sélectionnez un sujet</option>
              <option value="information">Demande d'information</option>
              <option value="boutique">Rendez-vous en boutique</option>
              <option value="service">Service après-vente</option>
              <option value="other">Autre</option>
            </select>
          </div>
          
          <div class="form-group">
            <label for="message">Message</label>
            <textarea id="message" name="message" class="form-control" required></textarea>
          </div>
          
          <button type="submit" class="submit-btn">Envoyer le message</button>
        </form>
      </div>
      
      <div class="contact-info animated-element">
        <h3 class="contact-info-title">Nos coordonnées</h3>
        
        <div class="contact-item">
          <div class="contact-icon">
            <i class="fas fa-map-marker-alt"></i>
          </div>
          <div class="contact-text">
            <h4>Siège social</h4>
            <p>15 Rue du Rhône<br>1204 Genève, Suisse</p>
          </div>
        </div>
        
        <div class="contact-item">
          <div class="contact-icon">
            <i class="fas fa-phone-alt"></i>
          </div>
          <div class="contact-text">
            <h4>Téléphone</h4>
            <p><a href="tel:+41223456789">+41 22 345 67 89</a></p>
          </div>
        </div>
        
        <div class="contact-item">
          <div class="contact-icon">
            <i class="fas fa-envelope"></i>
          </div>
          <div class="contact-text">
            <h4>Email</h4>
            <p><a href="mailto:contact@elixirdutemps.com">contact@elixirdutemps.com</a></p>
          </div>
        </div>
        
        <div class="contact-item">
          <div class="contact-icon">
            <i class="fas fa-clock"></i>
          </div>
          <div class="contact-text">
            <h4>Horaires d'ouverture</h4>
            <p>Lundi - Vendredi: 10h00 - 19h00<br>
               Samedi: 10h00 - 18h00<br>
               Dimanche: Fermé</p>
          </div>
        </div>
        
        <div class="contact-item">
          <div class="contact-icon">
            <i class="fas fa-comments"></i>
          </div>
          <div class="contact-text">
            <h4>Service client</h4>
            <p>Notre équipe est à votre disposition pour vous accompagner dans votre choix et répondre à vos questions.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Map Section -->
<section class="section map-section">
  <div class="map-container">
    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2761.372366368402!2d6.148259600000001!3d46.201513100000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x478c652f05aac649%3A0x23a2022eb1772adb!2sRue%20du%20Rh%C3%B4ne%2C%20Gen%C3%A8ve%2C%20Suisse!5e0!3m2!1sfr!2sfr!4v1713707642818!5m2!1sfr!2sfr" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
  </div>
</section>

<!-- Boutiques Section -->
<section class="section stores-section">
  <div class="container">
    <h2 class="section-title animated-element">Nos boutiques</h2>
    <p class="section-subtitle animated-element">Découvrez nos espaces dédiés à l'horlogerie de prestige</p>
    
    <div class="stores-grid">
      <div class="store-card animated-element">
        <div class="store-image">
          <img src="<?= $relativePath ?>/assets/img/boutiques/geneve.jpg" alt="Boutique Genève" loading="lazy">
        </div>
        <div class="store-info">
          <h3>Genève</h3>
          <p>15 Rue du Rhône<br>1204 Genève, Suisse</p>
          <p><a href="tel:+41223456789">+41 22 345 67 89</a></p>
          
          <div class="store-hours">
            <h4>Horaires</h4>
            <div class="hour-row">
              <span>Lundi - Vendredi</span>
              <span>10h00 - 19h00</span>
            </div>
            <div class="hour-row">
              <span>Samedi</span>
              <span>10h00 - 18h00</span>
            </div>
            <div class="hour-row">
              <span>Dimanche</span>
              <span>Fermé</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="store-card animated-element">
        <div class="store-image">
          <img src="<?= $relativePath ?>/assets/img/boutiques/paris.jpg" alt="Boutique Paris" loading="lazy">
        </div>
        <div class="store-info">
          <h3>Paris</h3>
          <p>12 Place Vendôme<br>75001 Paris, France</p>
          <p><a href="tel:+33142601234">+33 1 42 60 12 34</a></p>
          
          <div class="store-hours">
            <h4>Horaires</h4>
            <div class="hour-row">
              <span>Lundi - Vendredi</span>
              <span>10h30 - 19h30</span>
            </div>
            <div class="hour-row">
              <span>Samedi</span>
              <span>11h00 - 19h00</span>
            </div>
            <div class="hour-row">
              <span>Dimanche</span>
              <span>Fermé</span>
            </div>
          </div>
        </div>
      </div>
      
      <div class="store-card animated-element">
        <div class="store-image">
          <img src="<?= $relativePath ?>/assets/img/boutiques/london.jpg" alt="Boutique Londres" loading="lazy">
        </div>
        <div class="store-info">
          <h3>Londres</h3>
          <p>23 New Bond Street<br>London W1S 2RT, UK</p>
          <p><a href="tel:+442071234567">+44 20 7123 4567</a></p>
          
          <div class="store-hours">
            <h4>Horaires</h4>
            <div class="hour-row">
              <span>Lundi - Vendredi</span>
              <span>09h30 - 18h30</span>
            </div>
            <div class="hour-row">
              <span>Samedi</span>
              <span>10h00 - 18h00</span>
            </div>
            <div class="hour-row">
              <span>Dimanche</span>
              <span>12h00 - 17h00</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include_once $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/Includes/footer.php'; ?>

<!-- Script pour les animations au scroll -->
<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Animation au scroll pour les éléments avec classe .animated-element
    const animatedElements = document.querySelectorAll('.animated-element');
    
    const checkIfInView = function() {
      animatedElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        
        if (elementTop < windowHeight - 100) {
          element.classList.add('visible');
        }
      });
    };
    
    // Vérifier lors du scroll
    window.addEventListener('scroll', checkIfInView);
    
    // Vérifier aussi au chargement
    checkIfInView();

    // Validation du formulaire de contact
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
      contactForm.addEventListener('submit', function(e) {
        const nameField = document.getElementById('name');
        const emailField = document.getElementById('email');
        const messageField = document.getElementById('message');
        
        let isValid = true;
        
        // Validation basique
        if (nameField.value.trim() === '') {
          isValid = false;
          nameField.style.borderColor = 'red';
        } else {
          nameField.style.borderColor = '#ddd';
        }
        
        if (emailField.value.trim() === '' || !emailField.value.includes('@')) {
          isValid = false;
          emailField.style.borderColor = 'red';
        } else {
          emailField.style.borderColor = '#ddd';
        }
        
        if (messageField.value.trim() === '') {
          isValid = false;
          messageField.style.borderColor = 'red';
        } else {
          messageField.style.borderColor = '#ddd';
        }
        
        if (!isValid) {
          e.preventDefault();
          alert('Veuillez remplir correctement tous les champs obligatoires.');
        }
      });
    }
  });
</script>

<!-- Importation des fichiers JS modulaires (une seule fois chacun) -->
<script src="<?php echo $relativePath; ?>/assets/js/header-functions.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/gestion-cart.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/product-filters.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/quick-view.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/video-background.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/main.js"></script>
<script src="<?php echo $relativePath; ?>/assets/js/wishlist-manager.js"></script>

</body>
</html>