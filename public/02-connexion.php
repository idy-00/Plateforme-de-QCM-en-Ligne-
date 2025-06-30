<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $mot_de_passe = trim($_POST['mot_de_passe']);

    if (!empty($email) && !empty($mot_de_passe)) {
        // Préparation de la requête pour éviter les injections SQL
        $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        $utilisateur = $stmt->fetch();

        if ($utilisateur && password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            session_start();
            // Stockage des informations utilisateur dans la session
            $_SESSION['utilisateur'] = [
                'id' => $utilisateur['id'],
                'prenom' => $utilisateur['prenom'],
                'nom' => $utilisateur['nom'],
                'email' => $utilisateur['email'],
                'role' => $utilisateur['role'],
                'date_inscription' => $utilisateur['date_inscription']
            ];
            // Redirection vers la page d'accueil ou tableau de bord
            header('Location: index.php');
            exit;
        } else {
            afficherMessage('danger', "Identifiants incorrects.");
        }
    } else {
        afficherMessage('warning', "Veuillez remplir tous les champs.");
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Connexion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black text-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <h1 class="mb-4 text-center text-info">Connexion</h1>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control bg-dark text-light border-info" name="email" id="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control bg-dark text-light border-info" name="mot_de_passe" id="mot_de_passe" required>
                    </div>

                    <button type="submit" class="btn btn-info w-100 fw-bold">Se connecter</button>
                </form>

                <p class="mt-3 text-center">
                    <a href="01-inscription.php" class="text-info text-decoration-underline">Créer un compte</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
