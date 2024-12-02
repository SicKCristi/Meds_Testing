<?php
include "layout/header.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    include "tools/db.php";

    $Denumirea=$_POST['Denumirea'];
    $Descrierea=$_POST['Descrierea'];
    $Producatorul=$_POST['Producatorul'];
    $ID_Categorie=$_POST['ID_Categorie'];
    $DataAprobarii=$_POST['DataAprobarii'];

    $conexiune_bd = getDatabaseConnection();

    // Verificăm dacă ID_Categorie există
    $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM categorie WHERE ID_Categorie = ?");
    $stmt->bind_param('i', $ID_Categorie);
    $stmt->execute();
    $stmt->bind_result($exista_categorie);
    $stmt->fetch();
    $stmt->close();

    if($exista_categorie===0){
        echo "<div class='alert alert-danger'>ID-ul categoriei specificat nu există!</div>";
    } else{
        // Vom determina care este ID-ul maxim din tabela medicamente
        $rezultat=$conexiune_bd->query("SELECT MAX(ID_Medicament) AS max_id FROM medicamente");
        $rand=$rezultat->fetch_assoc();
        $ID_maxim=$rand['max_id']??0;
        $ID_nou=$ID_maxim+1;

        // Vom insera noul medicament în tabela medicamente
        $stmt=$conexiune_bd->prepare("INSERT INTO medicamente (ID_Medicament, Denumirea, Descrierea, Producatorul, ID_Categorie, DataAprobarii) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('isssis', $ID_nou, $Denumirea, $Descrierea, $Producatorul, $ID_Categorie, $DataAprobarii);

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Medicamentul a fost adăugat cu succes!</div>";
        } else {
            echo "<div class='alert alert-danger'>A apărut o eroare: " . htmlspecialchars($stmt->error)."</div>";
        }

        $stmt->close();
    }
}
?>

<div class="container py-5">
    <h2>Adaugă un nou medicament</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Denumirea</label>
            <input type="text" class="form-control" name="Denumirea" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrierea</label>
            <textarea class="form-control" name="Descrierea" required></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Producătorul</label>
            <input type="text" class="form-control" name="Producatorul" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ID Categorie</label>
            <input type="number" class="form-control" name="ID_Categorie" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Aprobării</label>
            <input type="date" class="form-control" name="DataAprobarii" required>
        </div>
        <button type="submit" class="btn btn-primary">Adaugă Medicament</button>
    </form>
</div>

<?php include "layout/footer.php"; ?>
