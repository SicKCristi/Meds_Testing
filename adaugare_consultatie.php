<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    $testari=[];
    $query_testari="
        SELECT 
            TP.ID_Testare, CONCAT('Testarea nr. ', TP.ID_Testare, ' - ', P.Numele, ' ', P.Prenumele) AS DetaliiTestare
        FROM testare_pacient TP JOIN pacienti P ON TP.ID_Pacient=P.ID_Pacient";
    $rezultat_testari=$conexiune_bd->query($query_testari);
    if($rezultat_testari){
        while($rand=$rezultat_testari->fetch_assoc()){
            $testari[]=$rand;
        }
    }

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ID_Testare'], $_POST['DataConsultatie'], $_POST['Observatii'])){
        $ID_Testare=$_POST['ID_Testare'];
        $DataConsultatie=$_POST['DataConsultatie'];
        $Observatii=$_POST['Observatii'] ?? null;
        $Emailul=$_SESSION['Email'];

        $stmt=$conexiune_bd->prepare("SELECT ID_Doctor FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();

        if(!$ID_Doctor){
            echo "<div class='alert alert-danger'>Eroare: Doctorul nu este înregistrat în baza de date!</div>";
        } else{
            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM testare_pacient WHERE ID_Testare = ?");
            $stmt->bind_param('i', $ID_Testare);
            $stmt->execute();
            $stmt->bind_result($exista_testare);
            $stmt->fetch();
            $stmt->close();

            if($exista_testare>0){
                $query="SELECT IFNULL(MAX(ID_Consultatie), 0) + 1 AS next_id FROM consultatie";
                $rezultat=$conexiune_bd->query($query);
                $rand=$rezultat->fetch_assoc();
                $ID_nou=$rand['next_id'];
                $rezultat->free();

                $stmt=$conexiune_bd->prepare("INSERT INTO consultatie (ID_Consultatie, ID_Doctor, ID_Testare, DataConsultatie, Observatii) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('iiiss', $ID_nou, $ID_Doctor, $ID_Testare, $DataConsultatie, $Observatii);

                if($stmt->execute()){
                    echo "
                        <div class='alert alert-success alert-dismissible fade show' role='alert'>
                        Consultația a fost adăugată cu succes!
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                } else{
                    echo "
                    <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        A apărut o eroare la adăugare: " . htmlspecialchars($stmt->error) . "
                        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                }
                $stmt->close();
            } else{
                echo "<div class='alert alert-danger'>Eroare: ID Testare specificat nu există!</div>";
            }
        }
    }
?>

<div class="container py-5">
    <h2>Înregistrarea unei consultații</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Alege testarea pentru consultație</label>
            <?php foreach($testari as $testare): ?>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="ID_Testare" 
                        value="<?= htmlspecialchars($testare['ID_Testare']) ?>" 
                        required>
                    <label class="form-check-label">
                        <?= htmlspecialchars($testare['DetaliiTestare']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Consultației</label>
            <input type="date" class="form-control" name="DataConsultatie" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Observații</label>
            <textarea class="form-control" name="Observatii" rows="3"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Adaugă Consultația</button>
    </form>
</div>

<?php 
    include "layout/footer.php"; 
?>