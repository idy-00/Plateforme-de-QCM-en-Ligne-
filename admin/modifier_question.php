<?php
require_once '../includes/header.php'; // Assurez-vous que ce fichier inclut db.php et functions.php

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$question_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// 1. Récupérer la question à modifier
$stmt_q = $pdo->prepare("SELECT * FROM questions WHERE id = ?");
$stmt_q->execute([$question_id]);
$question = $stmt_q->fetch();

if (!$question) {
    // Si la question n'existe pas, rediriger
    afficherMessage('danger', 'Question non trouvée.');
    header('Location: gerer_qcm.php'); // Rediriger vers la page de gestion des QCM
    exit;
}

// 2. Récupérer les réponses associées à cette question
$stmt_r = $pdo->prepare("SELECT * FROM reponses WHERE question_id = ?");
$stmt_r->execute([$question_id]);
$reponses = $stmt_r->fetchAll();

// --- Traitement du formulaire POST ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nouveau_texte_question = trim($_POST['texte_question']);

    // Vérifier si le texte de la question est rempli
    if (empty($nouveau_texte_question)) {
        afficherMessage('danger', 'Le texte de la question ne peut pas être vide.');
    } else {
        // Mettre à jour le texte de la question
        $stmt_update_q = $pdo->prepare("UPDATE questions SET texte = ? WHERE id = ?");
        $stmt_update_q->execute([$nouveau_texte_question, $question_id]);
        
        // Gérer la suppression des réponses existantes (si des IDs sont passés par le JS)
        if (isset($_POST['reponse_ids_a_supprimer']) && is_array($_POST['reponse_ids_a_supprimer'])) {
            foreach ($_POST['reponse_ids_a_supprimer'] as $id_reponse_a_supprimer) {
                $id_reponse_a_supprimer = (int)$id_reponse_a_supprimer; // Sécuriser l'ID
                $stmt_delete_rep = $pdo->prepare("DELETE FROM reponses WHERE id = ? AND question_id = ?");
                $stmt_delete_rep->execute([$id_reponse_a_supprimer, $question_id]);
            }
        }

        // Gérer les réponses soumises (mises à jour ou nouvelles)
        if (isset($_POST['reponses']) && is_array($_POST['reponses'])) {
            foreach ($_POST['reponses'] as $index => $reponse_texte) {
                // L'ID de la réponse existante est passé dans un champ caché 'reponse_ids[]'
                // Pour les nouvelles réponses, 'reponse_ids[]' sera vide.
                $reponse_id = $_POST['reponse_ids'][$index] ?? null; 
                
                // Vérifier si la case "correcte" pour cette réponse est cochée
                // Les valeurs de 'est_correcte[]' sont les INDEX des réponses cochées.
                $est_correcte = isset($_POST['est_correcte']) && in_array($index, $_POST['est_correcte']) ? 1 : 0;

                if (!empty(trim($reponse_texte))) { // Ne traiter que les réponses non vides
                    if ($reponse_id) {
                        // Mise à jour d'une réponse existante
                        $stmt_update_r = $pdo->prepare("UPDATE reponses SET texte = ?, est_correcte = ? WHERE id = ? AND question_id = ?");
                        $stmt_update_r->execute([trim($reponse_texte), $est_correcte, $reponse_id, $question_id]);
                    } else {
                        // Ajout d'une nouvelle réponse
                        $stmt_add_r = $pdo->prepare("INSERT INTO reponses (question_id, texte, est_correcte) VALUES (?, ?, ?)");
                        $stmt_add_r->execute([$question_id, trim($reponse_texte), $est_correcte]);
                    }
                }
            }
        }

        afficherMessage('success', 'Question et réponses mises à jour avec succès !');
        // Recharger la page des questions du QCM pour voir les modifications
        header("Location: modifier_questions.php?id=" . $question['qcm_id']);
        exit;
    }
}
?>

