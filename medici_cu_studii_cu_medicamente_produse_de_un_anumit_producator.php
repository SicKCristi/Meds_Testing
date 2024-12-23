<?php
    include "layout/header.php";

    $conexiune_bd=getDatabaseConnection();

    $producator=$_POST['producator'] ?? null;
    $medicamente_medici=[];

    if($producator!==null){
        // Interogarea 5: Medicii care au participat la studii cu medicamente produse de un anumit producător
        // Interogare simplă cu 3 join-uri (#10)
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
            WHERE M.Producatorul LIKE ?;";

        $stmt=$conexiune_bd->prepare($query5);
        $param="%$producator%";
        $stmt->bind_param('s', $param);
        $stmt->execute();
        $rezultat5=$stmt->get_result();
        $medicamente_medici=$rezultat5->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
?>

<div class="container py-5">
    <h2>Medicii care au participat la studii cu medicamente de un anumit producător</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3">
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalMedicamenteMedici">Introduceți producătorul</button>

    <div class="modal fade" id="modalMedicamenteMedici" tabindex="-1" aria-labelledby="modalMedicamenteMediciLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalMedicamenteMediciLabel">Introduceți producătorul medicamentului</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="producator" class="form-label">Producător</label>
                            <input type="text" class="form-control" name="producator" id="producator" value="<?= htmlspecialchars($producator ?? '') ?>" required>
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

    <?php if($producator===null): ?>
        <!-- Nu afișăm nimic dacă nu s-a introdus input -->
    <?php elseif(empty($medicamente_medici)): ?>
        <!-- Mesaj în cazul în care nu există rezultate -->
        <div class='alert alert-warning alert-dismissible fade show' role='alert'>
            Nu există niciun medic care să satisfacă cerința dată!
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
    <?php else: ?>
        <!-- Tabelul cu rezultate -->
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
                <?php foreach($medicamente_medici as $medicament): ?>
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
    <?php endif; ?>
</div>

<?php
    include "layout/footer.php";
?>