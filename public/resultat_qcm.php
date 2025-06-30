<?php
require_once '../includes/header.php';

// Vérifie que l'utilisateur est connecté
if (!estConnecte()) {
    header('Location: 02-connexion.php');
    exit;
}

$utilisateur = utilisateurConnecte();

// Récupération des paramètres
$qcm_id = isset($_GET['qcm_id']) ? (int)$_GET['qcm_id'] : 0;
$score = isset($_GET['score']) ? (int)$_GET['score'] : null;

// Vérifie que le QCM existe
$stmt = $pdo->prepare("SELECT titre FROM qcms WHERE id = ?");
$stmt->execute([$qcm_id]);
$qcm = $stmt->fetch();

if (!$qcm || $score === null) {
    header('Location: espace_qcm.php');
    exit;
}

// Nombre total de questions
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE qcm_id = ?");
$stmt->execute([$qcm_id]);
$total = $stmt->fetchColumn();

// Calcul du pourcentage
$percent = round(($score / $total) * 100);
?>

<div class="container my-5">
    <div class="card bg-dark border-info text-light">
        <div class="card-header border-bottom border-info d-flex justify-content-between align-items-center">
            <span class="fw-bold text-info">
                <i class="bi bi-bar-chart"></i> Résultat du QCM
            </span>
            <a href="passer_qcm.php" class="btn btn-outline-info btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card-body text-center">
            <h2 class="text-info"><?= htmlspecialchars($qcm['titre']) ?></h2>
            <p class="mt-4 fs-5">Bonjour <strong><?= htmlspecialchars($utilisateur['prenom']) ?></strong>,</p>
            <p class="fs-4">Vous avez obtenu un score de :</p>

            <div class="display-3 text-success fw-bold mb-3">
                <?= $score ?> / <?= $total ?>
            </div>

            <p class="fs-5 text-info">Soit <?= $percent ?>%</p>

            <?php if ($percent >= 50): ?>
                <div class="alert alert-success mt-4">
                    <i class="bi bi-check-circle-fill"></i> Bravo ! Vous avez réussi ce QCM.
                </div>
            <?php else: ?>
                <div class="alert alert-danger mt-4">
                    <i class="bi bi-x-circle-fill"></i> Dommage, vous pouvez réessayer pour vous améliorer.
                </div>
            <?php endif; ?>

            <a href="passer_qcm.php?id=<?= $qcm_id ?>" class="btn btn-info mt-4">
                <i class="bi bi-arrow-repeat"></i> Refaire le QCM
            </a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
