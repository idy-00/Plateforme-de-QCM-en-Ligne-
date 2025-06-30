<?php
require_once '../includes/header.php';

// Vérification de connexion
if (!estConnecte()) {
    header('Location: connexion.php');
    exit;
}

$utilisateur = $_SESSION['utilisateur'];
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <!-- Carte de profil -->
            <div class="card bg-dark border-info">
                <div class="card-header bg-dark border-bottom border-info">
                    <h3 class="text-info mb-0">
                        <i class="bi bi-person-circle"></i> Mon Profil
                    </h3>
                </div>
                
                <div class="card-body">
                    <!-- Photo de profil par défaut -->
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-secondary d-inline-flex align-items-center justify-content-center" 
                             style="width: 120px; height: 120px;">
                            <i class="bi bi-person-fill text-light" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    
                    <!-- Informations utilisateur -->
                    <div class="mb-3">
                        <label class="text-light-50">Nom complet</label>
                        <div class="form-control bg-dark text-light border-info">
                            <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-light-50">Email</label>
                        <div class="form-control bg-dark text-light border-info">
                            <?= htmlspecialchars($utilisateur['email']) ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="text-light-50">Rôle</label>
                        <div class="form-control bg-dark text-light border-info">
                            <?= $utilisateur['role'] === 'admin' ? 'Administrateur' : 'Étudiant' ?>
                        </div>
                    </div>
                    
                    <!-- Bouton d'édition -->
                    <div class="d-grid gap-2 mt-4">
                        <a href="editer_profil.php" class="btn btn-outline-info">
                            <i class="bi bi-pencil-square"></i> Modifier le profil
                        </a>
                    </div>
                </div>
                
                <div class="card-footer bg-dark border-top border-info text-end">
                    <small class="text-light-50">
                        Membre depuis <?= date('d/m/Y', strtotime($utilisateur['date_inscription'])) ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once '../includes/footer.php';
?>