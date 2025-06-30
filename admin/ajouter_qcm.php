<?php
require_once '../includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validation et traitement
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    
    if (!empty($titre)) {
        $stmt = $pdo->prepare("INSERT INTO qcms (titre, description, created_by) VALUES (?, ?, ?)");
        $stmt->execute([$titre, $description, $_SESSION['utilisateur']['id']]);
        
        $qcm_id = $pdo->lastInsertId();
        header("Location: ajouter_questions.php?qcm_id=$qcm_id");
        exit;
    }
}
?>

<form method="post" class="container my-5">
    <div class="card bg-dark border-info">
        <div class="card-header">
            <h3 class="text-info"><i class="bi bi-file-earmark-plus"></i> Créer un nouveau QCM</h3>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="text-light">Titre du QCM</label>
                <input type="text" name="titre" class="form-control bg-dark text-light border-info" required>
            </div>
            <div class="mb-3">
                <label class="text-light">Description</label>
                <textarea name="description" class="form-control bg-dark text-light border-info" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-info">Créer et ajouter des questions</button>
        </div>
    </div>
</form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once '../includes/footer.php';
?>