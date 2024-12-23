<?php
    include "layout/header.php";

    $conexiune_bd=getDatabaseConnection();

    $medici_medicamente=[];
    $an_minim=$_POST['an_minim'] ?? null;
    if($an_minim!==null){
        // Interogarea 2: Medicii care au participat la studii cu medicamente aprobate după un an dat
        // Interogare complexă cu subcerere (#2)
        $query2="
            SELECT 
                D.NumeDoctor,
                D.PrenumeDoctor,
                D.Specializarea,
                D.Spitalul
            FROM doctor AS D
            WHERE D.ID_Doctor IN (
                                    SELECT SD.ID_Doctor
                                    FROM studiu_doctor AS SD
                                    JOIN studiu_clinic AS SC ON SD.ID_Studiu=SC.ID_Studiu
                                    JOIN medicamente AS M ON SC.ID_Medicament=M.ID_Medicament
                                    WHERE M.DataAprobarii>'$an_minim-01-01');";

        $rezultat2=$conexiune_bd->query($query2);
        $medici_medicamente=$rezultat2->fetch_all(MYSQLI_ASSOC);
    }
?>

<div class="container py-5">
    <h2>Medicii care au participat la studii cu medicamente aprobate după un anumit an</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3">
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalMediciMedicamente">Introduceți anul de referință</button>
    
    <div class="modal fade" id="modalMediciMedicamente" tabindex="-1" aria-labelledby="modalMediciMedicamenteLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMediciMedicamenteLabel">Anul minim pentru aprobare</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="an_minim" class="form-label">An de referință</label>
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

    <?php if($an_minim!==null): ?>
        <?php if(!empty($medici_medicamente)): ?>
            <table class="table table-striped table-bordered mb-5">
                <thead>
                    <tr>
                        <th>Nume</th>
                        <th>Prenume</th>
                        <th>Specializarea</th>
                        <th>Spital</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($medici_medicamente as $medic): ?>
                        <tr>
                            <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                            <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                            <td><?= htmlspecialchars($medic['Specializarea']) ?></td>
                            <td><?= htmlspecialchars($medic['Spitalul']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class='alert alert-warning alert-dismissible fade show' role='alert'>
                Nu există niciun medic care să satisfacă cerința dată! 
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
    include "layout/footer.php";
?>