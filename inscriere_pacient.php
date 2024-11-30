<?php
include "layout/header.php";

if ($_SERVER['REQUEST_METHOD']==='POST') {
    include "tools/db.php";

    if (!isset($_SESSION['Adresa'], $_SESSION['ID_Utilizator'], $_SESSION['Nume'], $_SESSION['Prenume'], $_SESSION['Email'], $_SESSION['Telefon'])) {
        echo "<div class='alert alert-danger'>Unele informații lipsesc din sesiune. Vă rugăm să vă autentificați din nou.</div>";
        exit();
    }

    $DataNasterii=$_POST['DataNasterii'];
    $Sex=$_POST['Sex'];
    $Adresa=$_SESSION['Adresa'];
    $id_utilizator=$_SESSION['ID_Utilizator'];
    $Numele=$_SESSION['Nume'];
    $Prenumele=$_SESSION['Prenume'];
    $Emailul=$_SESSION['Email'];
    $Telefonul=$_SESSION['Telefon'];

    $conexiune_bd = getDatabaseConnection();

    // Verifică dacă există deja un pacient cu același email sau telefon
    $stmt = $conexiune_bd->prepare("SELECT COUNT(*) FROM pacienti WHERE Emailul = ? OR Telefonul = ?");
    $stmt->bind_param('ss', $Emailul, $Telefonul);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count > 0) {
        echo "<div class='alert alert-danger'>Ești deja înscris ca pacient pentru testare!</div>";
    } else {
        // Cautam care este indexul maxim din tabela
        $query="SELECT IFNULL(MAX(ID_Pacient),0)+1 AS next_id FROM pacienti";
        $result=$conexiune_bd->query($query);
        $row=$result->fetch_assoc();
        $next_id=$row['next_id'];
        $result->free();

        // Inseram in baza de date noul pacient
        $stmt = $conexiune_bd->prepare("INSERT INTO pacienti (ID_Pacient, Adresa, DataNasterii, Emailul, Numele, Prenumele, Sex, Telefonul) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssssss', $next_id, $Adresa, $DataNasterii, $Emailul, $Numele, $Prenumele, $Sex, $Telefonul);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Te-ai înscris cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>A apărut o eroare la înregistrare: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}
?>

<div class="container py-5">
    <h2>Inscriere Pacient la Testare</h2>
    <form method="post">
        <div class="row mb-3">
            <label for="DataNasterii" class="form-label">Data Nasterii</label>
            <div class="col-sm-8">
                <input type="date" class="form-control" name="DataNasterii" required>
            </div>
        </div>
        <div class="row mb-3">
            <label for="Sex" class="form-label">Sexul</label>
            <div class="col-sm-8">
                <select class="form-select" name="Sex" id="Sex" required>
                    <option value="F">Femeie</option>
                    <option value="M">Bărbat</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Înscriere</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
