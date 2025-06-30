<?php
require_once '../includes/header.php';

// Vérifier si l'utilisateur est connecté
if (!estConnecte()) {
    header('Location: ../connexion.php');
    exit;
}

// Récupérer l'ID du QCM
$qcm_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Vérifier que le QCM existe et est publié
$stmt = $pdo->prepare("
    SELECT q.*, COUNT(qu.id) AS nb_questions
    FROM qcms q
    LEFT JOIN questions qu ON q.id = qu.qcm_id
    WHERE q.id = ? AND q.is_published = TRUE
    GROUP BY q.id
");
$stmt->execute([$qcm_id]);
$qcm = $stmt->fetch();

if (!$qcm) {
    header('Location: passer_qcm.php');
    exit;
}

// Récupérer les questions
$stmt = $pdo->prepare("SELECT * FROM questions WHERE qcm_id = ? ORDER BY RAND()"); // Mélanger les questions 
$stmt->execute([$qcm_id]);
$questions = $stmt->fetchAll();



// Si formulaire soumis (validation finale)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
 
    $score = 0;
    // Vérifier que des réponses ont été soumises
    if (isset($_POST['reponse']) && is_array($_POST['reponse'])) {
        foreach ($_POST['reponse'] as $question_id => $reponse_id) {
            // Vérifier que la réponse est valide
           

            $stmt = $pdo->prepare("
                SELECT est_correcte 
                FROM reponses 
                WHERE id = ? AND question_id = ?
            ");
            $stmt->execute([(int)$reponse_id, (int)$question_id]);// Vérifier que la réponse appartient à la question
            // Récupérer la réponse
            $reponse = $stmt->fetch();
            // Vérifier si la réponse est correcte
           echo "<pre>";
echo "Question ID: $question_id | Réponse ID: $reponse_id\n";
var_dump($reponse);
echo "</pre>";
echo "<pre>";
echo "Question ID: $question_id | Réponse ID: $reponse_id\n";
var_dump($reponse);
echo "</pre>";

            if ($reponse && intval($reponse['est_correcte'])===1) {
                $score++;
            }
        }
        
    }
    
    // Enregistrer le résultat
    $stmt = $pdo->prepare("
        INSERT INTO resultats 
        (utilisateur_id, qcm_id, score) 
        VALUES (?, ?, ?)
    ");
    $stmt->execute([
        $_SESSION['utilisateur']['id'],
        $qcm_id,
        $score
    ]);
    
    // Rediriger vers la page de résultats
    header("Location: resultat_qcm.php?qcm_id=$qcm_id&score=$score");
    exit;
}
?>

<div class="container my-5">
    <div class="card bg-dark border-info">
        <div class="card-header bg-dark border-bottom border-info">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <button onclick="history.back()" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-arrow-left"></i> Retour
                    </button>
                    <span class="ms-3 text-info fw-bold">
                        <?= htmlspecialchars($qcm['titre']) ?>
                    </span>
                </div>
                <div class="text-light">
                    Question <span id="current-question">1</span>/<?= count($questions) ?>
                </div>
            </div>
        </div>
        
        <div class="card-body">
            <form id="qcm-form" method="post">
                <?php foreach ($questions as $i => $question): ?>
                <div class="question-container <?= $i > 0 ? 'd-none' : '' ?>" 
                     data-question-id="<?= $question['id'] ?>">
                    <h5 class="text-light mb-4">
                        Question <?= $i+1 ?>: <?= htmlspecialchars($question['texte']) ?>
                    </h5>
                    
                    <?php 
                    // Récupérer les réponses (mélangées)
                    $stmt = $pdo->prepare("
                        SELECT * FROM reponses 
                        WHERE question_id = ?
                        ORDER BY RAND()
                    ");
                    $stmt->execute([$question['id']]);
                    $reponses = $stmt->fetchAll();
                    ?>
                    
                    <div class="list-group">
                        <?php foreach ($reponses as $reponse): ?>
                        <label class="list-group-item bg-dark text-light border-info mb-2">
                            <input class="form-check-input me-2" 
                                   type="radio" 
                                   name="reponse[<?= $question['id'] ?>]" 
                                   value="<?= $reponse['id'] ?>" required>
                            <?= htmlspecialchars($reponse['texte']) ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <div class="d-flex justify-content-between mt-4">
                    <button type="button" id="btn-precedent" 
                            class="btn btn-outline-info d-none">
                        <i class="bi bi-chevron-left"></i> Précédent
                    </button>
                    
                    <button type="button" id="btn-suivant" 
                            class="btn btn-info ms-auto">
                        Suivant <i class="bi bi-chevron-right"></i>
                    </button>
                    
                    <button type="submit" id="btn-valider" 
                            class="btn btn-success d-none">
                        <i class="bi bi-check-circle"></i> Valider le QCM
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.question-container');
    const btnPrecedent = document.getElementById('btn-precedent');
    const btnSuivant = document.getElementById('btn-suivant');
    const btnValider = document.getElementById('btn-valider');
    const currentQuestionDisplay = document.getElementById('current-question');
    
    let currentQuestion = 0;
    // CORRECTION : Cas où il n'y a qu'une seule question
    if (questions.length === 1) {
        btnSuivant.classList.add('d-none');
        btnValider.classList.remove('d-none');
    }
    
    // Navigation
    btnSuivant.addEventListener('click', function() {
        // Valider que l'utilisateur a sélectionné une réponse
        const selected = document.querySelector(`.question-container:not(.d-none) input[type="radio"]:checked`);
        if (!selected) {
            alert('Veuillez sélectionner une réponse avant de continuer');
            return;
        }
        
        questions[currentQuestion].classList.add('d-none');
        currentQuestion++;
        questions[currentQuestion].classList.remove('d-none');
        currentQuestionDisplay.textContent = currentQuestion + 1;
        
        // Gestion des boutons
        btnPrecedent.classList.remove('d-none');
        if (currentQuestion === questions.length - 1) {
            btnSuivant.classList.add('d-none');
            btnValider.classList.remove('d-none');
        }
    });
    
    btnPrecedent.addEventListener('click', function() {
        questions[currentQuestion].classList.add('d-none');
        currentQuestion--;
        questions[currentQuestion].classList.remove('d-none');
        currentQuestionDisplay.textContent = currentQuestion + 1;
        
        // Gestion des boutons
        btnSuivant.classList.remove('d-none');
        btnValider.classList.add('d-none');
        if (currentQuestion === 0) {
            btnPrecedent.classList.add('d-none');
        }
    });
    
    let formSubmitting = false;

document.getElementById('qcm-form').addEventListener('submit', function() {
    formSubmitting = true;
});

window.addEventListener('beforeunload', function(e) {
    if (!formSubmitting && document.querySelector('input[type="radio"]:checked')) {
        e.preventDefault();
        e.returnValue = 'Vous avez des réponses non enregistrées. Voulez-vous vraiment quitter ?';
    }
});

});
</script>

<style>
.list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}
.list-group-item:hover {
    background-color: rgba(13, 110, 253, 0.1) !important;
}
.question-container {
    min-height: 300px;
}
</style>

<?php require_once '../includes/footer.php'; ?>