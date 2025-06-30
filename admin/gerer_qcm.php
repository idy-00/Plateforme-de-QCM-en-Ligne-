<?php
require_once '../includes/header.php';

// Vérifie que l'utilisateur est admin
if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

// Traitement suppression
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM qcms WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: gerer_qcm.php');
    exit;
}

// Récupération des QCMs
$qcms = $pdo->query("SELECT * FROM qcms ORDER BY created_at DESC")->fetchAll();
?>

<div class="container my-5">
    <div class="card bg-dark border-info">
        <div class="card-header border-bottom border-info d-flex justify-content-between align-items-center">
            <h3 class="text-info mb-0"><i class="bi bi-folder2-open"></i> Gérer les QCM</h3>
            <a href="ajouter_qcm.php" class="btn btn-info btn-sm">
                <i class="bi bi-plus-circle"></i> Nouveau QCM
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($qcms)): ?>
                <p class="text-light">Aucun QCM enregistré.</p>
            <?php else: ?>
                <table class="table table-dark table-striped border border-info">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Publié</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($qcms as $qcm): ?>
                            <tr>
                                <td><?= $qcm['id'] ?></td>
                                <td><?= htmlspecialchars($qcm['titre']) ?></td>
                                <td><?= $qcm['is_published'] ? '<span class="text-success">Oui</span>' : '<span class="text-warning">Non</span>' ?></td>
                                <td><?= date('d/m/Y', strtotime($qcm['created_at'])) ?></td>
                                <td>
                                    <a href="modifier_qcm.php?id=<?= $qcm['id'] ?>" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="?delete=<?= $qcm['id'] ?>" onclick="return confirm('Supprimer ce QCM ?')" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
