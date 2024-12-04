<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "tools/db.php";
    include "functii_stergere_doctor.php";

    $conexiune_bd=getDatabaseConnection();

    // Gestionarea ștergerii
    if(isset($_POST['delete_studiu_doctor'])){
        $ID_Studiu=$_POST['ID_Studiu'] ?? null;
        if($ID_Studiu){
            sterge_studiu_selectat($conexiune_bd, $ID_Studiu);
        }
    }

    // Operația de insert a medicului în tabela de legătură cu studiile clinice studiu_doctor
    if(isset($_POST['ID_Studiu']) && isset($_POST['Rolul'])){
        $ID_Studiu=$_POST['ID_Studiu'];
        $Rolul=$_POST['Rolul'];
        $Emailul=$_SESSION['Email'];

        // Determinăm ID_Doctor pe baza emailului (pentru că a fost deja înscris anterior)
        $stmt=$conexiune_bd->prepare("SELECT ID_Doctor FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();

        if(!$ID_Doctor){
            echo "<div class='alert alert-danger'>Eroare: Doctorul nu este înscris!</div>";
        } else{
            // Test de verificare dacă studiul există deja
            $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM studiu_clinic WHERE ID_Studiu = ?");
            $stmt->bind_param('i', $ID_Studiu);
            $stmt->execute();
            $stmt->bind_result($exista_studiu);
            $stmt->fetch();
            $stmt->close();

            if($exista_studiu>0){
                // În cazul favorabil, inserăm în tabela de legătură studiu_doctor
                $stmt=$conexiune_bd->prepare("INSERT INTO studiu_doctor (ID_Doctor, ID_Studiu, RolDoctor) VALUES (?, ?, ?)");
                $stmt->bind_param('iis', $ID_Doctor, $ID_Studiu, $Rolul);

                if($stmt->execute()){
                    echo "<div class='alert alert-success'>Te-ai înscris cu succes în cadrul studiului!</div>";
                } else{
                    echo "<div class='alert alert-danger'>A apărut o eroare la înscrierea în studiu: " . htmlspecialchars($stmt->error) . "</div>";
                }
                $stmt->close();
            } else{
                echo "<div class='alert alert-danger'>Studiul specificat nu există!</div>";
            }
        }
    }

    // Șterge din tabela studiu_doctor
    if(isset($_POST['delete_studiu_doctor'])){
        stergere_doctor_studiu($conexiune_bd);
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea doctorului în cadrul unui studiu clinic</h2>
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
    <form method="post">
        <button type="submit" name="delete_studiu_doctor" class="btn btn-danger mt-2">Șterge înregistrarea</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>