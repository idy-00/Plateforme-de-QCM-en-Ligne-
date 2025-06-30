<footer class="bg-dark border-top border-info mt-5">
    <div class="container py-5">
        <div class="row">
            <!-- Colonne Logo + Description -->
            <div class="col-lg-4 mb-4">
                <h3 class="text-info mb-3">
                    <i class="bi bi-journal-bookmark"></i> PLATEFORME QCM
                </h3>
                <p class="text-light-50">
                    La référence pour l'apprentissage interactif par questionnaires.
                    Développez vos compétences à votre rythme.
                </p>
            </div>

            <!-- Colonne Liens rapides -->
            <div class="col-md-4 col-lg-2 mb-4">
                <h5 class="text-info mb-3">Navigation</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="index.php" class="text-light-50 text-decoration-none">Accueil</a></li>
                    <li class="mb-2"><a href="qcm.php" class="text-light-50 text-decoration-none">QCM</a></li>
                    <?php if (estConnecte()): ?>
                        <li class="mb-2"><a href="profil.php" class="text-light-50 text-decoration-none">Profil</a></li>
                    <?php else: ?>
                        <li class="mb-2"><a href="connexion.php" class="text-light-50 text-decoration-none">Connexion</a></li>
                        <li class="mb-2"><a href="inscription.php" class="text-light-50 text-decoration-none">Inscription</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Colonne Contacts -->
            <div class="col-md-4 col-lg-3 mb-4">
                <h5 class="text-info mb-3">Contact</h5>
                <ul class="list-unstyled text-light-50">
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i> idykane03@gmail.com</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i> +221 78 119 48 05 </li>
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i> Dakar, senegal</li>
                </ul>
            </div>

            <!-- Colonne Réseaux sociaux -->
            <div class="col-md-4 col-lg-3 mb-4">
                <h5 class="text-info mb-3">Nous suivre</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-info fs-4"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="text-info fs-4"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="text-info fs-4"><i class="bi bi-linkedin"></i></a>
                    <a href="#" class="text-info fs-4"><i class="bi bi-instagram"></i></a>
                </div>
                
                
            </div>
        </div>

        <!-- Copyright -->
        <div class="row pt-4 mt-4 border-top border-secondary">
            <div class="col-12 text-center text-light-50">
                <p class="mb-0">&copy; <?= date('Y') ?> Plateforme QCM. Tous droits réservés.</p>
            </div>
        </div>
    </div>
</footer>

<!-- Scripts Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
