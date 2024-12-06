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
                <img src="/images/Testare_medicala.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide" style="background-color: #08618d">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a consultațiilor efectuate</strong></h1>
                <p>
                    Obțineți o imagine de ansamblu asupra consultațiilor efectuate, cu detalii complete despre diagnostice, recomandări și tratamente prescrise.
                    Monitorizați istoricul interacțiunilor cu pacienții pentru a oferi o îngrijire medicală mai bine informată și personalizată.
                </p>
                <a href="vizualizare_consultatii.php" class="btn btn-primary btn-lg mt-3">
                    Vizualizați
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Consultatie.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide" style="background-color: #08618d">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a studiilor la care participați</strong></h1>
                <p>
                    Accesați o evidență completă a studiilor clinice la care dumneavoastră participați.
                    Gestionați informațiile cu ușurință pentru a oferi îngrijire personalizată și bine documentată.
                </p>  
                <a href="vizualizare_studii.php" class="btn btn-primary btn-lg mt-3">
                    Vizualizați
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Doctor_studiu_clinic.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<?php
    include "layout/footer.php"
?>