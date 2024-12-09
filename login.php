<?php
   include "layout/header.php";
   
   if(isset($_SESSION["Email"])){
        if ($user['rol']==='Pacient'){
            header("Location: index_pacient.php");
        } elseif($user['rol']==='Medic'){
        header("Location: index_medic.php");
    }
    exit;
   }

   $Email="";
   $eroare="";

   if($_SERVER['REQUEST_METHOD']=='POST'){
    $Email=$_POST['Email'];
    $Parola=$_POST['Parola'];

    if(empty($Email) || empty($Parola)){
        $eroare="Emailul și parola sunt necesare!";
    } else{
        include "tools/db.php";
        $conexiune_bd=getDatabaseConnection();
        $statement=$conexiune_bd->prepare("SELECT ID_Utilizator, Nume, Prenume, Telefon, Adresa, Rol, Parola FROM utilizator WHERE email=?");
        $statement->bind_param('s', $Email);
        $statement->execute();

        $statement->bind_result($ID_Utilizator, $Nume, $Prenume, $Telefon, $Adresa, $Rol, $Parola_stocata);

        if($statement->fetch()){
            if(password_verify($Parola, $Parola_stocata)){
                $_SESSION["ID_Utilizator"]=$ID_Utilizator;
                $_SESSION["Nume"]=$Nume;
                $_SESSION["Prenume"]=$Prenume;
                $_SESSION["Email"]=$Email;
                $_SESSION["Telefon"]=$Telefon;
                $_SESSION["Adresa"]=$Adresa;
                $_SESSION["Rol"]=$Rol;

                if($Rol==='Pacient'){
                    header("Location: index_pacient.php");
                } elseif($Rol==='Medic'){
                    header("Location: index_medic.php");
                }
                exit;
            }
        }

        $statement->close();
        $eroare="Email sau parolă invalidă!";
    }
}
?>

<div class="containter py-5">
    <div class="mx-auto border shadow p-4" style="width: 400px">
        <h2 class="text-center mb-4">Login</h2>
        <hr />

        <?php if(!empty($eroare)){ ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><?= $eroare ?></strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php } ?>


        <form method="post">

            <div class="mb-3">
                <label class="form-label">Emailul dumneavoastra</label>
                <input class="form-control" name="Email" value="<?= $Email ?>" />
            </div>

            <div class="mb-3">
                <label class="form-label">Parola dumneavoastra</label>
                <input class="form-control" type="password" name="Parola" />
            </div>

            <div class="d-flex justify-content-between mb-3">
                <button type="submit" class="btn btn-primary w-45">Conectare</button>
                <a href="index.php" class="btn btn-outline-primary w-45">Anulează</a>
            </div>

        </form>

        <div class="text-center mt-3">
            <p><b>Nu aveți un cont deja?</b></p>
            <a href="register.php" class="btn btn-success">Înregistrează-te</a>
        </div>

    </div>
</div>

<?php
    include "layout/footer.php";
?>