<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    // Vom adăuga noua înregistrare a doctorului în tabela doctor
    if(isset($_POST['Specializarea']) && isset($_POST['Spitalul'])){
        $Specializarea=$_POST['Specializarea'];
        $Spitalul=$_POST['Spitalul'];
        $Nume=$_SESSION['Nume'];
        $Prenume=$_SESSION['Prenume'];
        $Emailul=$_SESSION['Email'];
        $Telefonul=$_SESSION['Telefon'];

        // Test de verificare dacă doctorul există deja în tabela doctor
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if($count>0){
            echo "<div class='alert alert-danger'>Ești deja înscris ca doctor!</div>";
        } else{
            $query="SELECT IFNULL(MAX(ID_Doctor), 0) + 1 AS next_id FROM doctor";
            $rezultat=$conexiune_bd->query($query);
            $rand=$rezultat->fetch_assoc();
            $ID_nou=$rand['next_id'];
            $rezultat->free();

            $stmt=$conexiune_bd->prepare("INSERT INTO doctor (ID_Doctor, Emailul, NumeDoctor, PrenumeDoctor, Specializarea, Spitalul, Telefonul) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('issssss', $ID_nou, $Emailul, $Nume, $Prenume, $Specializarea, $Spitalul, $Telefonul);
            if($stmt->execute()){
                echo "<div class='alert alert-success'>Doctorul a fost adăugat cu succes!</div>";
            } else{
                echo "<div class='alert alert-danger'>Eroare la adăugare: ".htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        }
    }

    // Ștergerea doctorului
    if(isset($_POST['delete_doctor'])){
        $Emailul=$_SESSION['Email'];

        // Vom determina ID-ul doctorului
        $stmt=$conexiune_bd->prepare("SELECT ID_Doctor FROM doctor WHERE Emailul = ?");
        $stmt->bind_param('s', $Emailul);
        $stmt->execute();
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();

        if(!empty($ID_Doctor)){
            // Vom șterge din tabela studiu_doctor
            $stmt=$conexiune_bd->prepare("DELETE FROM studiu_doctor WHERE ID_Doctor = ?");
            $stmt->bind_param('i', $ID_Doctor);
            $stmt->execute();
            $stmt->close();

            // Vom șterge din tabela consultatie
            $stmt=$conexiune_bd->prepare("DELETE FROM consultatie WHERE ID_Doctor = ?");
            $stmt->bind_param('i', $ID_Doctor);
            $stmt->execute();
            $stmt->close();

            // Acum, după ce am șters toate înregistrările doctorului din tabelele de legătură
            // Vom șterge înregistrarea și din tabela doctor
            $stmt=$conexiune_bd->prepare("DELETE FROM doctor WHERE ID_Doctor = ?");
            $stmt->bind_param('i', $ID_Doctor);
            if($stmt->execute()){
                echo "<div class='alert alert-success'>Doctorul a fost șters cu succes!</div>";
            } else{
                echo "<div class='alert alert-danger'>Eroare la ștergere: " . htmlspecialchars($stmt->error) . "</div>";
            }
            $stmt->close();
        } else{
            echo "<div class='alert alert-warning'>Doctorul nu a fost găsit.</div>";
        }
    }
}
?>

<div class="container py-5">
    <h2>Înscrierea doctorului</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Specializarea</label>
            <input type="text" class="form-control" name="Specializarea" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Spitalul</label>
            <input type="text" class="form-control" name="Spitalul" required>
        </div>
        <button type="submit" class="btn btn-success">Adaugă doctor</button>
    </form>
    <form method="post">
        <button type="submit" name="delete_doctor" class="btn btn-danger mt-2">Șterge doctor</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>