<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

// Statistiques générales
$total_utilisateurs = $pdo->query("SELECT COUNT(*) FROM utilisateurs")->fetchColumn();
$total_etudiants = $pdo->query("SELECT COUNT(*) FROM utilisateurs WHERE role = 'etudiant'")->fetchColumn();
$total_qcm = $pdo->query("SELECT COUNT(*) FROM qcms")->fetchColumn();
$total_questions = $pdo->query("SELECT COUNT(*) FROM questions")->fetchColumn();
?>

<div class="container my-5">
    <h2 class="text-info mb-4"><i class="bi bi-speedometer2"></i> Tableau de bord administrateur</h2>

    <div class="row g-4">
        <div class="col-md-3">
            <div class="card bg-dark border-info text-light h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-people-fill display-4 text-info"></i>
                    <h5 class="mt-3">Utilisateurs</h5>
                    <p class="fs-2 fw-bold text-success"><?= $total_utilisateurs ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-info text-light h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-mortarboard-fill display-4 text-info"></i>
                    <h5 class="mt-3">Étudiants</h5>
                    <p class="fs-2 fw-bold text-success"><?= $total_etudiants ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-info text-light h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-folder2-open display-4 text-info"></i>
                    <h5 class="mt-3">QCM créés</h5>
                    <p class="fs-2 fw-bold text-success"><?= $total_qcm ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-dark border-info text-light h-100 text-center">
                <div class="card-body">
                    <i class="bi bi-question-circle-fill display-4 text-info"></i>
                    <h5 class="mt-3">Questions</h5>
                    <p class="fs-2 fw-bold text-success"><?= $total_questions ?></p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h3 class="text-info"><i class="bi bi-trophy"></i> Meilleur étudiant</h3>
        <p class="text-light">L'étudiant avec le score le plus élevé sur vos QCM :</p>
        <?php
        $meilleur = $pdo->query("
            SELECT u.nom, q.titre, r.score
            FROM resultats r
            JOIN utilisateurs u ON r.utilisateur_id = u.id
            JOIN qcms q ON r.qcm_id = q.id
            WHERE u.role = 'etudiant'
            ORDER BY r.score DESC
            LIMIT 1
        ")->fetch();

        if ($meilleur) {
            echo '<div class="alert alert-success">';
            echo '<strong>Nom :</strong> ' . htmlspecialchars($meilleur['nom']) . '<br>';
            echo '<strong>QCM :</strong> ' . htmlspecialchars($meilleur['titre']) . '<br>';
            // Calcul du pourcentage de score
            $score = $meilleur['score'];
            // Récupérer le nombre total de questions du QCM correspondant
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE qcm_id = (SELECT id FROM qcms WHERE titre = ?)");
            $stmt->execute([$meilleur['titre']]);
            $total = $stmt->fetchColumn();
            if ($total > 0) {
                $percent = round(($score / $total) * 100);
                echo '<strong>Score :</strong> ' . $percent . ' %';
            } else {
                echo '<strong>Score :</strong> N/A';
            }
            echo '</div>';
        } else {
            echo '<div class="alert alert-warning">Aucun résultat trouvé.</div>';
        }
?>
    <div class="mt-5">
        <h3 class="text-info"><i class="bi bi-gear"></i> Actions administratives</h3>
        <p class="text-light">Utilisez les boutons ci-dessous pour gérer les utilisateurs et les QCM.</p>

    <div class="text-center mt-5">
        <a href="utilisateurs.php" class="btn btn-outline-info me-3">
            <i class="bi bi-people"></i> Gérer les utilisateurs
        </a>
        <a href="gerer_qcm.php" class="btn btn-info fw-bold">
            <i class="bi bi-folder2"></i> Gérer les QCM
        </a>
    </div>
</div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php require_once '../includes/footer.php'; ?>
