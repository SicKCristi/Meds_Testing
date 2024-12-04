<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "tools/db.php";
    include "functii_stergere_pacient.php";

    $conexiune_bd=getDatabaseConnection();

    if(isset($_POST['delete_testare_pacient'])){
        $ID_Testare=$_POST['ID_Testare'] ?? null;
        if($ID_Testare){
            sterge_testare_selectata($conexiune_bd,$ID_Testare);
        }
    }

    if(isset($_POST['inscriere_testare_pacient'])){
        // Obține ID_Pacient pentru utilizatorul curent (din sesiune)
        $ID_Utilizator=$_SESSION['ID_Utilizator'];
        $stmt=$conexiune_bd->prepare("SELECT ID_Pacient FROM pacienti WHERE Emailul = ? OR Telefonul = ?");
        $stmt->bind_param('ss', $_SESSION['Email'], $_SESSION['Telefon']);
        $stmt->execute();
        $stmt->bind_result($ID_Pacient);
        $stmt->fetch();
        $stmt->close();
    
        if(empty($ID_Pacient)){
            echo "<div class='alert alert-danger'>Nu există un pacient asociat acestui utilizator.</div>";
        } else{
            $ID_Studiu=$_POST['ID_Studiu'];
            $DataInrolarii=$_POST['DataInrolarii'];
            $Statusul=$_POST['Statusul'];
    
            // Verificăm dacă ID_Studiu există în tabela studiu_clinic
            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_clinic WHERE ID_Studiu = ?");
            $stmt->bind_param('i', $ID_Studiu);
            $stmt->execute();
            $stmt->bind_result($studiu_exists);
            $stmt->fetch();
            $stmt->close();
    
            if($studiu_exists==0){
                echo "<div class='alert alert-danger'>ID-ul studiului nu există!</div>";
            } else{
                // Obține următorul ID_Testare
                $query="SELECT IFNULL(MAX(ID_Testare),0)+1 AS next_id FROM testare_pacient";
                $result=$conexiune_bd->query($query);
                $row=$result->fetch_assoc();
                $next_id=$row['next_id'];
                $result->free();
    
                // Inserăm datele în tabelul testare_pacient
                $stmt=$conexiune_bd->prepare("INSERT INTO testare_pacient (ID_Testare, ID_Pacient, ID_Studiu, DataInrolarii, Statusul) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('iiiss', $next_id, $ID_Pacient, $ID_Studiu, $DataInrolarii, $Statusul);
    
                if ($stmt->execute()){
                    echo "<div class='alert alert-success'>Pacientul a fost înscris cu succes în testare!</div>";
                } else{
                    echo "<div class='alert alert-danger'>A apărut o eroare la înregistrare: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            }
        }
    }

    // Ștergere din tabela testare_pacient
    if(isset($_POST['delete_testare_pacient'])){
        stergere_testare_pacient($conexiune_bd);
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea pacientului la o testare</h2>
    <form method="post">
        <input type="hidden" name="inscriere_testare_pacient" value="1">
        <div class="mb-3">
            <label class="form-label">ID Studiu</label>
            <input type="number" class="form-control" name="ID_Studiu" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Înrolării</label>
            <input type="date" class="form-control" name="DataInrolarii" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Statusul</label>
            <input type="text" class="form-control" name="Statusul" required>
        </div>
        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
    <form method="post">
        <button type="submit" name="delete_testare_pacient" class="btn btn-danger mt-2">Șterge înregistrarea</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
