<?php
session_start();

$autentificat=false;
if(isset($_SESSION["Email"])){
    $autentificat=true;
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
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm">
    <div class="container">
      <a class="navbar-brand" href="<?php 
          if($autentificat){
              echo ($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
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
                echo ($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
              ?>" title="HOME">
                <img src="/images/Home.png" alt="Home" style="width: 24px; height: 24px;">
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
                <li><a class="dropdown-item" href="/inscriere_pacient.php">Înrolează-te ca pacient</a></li>
                <li><a class="dropdown-item" href="/inscriere_testare.php">Adaugă o testare</a></li>
                <li><a class="dropdown-item" href="/inscriere_testare_medicament.php">Adaugă un medicament testat</a></li>
              </ul>
          </div>
          <?php } elseif($_SESSION["Rol"]==='Medic'){ ?>
            <div class="nav-item dropdown ms-2">
              <a class="btn btn-outline-success dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Acțiuni Medic
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/inscriere_medic.php">Alătură-te medicilor din testări</a></li>
                <li><a class="dropdown-item" href="/inrolare_medic_la_studiu.php">Înrolează-te la studii</a></li>
                <li><a class="dropdown-item" href="/adaugare_consultatie.php">Adaugă o nouă consultație</a></li>
                <li><a class="dropdown-item" href="/adaugare_categorie.php">Adaugă o nouă categorie</a></li>
                <li><a class="dropdown-item" href="/adaugare_medicament.php">Adaugă un nou medicament</a></li>
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