<div class="container my-5">
    <div class="card bg-dark border-info text-light">
        <div class="card-header border-bottom border-info">
            <h4 class="text-info mb-0">
                <i class="bi bi-pencil"></i> Modifier la question
            </h4>
        </div>

        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="texte_question" class="form-label">Texte de la question</label>
                    <textarea name="texte_question" id="texte_question" class="form-control bg-dark text-light border-info" rows="3" required><?= htmlspecialchars($question['texte']) ?></textarea>
                </div>

                <h5>Réponses :</h5>
                <div id="reponses-container">
                    <?php foreach ($reponses as $index => $rep): ?>
                        <div class="input-group mb-2 reponse-item" data-reponse-id="<?= $rep['id'] ?>">
                            <input type="hidden" name="reponse_ids[]" value="<?= $rep['id'] ?>">
                            <input type="text" name="reponses[]" class="form-control bg-dark text-light border-info" value="<?= htmlspecialchars($rep['texte']) ?>" placeholder="Texte de la réponse" required>
                            <div class="input-group-text bg-dark border-info">
                                <input class="form-check-input mt-0" type="checkbox" name="est_correcte[]" value="<?= $index ?>" <?= $rep['est_correcte'] ? 'checked' : '' ?> aria-label="Réponse correcte">
                                <label class="form-check-label ms-2 text-light">Correcte</label>
                            </div>
                            <button type="button" class="btn btn-outline-danger remove-reponse">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" id="add-reponse" class="btn btn-outline-success btn-sm mb-3">
                    <i class="bi bi-plus"></i> Ajouter une réponse
                </button>

                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-info">Sauvegarder les modifications</button>
                    <a href="modifier_questions.php?id=<?= $question['qcm_id'] ?>" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const reponsesContainer = document.getElementById('reponses-container');
    const addReponseButton = document.getElementById('add-reponse');
    let reponseIndex = <?= count($reponses); ?>; // Assure un index unique pour les nouvelles réponses

    // Gérer l'ajout d'un nouveau champ de réponse
    addReponseButton.addEventListener('click', function() {
        const newReponseDiv = document.createElement('div');
        newReponseDiv.classList.add('input-group', 'mb-2', 'reponse-item');
        // Note: value="" pour reponse_ids[] indique que c'est une nouvelle réponse
        // value="${reponseIndex}" pour est_correcte[] permet d'associer la checkbox à l'index PHP
        newReponseDiv.innerHTML = `
            <input type="hidden" name="reponse_ids[]" value="">
            <input type="text" name="reponses[]" class="form-control bg-dark text-light border-info" placeholder="Texte de la réponse" required>
            <div class="input-group-text bg-dark border-info">
                <input class="form-check-input mt-0" type="checkbox" name="est_correcte[]" value="${reponseIndex}" aria-label="Réponse correcte">
                <label class="form-check-label ms-2 text-light">Correcte</label>
            </div>
            <button type="button" class="btn btn-outline-danger remove-reponse">
                <i class="bi bi-trash"></i>
            </button>
        `;
        reponsesContainer.appendChild(newReponseDiv);
        reponseIndex++; // Incrémente l'index pour la prochaine nouvelle réponse
    });

    // Gérer la suppression de réponses (existantes ou nouvellement ajoutées)
    reponsesContainer.addEventListener('click', function(event) {
        // Vérifie si le clic est sur le bouton de suppression ou un de ses enfants
        if (event.target.classList.contains('remove-reponse') || event.target.closest('.remove-reponse')) {
            const button = event.target.closest('.remove-reponse');
            const reponseItem = button.closest('.reponse-item');
            
            // Récupère l'ID de la réponse s'il existe (pour les réponses existantes)
            const reponseId = reponseItem.dataset.reponseId;
            if (reponseId) {
                // Si la réponse est une réponse existante (avec un ID de BD), 
                // ajoute un champ caché pour indiquer au PHP de la supprimer.
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'reponse_ids_a_supprimer[]'; // Nom du tableau pour le PHP
                hiddenInput.value = reponseId;
                reponsesContainer.appendChild(hiddenInput); // Ajoute le champ caché au formulaire
            }
            // Supprime l'élément de réponse du DOM (visuellement)
            reponseItem.remove(); 
        }
    });
});
</script>

