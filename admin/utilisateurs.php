<?php
require_once  '../includes/header.php';

// Vérification des droits admin
if (!estAdmin()) {
    header('Location: ../index.php');
    exit;
}

// Récupération des utilisateurs
$stmt = $pdo->query("SELECT id, prenom, nom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC");
$utilisateurs = $stmt->fetchAll();

// Traitement suppression utilisateur
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];
    
    // Empêche la suppression de soi-même
    if ($id !== $_SESSION['utilisateur']['id']) {
        $stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($stmt->rowCount() > 0) {
            $_SESSION['message'] = "Utilisateur supprimé avec succès";
            header('Location: utilisateurs.php');
            exit;
        }
    }
}
?>

<div class="container py-5">
    <!-- Message de confirmation -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_SESSION['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <div class="card bg-dark border-info">
        <div class="card-header bg-dark border-bottom border-info">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="text-info mb-0">
                    <i class="bi bi-people-fill"></i> Gestion des utilisateurs
                </h3>
                <a href="ajouter_etudiants.php" class="btn btn-info">
                    <i class="bi bi-person-plus"></i> Ajouter
                </a>
            </div>
        </div>
        
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-dark table-hover align-middle">
                    <thead>
                        <tr class="text-info">
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Rôle</th>
                            <th>Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                        <tr>
                            <td><?= $utilisateur['id'] ?></td>
                            <td><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></td>
                            <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                            <td>
                                <span class="badge bg-<?= $utilisateur['role'] === 'admin' ? 'info' : 'secondary' ?>">
                                    <?= $utilisateur['role'] === 'admin' ? 'Admin' : 'Étudiant' ?>
                                </span>
                            </td>
                            <td><?= date('d/m/Y', strtotime($utilisateur['date_inscription'])) ?></td>
                            <td>
                                <div class="d-flex gap-2">
                                    <a href="modifier_utilisateur.php?id=<?= $utilisateur['id'] ?>" 
                                       class="btn btn-sm btn-outline-info"
                                       title="Modifier">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    
                                    <?php if ($utilisateur['id'] !== $_SESSION['utilisateur']['id']): ?>
                                    <a href="utilisateurs.php?supprimer=<?= $utilisateur['id'] ?>" 
                                       class="btn btn-sm btn-outline-danger"
                                       title="Supprimer"
                                       onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-dark border-top border-info">
            <small class="text-light-50">
                Total : <?= count($utilisateurs) ?> utilisateur(s)
            </small>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once '../includes/footer.php';
?>