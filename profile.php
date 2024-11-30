<?php
    include "layout/header.php";

    if(!isset($_SESSION["Email"])){
        header("location: /login.php");
        exit;
    }
?>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-16 mx-auto border shadow p-4">
                <h2 class="text-center-mb-4">Profil</h2>
                <hr />
                    
                <div class="row mb-3">
                    <div class="col-sm-4">Numele:</div>
                    <div class="col-sm-8"><?= $_SESSION["Nume"] ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">Prenumele:</div>
                    <div class="col-sm-8"><?= $_SESSION["Prenume"] ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">Emailul:</div>
                    <div class="col-sm-8"><?= $_SESSION["Email"] ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">Telefonul:</div>
                    <div class="col-sm-8"><?= $_SESSION["Telefon"] ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">Rolul:</div>
                    <div class="col-sm-8"><?= $_SESSION["Rol"] ?></div>
                </div>

                <div class="row mb-3">
                    <div class="col-sm-4">Adresa:</div>
                    <div class="col-sm-8"><?= $_SESSION["Adresa"] ?></div>
                </div>

            </div>
        </div>

    </div>

<?php
    include "layout/footer.php";
?>