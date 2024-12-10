<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd = getDatabaseConnection();

    $studii=[];
    $query_studii="SELECT ID_Studiu, Scopul FROM studiu_clinic";
    $rezultat_studii=$conexiune_bd->query($query_studii);
    if($rezultat_studii){
        while($rand=$rezultat_studii->fetch_assoc()){
            $studii[]=$rand;
        }
    }

    // Gestionarea formularului de înscriere
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['inscriere_testare_pacient'])){
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

            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_clinic WHERE ID_Studiu = ?");
            $stmt->bind_param('i', $ID_Studiu);
            $stmt->execute();
            $stmt->bind_result($studiu_exista);
            $stmt->fetch();
            $stmt->close();

            if($studiu_exista==0){
                echo "<div class='alert alert-danger'>ID-ul studiului nu există!</div>";
            } else {
                $query="SELECT IFNULL(MAX(ID_Testare), 0) + 1 AS next_id FROM testare_pacient";
                $rezultat=$conexiune_bd->query($query);
                $rand=$rezultat->fetch_assoc();
                $ID_nou=$rand['next_id'];
                $rezultat->free();

                $stmt=$conexiune_bd->prepare("INSERT INTO testare_pacient (ID_Testare, ID_Pacient, ID_Studiu, DataInrolarii, Statusul) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('iiiss', $ID_nou, $ID_Pacient, $ID_Studiu, $DataInrolarii, $Statusul);

                if($stmt->execute()){
                    echo '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Înregistrarea a fost adăugată cu succes!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                } else{
                    echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        Eroare la adăugarea înregistrării!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
                }
                $stmt->close();
            }
        }
    }
?>

<div class="container py-5">
    <h2>Înscrierea pacientului la o testare</h2>
    <br>
    <form method="post">
        <input type="hidden" name="inscriere_testare_pacient" value="1">
        <div class="mb-3">
            <label class="form-label">Alege studiul la care vrei să participi</label>
            <?php foreach($studii as $studiu): ?>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="ID_Studiu" 
                        value="<?= htmlspecialchars($studiu['ID_Studiu']) ?>" 
                        required>
                    <label class="form-check-label">
                        <?= htmlspecialchars($studiu['Scopul']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
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
</div>

<?php 
    include "layout/footer.php"; 
?>