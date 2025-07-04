<?php
require_once '../../php/config/database.php';
require_once '../../php/utils/auth.php';

// Créer le répertoire pour les avatars par défaut s'il n'existe pas
$avatarsDir = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/avatars/';
if (!is_dir($avatarsDir)) {
    mkdir($avatarsDir, 0755, true);
}

// Copier l'image existante comme image par défaut si elle n'existe pas déjà
$defaultAdminImage = $avatarsDir . 'admin-default.jpg';
$defaultUserImage = $avatarsDir . 'user-default.jpg';

// Image source (l'image que vous utilisez actuellement)
$sourceImage = $_SERVER['DOCUMENT_ROOT'] . '/Site-Vitrine/public/assets/img/layout/jb3.jpg';

// Copier l'image si elle existe et n'est pas déjà copiée
if (file_exists($sourceImage)) {
    if (!file_exists($defaultAdminImage)) {
        copy($sourceImage, $defaultAdminImage);
    }
    if (!file_exists($defaultUserImage)) {
        copy($sourceImage, $defaultUserImage);
    }
}

// Vérification de l'authentification
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /Site-Vitrine/public/pages/auth/login.php?redirect=admin');
    exit;
}

// Pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Construire la requête avec les filtres
$filters = [];
$params = [];

// Filtres de recherche
if (isset($_GET['level']) && !empty($_GET['level'])) {
    $filters[] = "level = ?";
    $params[] = $_GET['level'];
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $filters[] = "category = ?";
    $params[] = $_GET['category'];
}

if (isset($_GET['action']) && !empty($_GET['action'])) {
    $filters[] = "action = ?";
    $params[] = $_GET['action'];
}

if (isset($_GET['user_type']) && !empty($_GET['user_type'])) {
    $filters[] = "user_type = ?";
    $params[] = $_GET['user_type'];
}

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $filters[] = "user_id = ?";
    $params[] = $_GET['user_id'];
}

if (isset($_GET['entity_type']) && !empty($_GET['entity_type'])) {
    $filters[] = "entity_type = ?";
    $params[] = $_GET['entity_type'];
}

if (isset($_GET['entity_id']) && !empty($_GET['entity_id'])) {
    $filters[] = "entity_id = ?";
    $params[] = $_GET['entity_id'];
}

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $filters[] = "(details LIKE ? OR context LIKE ?)";
    $searchTerm = "%" . $_GET['search'] . "%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if (isset($_GET['date_start']) && !empty($_GET['date_start'])) {
    $filters[] = "created_at >= ?";
    $params[] = $_GET['date_start'] . ' 00:00:00';
}

if (isset($_GET['date_end']) && !empty($_GET['date_end'])) {
    $filters[] = "created_at <= ?";
    $params[] = $_GET['date_end'] . ' 23:59:59';
}

if (isset($_GET['ip_address']) && !empty($_GET['ip_address'])) {
    $filters[] = "ip_address = ?";
    $params[] = $_GET['ip_address'];
}

$whereClause = "";
if (!empty($filters)) {
    $whereClause = " WHERE " . implode(" AND ", $filters);
}

// Récupérer les données
try {
    // Compter le nombre total de logs
    $countSql = "SELECT COUNT(*) FROM system_logs" . $whereClause;
    $countStmt = $pdo->prepare($countSql);
    $countStmt->execute($params);
    $totalLogs = $countStmt->fetchColumn();
    $totalPages = ceil($totalLogs / $limit);
    
    // Récupérer les logs avec la pagination
    $sql = "SELECT l.*, 
                  COALESCE(a.prenom, u.prenom, 'Système') as user_firstname,
                  COALESCE(a.nom, u.nom, '') as user_lastname,
                  COALESCE(a.email, u.email, '') as user_email
           FROM system_logs l
           LEFT JOIN utilisateurs a ON l.user_id = a.id AND l.user_type = 'admin'
           LEFT JOIN utilisateurs u ON l.user_id = u.id AND l.user_type = 'customer'
           $whereClause
           ORDER BY l.created_at DESC
           LIMIT $offset, $limit";
           
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mise en cache des données statiques
    $cacheFile = '../cache/log_filters_' . md5(serialize($params)) . '.cache';
    $cacheExpiry = 3600; // 1 heure

    if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheExpiry)) {
        $cachedData = unserialize(file_get_contents($cacheFile));
        $levels = $cachedData['levels'];
        $categories = $cachedData['categories'];
        $actions = $cachedData['actions'];
        $entityTypes = $cachedData['entityTypes'];
        $userTypes = $cachedData['userTypes'];
    } else {
        // Utiliser des requêtes préparées avec LIMIT pour optimiser les performances
        $levels = $pdo->query("SELECT DISTINCT level FROM system_logs ORDER BY level LIMIT 20")->fetchAll(PDO::FETCH_COLUMN);
        $categories = $pdo->query("SELECT DISTINCT category FROM system_logs ORDER BY category LIMIT 50")->fetchAll(PDO::FETCH_COLUMN);
        $actions = $pdo->query("SELECT DISTINCT action FROM system_logs ORDER BY action LIMIT 100")->fetchAll(PDO::FETCH_COLUMN);
        $entityTypes = $pdo->query("SELECT DISTINCT entity_type FROM system_logs WHERE entity_type IS NOT NULL ORDER BY entity_type LIMIT 30")->fetchAll(PDO::FETCH_COLUMN);
        $userTypes = $pdo->query("SELECT DISTINCT user_type FROM system_logs ORDER BY user_type LIMIT 10")->fetchAll(PDO::FETCH_COLUMN);
        
        // Créer le répertoire de cache si nécessaire
        if (!is_dir('../cache')) {
            mkdir('../cache', 0755, true);
        }
        
        // Sauvegarder dans le cache
        file_put_contents($cacheFile, serialize([
            'levels' => $levels,
            'categories' => $categories,
            'actions' => $actions,
            'entityTypes' => $entityTypes,
            'userTypes' => $userTypes
        ]));
    }
    
} catch (PDOException $e) {
    $error = "Erreur lors de la récupération des logs: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Journaux système - Administration</title>
    <link rel="icon" href="../assets/img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/admin.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="css/header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="js/header.js" defer></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Vérifier la préférence utilisateur enregistrée
    const currentTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', currentTheme);
    
    // Ajouter le bouton de basculement au menu
    const headerActions = document.querySelector('.header-actions');
    if (headerActions) {
        const themeToggle = document.createElement('button');
        themeToggle.classList.add('btn-icon', 'theme-toggle');
        themeToggle.innerHTML = currentTheme === 'dark' 
            ? '<i class="fas fa-sun" title="Passer en mode clair"></i>' 
            : '<i class="fas fa-moon" title="Passer en mode sombre"></i>';
        
        themeToggle.addEventListener('click', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            this.innerHTML = newTheme === 'dark' 
                ? '<i class="fas fa-sun" title="Passer en mode clair"></i>' 
                : '<i class="fas fa-moon" title="Passer en mode sombre"></i>';
        });
        
        headerActions.prepend(themeToggle);
    }
});

