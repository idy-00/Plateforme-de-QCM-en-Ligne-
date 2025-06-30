<?php
require_once '../includes/header.php';

if (!estConnecte()) {
    header('Location: 02-connexion.php');
    exit;
}

$user = utilisateurConnecte();

// Vérifie que l’utilisateur est un étudiant
if ($user['role'] !== 'etudiant') {
    header('Location: ../admin/dashbord_admin.php'); // ou vers dashboard admin plus tard
    exit;
}

// Récupération du nombre total de QCM passés
$stmt = $pdo->prepare("SELECT COUNT(*) FROM resultats WHERE utilisateur_id = ?");
$stmt->execute([$user['id']]);
$total_qcm = $stmt->fetchColumn();

// Récupération du taux de réussite moyen
$stmt = $pdo->prepare("
    SELECT AVG(score / total.nb_questions) * 100 AS taux
    FROM resultats r
    JOIN (
        SELECT qcm_id, COUNT(*) AS nb_questions
        FROM questions
        GROUP BY qcm_id
    ) AS total ON r.qcm_id = total.qcm_id
    WHERE r.utilisateur_id = ?
");
$stmt->execute([$user['id']]);
$taux = round($stmt->fetchColumn());

?>

<div class="container my-5">
    <h2 class="text-info mb-4"><i class="bi bi-speedometer2"></i> Tableau de bord</h2>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card bg-dark border-info text-light h-100">
                <div class="card-body text-center">
                    <i class="bi bi-journal-check display-4 text-info"></i>
                    <h4 class="mt-3">QCM passés</h4>
                    <p class="fs-2 fw-bold text-success"><?= $total_qcm ?></p>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card bg-dark border-info text-light h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bar-chart-line-fill display-4 text-info"></i>
                    <h4 class="mt-3">Taux de réussite</h4>
                    <p class="fs-2 fw-bold text-success"><?= $taux ?: 0 ?>%</p>
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5">
        <h3 class="text-info mb-4"><i class="bi bi-list-ul"></i> Mes QCM passés</h3>
        <table class="table table-dark table-striped">  
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">QCM</th>
                    <th scope="col">Score</th>
                    <th scope="col">Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT r.id, q.titre, r.score, r.date_passe FROM resultats r JOIN qcms q ON r.qcm_id = q.id WHERE r.utilisateur_id = ? ORDER BY r.date_passe DESC");
                $stmt->execute([$user['id']]);
                $resultats = $stmt->fetchAll();

                foreach ($resultats as $index => $resultat) {
                    echo "<tr>
                        <th scope='row'>" . ($index + 1) . "</th>
                        <td>" . htmlspecialchars($resultat['titre']) . "</td>
                        <td>" . htmlspecialchars($resultat['score']) . "</td>
                        <td>" . htmlspecialchars($resultat['date_passe']) . "</td>
                    </tr>";
                }
                ?>
            </tbody>
        </table>
    </div>  
     <div class="mt-5">
        <h3 class="text-info mb-4"><i class="bi bi-graph-up"></i> Mes performances</h3>
        <p class="text-light">Voici un aperçu de vos performances sur les QCM passés :</p>
    </div>
    <div class="mt-5">
        <h3 class="text-info mb-4"><i class="bi bi-trophy"></i> Mon meilleur score</h3>
        <?php
        // Récupérer le meilleur score et les infos du QCM correspondant
        $stmt = $pdo->prepare("
            SELECT r.score, q.titre, q.id AS qcm_id, 
                (SELECT COUNT(*) FROM questions WHERE qcm_id = q.id) AS total_questions
            FROM resultats r
            JOIN qcms q ON r.qcm_id = q.id
            WHERE r.utilisateur_id = ?
            ORDER BY r.score DESC
            LIMIT 1
        ");
        $stmt->execute([$user['id']]);
        $meilleur = $stmt->fetch();

        if ($meilleur && $meilleur['total_questions'] > 0) {
            $percent = round(($meilleur['score'] / $meilleur['total_questions']) * 100);
            echo "<p class='fs-2 fw-bold text-success'>{$percent} %</p>";
            echo "<div><strong>QCM :</strong> <a href='qcm.php?id=" . urlencode($meilleur['qcm_id']) . "'>" . htmlspecialchars($meilleur['titre']) . "</a></div>";
        } else {
            echo "<p class='text-warning'>Aucun score enregistré.</p>";
        }
        ?>
    </div>
   
    <div class="mt-5">  
        <h3 class="text-info mb-4"><i class="bi bi-exclamation-triangle"></i> Mon pire score</h3>
        <?php
        // Récupérer le pire score et les infos du QCM correspondant
        $stmt = $pdo->prepare("
            SELECT r.score, q.titre, q.id AS qcm_id, 
                (SELECT COUNT(*) FROM questions WHERE qcm_id = q.id) AS total_questions
            FROM resultats r
            JOIN qcms q ON r.qcm_id = q.id
            WHERE r.utilisateur_id = ?
            ORDER BY r.score ASC
            LIMIT 1
        ");
        $stmt->execute([$user['id']]);
        $pire = $stmt->fetch();

        if ($pire && $pire['total_questions'] > 0) {
            $percent = round(($pire['score'] / $pire['total_questions']) * 100);
            echo "<p class='fs-2 fw-bold text-danger'>{$percent} %</p>";
            echo "<div><strong>QCM :</strong> <a href='qcm.php?id=" . urlencode($pire['qcm_id']) . "'>" . htmlspecialchars($pire['titre']) . "</a></div>";
        } else {
            echo "<p class='text-warning'>Aucun score enregistré.</p>";
        }
        ?>
    </div>
    



    <div class="text-center mt-5">
        <a href="qcm.php" class="btn btn-info fw-bold">
            <i class="bi bi-play-circle"></i> Passer un QCM
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
