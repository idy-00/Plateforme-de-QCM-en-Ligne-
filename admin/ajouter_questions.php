<?php
require_once '../includes/header.php';

// Vérification admin et paramètre qcm_id
if (!estAdmin() || !isset($_GET['qcm_id'])) {
    header('Location: ../index.php');
    exit;
}

$qcm_id = (int)$_GET['qcm_id'];

// Vérification que le QCM existe
$stmt = $pdo->prepare("SELECT id FROM qcms WHERE id = ?");
$stmt->execute([$qcm_id]);
$qcm_existe = $stmt->fetch();

if (!$qcm_existe) {
    $_SESSION['erreur'] = "Le QCM sélectionné n'existe pas";
    header('Location: ajouter_qcm.php');
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    try {
        $pdo->beginTransaction();
        
        // 1. Insertion de la question (avec texte_question comme colonne)
        $stmt = $pdo->prepare("INSERT INTO questions (qcm_id, texte) VALUES (?, ?)");
        $stmt->execute([$qcm_id, trim($_POST['question'])]); 
        $question_id = $pdo->lastInsertId();
        
        // 2. Insertion des réponses
        foreach ($_POST['reponses'] as $index => $texte) {
            $est_correcte = ($index == $_POST['bonne_reponse']);
            $stmt = $pdo->prepare("INSERT INTO reponses (question_id, texte, est_correcte) VALUES (?, ?, ?)");
            $stmt->execute([$question_id, trim($texte), $est_correcte]);
        }
        
        $pdo->commit();
        $_SESSION['succes'] = "Question ajoutée avec succès !";
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['erreur'] = "Erreur : " . $e->getMessage();
    }
    
    header("Location: ajouter_questions.php?qcm_id=$qcm_id");
    exit;
}

// Récupérer les questions existantes
$questions = $pdo->prepare("SELECT * FROM questions WHERE qcm_id = ?");
$questions->execute([$qcm_id]);

?>

<!-- HTML -->
<div class="container my-5">
    <div class="card bg-dark border-info">
        <div class="card-header">
            <h3 class="text-info">
                <i class="bi bi-question-circle"></i> Ajout de questions
                <small class="text-light">(QCM #<?= $qcm_id ?>)</small>
            </h3>
        </div>
        
        <div class="card-body">
            <!-- Messages d'erreur/succès -->
            <?php if (isset($_SESSION['erreur'])): ?>
                <div class="alert alert-danger"><?= $_SESSION['erreur'] ?></div>
                <?php unset($_SESSION['erreur']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['succes'])): ?>
                <div class="alert alert-success"><?= $_SESSION['succes'] ?></div>
                <?php unset($_SESSION['succes']); ?>
            <?php endif; ?>
            
            <!-- Formulaire -->
            <form method="post">
                <div class="mb-3">
                    <label class="text-light">Question</label>
                    <textarea name="question" class="form-control bg-dark text-light border-info" required></textarea>
                </div>
                
                <h5 class="text-light mt-4">Réponses :</h5>
                <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text">
                            <input type="radio" name="bonne_reponse" value="<?= $i ?>" <?= $i === 0 ? 'checked' : '' ?>>
                        </span>
                        <input type="text" name="reponses[]" class="form-control bg-dark text-light border-info" required>
                    </div>
                </div>
                <?php endfor; ?>
                
                <button type="submit" class="btn btn-info">
                    <i class="bi bi-save"></i> Enregistrer
                </button>
            </form>
            
            <!-- Liste existante -->
            <h5 class="text-light mt-5">Questions existantes :</h5>
            <ul class="list-group">
                <?php while ($question = $questions->fetch()): ?>
                <li class="list-group-item bg-dark text-light border-info">
                    <?= htmlspecialchars($question['texte']) ?>
                </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>
</div>
<div class="text-center mt-5">
    <a href="finaliser_qcm.php?qcm_id=<?= $qcm_id ?>" 
   class="btn btn-success btn-lg btn-finaliser py-3 px-5">
    <i class="bi bi-check-all"></i> FINALISER LE QCM
</a>
    <p class="text-light mt-3">Cliquez ici pour finaliser le QCM et le rendre disponible aux étudiants.</p>
</div>
<style>
.btn-finaliser {
    transition: all 0.3s;
    font-weight: bold;
}
.btn-finaliser:hover {
    transform: scale(1.05);
}
</style>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>