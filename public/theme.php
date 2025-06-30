<?php
/**
 * Script de changement de thème (clair/sombre)
 */
session_start();

// Vérifier si un thème est défini dans les cookies
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'light';

// Basculer entre les thèmes clair et sombre
$nouveau_theme = ($theme == 'light') ? 'dark' : 'light';

// Définir le cookie pour 30 jours
setcookie('theme', $nouveau_theme, time() + (3600 * 24 * 30), "/");
// Assurez-vous que la session est bien enregistrée
    session_write_close();

// Rediriger vers la page précédente ou l'accueil
header('Location: ' . (isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php'));
exit();
// Fonction pour récupérer le thème actuel
function getTheme() {
    return $_SESSION['theme'];
}
?>

