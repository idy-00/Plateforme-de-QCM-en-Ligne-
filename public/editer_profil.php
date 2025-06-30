<?php
require_once '../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    header('Location: ../connexion.php');
    exit;
}

// Récupérer les infos actuelles de l'utilisateur
$utilisateur = $_SESSION['utilisateur'];

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);

    // Validation
    if (empty($nom) || empty($prenom)) {
        $erreur = "Le nom et le prénom sont obligatoires";
    } else {
        try {
            // Mise à jour en base
            $stmt = $pdo->prepare("UPDATE utilisateurs SET nom = ?, prenom = ? WHERE id = ?");
            $stmt->execute([$nom, $prenom, $utilisateur['id']]);

            // Mise à jour de la session
            $_SESSION['utilisateur']['nom'] = $nom;
            $_SESSION['utilisateur']['prenom'] = $prenom;

            $_SESSION['succes'] = "Profil mis à jour avec succès";
            header('Location: 03-profil.php');
            exit;
        } catch (PDOException $e) {
            $erreur = "Une erreur est survenue : " . $e->getMessage();
        }
    }
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card bg-dark border-info">
                <div class="card-header bg-dark border-bottom border-info">
                    <h3 class="text-info mb-0">
                        <i class="bi bi-person-gear"></i> Modifier mon profil
                    </h3>
                </div>
                
                <div class="card-body">
                    <?php if (isset($erreur)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($erreur) ?></div>
                    <?php endif; ?>

                    <form method="post">
                        <div class="mb-3">
                            <label for="prenom" class="form-label text-light">Prénom</label>
                            <input type="text" class="form-control bg-dark text-light border-info" 
                                   id="prenom" name="prenom" 
                                   value="<?= htmlspecialchars($utilisateur['prenom'] ?? '') ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nom" class="form-label text-light">Nom</label>
                            <input type="text" class="form-control bg-dark text-light border-info" 
                                   id="nom" name="nom" 
                                   value="<?= htmlspecialchars($utilisateur['nom'] ?? '') ?>" required>
                        </div>
                        
                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-info">
                                <i class="bi bi-save"></i> Enregistrer les modifications
                            </button>
                            <a href="profil.php" class="btn btn-outline-info">
                                <i class="bi bi-x-circle"></i> Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .form-control:focus {
        border-color: #0dcaf0;
        box-shadow: 0 0 0 0.25rem rgba(13, 202, 240, 0.25);
    }
</style>

<?php require_once '../includes/footer.php'; ?>