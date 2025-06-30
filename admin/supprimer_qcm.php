<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Suppression du QCM (et de ses questions/réponses si cascade activée)
$stmt = $pdo->prepare("DELETE FROM qcms WHERE id = ?");
$stmt->execute([$id]);

header('Location: gerer_qcm.php');
exit;
?>
