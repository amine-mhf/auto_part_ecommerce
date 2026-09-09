<?php

?>

</main> 

<!--  FOOTER MODERNE (3 COLONNES) -->

<footer class="footer-modern">
    <div class="container">
        <div class="footer-content">

            <!-- COLONNE 1 : Catégories populaires -->
            <div class="footer-col">
                <h3>Catégories populaires</h3>
                <ul>
                    <li><a href="/pieces_auto/produits.php?categorie=3">Moteur</a></li>
                    <li><a href="/pieces_auto/produits.php?categorie=4">Embrayage</a></li>
                    <li><a href="/pieces_auto/produits.php?categorie=5">Suspension</a></li>
                    <li><a href="/pieces_auto/produits.php?categorie=6">Carrosserie</a></li>
                    <li><a href="/pieces_auto/produits.php?categorie=8">Électricité</a></li>
                </ul>
            </div>

            <!-- COLONNE 2 : Coordonnées de contact -->
            <div class="footer-col">
                <h3>Contact</h3>
                <ul class="contact-list">
                    <li>
                        <ion-icon name="location-outline"></ion-icon>
                        Les Mimosas, Cité, N° 590<br>Boumerdès 35000
                    </li>
                    <li>
                        <ion-icon name="call-outline"></ion-icon>
                        <a href="tel:0542078200">0542 07 82 00</a>
                    </li>
                    <li>
                        <ion-icon name="mail-outline"></ion-icon>
                        <a href="mailto:sarlapisrapido@gmail.com">sarlapisrapido@gmail.com</a>
                    </li>
                </ul>
            </div>

            <!-- COLONNE 3 : Réseaux sociaux -->
            <div class="footer-col">
                <h3>Suivez-nous</h3>
                <div class="social-icons">
                    <a href="https://www.facebook.com/profile.php?id=61561966902150" target="_blank" rel="noopener noreferrer">
                        <ion-icon name="logo-facebook"></ion-icon>
                    </a>
                    <a href="https://www.instagram.com/apisrapido/" target="_blank" rel="noopener noreferrer">
                        <ion-icon name="logo-instagram"></ion-icon>
                    </a>
                </div>
            </div>

        </div>

        <!-- Pied de page (copyright) -->
        <div class="footer-bottom">
            <p>&copy; 2026 APIS RAPIDO – Tous droits réservés</p>
        </div>
    </div>
</footer>

<!-- SCRIPTS (Ionicons uniquement, JS principal via main.js) -->

<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
<script src="/pieces_auto/ressources/js/main.js?v=8"></script>
<?php if (strpos($_SERVER['SCRIPT_NAME'], '/admin/') !== false): ?>
    <script src="/pieces_auto/ressources/js/admin.js?v=8"></script>
<?php endif; ?>

</body>
</html>