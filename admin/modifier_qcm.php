<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

// Récupérer l'ID du QCM à modifier
$qcm_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Récupérer le QCM
$stmt = $pdo->prepare("SELECT * FROM qcms WHERE id = ?");
$stmt->execute([$qcm_id]);
$qcm = $stmt->fetch();

if (!$qcm) {
    header('Location: gerer_qcm.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    if ($titre && $description) {
        $stmt = $pdo->prepare("UPDATE qcms SET titre = ?, description = ?, is_published = ? WHERE id = ?");
        $stmt->execute([$titre, $description, $is_published, $qcm_id]);

        header("Location: gerer_qcm.php");
        exit;
    } else {
        $message = "Tous les champs sont obligatoires.";
    }
}
?>

<div class="container my-5">
    <div class="card bg-dark border-info text-light">
        <div class="card-header border-bottom border-info d-flex justify-content-between align-items-center">
            <h3 class="text-info mb-0">
                <i class="bi bi-pencil-square"></i> Modifier le QCM
            </h3>
            <a href="gerer_qcm.php" class="btn btn-outline-info btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="titre" class="form-label">Titre du QCM</label>
                    <input type="text" class="form-control bg-dark text-light border-info" name="titre" id="titre" value="<?= htmlspecialchars($qcm['titre']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control bg-dark text-light border-info" name="description" id="description" rows="4" required><?= htmlspecialchars($qcm['description']) ?></textarea>
                </div>

                <div class="form-check form-switch mb-4">
                    <input class="form-check-input bg-info" type="checkbox" name="is_published" id="is_published" <?= $qcm['is_published'] ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_published">Publié</label>
                </div>
                <a href="modifier_questions.php?id=<?= $qcm['id'] ?>" class="btn btn-outline-info ms-2">
    <i class="bi bi-question-circle"></i> Modifier les questions
</a>
                

                <button type="submit" class="btn btn-info fw-bold">
                    <i class="bi bi-save"></i> Enregistrer les modifications
                </button>
            </form>
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
