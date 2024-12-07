<?php
include "layout/header.php";
include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'];

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
    <h2>Medicamentele pe care le testați</h2>

    <?php if(empty($data1)) : ?>
        <p class="text-center fs-4">Nu participați la nicio testare!</p>
    <?php else : ?>
        <table class="table table-striped">
            <thead>
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
                                                Ești sigur?
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Închide"></button>
                                        </div>
                                        <div class="modal-body">
                                            Acțiunea va șterge această înregistrare.
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST" action="">
                                                <input type="hidden" name="ID_Medicament" value="<?= $entry['ID_Medicament']; ?>">
                                                <button type="submit" class="btn btn-danger" name="delete_medicament_pacient">Confirmă</button>
                                            </form>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
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
    // Funcția de ștergere pentru înregistrarea din tabela pacient_medicament
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_medicament_pacient'])){
        $ID_Medicament=$_POST['ID_Medicament'];

        $deleteQuery="DELETE FROM pacient_medicament WHERE ID_Medicament = ? AND ID_Pacient = ?";
        $stmtDelete=$conexiune_bd->prepare($deleteQuery);
        $stmtDelete->bind_param('ii', $ID_Medicament, $data1[0]['ID_Pacient']); // Folosim ID_Pacient din prima interogare
        if($stmtDelete->execute()){
        echo "<script>alert('Înregistrarea a fost ștearsă!'); window.location.reload();</script>";
    } else{
        echo "<script>alert('Eroare la ștergere!');</script>";
    }
    $stmtDelete->close();
}

include "layout/footer.php";
?>
