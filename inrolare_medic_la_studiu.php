<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    $studii=[];
    $query_studii="SELECT ID_Studiu, Scopul FROM studiu_clinic";
    $rezultat_studii=$conexiune_bd->query($query_studii);
    if($rezultat_studii){
        while($rand=$rezultat_studii->fetch_assoc()){
            $studii[]=$rand;
        }
    }

    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['ID_Studiu'], $_POST['Rolul'])){
        $ID_Studiu=$_POST['ID_Studiu'];
        $Rolul=$_POST['Rolul'];
        $Emailul=$_SESSION['Email'];

        $stmt=$conexiune_bd->prepare("SELECT ID_Doctor FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();

        if(!$ID_Doctor){
            echo "<div class='alert alert-danger'>Eroare: Doctorul nu este înscris!</div>";
        } else{
            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_doctor WHERE ID_Doctor = ? AND ID_Studiu = ?");
            $stmt->bind_param('ii', $ID_Doctor, $ID_Studiu);
            $stmt->execute();
            $stmt->bind_result($deja_inscris);
            $stmt->fetch();
            $stmt->close();

            if($deja_inscris>0){
                echo 
                    '<div class="alert alert-warning alert-dismissible fade show" role="alert">
                        V-ați înscris deja la acest studiu!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
            } else{
                $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_clinic WHERE ID_Studiu = ?");
                $stmt->bind_param('i', $ID_Studiu);
                $stmt->execute();
                $stmt->bind_result($exista_studiu);
                $stmt->fetch();
                $stmt->close();

                if($exista_studiu>0){
                    $stmt=$conexiune_bd->prepare("INSERT INTO studiu_doctor (ID_Doctor, ID_Studiu, RolDoctor) VALUES (?, ?, ?)");
                    $stmt->bind_param('iis', $ID_Doctor, $ID_Studiu, $Rolul);

                    if($stmt->execute()){
                        echo '
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Te-ai înscris cu succes în cadrul studiului!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    } else{
                        echo '
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            A apărut o eroare la înscriere: ' . htmlspecialchars($stmt->error) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    }
                    $stmt->close();
                } else{
                echo "<div class='alert alert-danger'>Studiul specificat nu există!</div>";
                }
            }
        }
    }
?>

<div class="container py-5">
    <h2>Înscrierea doctorului în cadrul unui studiu clinic</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Alege studiul la care dorești să participi</label>
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
            <label class="form-label">Rolul în cadrul studiului</label>
            <input type="text" class="form-control" name="Rolul" required>
        </div>
        <button type="submit" class="btn btn-success">Înscrie-te!</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
