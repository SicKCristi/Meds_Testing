<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $studii_medici=[];
    $data_inceput=$_POST['data_inceput'] ?? null;

    if($data_inceput!==null){
        // Interogarea 4: Lista doctorilor care fac parte din studii după o dată dată
        // Interogare simplă cu două join-uri între tabele (#9)
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
        }
    }
?>

<div class="container py-5">
    <h2>Medicii care au participat la studii clinice după o anumită dată</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3">
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalStudiiMedici">Introduceți dată</button>

    <div class="modal fade" id="modalStudiiMedici" tabindex="-1" aria-labelledby="modalStudiiMediciLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalStudiiMediciLabel">Data minimă pentru studii</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="data_inceput" class="form-label">Prima dată</label>
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

    <?php if($data_inceput !== null): ?>
        <?php if(!empty($studii_medici)): ?>
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
                    <?php foreach($studii_medici as $studiu): ?>
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
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                Nu a fost găsit medic care să corespundă căutării date!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
    include "layout/footer.php";
?>
