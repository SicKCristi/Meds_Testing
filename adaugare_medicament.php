<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    $categoriile=[];
    $query_categorii="SELECT ID_Categorie, Denumirea FROM categorie";
    $rezultat_categorii=$conexiune_bd->query($query_categorii);
    if($rezultat_categorii){
        while($rand=$rezultat_categorii->fetch_assoc()){
            $categoriile[]=$rand;
        }
    }

    if($_SERVER['REQUEST_METHOD']==='POST'){
        $Denumirea=$_POST['Denumirea'];
        $Descrierea=$_POST['Descrierea'];
        $Producatorul=$_POST['Producatorul'];
        $ID_Categorie=$_POST['ID_Categorie'];
        $DataAprobarii=$_POST['DataAprobarii'];

        // Verificăm dacă ID_Categorie există
        $stmt=$conexiune_bd->prepare("SELECT COUNT(*) FROM categorie WHERE ID_Categorie = ?");
        $stmt->bind_param('i', $ID_Categorie);
        $stmt->execute();
        $stmt->bind_result($exista_categorie);
        $stmt->fetch();
        $stmt->close();

        if($exista_categorie===0){
            echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        ID-ul categoriei nu există
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        } else{
            // Vom determina care este ID-ul maxim din tabela medicamente
            $rezultat=$conexiune_bd->query("SELECT MAX(ID_Medicament) AS max_id FROM medicamente");
            $rand=$rezultat->fetch_assoc();
            $ID_maxim=$rand['max_id']??0;
            $ID_nou=$ID_maxim+1;

            // Vom insera noul medicament în tabela medicamente
            $stmt=$conexiune_bd->prepare("INSERT INTO medicamente (ID_Medicament, Denumirea, Descrierea, Producatorul, ID_Categorie, DataAprobarii) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param('isssis', $ID_nou, $Denumirea, $Descrierea, $Producatorul, $ID_Categorie, $DataAprobarii);

            if($stmt->execute()){
                echo '
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Medicamentul prescris de dumneavoastră a fost introdus!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
            } else{
                echo '
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            A apărut o eroare la introducerea medicamentului!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
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
            <label class="form-label">Alege Categoria</label>
            <?php foreach($categoriile as $categorie): ?>
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="radio" 
                        name="ID_Categorie" 
                        value="<?= htmlspecialchars($categorie['ID_Categorie']) ?>" 
                        required>
                    <label class="form-check-label">
                        <?= htmlspecialchars($categorie['Denumirea']) ?>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="mb-3">
            <label class="form-label">Data Aprobării</label>
            <input type="date" class="form-control" name="DataAprobarii" required>
        </div>
        <button type="submit" class="btn btn-primary">Adaugă Medicament</button>
    </form>
</div>

<?php 
    include "layout/footer.php";
?>
