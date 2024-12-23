<?php
    include "layout/header.php";

    $conexiune_bd=getDatabaseConnection();

    // Interogarea 1: Lista medicilor cu numărul de consultații
    // Interogare complexă cu subcerere (#1)
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
?>

<div class="container py-5">
    <h2>Lista medicilor și numărul de consultații pentru fiecare</h2>

    <!-- Butonul care face legătura cu pagina echipa_medici.php -->
    <div class="mt-4 mb-3" >
        <a href="echipa_medici.php" class="btn btn-primary">Înapoi pe pagina echipei de medici</a>
    </div>

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
</div>

<?php
    include "layout/footer.php";
?>
