<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "functii_stergere_pacient.php";

    $conexiune_bd=getDatabaseConnection();

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
            echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Ești deja înscris în evidență!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
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
                echo '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Te-ai înscris cu succes la evidență!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            } else{
                echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        A apărut o eroare la înregistrare!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            }
            $stmt->close();
        }
    }

    // Ștergere pacient din pacient
    if(isset($_POST['delete_pacient'])){
        sterge_pacient_din_pacienti($conexiune_bd);
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

<?php include "layout/footer.php"; ?>
