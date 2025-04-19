<header class="header">
    <div class="container">
        <div class="header-wrapper">
            <div class="logo-container">
                <a href="../pages/Accueil.html">
                    <img src="../assets/img/layout/logo.png" alt="Elixir du Temps" class="logo">
                </a>
            </div>
            
            <div class="user-tools">
                <div class="user-icon">
                    <i class="fas fa-bars sidebar-toggle"></i>
                </div>
                
                <div class="cart-icon">
                    <a href="../pages/products/panier.php">
                        <i class="fas fa-shopping-cart"></i>
                        <?php
                        // Récupérer le nombre d'articles dans le panier
                        $cartCount = 0;
                        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                            foreach ($_SESSION['cart'] as $item) {
                                $cartCount += $item['quantity'];
                            }
                        }
                        ?>
                        <span class="cart-badge" id="cart-count"><?php echo $cartCount; ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>