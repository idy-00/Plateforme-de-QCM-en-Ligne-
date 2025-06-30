<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$qcm_id = isset($_GET['qcm_id']) ? (int)$_GET['qcm_id'] : 0;

// Vérifie que le QCM existe
$stmt = $pdo->prepare("SELECT * FROM qcms WHERE id = ?");
$stmt->execute([$qcm_id]);
$qcm = $stmt->fetch();

if (!$qcm) {
    header('Location: gerer_qcm.php');
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $texte = trim($_POST['texte']);
    $reponses = $_POST['reponses'];
    $correcte = $_POST['correcte']; // index de la réponse correcte

    if ($texte && count($reponses) === 4 && $correcte !== '') {
        // 1. Ajouter la question
        $stmt = $pdo->prepare("INSERT INTO questions (qcm_id, texte) VALUES (?, ?)");
        $stmt->execute([$qcm_id, $texte]);
        $question_id = $pdo->lastInsertId();

        // 2. Ajouter les réponses
        foreach ($reponses as $index => $rep) {
            $texte_rep = trim($rep);
            $est_correcte = ($index == $correcte) ? 1 : 0;

            $stmt = $pdo->prepare("INSERT INTO reponses (question_id, texte_reponse, est_correcte) VALUES (?, ?, ?)");
            $stmt->execute([$question_id, $texte_rep, $est_correcte]);
        }

        header("Location: modifier_questions.php?id=$qcm_id");
        exit;
    } else {
        $message = "Veuillez remplir la question et 4 réponses, et sélectionner la bonne réponse.";
    }
}
?>

<div class="container my-5">
    <div class="card bg-dark border-info text-light">
        <div class="card-header border-bottom border-info d-flex justify-content-between align-items-center">
            <h4 class="text-info mb-0">
                <i class="bi bi-plus-circle"></i> Ajouter une question au QCM : <?= htmlspecialchars($qcm['titre']) ?>
            </h4>
            <a href="modifier_questions.php?id=<?= $qcm['id'] ?>" class="btn btn-outline-info btn-sm">
                <i class="bi bi-arrow-left"></i> Retour
            </a>
        </div>

        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger"><?= $message ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label for="texte" class="form-label">Texte de la question</label>
                    <textarea name="texte" id="texte" rows="3" class="form-control bg-dark text-light border-info" required></textarea>
                </div>

                <label class="form-label">Réponses possibles</label>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="input-group mb-2">
                        <div class="input-group-text bg-dark border-info">
                            <input type="radio" name="correcte" value="<?= $i ?>" required>
                        </div>
                        <input type="text" name="reponses[]" class="form-control bg-dark text-light border-info" placeholder="Réponse <?= $i + 1 ?>" required>
                    </div>
                <?php endfor; ?>

                <button type="submit" class="btn btn-info fw-bold mt-3">
                    <i class="bi bi-save"></i> Enregistrer la question
                </button>
            </form>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
