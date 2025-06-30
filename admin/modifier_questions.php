<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$qcm_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifie que le QCM existe
$stmt = $pdo->prepare("SELECT * FROM qcms WHERE id = ?");
$stmt->execute([$qcm_id]);
$qcm = $stmt->fetch();

if (!$qcm) {
    header('Location: gerer_qcm.php');
    exit;
}

// Récupération des questions de ce QCM
$stmt = $pdo->prepare("SELECT * FROM questions WHERE qcm_id = ? ORDER BY id");
$stmt->execute([$qcm_id]);
$questions = $stmt->fetchAll();
?>

<div class="container my-5">
    <div class="card bg-dark border-info text-light">
        <div class="card-header border-bottom border-info d-flex justify-content-between align-items-center">
            <h4 class="text-info mb-0">
                <i class="bi bi-question-circle"></i> Questions du QCM : <?= htmlspecialchars($qcm['titre']) ?>
            </h4>
            <a href="ajouter_question.php?qcm_id=<?= $qcm['id'] ?>" class="btn btn-info btn-sm">
                <i class="bi bi-plus-circle"></i> Ajouter une question
            </a>
        </div>

        <div class="card-body">
            <?php if (empty($questions)): ?>
                <p class="text-light">Aucune question pour ce QCM.</p>
            <?php else: ?>
                <div class="list-group">
                    <?php foreach ($questions as $q): ?>
                        <div class="list-group-item bg-dark text-light border-info mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><?= htmlspecialchars($q['texte']) ?></span>
                                <div>
                                    <a href="modifier_question.php?id=<?= $q['id'] ?>" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="supprimer_question.php?id=<?= $q['id'] ?>&qcm_id=<?= $qcm['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Supprimer cette question ?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
