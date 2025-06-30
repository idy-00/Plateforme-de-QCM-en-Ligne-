<?php
require_once '../includes/header.php';

// Récupère les QCM publiés et non expirés
$now = date('Y-m-d H:i:s');
$stmt = $pdo->prepare("
    SELECT q.*, 
           COUNT(qu.id) AS nb_questions,
           u.prenom AS createur
    FROM qcms q
    LEFT JOIN questions qu ON q.id = qu.qcm_id
    LEFT JOIN utilisateurs u ON q.created_by = u.id
    WHERE q.is_published = TRUE
    GROUP BY q.id
    ORDER BY q.created_at DESC
");
$stmt->execute();
$qcms = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-info">
                <i class="bi bi-journal-bookmark"></i> QCM Disponibles
            </h2>
            <p class="text-light-50">Sélectionnez un QCM pour commencer</p>
        </div>
    </div>

    <div class="row">
        <?php if (empty($qcms)): ?>
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun QCM disponible pour le moment.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($qcms as $qcm): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card bg-dark border-info h-100">
                    <div class="card-header bg-dark border-bottom border-info">
                        <h5 class="text-info mb-0"><?= htmlspecialchars($qcm['titre']) ?></h5>
                    </div>
                    <div class="card-body">
                        <p class="text-light-50"><?= htmlspecialchars($qcm['description']) ?></p>
                        <ul class="list-unstyled text-light-50 small">
                            <li><i class="bi bi-question-circle"></i> <?= $qcm['nb_questions'] ?> questions</li>
                            <li><i class="bi bi-person"></i> Créé par <?= htmlspecialchars($qcm['createur']) ?></li>
                            <li><i class="bi bi-calendar"></i> <?= date('d/m/Y', strtotime($qcm['created_at'])) ?></li>
                        </ul>
                    </div>
                    <div class="card-footer bg-dark border-top border-info text-center">
                        <a href="passer_qcm.php?id=<?= $qcm['id'] ?>" class="btn btn-info w-100">
                            <i class="bi bi-play-circle"></i> Commencer
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<style>
    .qcm-card {
    transition: transform 0.3s;
}
.qcm-card:hover {
    transform: translateY(-5px);
}
    .btn-info {
        transition: background-color 0.3s, border-color 0.3s;
    }
    .btn-info:hover {
        background-color: #007bff;
        border-color: #007bff;
    }
    

</style>

<?php require_once '../includes/footer.php'; ?>