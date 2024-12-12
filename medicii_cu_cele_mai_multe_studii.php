<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    // Interogarea 3: Medicii care au participat la cele mai multe studii
    // Interogare complexă cu mai multe subcereri (#3)
    $query3="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea,
            D.Spitalul
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
?>

<div class="container py-5">
    <h2>Medicii care au participat la cele mai multe studii</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3">
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
                <th>Spitalul</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($medici_studii as $medic): ?>
                <tr>
                    <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['Specializarea']) ?></td>
                    <td><?= htmlspecialchars($medic['Spitalul']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