<style>
    /* Styles généraux pour le corps et les conteneurs */
    body {
        background-color: #1a1a1a; /* Un noir très foncé pour le fond */
        color: #e0e0e0; /* Couleur de texte claire par défaut */
    }

    .container {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    /* Styles pour la carte principale */
    .card {
        border-radius: 8px; /* Bords légèrement arrondis */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Légère ombre */
    }

    .card-header {
        background-color: #2a2a2a; /* Fond d'en-tête plus clair que le corps de la carte */
        border-bottom: 1px solid #00bcd4; /* Bordure d'info */
        padding: 15px 20px;
    }

    .card-body {
        padding: 25px 20px;
    }

    .card-footer {
        background-color: #2a2a2a;
        border-top: 1px solid #00bcd4;
        padding: 15px 20px;
    }

    /* Styles des labels de formulaire */
    .form-label {
        font-weight: bold;
        color: #add8e6; /* Bleu clair pour les labels */
        margin-bottom: 5px;
    }

    /* Styles des champs de formulaire */
    .form-control, .form-select {
        background-color: #212529; /* Couleur sombre pour les champs */
        color: #e0e0e0; /* Texte clair dans les champs */
        border: 1px solid #00bcd4; /* Bordure bleue info */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #00a0b0; /* Bordure légèrement plus foncée au focus */
        box-shadow: 0 0 0 0.25rem rgba(0, 188, 212, 0.25); /* Ombre légère de focus */
        background-color: #212529; /* Maintenir le fond sombre au focus */
        color: #e0e0e0;
    }

    /* Styles des boutons */
    .btn-info {
        background-color: #00bcd4; /* Bleu Cyan éclatant */
        border-color: #00bcd4;
        color: #fff; /* Texte blanc */
        font-weight: bold;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-info:hover {
        background-color: #0097a7; /* Bleu Cyan plus foncé au survol */
        border-color: #0097a7;
        transform: translateY(-2px); /* Léger effet de soulèvement */
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: #fff;
        font-weight: bold;
        transition: background-color 0.3s ease, border-color 0.3s ease, transform 0.2s ease;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #5a6268;
        transform: translateY(-2px);
    }

    .btn-outline-danger {
        color: #dc3545; /* Rouge vif */
        border-color: #dc3545;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .btn-outline-danger:hover {
        background-color: #dc3545;
        color: #fff;
        border-color: #dc3545;
    }
    
    .btn-outline-success {
        color: #28a745; /* Vert vif */
        border-color: #28a745;
        transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .btn-outline-success:hover {
        background-color: #28a745;
        color: #fff;
        border-color: #28a745;
    }

    /* Styles spécifiques aux réponses */
    .reponses-container .input-group {
        align-items: center; /* Aligner verticalement les éléments */
    }

    .reponse-item {
        background-color: #2a2a2a; /* Fond légèrement différent pour chaque item de réponse */
        border-radius: 5px;
        padding: 5px 10px;
    }

    .reponse-item .form-control {
        border-right: none; /* Enlève la bordure à droite pour mieux fusionner avec l'input-group-text */
    }

    .input-group-text {
        background-color: #343a40; /* Fond plus foncé pour le texte des groupes d'input */
        border: 1px solid #00bcd4; /* Bordure assortie */
        color: #e0e0e0;
    }

    .form-check-input {
        background-color: #343a40; /* Fond de la checkbox */
        border-color: #00bcd4; /* Bordure de la checkbox */
    }

    .form-check-input:checked {
        background-color: #00bcd4; /* Couleur de la checkbox cochée */
        border-color: #00bcd4;
    }

    /* Ajouté pour s'assurer que les icônes Bootstrap sont correctement stylisées */
    i.bi {
        vertical-align: middle;
        margin-right: 5px;
    }

</style>