<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];

    if ($nom && $prenom && $email && $mot_de_passe) {
        $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe) VALUES (?, ?, ?, ?)");
        try {
            $stmt->execute([$nom, $prenom, $email, $hash]);
            afficherMessage('success', "Inscription réussie !");
            header('Location: 02-connexion.php');
            exit();
        } catch (PDOException $e) {
            afficherMessage('danger', "Email déjà utilisé.");
        }
    } else {
        afficherMessage('danger', "Veuillez remplir tous les champs.");
    }
}
?>
<!DOCTYPE html>
<html lang="fr" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inscription</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-black text-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
                <h1 class="mb-4 text-center text-info">Inscription</h1>

                <form method="post" novalidate>
                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control bg-dark text-light border-info" name="prenom" id="prenom" required>
                    </div>

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control bg-dark text-light border-info" name="nom" id="nom" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control bg-dark text-light border-info" name="email" id="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="mot_de_passe" class="form-label">Mot de passe</label>
                        <input type="password" class="form-control bg-dark text-light border-info" name="mot_de_passe" id="mot_de_passe" required>
                    </div>

                    <button type="submit" class="btn btn-info w-100 fw-bold">S'inscrire</button>
                </form>

                <p class="mt-3 text-center">
                    <a href="02-connexion.php" class="text-info text-decoration-underline">Se connecter</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
