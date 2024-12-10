<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'];

    $alert_message=null;
    $alert_class="";

    // Obținem ID-ul pacientului asociat utilizatorului
    $query_pacient="SELECT ID_Pacient FROM pacienti WHERE Emailul=?";
    $stmt_pacient=$conexiune_bd->prepare($query_pacient);
    $stmt_pacient->bind_param('s', $Email_utilizator);
    $stmt_pacient->execute();
    $rezultat_pacient=$stmt_pacient->get_result();
    $pacient=$rezultat_pacient->fetch_assoc();
    $stmt_pacient->close();

    $ID_Pacient=$pacient['ID_Pacient'] ?? null;

    if(!$ID_Pacient){
        die("Pacientul nu a fost găsit.");
    }

    // Funcția pentru ștergerea înregistrării specificate
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_medicament_pacient'])){
        $ID_Medicament=$_POST['ID_Medicament'] ?? null;
        if($ID_Medicament){
            $deleteQuery="DELETE FROM pacient_medicament WHERE ID_Medicament = ? AND ID_Pacient = ?";
            $stmt_delete=$conexiune_bd->prepare($deleteQuery);
            $stmt_delete->bind_param('ii', $ID_Medicament, $ID_Pacient);
            if($stmt_delete->execute()){
                $alert_message="Înregistrarea a fost ștearsă cu succes!";
                $alert_class="alert-success";
            } else{
                $alert_message="Eroare la ștergere!";
                $alert_class="alert-danger";
            }
            $stmt_delete->close();
        }
    }

    // Interogarea 1, făcută cu tabelele utilizator, pacienti și testare_pacient
    $query1="
        SELECT 
            PM.ID_Medicament,
            PM.DataStart,
            PM.DataFinalizare,
            P.ID_Pacient
        FROM utilizator AS U    JOIN pacienti AS P ON U.Email=P.Emailul
                                JOIN pacient_medicament AS PM ON P.ID_Pacient=PM.ID_Pacient
        WHERE U.Email=?";

    $stmt1=$conexiune_bd->prepare($query1);
    $stmt1->bind_param('s', $Email_utilizator);
    $stmt1->execute();
    $rezultat1=$stmt1->get_result();

    $data1=[];
    while($rand=$rezultat1->fetch_assoc()){
        $data1[]=$rand;
    }

    $stmt1->close();

    // Interogarea 2, făcută cu tabelele pacient_medicament, medicamente și categorie
    $query2="
        SELECT 
            PM.ID_Medicament,
            M.Denumirea AS MedicamentDenumire,
            M.Descrierea AS MedicamentDescriere,
            C.Denumirea AS CategorieDenumire
        FROM pacient_medicament AS PM   JOIN medicamente AS M ON PM.ID_Medicament=M.ID_Medicament
                                        JOIN categorie AS C ON M.ID_Categorie=C.ID_Categorie";

    $rezultat2=$conexiune_bd->query($query2);
    $data2=[];
    while($rand=$rezultat2->fetch_assoc()){
        // Indexare se va face după ID_Medicament
        $data2[$rand['ID_Medicament']]=$rand;
    }

    $rezultat2->free();
?>

<div class="container py-5">
    <!-- Mesajul de alertă -->
    <?php if($alert_message): ?>
        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alert_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2>Medicamentele pe care le testați</h2>
    <?php if(empty($data1)) : ?>
        <p class="text-center fs-4">Nu participați la nicio testare!</p>
    <?php else : ?>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th>Denumirea Categoriei</th>
                    <th>Denumirea Medicamentului</th>
                    <th>Descrierea Medicamentului</th>
                    <th>Data Start</th>
                    <th>Data Finalizare</th>
                    <th>Acțiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($data1 as $entry) : ?>
                    <?php 
                        $medicamentData=$data2[$entry['ID_Medicament']] ?? [
                            'MedicamentDenumire' => 'N/A',
                            'MedicamentDescriere' => 'N/A',
                            'CategorieDenumire' => 'N/A',
                        ];
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($medicamentData['CategorieDenumire']); ?></td>
                        <td><?= htmlspecialchars($medicamentData['MedicamentDenumire']); ?></td>
                        <td><?= htmlspecialchars($medicamentData['MedicamentDescriere']); ?></td>
                        <td><?= htmlspecialchars($entry['DataStart']); ?></td>
                        <td><?= htmlspecialchars($entry['DataFinalizare']); ?></td>
                        <td>
                            <button 
                                class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalStergere<?= $entry['ID_Medicament']; ?>">
                                Renunță
                            </button>

                            <!-- Modal pentru ștergere -->
                            <div 
                                class="modal fade" 
                                id="modalStergere<?= $entry['ID_Medicament']; ?>" 
                                tabindex="-1" 
                                aria-labelledby="modalLabel<?= $entry['ID_Medicament']; ?>" 
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?= $entry['ID_Medicament']; ?>">
                                                Confirmare
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Închide"></button>
                                        </div>
                                        <div class="modal-body">
                                            Ești sigur că vrei să renunți la testarea acestui medicament?
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="">
                                                <input type="hidden" name="ID_Medicament" value="<?= $entry['ID_Medicament']; ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                <button type="submit" class="btn btn-danger" name="delete_medicament_pacient">Confirmă</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <!-- Butonul care face legătura cu pagina vizualizare_testari.php -->
    <div class="mt-4">
        <a href="vizualizare_testari.php" class="btn btn-primary">Vizualizează Testările</a>
    </div>
</div>

<?php
    include "layout/footer.php";
?>