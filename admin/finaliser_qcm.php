<?php
require_once '../includes/header.php';

if (!estAdmin() || !isset($_GET['qcm_id'])) {
    header('Location: ../index.php');
    exit;
}

$qcm_id = (int)$_GET['qcm_id'];

// Vérifier qu'il y a au moins une question
$stmt = $pdo->prepare("SELECT COUNT(*) FROM questions WHERE qcm_id = ?");
$stmt->execute([$qcm_id]);
$nb_questions = $stmt->fetchColumn();

if ($nb_questions > 0) {
    // Marquer le QCM comme complet
    $stmt = $pdo->prepare("UPDATE qcms SET is_published = TRUE WHERE id = ?");
    $stmt->execute([$qcm_id]);
    
    $_SESSION['succes'] = "QCM finalisé et publié avec succès !";
} else {
    $_SESSION['erreur'] = "Impossible de publier un QCM sans questions";
}

header("Location: ajouter_qcm.php");
exit;
?>