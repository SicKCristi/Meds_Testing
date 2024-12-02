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

    if(isset($_POST['delete_medicament_pacient'])){
        $ID_Medicament=$_POST['ID_Medicament'] ?? null;
        if($ID_Medicament){
            sterge_medicament_selectat($conexiune_bd, $ID_Medicament);
        }
    }

    if(isset($_POST['delete_pacient'])){
        sterge_pacient_din_pacienti($conexiune_bd);
    }

    if(isset($_POST['inscriere_pacient'])){
        // Formularul pentru inscrierea pacientului in tabela pacienti
        $DataNasterii=$_POST['DataNasterii'];
        $Sex=$_POST['Sex'];
        $Adresa=$_SESSION['Adresa'];
        $id_utilizator=$_SESSION['ID_Utilizator'];
        $Numele=$_SESSION['Nume'];
        $Prenumele=$_SESSION['Prenume'];
        $Emailul=$_SESSION['Email'];
        $Telefonul=$_SESSION['Telefon'];

        // Verifică dacă există deja un pacient cu același email sau telefon
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM pacienti WHERE Emailul = ? OR Telefonul = ?");
        $stmt->bind_param('ss', $Emailul, $Telefonul);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if($count>0){
            echo "<div class='alert alert-danger'>Ești deja înscris ca pacient pentru testare!</div>";
        } else{
            $query="SELECT IFNULL(MAX(ID_Pacient),0)+1 AS next_id FROM pacienti";
            $result=$conexiune_bd->query($query);
            $row=$result->fetch_assoc();
            $id_pacient_nou=$row['next_id'];
            $result->free();

            // Inserăm noul pacient în baza de date, folosind ID_Utilizator ca ID_Pacient
            $stmt=$conexiune_bd->prepare("INSERT INTO pacienti (ID_Pacient, Adresa, DataNasterii, Emailul, Numele, Prenumele, Sex, Telefonul) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('isssssss', $id_pacient_nou, $Adresa, $DataNasterii, $Emailul, $Numele, $Prenumele, $Sex, $Telefonul);

            if($stmt->execute()){
                echo "<div class='alert alert-success'>Te-ai înscris cu succes!</div>";
            } else{
                echo "<div class='alert alert-danger'>A apărut o eroare la înregistrare: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
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

    // Ștergere pacient din pacient
    if(isset($_POST['delete_pacient'])){
        sterge_pacient_din_pacienti($conexiune_bd);
    }

    // Ștergere din tabela testare_pacient
    if(isset($_POST['delete_testare_pacient'])){
        stergere_testare_pacient($conexiune_bd);
    }

    // Ștergere din tabela consultație
    if(isset($_POST['delete_medicament_pacient'])){
        stergere_medicament_pacient($conexiune_bd);
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea pacientului în lista cu pacienții</h2>
    <form method="post">
        <input type="hidden" name="inscriere_pacient" value="1">

        <div class="mb-3">
            <label for="DataNasterii" class="form-label">Data Nasterii</label>
            <input type="date" class="form-control" name="DataNasterii" required>
        </div>

        <div class="mb-3">
            <label for="Sex" class="form-label">Sexul</label>
            <select class="form-select" name="Sex" id="Sex" required>
                <option value="F" <?= isset($Sex) && $Sex === 'F' ? 'selected' : '' ?>>Femeie</option>
                <option value="M" <?= isset($Sex) && $Sex === 'M' ? 'selected' : '' ?>>Bărbat</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
    <form method="post">
        <button type="submit" name="delete_pacient" class="btn btn-danger mt-2">Șterge înregistrarea</button>
    </form>
</div>

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