// Ajouter à la fin du script JavaScript existant
document.addEventListener('keydown', function(event) {
    // Alt+F pour focus sur la recherche
    if (event.altKey && event.key === 'f') {
        event.preventDefault();
        document.getElementById('search').focus();
    }
    
    // Alt+N pour page suivante
    if (event.altKey && event.key === 'n') {
        const nextPageLink = document.querySelector('.pagination a:last-child');
        if (nextPageLink && !nextPageLink.classList.contains('active')) {
            nextPageLink.click();
        }
    }
    
    // Alt+P pour page précédente
    if (event.altKey && event.key === 'p') {
        const prevPageLink = document.querySelector('.pagination a:first-child');
        if (prevPageLink && !prevPageLink.classList.contains('active')) {
            prevPageLink.click();
        }
    }
    
    // Alt+E pour exporter
    if (event.altKey && event.key === 'e') {
        event.preventDefault();
        document.querySelector('.export-csv').click();
    }
    
    // Alt+R pour réinitialiser les filtres
    if (event.altKey && event.key === 'r') {
        event.preventDefault();
        window.location = 'system-logs.php';
    }
});

// Ajouter des informations d'accessibilité
document.querySelectorAll('button, a').forEach(el => {
    if (!el.getAttribute('aria-label') && el.title) {
        el.setAttribute('aria-label', el.title);
    }
});

// Ajouter une notification pour les raccourcis clavier
const dashboardEl = document.querySelector('.dashboard');
if (dashboardEl) {
    const keyboardHelpBtn = document.createElement('button');
    keyboardHelpBtn.className = 'btn-icon keyboard-help';
    keyboardHelpBtn.innerHTML = '<i class="fas fa-keyboard" title="Raccourcis clavier"></i>';
    keyboardHelpBtn.setAttribute('aria-label', 'Afficher les raccourcis clavier');
    
    keyboardHelpBtn.addEventListener('click', function() {
        const helpModal = document.createElement('div');
        helpModal.className = 'log-modal';
        helpModal.style.display = 'block';
        
        helpModal.innerHTML = `
            <div class="log-modal-content" style="max-width: 600px;">
                <div class="log-modal-header">
                    <h2>Raccourcis clavier</h2>
                    <span class="log-modal-close">&times;</span>
                </div>
                <div style="padding: 1rem;">
                    <table class="modal-details-table">
                        <tr><th>Alt + F</th><td>Focus sur la recherche</td></tr>
                        <tr><th>Alt + N</th><td>Page suivante</td></tr>
                        <tr><th>Alt + P</th><td>Page précédente</td></tr>
                        <tr><th>Alt + E</th><td>Exporter les données</td></tr>
                        <tr><th>Alt + R</th><td>Réinitialiser les filtres</td></tr>
                        <tr><th>Echap</th><td>Fermer la fenêtre modale</td></tr>
                    </table>
                </div>
            </div>
        `;
        
        document.body.appendChild(helpModal);
        
        setTimeout(() => {
            helpModal.classList.add('show');
        }, 10);
        
        const closeBtn = helpModal.querySelector('.log-modal-close');
        closeBtn.addEventListener('click', function() {
            helpModal.classList.remove('show');
            setTimeout(() => {
                helpModal.remove();
            }, 300);
        });
        
        helpModal.addEventListener('click', function(e) {
            if (e.target === helpModal) {
                helpModal.classList.remove('show');
                setTimeout(() => {
                    helpModal.remove();
                }, 300);
            }
        });
    });
    
    document.querySelector('.dashboard h1').appendChild(keyboardHelpBtn);
}
</script>

<!-- Ajouter dans le header -->
<div id="notifications-container" style="position: fixed; top: 70px; right: 20px; width: 350px; z-index: 9999;"></div>

