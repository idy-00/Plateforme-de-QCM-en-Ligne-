<?php
require_once  '../includes/header.php';

// Vérification des droits admin
if (!estAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom']);
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mot_de_passe = $_POST['mot_de_passe'];
    $confirmation = $_POST['confirmation_mot_de_passe'];

    // Validation
    if (empty($prenom) || empty($nom) || empty($email) || empty($mot_de_passe)) {
        $erreur = "Tous les champs sont obligatoires";
    } elseif ($mot_de_passe !== $confirmation) {
        $erreur = "Les mots de passe ne correspondent pas";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur = "Email invalide";
    } else {
        // Vérification email existant
        $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $erreur = "Cet email est déjà utilisé";
        } else {
            // Insertion
            $hash = password_hash($mot_de_passe, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, 'etudiant')");
            
            if ($stmt->execute([$prenom, $nom, $email, $hash])) {
                $succes = "Étudiant ajouté avec succès";
                // Réinitialisation du formulaire
                $_POST = [];
            } else {
                $erreur = "Erreur lors de l'ajout";
            }
        }
    }
}
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card bg-dark border-info">
                <div class="card-header bg-dark border-bottom border-info">
                    <h3 class="text-info mb-0">
                        <i class="bi bi-person-plus"></i> Ajouter un étudiant
                    </h3>
                </div>
                
                <div class="card-body">
                    <?php if (isset($erreur)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                    <?php elseif (isset($succes)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($succes) ?></div>
                    <?php endif; ?>
                    
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label for="prenom" class="form-label text-light">Prénom</label>
                            <input type="text" class="form-control bg-dark text-light border-info" 
                                   id="prenom" name="prenom" 
                                   value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label text-light">Nom</label>
                            <input type="text" class="form-control bg-dark text-light border-info" 
                                   id="nom" name="nom" 
                                   value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label text-light">Email</label>
                            <input type="email" class="form-control bg-dark text-light border-info" 
                                   id="email" name="email" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label text-light">Mot de passe</label>
                            <input type="password" class="form-control bg-dark text-light border-info" 
                                   id="mot_de_passe" name="mot_de_passe" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="confirmation_mot_de_passe" class="form-label text-light">Confirmation</label>
                            <input type="password" class="form-control bg-dark text-light border-info" 
                                   id="confirmation_mot_de_passe" name="confirmation_mot_de_passe" required>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-save"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-3">
                <a href="utilisateurs.php" class="btn btn-outline-info">
                    <i class="bi bi-arrow-left"></i> Retour à la liste
                </a>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once  '../includes/footer.php';
?>