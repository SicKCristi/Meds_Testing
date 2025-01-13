<?php
    include "layout/header.php";

    $conexiune_bd=getDatabaseConnection();

    // Interogarea 5: Procentajul studiilor pentru fiecare doctor față de numărul total de studii
    // Interogare complexă cu subcerere (#5)
    $query_procentaj_studii="
        SELECT 
            D.NumeDoctor,
            D.PrenumeDoctor,
            COUNT(SD.ID_Studiu) AS NumarStudii,
            ROUND((COUNT(SD.ID_Studiu)*100.0)/( SELECT COUNT(SD2.ID_Studiu)
                                                FROM studiu_doctor AS SD2
                                                ),2) AS ProcentajStudii
        FROM doctor AS D 
        JOIN studiu_doctor AS SD ON D.ID_Doctor=SD.ID_Doctor
        GROUP BY D.ID_Doctor, D.NumeDoctor, D.PrenumeDoctor
        ORDER BY ProcentajStudii DESC; ";

    $rezultat_procentaj=$conexiune_bd->query($query_procentaj_studii);
    $procentaj_studii=$rezultat_procentaj->fetch_all(MYSQLI_ASSOC);
?>

<div class="container py-5">
    <h2>Procentajul studiilor pentru fiecare doctor</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3">
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

    <table class="table table-striped table-bordered mb-5">
        <thead>
            <tr>
                <th>Nume</th>
                <th>Prenume</th>
                <th>Număr Studii</th>
                <th>Procentaj Studii(%)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($procentaj_studii as $medic): ?>
                <tr>
                    <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                    <td><?= htmlspecialchars($medic['NumarStudii']) ?></td>
                    <td><?= htmlspecialchars($medic['ProcentajStudii']) ?>%</td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
    include "layout/footer.php";
?>
