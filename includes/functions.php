<?php
require_once 'db.php';

// Modification de la fonction ajouterUtilisateur pour inclure le paramètre $classe
function ajouterUtilisateur($prenom, $nom, $email, $mot_de_passe, $classe) {
    global $pdo;

    // Vérifier les champs requis, y compris la classe
    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe) || empty($classe)) {
        return "Tous les champs sont requis.";
    }

    // Vérifier si l'email est déjà utilisé
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return "Email déjà utilisé.";
    }

    $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
    // Requête SQL modifiée pour insérer la 'classe'
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, role, classe) VALUES (?, ?, ?, ?, 'etudiant', ?)");
    $stmt->execute([$prenom, $nom, $email, $hash, $classe]);

    return true;
}

function verifierConnexion($email, $mot_de_passe) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) return "Email incorrect.";
    if (!password_verify($mot_de_passe, $user['mot_de_passe'])) return "Mot de passe incorrect.";

    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['utilisateur'] = $user;
    return true;
}

function estConnecte() {
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['utilisateur']);
}

function utilisateurConnecte() {
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION['utilisateur'] ?? null;
}

// section pour les administrateurs
function estAdmin(){
    // Démarrer la session si ce n'est pas déjà fait
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    return isset($_SESSION['utilisateur']) && $_SESSION['utilisateur']['role'] === 'admin';
}

function afficherMessage($type, $texte) {
    echo "<div class='alert alert-$type mt-3'>$texte</div>";
}