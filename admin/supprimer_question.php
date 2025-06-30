<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$qcm_id = isset($_GET['qcm_id']) ? (int)$_GET['qcm_id'] : 0;

// Suppression de la question (et ses réponses si cascade)
$stmt = $pdo->prepare("DELETE FROM questions WHERE id = ?");
$stmt->execute([$id]);

header("Location: modifier_questions.php?id=$qcm_id");
exit;
?>
