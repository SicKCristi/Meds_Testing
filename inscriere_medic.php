<?php
include "layout/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "tools/db.php";

    $Specializarea=$_POST['Specializarea'];
    $Spitalul=$_POST['Spitalul'];
    $id_utilizator=$_SESSION['ID_Utilizator'];
    $Nume=$_SESSION['Nume'];
    $Prenume=$_SESSION['Prenume'];
    $Emailul=$_SESSION['Email'];
    $Telefonul=$_SESSION['Telefon'];

    $conexiune_bd=getDatabaseConnection();

    // Verificăm dacă doctorul din sesiune este deja înscris
    $stmt = $conexiune_bd->prepare("SELECT COUNT(*) FROM doctor WHERE Emailul = ?");
    $stmt->bind_param('s', $Emailul);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<div class='alert alert-danger'>Ești deja înscris ca medic pentru testare!</div>";
    } else {
        $query="SELECT IFNULL(MAX(ID_Doctor),0)+1 AS next_id FROM doctor";
        $result=$conexiune_bd->query($query);
        $row=$result->fetch_assoc();
        $next_id=$row['next_id'];
        $result->free();

        $stmt = $conexiune_bd->prepare("INSERT INTO doctor (ID_Doctor, Emailul, NumeDoctor, PrenumeDoctor, Specializarea, Spitalul, Telefonul) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('issssss', $next_id,$Emailul, $Nume, $Prenume, $Specializarea, $Spitalul, $Telefonul);
        $stmt->execute();
        $Inserare_ID_Doctor = $stmt->insert_id;
        $stmt->close();

        echo "<div class='alert alert-success'>Te-ai înscris cu succes!</div>";
    }
}
?>

<div class="container py-5">
    <h2>Inscriere Medic pentru Testare</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Specializarea</label>
            <input type="text" class="form-control" name="Specializarea" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Spitalul la care profesați</label>
            <input type="text" class="form-control" name="Spitalul" required>
        </div>
        <button type="submit" class="btn btn-success">Înscriere</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
