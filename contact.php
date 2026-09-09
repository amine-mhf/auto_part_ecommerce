<?php
// PAGE CONTACT
require_once 'includes/config.php';
session_start();

include 'includes/header.php';
?>

<!--  PAGE CONTACT (2 COLONNES : INFOS + MAPS)-->
  
<div class="contact-page">
    <div class="container">
        <h1>Nous contacter</h1>
        <p class="contact-subtitle">Retrouvez nos coordonnées et notre localisation.</p>

        <div class="contact-grid">

            
            <!-- COLONNE GAUCHE : COORDONNÉES -->
            <div class="contact-infos">

                <!-- Adresse -->
                <div class="contact-card">
                    <div class="contact-icon">
                        <ion-icon name="location-outline"></ion-icon>
                    </div>
                    <div>
                        <h3>Adresse</h3>
                        <p>Les Mimosas, Cité, N° 590<br>Boumerdès 35000, Algérie</p>
                    </div>
                </div>

                <!-- Téléphone -->
                <div class="contact-card">
                    <div class="contact-icon">
                        <ion-icon name="call-outline"></ion-icon>
                    </div>
                    <div>
                        <h3>Téléphone</h3>
                        <p><a href="tel:0542078200">0542 07 82 00</a></p>
                    </div>
                </div>

                <!-- Email -->
                <div class="contact-card">
                    <div class="contact-icon">
                        <ion-icon name="mail-outline"></ion-icon>
                    </div>
                    <div>
                        <h3>Email</h3>
                        <p><a href="mailto:sarlapisrapido@gmail.com">sarlapisrapido@gmail.com</a></p>
                    </div>
                </div>

                <!-- Horaires -->
                <div class="contact-card">
                    <div class="contact-icon">
                        <ion-icon name="time-outline"></ion-icon>
                    </div>
                    <div>
                        <h3>Horaires</h3>
                        <p>Samedi – Jeudi : 8h30 – 17h<br>Vendredi : fermé</p>
                    </div>
                </div>

            </div>

            <!-- COLONNE DROITE : GOOGLE MAPS -->

            <div class="contact-map">
                <iframe 
                    src="https://www.google.com/maps?q=36.751010905607274,3.4668199402499056&z=16&output=embed" 
                    width="100%" 
                    height="400" 
                    style="border:0; border-radius:24px;" 
                    allowfullscreen="" 
                    loading="lazy">
                </iframe>
                <p class="map-note">Les Mimosas, Cité, N° 590, Boumerdès</p>
            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>