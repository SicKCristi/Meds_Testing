<?php
include "layout/header.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include "tools/db.php";

    $conexiune_bd = getDatabaseConnection();

    // Vom înscrie medicul în tabela doctor
    if (isset($_POST['Specializarea']) && isset($_POST['Spitalul'])) {
        $Specializarea = $_POST['Specializarea'];
        $Spitalul = $_POST['Spitalul'];
        $id_utilizator = $_SESSION['ID_Utilizator'];
        $Nume = $_SESSION['Nume'];
        $Prenume = $_SESSION['Prenume'];
        $Emailul = $_SESSION['Email'];
        $Telefonul = $_SESSION['Telefon'];

        // Testul de verificare dacă medicul există deja în baza de date
        $stmt = $conexiune_bd->prepare("SELECT COUNT(*) FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            echo "<div class='alert alert-danger'>Ești deja înscris ca medic pentru testare!</div>";
        } else {
            // Obținem următorul ID_Doctor din tabela doctor
            $query = "SELECT IFNULL(MAX(ID_Doctor), 0) + 1 AS next_id FROM doctor";
            $result = $conexiune_bd->query($query);
            $row = $result->fetch_assoc();
            $next_id = $row['next_id'];
            $result->free();

            // Insert în tabela doctor
            $stmt = $conexiune_bd->prepare("INSERT INTO doctor (ID_Doctor, Emailul, NumeDoctor, PrenumeDoctor, Specializarea, Spitalul, Telefonul) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('issssss', $next_id, $Emailul, $Nume, $Prenume, $Specializarea, $Spitalul, $Telefonul);
            if ($stmt->execute()) {
                echo "<div class='alert alert-success'>Te-ai înscris cu succes ca medic!</div>";
            } else {
                echo "<div class='alert alert-danger'>A apărut o eroare la înregistrare: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }

    // Înscrierea medicului în tabela de legătură studiu_doctor
    if (isset($_POST['ID_Studiu']) && isset($_POST['Rolul'])) {
        $ID_Studiu = $_POST['ID_Studiu'];
        $Rolul = $_POST['Rolul'];
        $Emailul = $_SESSION['Email'];

        // Obținem ID_Doctor pe baza emailului (pentru că a fost deja înscris anterior)
        $stmt = $conexiune_bd->prepare("SELECT ID_Doctor FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();

        if (!$ID_Doctor) {
            echo "<div class='alert alert-danger'>Eroare: Doctorul nu este înscris!</div>";
        } else {
            // Verificăm dacă studiul există
            $stmt = $conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_clinic WHERE ID_Studiu = ?");
            $stmt->bind_param('i', $ID_Studiu);
            $stmt->execute();
            $stmt->bind_result($studiu_exists);
            $stmt->fetch();
            $stmt->close();

            if ($studiu_exists > 0) {
                // Inserăm în tabelul studiu_doctor
                $stmt = $conexiune_bd->prepare("INSERT INTO studiu_doctor (ID_Doctor, ID_Studiu, RolDoctor) VALUES (?, ?, ?)");
                $stmt->bind_param('iis', $ID_Doctor, $ID_Studiu, $Rolul);

                if ($stmt->execute()) {
                    echo "<div class='alert alert-success'>Te-ai înscris cu succes în cadrul studiului!</div>";
                } else {
                    echo "<div class='alert alert-danger'>A apărut o eroare la înscrierea în studiu: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            } else {
                echo "<div class='alert alert-danger'>Studiul specificat nu există!</div>";
            }
        }
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea ca medic pentru testări</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Specializarea</label>
            <input type="text" class="form-control" name="Specializarea" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Spitalul la care profesați</label>
            <input type="text" class="form-control" name="Spitalul" required>
        </div>
        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
</div>

<div class="container py-5">
    <h2>Înscrierea în cadrul unui studiu clinic</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Studiul</label>
            <input type="number" class="form-control" name="ID_Studiu" min="1" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Rolul în cadrul studiului</label>
            <input type="text" class="form-control" name="Rolul" required>
        </div>
        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
