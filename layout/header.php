<?php
    session_start();

    $autentificat = false;
    if (isset($_SESSION["Email"])) {
        $autentificat = true;
    }
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Evidenta Testare</title>
    <link rel="icon" href="/images/logo.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>

  <nav class="navbar navbar-expand-lg bg-body-tertiary border-bottom shadow-sm">
    <div class="container">
    <a class="navbar-brand" href="<?php 
      if ($autentificat) {
        echo ($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
      } else {
        echo 'index.php';
      }
    ?>">
        <img src="/images/logo.png" width="30" height="30" class="d-inline-block align-top" alt=""> Evidenta testare
    </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
          <a class="nav-link text-dark" href="<?php 
            if ($autentificat) {
              echo ($_SESSION["Rol"]==='Pacient') ? 'index_pacient.php' : 'index_medic.php';
            } else {
              echo 'index.php';
            }
            ?>">Home</a>
          </li>
        </ul>

        <?php if ($autentificat) { ?>
        <ul class="navbar-nav">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-dark" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= $_SESSION["Rol"] ?? 'Utilizator'; ?>
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="/profile.php">Profile</a></li>
              <li>
                  <hr class="dropdown-divider">
              </li>
              <li><a class="dropdown-item" href="/logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
        <?php } else { ?>
        <ul class="navbar-nav">
          <li class="nav-item">
              <a href="/register.php" class="btn btn-outline-primary me-2">Register</a>
          </li>
          <li class="nav-item">
              <a href="/login.php" class="btn btn-outline-primary me-2">Login</a>
          </li>
        </ul>
        <?php } ?>

      </div>
    </div>
  </nav>
