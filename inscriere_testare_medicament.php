<?php
    include "layout/header.php";

    $conexiune_bd = getDatabaseConnection();

    $medicamente=[];
    $query_medicamente="SELECT ID_Medicament, Denumirea FROM medicamente";
    $rezultat_medicamente=$conexiune_bd->query($query_medicamente);
    if ($rezultat_medicamente){
        while($rand=$rezultat_medicamente->fetch_assoc()){
            $medicamente[]=$rand;
        }
    }

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inscriere_pacient_medicament'])){
        if(!isset($_SESSION['ID_Pacient'])){
            if(!isset($_SESSION['Email'])){
                echo "<div class='alert alert-danger'>Eroare: Utilizatorul nu este autentificat. Te rugăm să te autentifici din nou!</div>";
                exit();
            }
            $email_utilizator=$_SESSION['Email'];
            $stmt=$conexiune_bd->prepare("SELECT ID_Pacient FROM pacienti WHERE Emailul = ?");
            $stmt->bind_param('s', $email_utilizator);
            $stmt->execute();
            $stmt->bind_result($ID_Pacient);

            if($stmt->fetch()){
                $_SESSION['ID_Pacient']=$ID_Pacient;
            } else{
                echo "<div class='alert alert-danger'>Eroare: Nu s-a găsit pacient asociat acestui utilizator! Te rugăm să contactezi administratorul.</div>";
                $stmt->close();
                exit();
            }
            $stmt->close();
        }

        $ID_Pacient=$_SESSION['ID_Pacient'];
        $ID_Medicament=$_POST['ID_Medicament'];
        $DataStart=$_POST['DataStart'];
        $DataFinalizare=$_POST['DataFinalizare'];

        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM medicamente WHERE ID_Medicament = ?");
        $stmt->bind_param('i', $ID_Medicament);
        $stmt->execute();
        $stmt->bind_result($medicament_exista);
        $stmt->fetch();
        $stmt->close();

        if($medicament_exista==0){
            echo "<div class='alert alert-danger'>Medicamentul selectat nu există!</div>";
        } else{
            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM pacient_medicament WHERE ID_Pacient = ? AND ID_Medicament = ?");
            $stmt->bind_param('ii', $ID_Pacient, $ID_Medicament);
            $stmt->execute();
            $stmt->bind_result($medicament_testat);
            $stmt->fetch();
            $stmt->close();

            if($medicament_testat>0){
                echo "<div class='alert alert-danger'>Pacientul este deja înscris pentru testarea acestui medicament!</div>";
            } elseif($DataStart>=$DataFinalizare){
                echo "<div class='alert alert-danger'>Data de start trebuie să fie înainte de data de finalizare!</div>";
            } else{
                $stmt=$conexiune_bd->prepare("INSERT INTO pacient_medicament (ID_Pacient, ID_Medicament, DataStart, DataFinalizare) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('iiss', $ID_Pacient, $ID_Medicament, $DataStart, $DataFinalizare);
                if($stmt->execute()){
                    echo '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Pacientul a fost înscris cu succes pentru medicament!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } else{
                    echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        A apărut o eroare la înregistrare: ' . htmlspecialchars($stmt->error) . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                $stmt->close();
            }
        }
    }
?>

<div class="container py-5">
    <h2>Înscrierea pacientului pentru testarea unui medicament</h2>
    <form method="post">
        <input type="hidden" name="inscriere_pacient_medicament" value="1">
        <div class="mb-3">
            <label class="form-label">Alege medicamentul pentru testare</label>
            <?php foreach($medicamente as $medicament): ?>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="ID_Medicament" 
                        value="<?= htmlspecialchars($medicament['ID_Medicament']) ?>" 
                        required>
                    <label class="form-check-label">
                        <?= htmlspecialchars($medicament['Denumirea']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Start</label>
            <input type="date" class="form-control" name="DataStart" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Finalizare</label>
            <input type="date" class="form-control" name="DataFinalizare" required>
        </div>
        <button type="submit" class="btn btn-success">Adaugă medicamentul!</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
