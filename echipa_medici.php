<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    // Interogarea 1: Lista medicilor cu numărul de consultații
    $query1="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea,
            D.Spitalul,
                (SELECT COUNT(DISTINCT C.ID_Consultatie) 
                FROM consultatie C
                WHERE C.ID_Doctor=D.ID_Doctor) AS NumarPacienti
        FROM doctor D;";

    $rezultat1=$conexiune_bd->query($query1);
    $medici_consultatii=$rezultat1->fetch_all(MYSQLI_ASSOC);

    // Interogarea 2: Medicii care au participat la studii cu medicamente aprobate după un an dat
    $an_minim=$_POST['an_minim'] ?? 2019;
    $query2="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea
        FROM doctor AS D
        WHERE D.ID_Doctor IN (
                                SELECT SD.ID_Doctor
                                FROM studiu_doctor AS SD    JOIN studiu_clinic AS SC ON SD.ID_Studiu=SC.ID_Studiu
                                                            JOIN medicamente AS M ON SC.ID_Medicament=M.ID_Medicament
        WHERE M.DataAprobarii>'$an_minim-01-01');";

    $rezultat2=$conexiune_bd->query($query2);
    $medici_medicamente=$rezultat2->fetch_all(MYSQLI_ASSOC);

    // Interogarea 3: Medicii care au participat la cele mai multe studii
    $query3="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea
        FROM doctor AS D
        WHERE D.ID_Doctor IN (
                                SELECT SD.ID_Doctor
                                FROM studiu_doctor AS SD
                                GROUP BY SD.ID_Doctor
                                HAVING COUNT(SD.ID_Studiu)=(
                                                            SELECT MAX(NumarStudii)
                                                            FROM (
                                                                    SELECT COUNT(SD2.ID_Studiu) AS NumarStudii
                                                                    FROM studiu_doctor AS SD2
                                                                    GROUP BY SD2.ID_Doctor) AS Subquery));";
    $rezultat3=$conexiune_bd->query($query3);
    $medici_studii=$rezultat3->fetch_all(MYSQLI_ASSOC);

    // Interogarea 4: Lista doctorilor care fac parte din studii după o dată dată
    $data_inceput=$_POST['data_inceput'] ?? date('Y-m-d');
    $studii_medici=[];
    $query4="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea,
            SC.DataInceput,
            SC.Scopul
        FROM doctor AS D    JOIN studiu_doctor AS SD ON D.ID_Doctor=SD.ID_Doctor
                            JOIN studiu_clinic AS SC ON SD.ID_Studiu=SC.ID_Studiu
        WHERE SC.DataInceput>='$data_inceput';";
        
    $rezultat4=$conexiune_bd->query($query4);
    if($rezultat4){
        $studii_medici=$rezultat4->fetch_all(MYSQLI_ASSOC);
    } else{
        $studii_medici=[];
    }

    // Interogarea 5: Medicii care au participat la studii cu medicamente produse de un anumit producător
    $producator=$_POST['producator'] ?? '';
    $medicamente_medici=[];
    $query5="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea,
            M.Denumirea AS Medicament,
            M.Producatorul
        FROM doctor AS D    JOIN studiu_doctor AS SD ON D.ID_Doctor=SD.ID_Doctor
                            JOIN studiu_clinic AS SC ON SD.ID_Studiu=SC.ID_Studiu
                            JOIN medicamente AS M ON SC.ID_Medicament=M.ID_Medicament
        WHERE M.Producatorul LIKE '%$producator%';";

    $rezultat5=$conexiune_bd->query($query5);
    if($rezultat5){
        $medicamente_medici=$rezultat5->fetch_all(MYSQLI_ASSOC);
    } else{
        $medicamente_medici=[];
    }
?>

<div class="container py-5">

    <!-- Tabelul 1 -->
    <h2>Lista medicilor și numărul de consultații pentru fiecare</h2>
    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
                <th>Spital</th>
                <th>Număr Consultații</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($medici_consultatii as $medic): ?>
                <tr>
                    <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['Specializarea']) ?></td>
                    <td><?= htmlspecialchars($medic['Spitalul']) ?></td>
                    <td><?= htmlspecialchars($medic['NumarPacienti']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabelul 2 -->
    <h2>Medicii care au participat la studii cu medicamente aprobate după un anumit an</h2>

    <button class="btn btn-primary 5" data-bs-toggle="modal" data-bs-target="#modalMediciMedicamente">Introdu date</button>
    <div class="modal fade" id="modalMediciMedicamente" tabindex="-1" aria-labelledby="modalMediciMedicamenteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMediciMedicamenteLabel">Setează anul minim pentru aprobare</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="an_minim" class="form-label">An Minim</label>
                            <input type="number" class="form-control" name="an_minim" id="an_minim" value="<?= htmlspecialchars($an_minim) ?>" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                        <button type="submit" class="btn btn-primary">Confirmă</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($medici_medicamente as $medic): ?>
                <tr>
                    <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['Specializarea']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>


    <!-- Tabelul 3 -->
    <h2>Medicii care au participat la cele mai multe studii</h2>
    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($medici_studii as $medic): ?>
                <tr>
                    <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['Specializarea']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tabelul 4 -->
<h2>Medicii care au participat la studii clinice după o anumită dată</h2>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalStudiiMedici">Introdu date</button>

<div class="modal fade" id="modalStudiiMedici" tabindex="-1" aria-labelledby="modalStudiiMediciLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalStudiiMediciLabel">Setează data minimă pentru studii</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="data_inceput" class="form-label">Data Minimă</label>
                        <input type="date" class="form-control" name="data_inceput" id="data_inceput" value="<?= htmlspecialchars($data_inceput) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Confirmă</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($studii_medici)): ?>
    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
                <th>Data Început</th>
                <th>Scop</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($studii_medici as $studiu): ?>
                <tr>
                    <td><?= htmlspecialchars($studiu['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($studiu['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($studiu['Specializarea']) ?></td>
                    <td><?= htmlspecialchars($studiu['DataInceput']) ?></td>
                    <td><?= htmlspecialchars($studiu['Scopul']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-muted">Nu există studii care să îndeplinească criteriile introduse.</p>
<?php endif; ?>

    <!-- Tabelul 5 -->
<h2>Medicii care au participat la studii cu medicamente de un anumit producător</h2>
<button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalMedicamenteMedici">Introdu date</button>

<div class="modal fade" id="modalMedicamenteMedici" tabindex="-1" aria-labelledby="modalMedicamenteMediciLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalMedicamenteMediciLabel">Setează producătorul medicamentului</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="producator" class="form-label">Producător</label>
                        <input type="text" class="form-control" name="producator" id="producator" value="<?= htmlspecialchars($producator) ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Confirmă</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($medicamente_medici)): ?>
    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
                <th>Medicament</th>
                <th>Producător</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medicamente_medici as $medicament): ?>
                <tr>
                    <td><?= htmlspecialchars($medicament['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medicament['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medicament['Specializarea']) ?></td>
                    <td><?= htmlspecialchars($medicament['Medicament']) ?></td>
                    <td><?= htmlspecialchars($medicament['Producatorul']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p class="text-muted">Nu există medici care să fi participat la studii cu medicamente produse de acest producător.</p>
<?php endif; ?>
</div>

<?php
    include "layout/footer.php";
?>
