<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "tools/db.php";
    include "functii_stergere_pacient.php";

    $conexiune_bd=getDatabaseConnection();

    if(isset($_POST['delete_medicament_pacient'])){
        $ID_Medicament=$_POST['ID_Medicament'] ?? null;
        if($ID_Medicament){
            sterge_medicament_selectat($conexiune_bd, $ID_Medicament);
        }
    }


    if (isset($_POST['inscriere_pacient_medicament'])) {
        // Verificăm dacă ID_Pacient există în sesiune
        if (!isset($_SESSION['ID_Pacient'])){
            // Dacă ID_Pacient nu este în sesiune, încercăm să îl obținem din tabela pacienti folosind email-ul utilizatorului
            if (!isset($_SESSION['Email'])){
                echo "<div class='alert alert-danger'>Eroare: Utilizatorul nu este autentificat. Te rugăm să te autentifici din nou!</div>";
                exit();
            }
    
            $email_utilizator=$_SESSION['Email'];
            $stmt=$conexiune_bd->prepare("SELECT ID_Pacient FROM pacienti WHERE Emailul = ?");
            $stmt->bind_param('s', $email_utilizator);
            $stmt->execute();
            $stmt->bind_result($ID_Pacient);
    
            if ($stmt->fetch()){
                $_SESSION['ID_Pacient']=$ID_Pacient;
            } else{
                echo "<div class='alert alert-danger'>Eroare: Nu s-a găsit pacient asociat acestui utilizator! Te rugăm să contactezi administratorul.</div>";
                $stmt->close();
                exit();
            }
            $stmt->close();
        }
    
        // Preluăm ID_Pacient din sesiune
        $ID_Pacient=$_SESSION['ID_Pacient'];
    
        // Preluăm valorile din formular
        $ID_Medicament=$_POST['ID_Medicament'];
        $DataStart=$_POST['DataStart'];
        $DataFinalizare=$_POST['DataFinalizare'];
    
        // Verificăm dacă ID_Medicament există în tabela medicamente
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM medicamente WHERE ID_Medicament = ?");
        $stmt->bind_param('i', $ID_Medicament);
        $stmt->execute();
        $stmt->bind_result($medicament_exists);
        $stmt->fetch();
        $stmt->close();
    
        if($medicament_exists==0){
            echo "<div class='alert alert-danger'>ID-ul medicamentului nu există!</div>";
        } elseif($DataStart >= $DataFinalizare){
            echo "<div class='alert alert-danger'>Data de start trebuie să fie înainte de data de finalizare!</div>";
        } else{
            // Inserăm datele în tabelul pacient_medicament
            $stmt=$conexiune_bd->prepare("INSERT INTO pacient_medicament (ID_Pacient, ID_Medicament, DataStart, DataFinalizare) VALUES (?, ?, ?, ?)");
            $stmt->bind_param('iiss', $ID_Pacient, $ID_Medicament, $DataStart, $DataFinalizare);
            if($stmt->execute()){
                echo "<div class='alert alert-success'>Pacientul a fost înscris cu succes pentru medicament!</div>";
            } else{
                echo "<div class='alert alert-danger'>A apărut o eroare la înregistrare: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }

    // Ștergere din tabela consultație
    if(isset($_POST['delete_medicament_pacient'])){
        stergere_medicament_pacient($conexiune_bd);
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea pacientului pentru testarea unui medicament</h2>
    <form method="post">
        <input type="hidden" name="inscriere_pacient_medicament" value="1">
        <div class="mb-3">
            <label class="form-label">ID Medicament</label>
            <input type="number" class="form-control" name="ID_Medicament" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Start</label>
            <input type="date" class="form-control" name="DataStart" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Finalizare</label>
            <input type="date" class="form-control" name="DataFinalizare" required>
        </div>
        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
    <form method="post">
        <button type="submit" name="delete_medicament_pacient" class="btn btn-danger mt-2">Șterge înregistrarea</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
