<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'] ?? null;

    $alert_message=null;
    $alert_class='';

    // Funcția pentru ștergerea înregistrării specificate
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_testare'])){
        $ID_Testare=$_POST['ID_Testare'] ?? null;

        if($ID_Testare){
            $stmt_delete=$conexiune_bd->prepare("DELETE FROM testare_pacient WHERE ID_Testare = ?");
            $stmt_delete->bind_param('i', $ID_Testare);
            if($stmt_delete->execute()){
                $alert_message="Înregistrarea cu ID $ID_Testare a fost ștearsă cu succes!";
                $alert_class="alert-success";
            } else{
                $alert_message="Eroare la ștergere!";
                $alert_class="alert-danger";
            }
            $stmt_delete->close();
        }
    }

    // Interogarea 1, între tabelele utilizator, pacient și testare_pacient
    $query_testari="
        SELECT 
            TP.ID_Testare,
            TP.DataInrolarii,
            TP.Statusul,
            SC.Scopul,
            SC.FazaStudiului,
            SC.DataInceput,
            SC.DataSfarsit
        FROM utilizator AS U    JOIN pacienti AS P ON U.Email=P.Emailul
                                JOIN testare_pacient AS TP ON P.ID_Pacient=TP.ID_Pacient
                                JOIN studiu_clinic AS SC ON TP.ID_Studiu=SC.ID_Studiu
        WHERE U.Email=?";

    $stmt_testari=$conexiune_bd->prepare($query_testari);
    $stmt_testari->bind_param('s', $Email_utilizator);
    $stmt_testari->execute();
    $rezultat_testari=$stmt_testari->get_result();

    $testari=[];
    while($rand=$rezultat_testari->fetch_assoc()){
        $testari[$rand['ID_Testare']]=$rand;
    }
    $stmt_testari->close();

    // Interogarea 2, folosind tabelele testare_pacient, studiu_clinic, doctor și doctor_studiu
    $query_medici="
        SELECT 
            TP.ID_Testare,
            D.NumeDoctor,
            D.PrenumeDoctor,
            SD.RolDoctor
        FROM testare_pacient AS TP  JOIN studiu_clinic AS SC ON TP.ID_Studiu=SC.ID_Studiu
                                    JOIN studiu_doctor AS SD ON SC.ID_Studiu=SD.ID_Studiu
                                    JOIN doctor AS D ON SD.ID_Doctor=D.ID_Doctor
        WHERE TP.ID_Testare IN (" . implode(',', array_keys($testari)) . ")";
    $stmt_medici=$conexiune_bd->prepare($query_medici);
    $stmt_medici->execute();
    $rezultat_medici=$stmt_medici->get_result();

    $medici_testari=[];
    while($rand=$rezultat_medici->fetch_assoc()){
        $medici_testari[$rand['ID_Testare']][]=$rand;
    }
    $stmt_medici->close();
?>

<div class="container py-5">
    <!-- Mesajul de alertă -->
    <?php if($alert_message): ?>
        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alert_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2>Testările la care dumneavoastră participați</h2>
    <?php if(!empty($testari)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Testare</th>
                    <th>Data Înrolării</th>
                    <th>Statusul</th>
                    <th>Scopul Studiului</th>
                    <th>Faza Studiului</th>
                    <th>Data Început</th>
                    <th>Data Sfârșit</th>
                    <th>Nume Doctor</th>
                    <th>Prenume Doctor</th>
                    <th>Rolul Doctorului</th>
                    <th>Acțiuni</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($testari as $id_testare => $testare): ?>
                    <?php if(!empty($medici_testari[$id_testare])): ?>
                        <?php foreach($medici_testari[$id_testare] as $medic): ?>
                            <tr>
                                <td><?= htmlspecialchars($testare['ID_Testare']) ?></td>
                                <td><?= htmlspecialchars($testare['DataInrolarii']) ?></td>
                                <td><?= htmlspecialchars($testare['Statusul']) ?></td>
                                <td><?= htmlspecialchars($testare['Scopul']) ?></td>
                                <td><?= htmlspecialchars($testare['FazaStudiului']) ?></td>
                                <td><?= htmlspecialchars($testare['DataInceput']) ?></td>
                                <td><?= htmlspecialchars($testare['DataSfarsit']) ?></td>
                                <td><?= htmlspecialchars($medic['NumeDoctor']) ?></td>
                                <td><?= htmlspecialchars($medic['PrenumeDoctor']) ?></td>
                                <td><?= htmlspecialchars($medic['RolDoctor']) ?></td>
                                <td>
                                    <button 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDelete<?= $testare['ID_Testare'] ?>">
                                        Renunță
                                    </button>
                                    <div 
                                        class="modal fade" 
                                        id="modalDelete<?= $testare['ID_Testare'] ?>" 
                                        tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Ești sigur?</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Renunți la testarea cu ID <?= htmlspecialchars($testare['ID_Testare']) ?>?
                                                </div>
                                                <div class="modal-footer">
                                                    <form method="POST">
                                                        <input type="hidden" name="ID_Testare" value="<?= $testare['ID_Testare'] ?>">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                        <button type="submit" name="delete_testare" class="btn btn-danger">Confirmă</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h3 class="text-center mt-5">Nu participi la nicio testare!</h3>
    <?php endif; ?>

    <!-- Butonul care face legătura cu pagina vizualizare_medicamente.php -->
    <div class="mt-4">
        <a href="vizualizare_medicamente.php" class="btn btn-primary">Vizualizează Testările</a>
    </div>
</div>

<?php 
    include "layout/footer.php"; 
?>
