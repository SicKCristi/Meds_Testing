<?php
    include "layout/header.php";
    echo '<script src="tranzitie.js"></script>';
    echo '<link rel="stylesheet" type="text/css" href="stilizare_tranzitii.css">';
    echo '<link rel="stylesheet" type="text/css" href="stilizare_div_pagini_index.css">';

    $Email_Utilizator=$_SESSION['Email'] ?? null;
    $este_inrolat=false;
    if($Email_Utilizator){
        $conexiune_bd=getDatabaseConnection();
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM pacienti WHERE Emailul=?");
        $stmt->bind_param("s", $Email_Utilizator);
        $stmt->execute();
        $stmt->bind_result($numar_pacienti);
        $stmt->fetch();
        $stmt->close();

        $este_inrolat=$numar_pacienti>0;
    } else{
        echo "<p class='text-danger'>Email-ul utilizatorului nu este setat în sesiune!</p>";
    }
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

<div class="slide">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Aruncă o privire asupra echipei de medici</strong></h1>
                <p>
                    Explorează lista echipei de medici implicați în cele mai importante studii clinice și consultă specializările acestora pentru a afla mai multe despre expertiza lor.
                    Descoperă echipa noastră de medici dedicați, care contribuie activ la avansarea cercetărilor prin participarea în proiecte medicale de prestigiu.
                </p>
                <a href="informatii_despre_medici.php" class="btn btn-primary btn-lg mt-3">
                    Vizualizați
                </a>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Echipa_medici_2.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a testărilor dumneavoastră</strong></h1>
                <p>
                    Explorați istoricul testărilor dumneavoastră medicale într-un mod simplu și intuitiv.
                    Accesați rapid detalii despre întâlnirile cu medicii, recomandările primite și diagnosticele înregistrate.
                </p>
                <?php if($este_inrolat): ?>
                    <a href="vizualizare_testari.php" class="btn btn-primary btn-lg">Vizualizați</a>
                <?php else: ?>
                    <p class="text-warning">Nu sunteți înrolat în cadrul evidenței de testare. Nu puteți accesa această pagină.</p>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-center">
                <img src="/images/Testare_pacient.png" class="img-fluid" alt="hero" />
            </div>
        </div>
    </div>
</div>

<div class="slide">
    <div class="container text-white py-5">
        <div class="row align-items-center g-5">
            <div class="col-md-6">
                <h1 class="mb-5 display-2"><strong>Vizualizare a medicamentelor testate</strong></h1>
                <p>
                    Accesați rapid informații detaliate despre medicamentele testate în cadrul studiilor clinice.
                    Analizați istoricul tratamentelor și observați progresul pentru a înțelege mai bine impactul acestora asupra sănătății dumneavoastră.
                </p>
                <?php if($este_inrolat): ?>
                    <a href="vizualizare_medicamente.php" class="btn btn-primary btn-lg">Vizualizați</a>
                <?php else: ?>
                    <p class="text-warning">Nu sunteți înrolat în cadrul evidenței de testare. Nu puteți accesa această pagină.</p>
                <?php endif; ?>
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