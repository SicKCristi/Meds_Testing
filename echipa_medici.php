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

    // Interogarea 2: Medicii care au participat la studii cu medicamente aprobate după anul 2019
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
        WHERE M.DataAprobarii>'2019-01-01'
    );
";

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
?>

<div class="container py-5">

    <!-- Tabelul 1 -->
    <h2>Lista medicilor și numărul de consultații pentru fiecare</h2>
    <table class="table table-striped table-bordered">
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
    <h2>Medicii care au participat la studii cu medicamente aprobate după anul 2019</h2>
    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Specializarea</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($medici_medicamente as $medic): ?>
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
    <table class="table table-striped table-bordered">
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

</div>

<?php
    include "layout/footer.php";
?>
