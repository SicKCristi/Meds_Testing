<?php
    include_once "tools/db.php";
    session_start();
    $autentificat=false;
    if(isset($_SESSION["Email"])){
        $autentificat=true;
    }
    $medic_inrolat=false;
    $pacient_inrolat=false;
    $Email_Utilizator=$_SESSION['Email'] ?? null;

    if($Email_Utilizator){
        $conexiune_bd=getDatabaseConnection();
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM pacienti WHERE Emailul=?");
        $stmt->bind_param("s", $Email_Utilizator);
        $stmt->execute();
        $stmt->bind_result($numar_pacienti);
        $stmt->fetch();
        $stmt->close();

        $pacient_inrolat=$numar_pacienti>0;
    }

    if($Email_Utilizator){
        $conexiune_bd=getDatabaseConnection();
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM doctor WHERE Emailul=?");
        $stmt->bind_param("s", $Email_Utilizator);
        $stmt->execute();
        $stmt->bind_result($numar_doctori);
        $stmt->fetch();
        $stmt->close();

        $medic_inrolat=$numar_doctori>0;
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evidenta Testare</title>
    <link rel="icon" href="/images/Logo.jpeg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeoA6H36OyoBpaO96zrpO9GHqJZzPUF0CZR9QQ0LRn0o9b4k" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm" style="background-color: #daf3fd !important;">
    <div class="container">
      <a class="navbar-brand" href="<?php 
          if($autentificat){
              echo($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
          } else{
              echo 'index.php';
          }
      ?>">
        <img src="/images/Logo.jpeg" width="30" height="30" class="d-inline-block align-top" alt=""> Evidenta Testare
      </a>
      
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <?php if($autentificat){ ?>
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            <li class="nav-item">
              <a class="nav-link" href="<?php 
                echo($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
              ?>" title="HOME">
                <i class="fa-solid fa-house fa-lg" style="color: #08618d;"></i>
              </a>
            </li>
          </ul>

          <ul class="navbar-nav">
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $_SESSION["Rol"] ?? 'Utilizator'; ?>
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/profile.php">Profil</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
              </ul>
            </li>
          </ul>

          <?php if($_SESSION["Rol"]==='Pacient'){ ?>
            <div class="nav-item dropdown ms-2">
              <a class="btn btn-outline-success dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                 Acțiuni Pacient
              </a>
              <ul class="dropdown-menu">
                <?php if($pacient_inrolat){ ?>
                  <li><a class="dropdown-item" href="/inscriere_pacient.php">Înrolează-te ca pacient</a></li>
                  <li><a class="dropdown-item" href="/inscriere_testare.php">Adaugă o testare</a></li>
                  <li><a class="dropdown-item" href="/inscriere_testare_medicament.php">Adaugă un medicament testat</a></li>
                  <li><a class="dropdown-item" href="/vizualizare_testari.php">Vezi testările tale</a></li>
                  <li><a class="dropdown-item" href="/vizualizare_medicamente.php">Vezi medicamentele testate</a></li>
                  <li><a class="dropdown-item" href="/informatii_despre_medici.php">Vezi informații despre medicii</a></li>
                <?php } else{ ?>
                  <li><a class="dropdown-item" href="/inscriere_pacient.php">Înrolează-te ca pacient</a></li>
                  <li><a class="dropdown-item" href="/informatii_despre_medici.php">Vezi informații despre medicii</a></li>
                <?php } ?>
              </ul>
            </div>
          <?php } elseif($_SESSION["Rol"] === 'Medic'){ ?>
            <div class="nav-item dropdown ms-2">
              <a class="btn btn-outline-success dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Acțiuni Medic
              </a>
              <ul class="dropdown-menu">
                <?php if($medic_inrolat){ ?>
                  <li><a class="dropdown-item" href="/inscriere_medic.php">Alătură-te medicilor din testări</a></li>
                  <li><a class="dropdown-item" href="/inrolare_medic_la_studiu.php">Înrolează-te la studii</a></li>
                  <li><a class="dropdown-item" href="/adaugare_consultatie.php">Adaugă o nouă consultație</a></li>
                  <li><a class="dropdown-item" href="/adaugare_categorie.php">Adaugă o nouă categorie</a></li>
                  <li><a class="dropdown-item" href="/adaugare_medicament.php">Adaugă un nou medicament</a></li>
                  <li><a class="dropdown-item" href="/vizualizare_consultatii.php">Vezi consultațiile tale</a></li>
                  <li><a class="dropdown-item" href="/vizualizare_studii.php">Vezi studiile tale</a></li>
                  <li><a class="dropdown-item" href="/echipa_medici.php">Vezi echipa de medici a testării</a></li>
                <?php } else{ ?>
                  <li><a class="dropdown-item" href="/inscriere_medic.php">Alătură-te medicilor</a></li>
                  <li><a class="dropdown-item" href="/echipa_medici.php">Vezi echipa de medici a testării</a></li>
                <?php } ?>
              </ul>
            </div>
          <?php } ?>

        <?php } else{ ?>
          <div class="ms-auto">
            <a href="/register.php" class="btn btn-outline-primary me-2">Register</a>
            <a href="/login.php" class="btn btn-outline-primary me-2">Login</a>
          </div>
        <?php } ?>
      </div>
    </div>
  </nav>