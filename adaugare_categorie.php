<?php
    include "layout/header.php";

    if($_SERVER['REQUEST_METHOD']==='POST'){
        include "tools/db.php";

        $Denumirea=$_POST['Denumirea'];
        $Descrierea=$_POST['Descrierea'];

        $conexiune_bd=getDatabaseConnection();

        // Determinăm ID-ul maxim curent
        $rezultat=$conexiune_bd->query("SELECT MAX(ID_Categorie) AS max_id FROM categorie");
        $rand=$rezultat->fetch_assoc();
        $ID_maxim=$rand['max_id']??0;
        $ID_nou=$ID_maxim+1;

        // Vom insera în tabela categorie noua categorie din form
        $stmt=$conexiune_bd->prepare("INSERT INTO categorie (ID_Categorie, Denumirea, Descrierea) VALUES (?, ?, ?)");
        $stmt->bind_param('iss', $ID_nou, $Denumirea, $Descrierea);

        if($stmt->execute()){
            echo '
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        Categoria de medicamente a fost introdusă cu succes!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>'; 
        } else{
            echo '
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        A apărut o eroare la introducerea noii categorii!
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>';
        }

        $stmt->close();
    }
?>

<div class="container py-5">
    <h2>Adăugați o nouă categorie</h2>
    <form method="post">
        <div class="mb-3">
            <label class="form-label">Denumirea</label>
            <input type="text" class="form-control" name="Denumirea" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Descrierea</label>
            <textarea class="form-control" name="Descrierea" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Adaugăți noua categorie!</button>
    </form>
</div>

<?php 
    include "layout/footer.php"; 
?>
