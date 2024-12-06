<?php
    include "layout/header.php";
    echo '<script src="tranzitie.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="stilizare_tranzitii.css">';
?>

<div class="slide show" style="background-color: #08618d">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Site-ul pentru evidența testării pacienților</strong></h1>
                <p>
                    Acest site oferă informații despre un studiu clinic dedicat evaluării.
                    Scopul său este de a oferi transparență pentru cei interesați de participare și pentru profesioniștii din domeniul medical.
                </p>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Pacienti_la_studiu_clinic.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide" style="background-color: #08618d">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a testărilor dumneavoastră</strong></h1>
                <p>
                    Explorați istoricul testărilor dumneavoastră medicale într-un mod simplu și intuitiv.
                    Accesați rapid detalii despre întâlnirile cu medicii, recomandările primite și diagnosticele înregistrate.
                </p>
                <a href="vizualizare_testari.php" class="btn btn-primary btn-lg mt-3">
                    Vizualizați
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Testare_pacient.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide" style="background-color: #08618d">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a medicamentelor testate</strong></h1>
                <p>
                    Accesați rapid informații detaliate despre medicamentele testate în cadrul studiilor clinice.
                    Analizați istoricul tratamentelor și observați progresul pentru a înțelege mai bine impactul acestora asupra sănătății dumneavoastră.
                </p>
                <a href="vizualizare_medicamente.php" class="btn btn-primary btn-lg mt-3">
                    Vizualizați
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Medicamente.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<?php
    include "layout/footer.php"
?>