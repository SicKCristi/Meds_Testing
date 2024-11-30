<?php
    include "layout/header.php";

    if(isset($_SESSION["Email"])){
        header("location: /index.php");
        exit;
    }
    
    // Initializare variabile de valori
    $Nume = "";
    $Prenume = "";
    $Email = "";
    $Telefon = "";
    $Adresa = "";
    $Rol="";

    // Inițializăm variabilele de eroare
    $eroare_nume = "";
    $eroare_prenume = "";
    $eroare_email = "";
    $eroare_telefon = "";
    $eroare_adresa = "";
    $eroare_parola = "";
    $eroare_confimrare_parola = "";
    $eroare_rol = "";

    $eroare = false;

    // Dacă formularul a fost trimis (metoda POST)
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Preluăm datele din formular doar dacă acestea sunt setate în $_POST
        $Nume = isset($_POST['Nume']) ? $_POST['Nume'] : "";
        $Prenume = isset($_POST['Prenume']) ? $_POST['Prenume'] : "";
        $Email = isset($_POST['Email']) ? $_POST['Email'] : "";
        $Telefon = isset($_POST['Telefon']) ? $_POST['Telefon'] : "";
        $Adresa = isset($_POST['Adresa']) ? $_POST['Adresa'] : "";
        $Parola = isset($_POST['Parola']) ? $_POST['Parola'] : "";
        $Confirmare_parola = isset($_POST['Confirmare_parola']) ? $_POST['Confirmare_parola'] : "";
        $Rol = isset($_POST['Rol']) ? $_POST['Rol'] : "";

        // Verificăm să nu avem la nume NULL
        if (empty($Nume)) {
            $eroare_nume = "Numele este necesar!";
            $eroare = true;
        }

        // Verificăm să nu avem la prenume NULL
        if (empty($Prenume)) {
            $eroare_prenume = "Prenumele este necesar!";
            $eroare = true;
        }

        // Verificare format email
        if (!filter_var($Email, FILTER_VALIDATE_EMAIL)) {
            $eroare_email = "Email-ul are un format greșit!";
            $eroare = true;
        }

        // Validăm rolul - verificăm să fie doar "Pacient" sau "Medic"
        if (!in_array($Rol, ['Pacient', 'Medic'])) {
            die("Rol invalid! Trebuie să selectezi fie Pacient, fie Medic.");
        }

        include "tools/db.php";
        $conexiune_bd = getDatabaseConnection();

        $statement = $conexiune_bd->prepare("SELECT ID_Utilizator FROM utilizator WHERE Email=?");

        $statement->bind_param("s", $Email);

        $statement->execute();

        $statement->store_result();
        if ($statement->num_rows > 0) {
            $eroare_email = "Emailul specificat este deja folosit!";
            $eroare = true;
        }

        $statement->close();

        // Verificare număr de telefon - trebuie să fie exact 10 cifre
        if (!preg_match('/^[0-9]{10}$/', $Telefon)) {
            $eroare_telefon = "Numărul de telefon trebuie să aibă exact 10 cifre!";
            $eroare = true;
        }

        // Verificăm să nu avem la adresa NULL
        if (empty($Adresa)) {
            $eroare_adresa = "Adresa este necesara!";
            $eroare = true;
        }

        // Verificare parolă conform cerințelor
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=(?:.*\d){2,})(?=.*[@#$%*!?&^()]).{8,}$/', $Parola)) {
            $eroare_parola = "Parola trebuie să aibă cel puțin 8 caractere, o literă mică, o literă mare, cel puțin 2 cifre și un caracter special (@#$%*!?&^()).";
            $eroare = true;
        }        

        // Verificare dacă parola și confirmare_parola sunt identice
        if ($Parola != $Confirmare_parola) {
            $eroare_confirmare_parola = "Cele două parole nu sunt identice!";
            $eroare = true;
        }

        if (!$eroare) {
            $Parola = password_hash($Parola, PASSWORD_DEFAULT);

            $statement = $conexiune_bd->prepare(
                "INSERT INTO utilizator (Nume, Prenume, Email, Telefon, Adresa, Parola, Rol) VALUES (?, ?, ?, ?, ?, ?, ?)"
            );

            if ($statement) {
                $statement->bind_param('sssssss', $Nume, $Prenume, $Email, $Telefon, $Adresa, $Parola, $Rol);
                $statement->execute();

                $Inserare_ID_Utilizator = $statement->insert_id;

                $statement->close();

                $_SESSION["ID_Utilizator"] = $Inserare_ID_Utilizator;
                $_SESSION["Nume"] = $Nume;
                $_SESSION["Prenume"] = $Prenume;
                $_SESSION["Email"] = $Email;
                $_SESSION["Telefon"] = $Telefon;
                $_SESSION["Adresa"] = $Adresa;
                $_SESSION["Rol"] = $Rol;

                
                if ($rol==='Pacient') {
                    header("Location: index_pacient.php");
                } elseif ($rol==='Medic') {
                    header("Location: index_medic.php");
                }
                exit;
            } else {
                echo "Eroare la pregătirea interogării SQL: " . $conexiune_bd->error;
            }
        }
    }
?>


<div class="container py-5">
    <div class="row">
        <div class="col-lg-6 mx-auto border shadow p-4">
            <h2 class="text-center mb-4">Inregistreaza-te acum</h2>
            <hr />

            <form method="post">
                
                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Numele dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="Nume" value="<?=$Nume ?>">
                        <span class="text-danger"><?= $eroare_nume ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Prenume dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="Prenume" value="<?=$Prenume ?>">
                        <span class="text-danger"><?= $eroare_prenume ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Emailul dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="Email" value="<?=$Email ?>">
                        <span class="text-danger"><?= $eroare_email ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Telefonul dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="Telefon" value="<?=$Telefon ?>">
                        <span class="text-danger"><?= $eroare_telefon ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Adresa dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" name="Adresa" value="<?=$Adresa ?>">
                        <span class="text-danger"><?= $eroare_adresa ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="Rol" class="col-sm-4 col-form-label">Selectați rolul:</label>
                        <div class="col-sm-8">
                            <select class="form-select" name="Rol" id="Rol" required>
                            <option value="Pacient" <?= isset($Rol) && $Rol === 'Pacient' ? 'selected' : '' ?>>Pacient</option>
                            <option value="Medic" <?= isset($Rol) && $Rol === 'Medic' ? 'selected' : '' ?>>Medic</option>
                            </select>
                            <span class="text-danger"><?= isset($eroare_rol) ? $eroare_rol : '' ?></span>
                        </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Parola dumneavoastra:</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="password" name="Parola">
                        <span class="text-danger"><?= $eroare_parola ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Confrimare parola:</label>
                    <div class="col-sm-8">
                        <input class="form-control" type="password" name="Confirmare_parola">
                        <span class="text-danger"><?= $eroare_confimrare_parola ?></span>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="offset-sm-4 col-sm-4 d-grid">
                        <button type="submit" class="btn btn-primary">Inregistreaza-te</button>
                    </div>
                    <div class="col-sm-4">
                        <a href="index.php" class="btn btn-outline-primary">
                            Anuleaza
                        </a>
                    </div>
                </div>

            </form>

        </div>
    </div>
</div>

<?php
    include "layout/footer.php"
?>