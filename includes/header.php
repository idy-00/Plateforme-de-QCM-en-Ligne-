<?php
session_start();
require_once 'functions.php';
$theme = isset($_COOKIE['theme']) ? $_COOKIE['theme'] : 'dark';
$utilisateur = utilisateurConnecte();
$is_admin = $utilisateur && $utilisateur['role'] === 'admin';
$is_etudiant = $utilisateur && $utilisateur['role'] === 'etudiant';
?>

<!DOCTYPE html>
<html lang="fr" data-bs-theme="<?= $theme ?>">
<head>
    <meta charset="UTF-8">
    <title>PLATEFORME QCM</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

</head>
<body class="bg-black text-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom border-info">
        <div class="container-fluid">
            <a class="navbar-brand text-info fw-bold" href="../public/index.php">
                <i class="bi bi-house"></i> PLATEFORME QCM
            </a>
            <button class="navbar-toggler border-info" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
                <ul class="navbar-nav align-items-center">
                    <?php if (!estConnecte()): ?>
                        <li class="nav-item">
                            <a class="nav-link text-info" href="../public/01-inscription.php">
                                <i class="bi bi-person-plus"></i> Inscription
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-info" href="../public/02-connexion.php">
                                <i class="bi bi-box-arrow-in-right"></i> Connexion
                            </a>
                        </li>
                    <?php else: ?>

<?php if ($is_admin): ?>
    <li class="nav-item">
        <a class="nav-link text-info" href="../admin/dashbord_admin.php">
            <i class="bi bi-speedometer2"></i> tableau de bord
        </a>
    </li>

    
<li class="nav-item dropdown-center ms-2">
    <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <i class="bi bi-gear-fill"></i> Administration
    </button>
    <ul class="dropdown-menu dropdown-menu-dark">
        <li><h6 class="dropdown-header text-info">Gestion utilisateurs</h6></li>
        <li><a class="dropdown-item" href="../admin/ajouter_etudiants.php">
            <i class="bi bi-person-plus"></i> Ajouter un étudiant
        </a></li>
        <li><a class="dropdown-item" href="../admin/utilisateurs.php">
            <i class="bi bi-people-fill"></i> Liste des utilisateurs
        </a></li>
        <li><hr class="dropdown-divider"></li>
        
        <li><h6 class="dropdown-header text-info">Gestion QCM</h6></li>
        <li><a class="dropdown-item" href="../admin/ajouter_qcm.php">
            <i class="bi bi-file-earmark-plus"></i> Créer un QCM
        </a></li>
        <li><a class="dropdown-item" href="../admin/gerer_qcm.php">
            <i class="bi bi-pencil-square"></i> gerer QCM
        </a></li>
    </ul>
</li>
<?php endif; ?>

 <?php if ($is_etudiant): ?>
    <li class="nav-item">
        <a class="nav-link text-info" href="../public/dashbord_etudiant.php">
            <i class="bi bi-speedometer2"></i> tableau de bord
        </a>
    </li>

    
          <li class="nav-item">
              <a class="nav-link text-info" href="../public/qcm.php">
                 <i class="bi bi-question-circle"></i> Espace QCM
             </a>
         </li>
  <?php endif; ?>

     <li class="nav-item">
       <a class="nav-link text-info" href="../public/03-profil.php">
        <i class="bi bi-person-circle"></i> Profil
        </a>
    </li>

     <li class="nav-item d-flex ms-auto align-items-center">
      <a class="nav-link text-info me-2" href="../public/theme.php">
     <?php if ($theme == 'dark'): ?>
            <i class="bi bi-sun"></i> Light
       <?php else: ?>
          <i class="bi bi-moon"></i> Dark
    <?php endif; ?>
     </a>
    </li>

     <li class="nav-item">
          <a class="nav-link text-danger" href="../public/04-logout.php">
            <i class="bi bi-box-arrow-right"></i> Déconnexion
          </a>
       </li>
     <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Bootstrap JS Bundle avec Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>