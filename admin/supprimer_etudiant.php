<?php
require_once '../includes/header.php';

if (!estAdmin()) {
    header('Location: ../02-connexion.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("DELETE FROM utilisateurs WHERE id = ? AND role = 'etudiant'");
$stmt->execute([$id]);

header('Location: utilisateurs.php');
exit;
?>
