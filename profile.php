<?php
    include "layout/header.php";

    if (!isset($_SESSION["Email"])) {
        header("location: /login.php");
        exit;
    }

    include "tools/db.php";

    // Procesăm actualizările
    if ($_SERVER['REQUEST_METHOD']==='POST') {
        $camp = $_POST['camp'];
        $valoare = $_POST['valoare'];
        $id_utilizator = $_SESSION['ID_Utilizator'];

        $conexiune_bd = getDatabaseConnection();
        $statement = $conexiune_bd->prepare("UPDATE utilizator SET $camp = ? WHERE ID_Utilizator = ?");
        $statement->bind_param('si', $valoare, $id_utilizator);
        $statement->execute();
        $statement->close();

        $_SESSION[$camp] = $valoare;

        header("location: profile.php");
        exit;
    }
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-16 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Profil</h2>
            <hr />

            <!-- Numele -->
            <div class="row mb-3">
                <div class="col-sm-4">Numele:</div>
                <div class="col-sm-6"><?= $_SESSION["Nume"] ?></div>
                <div class="col-sm-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalNume">Modifică</button>
                </div>
            </div>

            <!-- Prenumele -->
            <div class="row mb-3">
                <div class="col-sm-4">Prenumele:</div>
                <div class="col-sm-6"><?= $_SESSION["Prenume"] ?></div>
                <div class="col-sm-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalPrenume">Modifică</button>
                </div>
            </div>

            <!-- Emailul -->
            <div class="row mb-3">
                <div class="col-sm-4">Emailul:</div>
                <div class="col-sm-6"><?= $_SESSION["Email"] ?></div>
                <div class="col-sm-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalEmail">Modifică</button>
                </div>
            </div>

            <!-- Telefonul -->
            <div class="row mb-3">
                <div class="col-sm-4">Telefonul:</div>
                <div class="col-sm-6"><?= $_SESSION["Telefon"] ?></div>
                <div class="col-sm-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTelefon">Modifică</button>
                </div>
            </div>
            
            <!-- Rolul nu poate fi modificat -->
            <div class="row mb-3">
                <div class="col-sm-4">Rolul:</div>
                <div class="col-sm-6"><?= $_SESSION["Rol"] ?></div>
                
            </div>

            <!-- Adresa -->
            <div class="row mb-3">
                <div class="col-sm-4">Adresa:</div>
                <div class="col-sm-6"><?= $_SESSION["Adresa"] ?></div>
                <div class="col-sm-2">
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAdresa">Modifică</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pentru fiecare câmp -->
<?php
function genereazaModal($id, $camp, $valoare, $label) {
?>
<div class="modal fade" id="<?= $id ?>" tabindex="-1" aria-labelledby="<?= $id ?>Label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="<?= $id ?>Label">Modifică <?= $label ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="camp" value="<?= $camp ?>" />
                    <div class="mb-3">
                        <label class="form-label"><?= $label ?> nou</label>
                        <input type="text" class="form-control" name="valoare" value="<?= $valoare ?>" required />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Salvează</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php
}
?>

<?php
genereazaModal("modalNume", "Nume", $_SESSION["Nume"], "Nume");
genereazaModal("modalPrenume", "Prenume", $_SESSION["Prenume"], "Prenume");
genereazaModal("modalEmail", "Email", $_SESSION["Email"], "Email");
genereazaModal("modalTelefon", "Telefon", $_SESSION["Telefon"], "Telefon");
genereazaModal("modalAdresa", "Adresa", $_SESSION["Adresa"], "Adresa");
?>

<?php include "layout/footer.php"; ?>
