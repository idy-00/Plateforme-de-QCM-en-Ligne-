<?php
require_once '../includes/header.php';// Inclut le header avec la nav
?>

<main class="container my-5">
    <!-- Hero Section -->
    <section class="row align-items-center">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold text-info mb-4">Bienvenue sur la Plateforme QCM</h1>
            <p class="lead text-light">
                Développez vos compétences grâce à nos questionnaires interactifs. 
                Accédez à une variété de QCM couvrant différents sujets et niveaux.
            </p>
            <div class="d-flex gap-3 mt-4">
                <?php if (!estConnecte()): ?>
                    <a href="01-connexion.php" class="btn btn-info btn-lg px-4">Se connecter</a>
                    <a href="02-inscription.php" class="btn btn-outline-info btn-lg px-4">S'inscrire</a>
                <?php else: ?>
                    <a href="qcm.php" class="btn btn-info btn-lg px-4">Accéder aux QCM</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="col-md-6">
            <img src="image.jpg"  class="img-fluid">
        </div>
    </section>

    <!-- Features Section -->
    <section class="row mt-5 pt-5">
        <div class="col-12 text-center mb-5">
            <h2 class="text-info">Pourquoi utiliser notre plateforme ?</h2>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-dark border-info h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-collection-play display-4 text-info mb-3"></i>
                    <h3 class="h4 text-light">QCM Variés</h3>
                    <p class="text-light-50">Accédez à des centaines de questions sur divers sujets académiques.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-dark border-info h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-graph-up display-4 text-info mb-3"></i>
                    <h3 class="h4 text-light">Suivi de Progrès</h3>
                    <p class="text-light-50">Visualisez vos résultats et améliorez-vous au fil du temps.</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-4 mb-4">
            <div class="card bg-dark border-info h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-award display-4 text-info mb-3"></i>
                    <h3 class="h4 text-light">Certifications</h3>
                    <p class="text-light-50">Obtenez des attestations pour valider vos compétences.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <?php if (!estConnecte()): ?>
    <section class="text-center mt-5 py-5 bg-dark rounded-3">
        <h2 class="text-info mb-4">Prêt à commencer ?</h2>
        <a href="inscription.php" class="btn btn-info btn-lg px-5">Créer un compte gratuit</a>
    </section>
    <?php endif; ?>
</main>
<br><br><br>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<?php
require_once '../includes/footer.php';; // Inclut le footer si vous en avez un
?>