<script>
// Script de notification en temps réel
document.addEventListener('DOMContentLoaded', function() {
    const notificationsContainer = document.getElementById('notifications-container');
    
    // Vérifier les nouvelles alertes toutes les 30 secondes
    function checkForAlerts() {
        fetch('api/check-alerts.php')
            .then(response => response.json())
            .then(data => {
                if (data.alerts && data.alerts.length > 0) {
                    data.alerts.forEach(alert => {
                        showNotification(alert.level, alert.message, alert.details);
                    });
                }
            })
            .catch(error => console.error('Erreur lors de la vérification des alertes:', error));
    }
    
    // Première vérification après 10 secondes
    setTimeout(checkForAlerts, 10000);
    
    // Puis toutes les 30 secondes
    setInterval(checkForAlerts, 30000);
    
    // Fonction pour afficher une notification
    function showNotification(level, message, details = '') {
        const notification = document.createElement('div');
        notification.className = `notification notification-${level}`;
        notification.style.cssText = `
            background-color: var(--bg-card);
            border-left: 4px solid ${getColorForLevel(level)};
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 1rem;
            margin-bottom: 1rem;
            animation: slideIn 0.3s forwards;
            position: relative;
            overflow: hidden;
        `;
        
        notification.innerHTML = `
            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                <div>
                    <span class="level-badge level-${level}" style="margin-bottom: 0.5rem;">
                        ${level}
                    </span>
                    <h4 style="margin: 0.5rem 0; font-size: 1rem;">${message}</h4>
                    ${details ? `<p style="margin: 0.5rem 0 0; font-size: 0.9rem; color: var(--text-light);">${details}</p>` : ''}
                </div>
                <button class="close-notification" style="background: none; border: none; color: var(--text-light); cursor: pointer; font-size: 1.25rem;">&times;</button>
            </div>
            <div class="progress-bar" style="position: absolute; bottom: 0; left: 0; height: 3px; width: 100%; background-color: ${getColorForLevel(level)}; transform: scaleX(1); transform-origin: left; transition: transform 5s linear;"></div>
        `;
        
        notificationsContainer.appendChild(notification);
        
        // Animation de la barre de progression
        setTimeout(() => {
            notification.querySelector('.progress-bar').style.transform = 'scaleX(0)';
        }, 100);
        
        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s forwards';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 5000);
        
        // Fermeture manuelle
        notification.querySelector('.close-notification').addEventListener('click', function() {
            notification.style.animation = 'slideOut 0.3s forwards';
            setTimeout(() => {
                notification.remove();
            }, 300);
        });
    }
    
    function getColorForLevel(level) {
        const colors = {
            'debug': '#6c757d',
            'info': '#17a2b8',
            'notice': '#20c997',
            'warning': '#ffc107',
            'error': '#dc3545',
            'critical': '#dc3545',
            'alert': '#dc3545',
            'emergency': '#901C1C'
        };
        
        return colors[level] || '#6c757d';
    }
    
    // Styles pour les animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
    `;
    document.head.appendChild(style);
});

// Stocker les références aux graphiques pour pouvoir les détruire lors d'un redimensionnement
let levelChart = null;
let activityChart = null;

// Récupérer les statistiques via AJAX
function loadCharts() {
    fetch('api/get-log-stats.php')
        .then(response => response.json())
        .then(data => {
            // Détruire les graphiques existants s'il y en a
            if (levelChart) levelChart.destroy();
            if (activityChart) activityChart.destroy();
            
            // Graphique de distribution par niveau
            const levelCtx = document.getElementById('levelChart').getContext('2d');
            levelChart = new Chart(levelCtx, {
                type: 'doughnut',
                data: {
                    labels: data.levels.map(l => l.level),
                    datasets: [{
                        data: data.levels.map(l => l.count),
                        backgroundColor: [
                            '#17a2b8', // info
                            '#20c997', // notice
                            '#ffc107', // warning
                            '#dc3545', // error
                            '#901C1C', // emergency
                            '#6c757d'  // autres
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.5,
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 15,
                                padding: 15,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.raw || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            
            // Graphique d'activité sur 30 jours
            const activityCtx = document.getElementById('activityChart').getContext('2d');
            activityChart = new Chart(activityCtx, {
                type: 'line',
                data: {
                    labels: data.activity.map(a => a.date),
                    datasets: [{
                        label: 'Nombre de logs',
                        data: data.activity.map(a => a.count),
                        borderColor: '#d4af37',
                        backgroundColor: 'rgba(212, 175, 55, 0.1)',
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 2,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                maxTicksLimit: 5
                            }
                        },
                        x: {
                            ticks: {
                                maxTicksLimit: 10,
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
                    }
                }
            });
        })
        .catch(error => {
            console.error('Erreur:', error);
        });
}

// Ajouter ce code pour le bouton de rechargement
document.addEventListener('DOMContentLoaded', function() {
    const refreshButton = document.getElementById('refresh-charts');
    if (refreshButton) {
        refreshButton.addEventListener('click', function() {
            loadCharts();
            this.classList.add('rotating');
            setTimeout(() => {
                this.classList.remove('rotating');
            }, 1000);
        });
    }
});

// Charger les graphiques au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadCharts();
    
    // Recharger les graphiques lors du redimensionnement de la fenêtre
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            loadCharts();
        }, 250);
    });
});
</script>

    <style>
        /* Variables pour le thème clair (par défaut) */
        :root {
            --primary-color: #d4af37;
            --primary-light: rgba(212, 175, 55, 0.1);
            --primary-dark: #b39130;
            --text-color: #333;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --bg-dark: #212529;
            --bg-main: #f5f5f7;
            --bg-card: #ffffff;
            --border-color: #e9ecef;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
        }
        
        /* Variables pour le thème sombre */
        [data-theme="dark"] {
            --primary-color: #ffd966;
            --primary-light: rgba(255, 217, 102, 0.15);
            --primary-dark: #e6c25a;
            --text-color: #e9ecef;
            --text-light: #adb5bd;
            --bg-light: #2c3034;
            --bg-dark: #1a1d20;
            --bg-main: #1c1e22;
            --bg-card: #212529;
            --border-color: #343a40;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        body {
            background-color: var(--bg-main);
            color: var(--text-color);
        }
        
        .filter-form, .table-container, .log-modal-content {
            background-color: var(--bg-card);
            border: 1px solid var(--border-color);
        }
        
        .data-table thead th {
            background-color: var(--bg-light);
            color: var(--text-color);
            border-bottom: 1px solid var(--border-color);
        }
        
        .data-table tbody td {
            border-bottom: 1px solid var(--border-color);
        }
        
        .theme-toggle {
            margin-right: 10px;
        }
        
        /* Transition fluide entre les thèmes */
        body, .filter-form, .table-container, .btn, .pagination a, .log-modal-content, 
        .data-table, .data-table thead th, .data-table tbody td {
            transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
        }

        :root {
            --primary-color: #d4af37;
            --primary-light: rgba(212, 175, 55, 0.1);
            --primary-dark: #b39130;
            --text-color: #333;
            --text-light: #6c757d;
            --bg-light: #f8f9fa;
            --bg-dark: #212529;
            --success: #28a745;
            --danger: #dc3545;
            --warning: #ffc107;
            --info: #17a2b8;
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --radius: 8px;
            --transition: all 0.25s ease;
        }

        /* Base styles */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            color: var(--text-color);
            background-color: #f5f5f7;
            line-height: 1.5;
        }

        .dashboard {
            padding: 1.5rem;
        }

        .dashboard h1 {
            font-weight: 700;
            margin-bottom: 2rem;
            color: var(--text-color);
            font-size: 1.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .dashboard h1 i {
            color: var(--primary-color);
            background: var(--primary-light);
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }

        /* Filters section */
        .filter-form {
            background-color: white;
            border-radius: var(--radius);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--shadow);
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-weight: 500;
            color: var(--text-light);
            font-size: 0.875rem;
        }

        .filter-group input,
        .filter-group select {
            border: 1px solid #ddd;
            border-radius: var(--radius);
            padding: 0.6rem 0.75rem;
            font-size: 0.9rem;
            transition: var(--transition);
        }

        .filter-group input:focus,
        .filter-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(212, 175, 55, 0.2);
            outline: none;
        }

        .filter-buttons {
            display: flex;
            gap: 0.75rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .btn {
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: none;
            font-size: 0.9rem;
        }

        .btn i {
            font-size: 0.875rem;
        }

        .btn.secondary {
            background-color: #f0f0f0;
            color: var(--text-color);
        }

        .btn.secondary:hover {
            background-color: #e0e0e0;
        }

        .reset-filters {
            background-color: transparent;
            border: 1px solid #ddd;
            color: var(--text-light);
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .reset-filters:hover {
            background-color: #f8f9fa;
        }

        .apply-filters {
            background-color: var(--primary-color);
            color: white;
            padding: 0.6rem 1.25rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            border: none;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .apply-filters:hover {
            background-color: var(--primary-dark);
        }

        .export-csv {
            background-color: var(--bg-light);
            color: var(--text-color);
            padding: 0.6rem 1rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .export-csv:hover {
            background-color: #e9ecef;
        }

        /* Table styles */
        .table-container {
            background-color: white;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            overflow: auto;
            margin-bottom: 2rem;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .data-table thead th {
            background-color: #f8f9fa;
            color: var(--text-color);
            font-weight: 600;
            text-align: left;
            padding: 1rem;
            font-size: 0.9rem;
            position: sticky;
            top: 0;
            z-index: 10;
            border-bottom: 1px solid #eee;
        }

        .data-table tbody tr {
            transition: var(--transition);
        }

        .data-table tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.015);
        }

        .data-table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .empty-table {
            text-align: center;
            padding: 3rem 1rem !important;
            font-style: italic;
            color: var(--text-light);
        }

        /* Level badges */
        .level-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.25rem 0.75rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .level-debug { background: #e9ecef; color: #495057; }
        .level-info { background: #e6f4ff; color: #0d6efd; }
        .level-notice { background: #e8f8f0; color: #20c997; }
        .level-warning { background: #fff8e6; color: #fd7e14; }
        .level-error { background: #feebee; color: #dc3545; }
        .level-critical { background: #fdd9d7; color: #dc3545; }
        .level-alert { background: #dc3545; color: white; }
        .level-emergency { background: #901C1C; color: white; }

        /* Entity pills */
        .entity-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            color: var(--text-color);
            padding: 0.2rem 0.6rem;
            border-radius: 30px;
            font-size: 0.75rem;
            font-weight: 500;
            white-space: nowrap;
        }

        .user-type-admin { background: #e6f4ff; color: #0d6efd; }
        .user-type-customer { background: #e8f8f0; color: #20c997; }
        .user-type-system { background: #e9ecef; color: #495057; }
        .user-type-guest { background: #feebee; color: #dc3545; }

        /* Pagination */
        .pagination {
            display: flex;
            gap: 0.25rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination a {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 2.25rem;
            height: 2.25rem;
            padding: 0 0.75rem;
            border-radius: var(--radius);
            background-color: white;
            color: var(--text-color);
            text-decoration: none;
            transition: var(--transition);
            font-size: 0.9rem;
        }

        .pagination a:hover {
            background-color: var(--primary-light);
            color: var(--primary-dark);
        }

        .pagination a.active {
            background-color: var(--primary-color);
            color: white;
        }

        /* Modal styles */
        .log-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .log-modal.show {
            opacity: 1;
        }

        .log-modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 2rem;
            border-radius: 12px;
            width: 85%;
            max-width: 1100px;
            max-height: 85vh;
            overflow-y: auto;
            transform: translateY(-20px);
            transition: transform 0.3s ease-out;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .log-modal.show .log-modal-content {
            transform: translateY(0);
        }

        .log-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #eee;
            padding-bottom: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .log-modal-header h2 {
            margin: 0;
            color: var(--text-color);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .log-modal-close {
            color: var(--text-light);
            font-size: 1.75rem;
            font-weight: 300;
            cursor: pointer;
            transition: var(--transition);
        }

        .log-modal-close:hover {
            color: var(--danger);
        }

        .log-modal-tabs {
            display: flex;
            border-bottom: 1px solid #eee;
            margin-bottom: 1.5rem;
            gap: 1.5rem;
        }

        .log-modal-tab {
            padding: 0.75rem 0.5rem;
            cursor: pointer;
            font-weight: 500;
            color: var(--text-light);
            position: relative;
            transition: var(--transition);
        }

        .log-modal-tab:hover {
            color: var(--text-color);
        }

        .log-modal-tab.active {
            color: var(--primary-color);
            font-weight: 600;
        }

        .log-modal-tab.active::after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            width: 100%;
            height: 3px;
            background-color: var(--primary-color);
            border-top-left-radius: 3px;
            border-top-right-radius: 3px;
        }

        .log-modal-section {
            display: none;
        }

        .log-modal-section.active {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-details-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .modal-details-table th {
            text-align: left;
            padding: 0.75rem 1rem;
            width: 160px;
            vertical-align: top;
            color: var(--text-light);
            font-weight: 500;
            border-bottom: 1px solid #f0f0f0;
        }

        .modal-details-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            word-break: break-word;
        }

        .modal-details-table tr:last-child th,
        .modal-details-table tr:last-child td {
            border-bottom: none;
        }

        .json-viewer {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
            font-family: 'JetBrains Mono', 'Fira Code', 'Roboto Mono', monospace;
            font-size: 0.9rem;
            white-space: pre-wrap;
            overflow-x: auto;
            color: #24292e;
            line-height: 1.6;
        }

        .diff-viewer {
            display: flex;
            gap: 1.5rem;
        }

        @media (max-width: 992px) {
            .diff-viewer {
                flex-direction: column;
            }
        }

        .diff-column {
            flex: 1;
        }

        .diff-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1rem;
        }

        .changes {
            background: #f8f9fa;
            padding: 1.25rem;
            border-radius: 8px;
            margin-top: 1.5rem;
        }

        .change-item {
            margin-bottom: 1.25rem;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #e9ecef;
        }

        .change-item:last-child {
            margin-bottom: 0;
            padding-bottom: 0;
            border-bottom: none;
        }

        .change-field {
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .change-old {
            color: var(--danger);
            text-decoration: line-through;
            margin-bottom: 0.25rem;
            padding-left: 1rem;
            position: relative;
        }

        .change-old::before {
            content: '-';
            position: absolute;
            left: 0;
        }

        .change-new {
            color: var(--success);
            padding-left: 1rem;
            position: relative;
        }

        .change-new::before {
            content: '+';
            position: absolute;
            left: 0;
        }

        .btn-icon {
            width: 34px;
            height: 34px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            background-color: #f0f0f0;
            color: var(--text-color);
            border: none;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-icon:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        /* Alert styles */
        .alert {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: var(--radius);
            font-size: 0.95rem;
        }

        .alert-error {
            background-color: var(--danger);
            color: white;
        }

        /* Amélioration spécifiques */
        .filter-advanced {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed #e9ecef;
        }

        .filter-form h3 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1.1rem;
            color: var(--text-color);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .filter-row {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .log-modal-content {
                width: 90%;
                margin: 10% auto;
            }
        }

        @media (max-width: 768px) {
            .filter-row, .filter-advanced {
                grid-template-columns: 1fr;
            }
            
            .filter-buttons {
                flex-direction: column;
            }
            
            .filter-buttons button, 
            .filter-buttons a {
                width: 100%;
            }
            
            .log-modal-content {
                width: 95%;
                padding: 1.5rem;
            }
            
            .log-modal-tabs {
                flex-wrap: wrap;
            }
        }

        /* Styles pour les graphiques */
        .stats-container {
            transition: all 0.3s ease;
        }

        .stats-card {
            background-color: var(--bg-card);
            border-radius: var(--radius);
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .stats-card h4 {
            margin-top: 0;
            margin-bottom: 1rem;
            font-size: 1rem;
            color: var(--text-color);
            text-align: center;
        }

        .chart-container {
            margin: 0 auto;
            max-width: 100%;
        }

        /* Assurer que les graphiques s'adaptent aux thèmes */
        [data-theme="dark"] .chart-container canvas {
            filter: brightness(0.95);
        }

        /* Ajouter ce style pour l'animation du bouton de rechargement */
        @keyframes rotate {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .rotating i {
            animation: rotate 1s linear;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Utiliser le template de sidebar -->
        <?php 
        // Définir la racine relative pour les liens dans la sidebar
        $admin_root = '';
        include 'templates/sidebar.php'; 
        ?>

        <main class="main-content">
            <!-- Intégration du template header -->
            <?php 
            // Définir la racine relative pour les liens dans le header
            $admin_root = '';
            
            // Personnaliser la recherche pour la page des journaux
            $search_placeholder = "Rechercher dans les journaux...";
            $search_action = "system-logs.php";
            $search_param = "search";
            $search_value = isset($_GET['search']) ? $_GET['search'] : '';
            
            include 'templates/header.php'; 
            ?>
            
            <div class="dashboard">
                <h1><i class="fas fa-shield-alt"></i> Journaux système</h1>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <!-- Filtres avancés -->
                <div class="filter-form">
                    <h3>Filtres de recherche</h3>
                    
                    <!-- Ajouter en haut du formulaire de filtres -->
                    <div class="saved-filters" style="margin-bottom: 1rem; display: flex; gap: 1rem; flex-wrap: wrap;">
                        <select id="saved-filter-select" class="form-control" style="max-width: 250px;">
                            <option value="">Mes filtres enregistrés</option>
                            <!-- Options chargées dynamiquement -->
                        </select>
                        
                        <button type="button" id="load-filter" class="btn secondary" style="min-width: 100px;">
                            <i class="fas fa-folder-open"></i> Charger
                        </button>
                        
                        <button type="button" id="save-filter" class="btn secondary" style="min-width: 100px;">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
                        
                        <button type="button" id="delete-filter" class="btn secondary" style="min-width: 100px;">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>

                    <form method="GET" action="system-logs.php">
                        <div class="filter-row">
                            <div class="filter-group">
                                <label for="search">Recherche</label>
                                <input type="text" id="search" name="search" placeholder="Rechercher..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="level">Niveau</label>
                                <select id="level" name="level">
                                    <option value="">Tous les niveaux</option>
                                    <?php foreach ($levels as $level): ?>
                                        <option value="<?= htmlspecialchars($level) ?>" <?= (isset($_GET['level']) && $_GET['level'] == $level) ? 'selected' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($level)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="category">Catégorie</label>
                                <select id="category" name="category">
                                    <option value="">Toutes les catégories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= htmlspecialchars($category) ?>" <?= (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($category)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="action">Action</label>
                                <select id="action" name="action">
                                    <option value="">Toutes les actions</option>
                                    <?php foreach ($actions as $action): ?>
                                        <option value="<?= htmlspecialchars($action) ?>" <?= (isset($_GET['action']) && $_GET['action'] == $action) ? 'selected' : '' ?>>
                                            <?= ucfirst(str_replace('_', ' ', htmlspecialchars($action))) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="user_type">Type d'utilisateur</label>
                                <select id="user_type" name="user_type">
                                    <option value="">Tous les types</option>
                                    <?php foreach ($userTypes as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>" <?= (isset($_GET['user_type']) && $_GET['user_type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($type)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_start">Date début</label>
                                <input type="date" id="date_start" name="date_start" value="<?= htmlspecialchars($_GET['date_start'] ?? '') ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="date_end">Date fin</label>
                                <input type="date" id="date_end" name="date_end" value="<?= htmlspecialchars($_GET['date_end'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="filter-advanced" id="advanced-filters" style="display: none;">
                            <div class="filter-group">
                                <label for="entity_type">Type d'entité</label>
                                <select id="entity_type" name="entity_type">
                                    <option value="">Toutes les entités</option>
                                    <?php foreach ($entityTypes as $type): ?>
                                        <option value="<?= htmlspecialchars($type) ?>" <?= (isset($_GET['entity_type']) && $_GET['entity_type'] == $type) ? 'selected' : '' ?>>
                                            <?= ucfirst(htmlspecialchars($type)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="filter-group">
                                <label for="entity_id">ID de l'entité</label>
                                <input type="text" id="entity_id" name="entity_id" value="<?= htmlspecialchars($_GET['entity_id'] ?? '') ?>">
                            </div>
                            
                            <div class="filter-group">
                                <label for="ip_address">Adresse IP</label>
                                <input type="text" id="ip_address" name="ip_address" value="<?= htmlspecialchars($_GET['ip_address'] ?? '') ?>">
                            </div>
                        </div>
                        
                        <div class="filter-buttons">
                            <button type="button" id="toggle-advanced" class="btn secondary">
                                <span id="advanced-text">Afficher les filtres avancés</span>
                                <i class="fas fa-chevron-down" id="advanced-icon"></i>
                            </button>
                            <button type="reset" class="reset-filters" onclick="window.location='system-logs.php'">Réinitialiser</button>
                            <button type="submit" class="apply-filters">Appliquer les filtres</button>
                            <div class="dropdown export-dropdown" style="position: relative; display: inline-block;">
                                <button class="export-csv" style="cursor: pointer;">
                                    <i class="fas fa-file-export"></i> Exporter
                                </button>
                                <div class="dropdown-menu" style="display: none; position: absolute; right: 0; min-width: 160px; background-color: var(--bg-card); box-shadow: var(--shadow); border-radius: var(--radius); z-index: 100; margin-top: 5px;">
                                    <a href="export.php?type=system_logs&format=csv<?= !empty($_GET) ? '&' . http_build_query($_GET) : '' ?>" class="dropdown-item">
                                        <i class="fas fa-file-csv"></i> Export CSV
                                    </a>
                                    <a href="export.php?type=system_logs&format=excel<?= !empty($_GET) ? '&' . http_build_query($_GET) : '' ?>" class="dropdown-item">
                                        <i class="fas fa-file-excel"></i> Export Excel
                                    </a>
                                    <a href="export.php?type=system_logs&format=pdf<?= !empty($_GET) ? '&' . http_build_query($_GET) : '' ?>" class="dropdown-item">
                                        <i class="fas fa-file-pdf"></i> Export PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Tableau des logs -->
                <div class="table-container">
                    <table class="data-table log-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Niveau</th>
                                <th>Utilisateur</th>
                                <th>Catégorie/Action</th>
                                <th>Entité</th>
                                <th>Détails</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($logs)): ?>
                                <tr>
                                    <td colspan="7" class="empty-table">Aucun log trouvé</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?></td>
                                        <td>
                                            <span class="level-badge level-<?= htmlspecialchars($log['level']) ?>">
                                                <?= htmlspecialchars($log['level']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($log['user_id']): ?>
                                                <span class="entity-pill user-type-<?= htmlspecialchars($log['user_type']) ?>">
                                                    <?= htmlspecialchars($log['user_type']) ?>
                                                </span>
                                                <?= htmlspecialchars($log['user_firstname'] . ' ' . $log['user_lastname']) ?>
                                            <?php else: ?>
                                                <span class="entity-pill user-type-<?= htmlspecialchars($log['user_type']) ?>">
                                                    <?= ucfirst(htmlspecialchars($log['user_type'])) ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars(ucfirst($log['category'])) ?></strong> / 
                                            <?= htmlspecialchars(str_replace('_', ' ', $log['action'])) ?>
                                        </td>
                                        <td>
                                            <?php if ($log['entity_type']): ?>
                                                <span class="entity-pill">
                                                    <?= htmlspecialchars(ucfirst($log['entity_type'])) ?> #<?= htmlspecialchars($log['entity_id']) ?>
                                                </span>
                                            <?php else: ?>
                                                -
                                            <?php endif; ?>
                                        </td>
                                        <td class="log-details">
                                            <?= htmlspecialchars(mb_substr($log['details'] ?? '-', 0, 80)) . (mb_strlen($log['details'] ?? '') > 80 ? '...' : '') ?>
                                        </td>
                                        <td>
                                            <button class="btn-icon view-log" data-id="<?= $log['id'] ?>" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Remplacer la section des stats existante par celle-ci -->
                <div class="stats-container" style="background-color: var(--bg-card); border-radius: var(--radius); box-shadow: var(--shadow); padding: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                        <h3><i class="fas fa-chart-pie"></i> Analyse des journaux</h3>
                        <button id="refresh-charts" class="btn secondary" style="padding: 0.4rem 0.75rem; font-size: 0.9rem;">
                            <i class="fas fa-sync-alt"></i> Actualiser
                        </button>
                    </div>
                    
                    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1.5rem;">
                        <div class="stats-card" style="height: 300px; position: relative;">
                            <h4>Distribution par niveau</h4>
                            <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                                <canvas id="levelChart"></canvas>
                            </div>
                        </div>
                        
                        <div class="stats-card" style="height: 300px; position: relative;">
                            <h4>Activité récente (30 jours)</h4>
                            <div class="chart-container" style="position: relative; height: 250px; width: 100%;">
                                <canvas id="activityChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page - 1])) ?>">&laquo; Précédent</a>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $page - 2);
                        $end = min($start + 4, $totalPages);
                        $start = max(1, $end - 4);
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" 
                               class="<?= $i === $page ? 'active' : '' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <a href="?<?= http_build_query(array_merge($_GET, ['page' => $page + 1])) ?>">Suivant &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Modal pour afficher les détails d'un log -->
                <div id="logModal" class="log-modal">
                    <div class="log-modal-content">
                        <div class="log-modal-header">
                            <h2>Détails du log</h2>
                            <span class="log-modal-close">&times;</span>
                        </div>
                        
                        <div class="log-modal-tabs">
                            <div class="log-modal-tab active" data-tab="overview">Vue d'ensemble</div>
                            <div class="log-modal-tab" data-tab="before-after">Avant/Après</div>
                            <div class="log-modal-tab" data-tab="context">Contexte</div>
                            <div class="log-modal-tab" data-tab="request">Requête</div>
                        </div>
                        
                        <div class="log-modal-section active" data-section="overview">
                            <table class="modal-details-table">
                                <tr>
                                    <th>Date:</th>
                                    <td id="modal-date"></td>
                                </tr>
                                <tr>
                                    <th>Niveau:</th>
                                    <td id="modal-level"></td>
                                </tr>
                                <tr>
                                    <th>Utilisateur:</th>
                                    <td id="modal-user"></td>
                                </tr>
                                <tr>
                                    <th>Catégorie:</th>
                                    <td id="modal-category"></td>
                                </tr>
                                <tr>
                                    <th>Action:</th>
                                    <td id="modal-action"></td>
                                </tr>
                                <tr>
                                    <th>Entité:</th>
                                    <td id="modal-entity"></td>
                                </tr>
                                <tr>
                                    <th>Détails:</th>
                                    <td id="modal-details"></td>
                                </tr>
                                <tr>
                                    <th>IP:</th>
                                    <td id="modal-ip"></td>
                                </tr>
                                <tr>
                                    <th>Session ID:</th>
                                    <td id="modal-session"></td>
                                </tr>
                            </table>
                        </div>
                        
                        <div class="log-modal-section" data-section="before-after">
                            <div class="diff-viewer">
                                <div class="diff-column">
                                    <div class="diff-title">État avant</div>
                                    <div id="modal-before-state" class="json-viewer"></div>
                                </div>
                                <div class="diff-column">
                                    <div class="diff-title">État après</div>
                                    <div id="modal-after-state" class="json-viewer"></div>
                                </div>
                            </div>
                            <h3>Changements détectés</h3>
                            <div id="modal-changes" class="changes"></div>
                        </div>
                        
                        <div class="log-modal-section" data-section="context">
                            <div id="modal-context" class="json-viewer"></div>
                        </div>
                        
                        <div class="log-modal-section" data-section="request">
                            <table class="modal-details-table">
                                <tr>
                                    <th>Méthode HTTP:</th>
                                    <td id="modal-http-method"></td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td id="modal-url"></td>
                                </tr>
                                <tr>
                                    <th>User Agent:</th>
                                    <td id="modal-user-agent"></td>
                                </tr>
                                <tr>
                                    <th>Temps d'exécution:</th>
                                    <td id="modal-execution-time"></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle filtres avancés
            const toggleBtn = document.getElementById('toggle-advanced');
            const advancedFilters = document.getElementById('advanced-filters');
            const advancedText = document.getElementById('advanced-text');
            const advancedIcon = document.getElementById('advanced-icon');
            
            if (toggleBtn && advancedFilters) {
                // Vérifier si des filtres avancés sont déjà appliqués
                const hasAdvancedFilters = <?= (isset($_GET['entity_type']) || isset($_GET['entity_id']) || isset($_GET['ip_address'])) ? 'true' : 'false' ?>;
                
                if (hasAdvancedFilters) {
                    advancedFilters.style.display = 'grid';
                    advancedText.textContent = 'Masquer les filtres avancés';
                    advancedIcon.className = 'fas fa-chevron-up';
                }
                
                toggleBtn.addEventListener('click', function() {
                    const isVisible = advancedFilters.style.display !== 'none';
                    advancedFilters.style.display = isVisible ? 'none' : 'grid';
                    advancedText.textContent = isVisible ? 
                        'Afficher les filtres avancés' : 'Masquer les filtres avancés';
                    advancedIcon.className = isVisible ? 
                        'fas fa-chevron-down' : 'fas fa-chevron-up';
                });
            }
            
            // Gestion de la modal
            const modal = document.getElementById('logModal');
            const closeBtn = document.querySelector('.log-modal-close');
            const modalTabs = document.querySelectorAll('.log-modal-tab');
            
            // Fermer la modal quand on clique sur le X
            if (closeBtn && modal) {
                closeBtn.addEventListener('click', function() {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                });
            }
            
            // Fermer la modal quand on clique en dehors
            window.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.remove('show');
                    setTimeout(() => {
                        modal.style.display = 'none';
                    }, 300);
                }
            });
            
            // Gestion des onglets
            modalTabs.forEach(tab => {
                tab.addEventListener('click', function() {
                    // Masquer tous les onglets et sections
                    modalTabs.forEach(t => t.classList.remove('active'));
                    document.querySelectorAll('.log-modal-section').forEach(s => s.classList.remove('active'));
                    
                    // Activer l'onglet cliqué
                    tab.classList.add('active');
                    const tabName = tab.getAttribute('data-tab');
                    document.querySelector(`.log-modal-section[data-section="${tabName}"]`).classList.add('active');
                });
            });
            
            // Charger les données du log dans la modal
            document.querySelectorAll('.view-log').forEach(btn => {
                btn.addEventListener('click', function() {
                    const logId = this.getAttribute('data-id');
                    
                    // Afficher la modal avant de charger les données
                    modal.style.display = 'block';
                    // Force reflow pour déclencher l'animation
                    void modal.offsetWidth;
                    modal.classList.add('show');
                    
                    // Charger les données via AJAX
                    fetch(`ajax/get-log.php?id=${logId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                alert(data.error);
                                return;
                            }
                            
                            // Remplir la modal avec les données
                            document.getElementById('modal-date').textContent = formatDate(data.created_at);
                            
                            const levelBadge = document.createElement('span');
                            levelBadge.className = `level-badge level-${data.level}`;
                            levelBadge.textContent = data.level;
                            document.getElementById('modal-level').innerHTML = '';
                            document.getElementById('modal-level').appendChild(levelBadge);
                            
                            // Utilisateur
                            let userHtml = '';
                            if (data.user_id) {
                                const userTypePill = document.createElement('span');
                                userTypePill.className = `entity-pill user-type-${data.user_type}`;
                                userTypePill.textContent = data.user_type;
                                userHtml = `${userTypePill.outerHTML} ${data.user_firstname} ${data.user_lastname}`;
                                if (data.user_email) {
                                    userHtml += ` <small>(${data.user_email})</small>`;
                                }
                            } else {
                                const systemPill = document.createElement('span');
                                systemPill.className = `entity-pill user-type-${data.user_type}`;
                                systemPill.textContent = data.user_type.charAt(0).toUpperCase() + data.user_type.slice(1);
                                userHtml = systemPill.outerHTML;
                            }
                            document.getElementById('modal-user').innerHTML = userHtml;
                            
                            document.getElementById('modal-category').textContent = data.category.charAt(0).toUpperCase() + data.category.slice(1);
                            document.getElementById('modal-action').textContent = data.action.replace(/_/g, ' ');
                            
                            // Entité
                            if (data.entity_type) {
                                document.getElementById('modal-entity').textContent = `${data.entity_type.charAt(0).toUpperCase() + data.entity_type.slice(1)} #${data.entity_id}`;
                            } else {
                                document.getElementById('modal-entity').textContent = '-';
                            }
                            
                            document.getElementById('modal-details').textContent = data.details || '-';
                            document.getElementById('modal-ip').textContent = data.ip_address || '-';
                            document.getElementById('modal-session').textContent = data.session_id || '-';
                            
                            // État avant/après
                            const beforeState = data.before_state ? JSON.parse(data.before_state) : null;
                            const afterState = data.after_state ? JSON.parse(data.after_state) : null;
                            
                            document.getElementById('modal-before-state').textContent = beforeState ? 
                                JSON.stringify(beforeState, null, 2) : 'Aucune donnée';
                            document.getElementById('modal-after-state').textContent = afterState ? 
                                JSON.stringify(afterState, null, 2) : 'Aucune donnée';
                            
                            // Changements
                            const changesContainer = document.getElementById('modal-changes');
                            changesContainer.innerHTML = '';
                            
                            if (data.context) {
                                const contextObj = JSON.parse(data.context);
                                if (contextObj.changes) {
                                    Object.entries(contextObj.changes).forEach(([field, change]) => {
                                        const changeItem = document.createElement('div');
                                        changeItem.className = 'change-item';
                                        
                                        changeItem.innerHTML = `
                                            <div class="change-field">${field}</div>
                                            <div class="change-old">Avant: ${formatValue(change.old)}</div>
                                            <div class="change-new">Après: ${formatValue(change.new)}</div>
                                        `;
                                        
                                        changesContainer.appendChild(changeItem);
                                    });
                                } else {
                                    changesContainer.textContent = 'Aucun changement détecté';
                                }
                                
                                // Contexte JSON
                                document.getElementById('modal-context').textContent = 
                                    JSON.stringify(contextObj, null, 2);
                            } else {
                                document.getElementById('modal-context').textContent = 'Aucun contexte disponible';
                                changesContainer.textContent = 'Aucun changement détecté';
                            }
                            
                            // Infos requête
                            document.getElementById('modal-http-method').textContent = data.http_method || '-';
                            document.getElementById('modal-url').textContent = data.request_url || '-';
                            document.getElementById('modal-user-agent').textContent = data.user_agent || '-';
                            document.getElementById('modal-execution-time').textContent = 
                                data.execution_time ? `${(data.execution_time * 1000).toFixed(2)} ms` : '-';
                                
                            // Appliquer la coloration syntaxique après avoir rempli le contenu
                            applySyntaxHighlighting();
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Erreur lors du chargement des détails du log');
                        });
                });
            });
            
            // Fonctions utilitaires
            function formatDate(dateStr) {
                const date = new Date(dateStr);
                return date.toLocaleDateString('fr-FR', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
            }
            
            function formatValue(value) {
                if (value === null || value === undefined) {
                    return '<em>null</em>';
                }
                
                if (typeof value === 'object') {
                    return JSON.stringify(value);
                }
                
                return String(value);
            }
            
            function applySyntaxHighlighting() {
                document.querySelectorAll('.json-viewer').forEach(viewer => {
                    const content = viewer.textContent;
                    if (content && content !== 'Aucune donnée' && content !== 'Aucun contexte disponible') {
                        try {
                            const json = JSON.parse(content);
                            viewer.innerHTML = syntaxHighlight(JSON.stringify(json, null, 2));
                        } catch (e) {
                            console.error('Error parsing JSON:', e);
                        }
                    }
                });
            }
            
            function syntaxHighlight(json) {
                json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
                    let cls = 'number';
                    if (/^"/.test(match)) {
                        if (/:$/.test(match)) {
                            cls = 'key';
                            match = match.replace(/"/, '<span style="color:#5B2C6F;">').replace(/":\s*$/, '</span>:');
                            return match;
                        } else {
                            cls = 'string';
                        }
                    } else if (/true|false/.test(match)) {
                        cls = 'boolean';
                    } else if (/null/.test(match)) {
                        cls = 'null';
                    }
                    
                    const colors = {
                        'string': '#2C7873',
                        'number': '#1E3F66',
                        'boolean': '#7D3C98',
                        'null': '#566573'
                    };
                    
                    return `<span style="color:${colors[cls]};">${match}</span>`;
                });
            }

            // Gestion des filtres enregistrés
            const savedFilterSelect = document.getElementById('saved-filter-select');
            const loadFilterBtn = document.getElementById('load-filter');
            const saveFilterBtn = document.getElementById('save-filter');
            const deleteFilterBtn = document.getElementById('delete-filter');
            
            // Charger les filtres sauvegardés
            function loadSavedFilters() {
                const savedFilters = JSON.parse(localStorage.getItem('systemLogFilters') || '{}');
                
                // Vider le select
                savedFilterSelect.innerHTML = '<option value="">Mes filtres enregistrés</option>';
                
                // Ajouter les options
                Object.keys(savedFilters).forEach(name => {
                    const option = document.createElement('option');
                    option.value = name;
                    option.textContent = name;
                    savedFilterSelect.appendChild(option);
                });
                
                // Désactiver les boutons si aucun filtre
                const hasFilters = Object.keys(savedFilters).length > 0;
                loadFilterBtn.disabled = !hasFilters;
                deleteFilterBtn.disabled = !hasFilters;
            }
            
            // Charger les filtres au chargement
            loadSavedFilters();
            
            // Événement pour charger un filtre
            loadFilterBtn.addEventListener('click', function() {
                const filterName = savedFilterSelect.value;
                if (!filterName) return;
                
                const savedFilters = JSON.parse(localStorage.getItem('systemLogFilters') || '{}');
                const filterData = savedFilters[filterName];
                
                if (filterData) {
                    // Appliquer les valeurs aux champs du formulaire
                    Object.keys(filterData).forEach(key => {
                        const field = document.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.value = filterData[key];
                        }
                    });
                    
                    // Soumettre le formulaire
                    document.querySelector('.filter-form form').submit();
                }
            });
            
            // Événement pour sauvegarder un filtre
            saveFilterBtn.addEventListener('click', function() {
                // Demander un nom pour le filtre
                const filterName = prompt('Nom du filtre:');
                if (!filterName) return;
                
                // Récupérer les valeurs actuelles du formulaire
                const form = document.querySelector('.filter-form form');
                const formData = new FormData(form);
                const filterData = {};
                
                for (const [key, value] of formData.entries()) {
                    if (value) {
                        filterData[key] = value;
                    }
                }
                
                // Sauvegarder dans localStorage
                const savedFilters = JSON.parse(localStorage.getItem('systemLogFilters') || '{}');
                savedFilters[filterName] = filterData;
                localStorage.setItem('systemLogFilters', JSON.stringify(savedFilters));
                
                // Recharger la liste
                loadSavedFilters();
                
                // Sélectionner le nouveau filtre
                savedFilterSelect.value = filterName;
            });
            
            // Événement pour supprimer un filtre
            deleteFilterBtn.addEventListener('click', function() {
                const filterName = savedFilterSelect.value;
                if (!filterName) return;
                
                if (confirm(`Êtes-vous sûr de vouloir supprimer le filtre "${filterName}" ?`)) {
                    const savedFilters = JSON.parse(localStorage.getItem('systemLogFilters') || '{}');
                    delete savedFilters[filterName];
                    localStorage.setItem('systemLogFilters', JSON.stringify(savedFilters));
                    
                    // Recharger la liste
                    loadSavedFilters();
                }
            });

            // Ajouter le JavaScript pour le dropdown
            const exportBtn = document.querySelector('.export-csv');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            
            if (exportBtn && dropdownMenu) {
                exportBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    dropdownMenu.style.display = dropdownMenu.style.display === 'block' ? 'none' : 'block';
                });
                
                document.addEventListener('click', function(e) {
                    if (!exportBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                        dropdownMenu.style.display = 'none';
                    }
                });
            }
            
            // Styles CSS pour le dropdown
            const style = document.createElement('style');
            style.textContent = `
                .dropdown-item {
                    display: flex;
                    align-items: center;
                    padding: 0.75rem 1rem;
                    color: var(--text-color);
                    text-decoration: none;
                    transition: background-color 0.15s ease;
                }
                
                .dropdown-item:hover {
                    background-color: var(--primary-light);
                    color: var(--primary-color);
                }
                
                .dropdown-item i {
                    margin-right: 0.5rem;
                    width: 16px;
                    text-align: center;
                }
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>