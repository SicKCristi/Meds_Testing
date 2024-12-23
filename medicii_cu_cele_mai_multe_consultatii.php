<?php
    include "layout/header.php";

    $conexiune_bd=getDatabaseConnection();

    // Interogare 6: Medicii care au efectuat cele mai multe consultații
    // Interogare complexă cu subcereri (#4)
    $query_consultatii="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            D.Specializarea,
            D.Spitalul
        FROM doctor AS D
        WHERE D.ID_Doctor IN (
                                SELECT C.ID_Doctor
                                FROM consultatie AS C
                                GROUP BY C.ID_Doctor
                                HAVING COUNT(C.ID_Consultatie) = (
                                                                    SELECT MAX(NumarConsultatii)
                                                                    FROM (
                                                                            SELECT COUNT(C2.ID_Consultatie) AS NumarConsultatii
                                                                            FROM consultatie AS C2
                                                                            GROUP BY C2.ID_Doctor
                                                                        ) AS Subquery));";

    $rezultat_consultatii=$conexiune_bd->query($query_consultatii);
    $medici_consultatii=$rezultat_consultatii->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-5">
    <h2>Medicii care au efectuat cele mai multe consultații</h2>

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
            <?php foreach($medici_consultatii as $medic): ?>
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

<?php
    include "layout/footer.php";
?>