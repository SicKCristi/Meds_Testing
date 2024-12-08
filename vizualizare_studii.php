<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'] ?? null;

    $alert_message=null;
    $alert_class="";

    // Funcția pentru ștergerea înregistrării specifice
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_studiu_doctor'])){
        $ID_Studiu=$_POST['ID_Studiu'] ?? null;
        $ID_Doctor=$_POST['ID_Doctor'] ?? null;

        if($ID_Studiu && $ID_Doctor){
            $stmt_delete=$conexiune_bd->prepare("DELETE FROM studiu_doctor WHERE ID_Studiu = ? AND ID_Doctor = ?");
            $stmt_delete->bind_param('ii', $ID_Studiu, $ID_Doctor);
            if($stmt_delete->execute()){
                $alert_message="Studiul a fost șters cu succes!";
                $alert_class="alert-success";
            } else{
                $alert_message="Eroare la ștergerea studiului!";
                $alert_class="alert-danger";
            }
            $stmt_delete->close();
        }
    }

    // Interogarea 1, folosim tabelele utilizator, doctor și studiu_doctor
    $query_roluri="
        SELECT 
            SD.RolDoctor,
            D.Specializarea
        FROM utilizator AS U    JOIN doctor AS D ON U.Email=D.Emailul
                                JOIN studiu_doctor AS SD ON D.ID_Doctor=SD.ID_Doctor
        WHERE U.Email=?";
    $stmt_roluri=$conexiune_bd->prepare($query_roluri);
    $stmt_roluri->bind_param('s', $Email_utilizator);
    $stmt_roluri->execute();
    $rezultat_roluri=$stmt_roluri->get_result();

    $roluri=[];
    while($rand=$rezultat_roluri->fetch_assoc()){
        $roluri[]=$rand;
    }
    $stmt_roluri->close();

    // Interogarea 2, folosim tabele studiu_doctor, studiu_clinic, medicamente și doctor
    $query_studii="
        SELECT 
            SD.ID_Studiu,
            SD.ID_Doctor,
            SD.RolDoctor,
            D.Specializarea,
            SC.DataInceput,
            SC.DataSfarsit,
            SC.Scopul,
            M.Denumirea AS Medicament
        FROM studiu_doctor AS SD    JOIN doctor AS D ON SD.ID_Doctor = D.ID_Doctor
                                    JOIN studiu_clinic AS SC ON SD.ID_Studiu = SC.ID_Studiu
                                    JOIN medicamente AS M ON SC.ID_Medicament = M.ID_Medicament
        WHERE D.Emailul=?";
    $stmt_studii=$conexiune_bd->prepare($query_studii);
    $stmt_studii->bind_param('s', $Email_utilizator);
    $stmt_studii->execute();
    $rezultat_studii=$stmt_studii->get_result();

    $studii=[];
    while($rand=$rezultat_studii->fetch_assoc()){
        $studii[]=$rand;
    }
    $stmt_studii->close();
?>

<div class="container py-5">
    <!-- Mesajul de alertă -->
    <?php if ($alert_message): ?>
        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alert_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2>Studiile la care participați</h2>
    <?php if (!empty($studii)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Rol Doctor</th>
                    <th>Specializarea</th>
                    <th>Data Început</th>
                    <th>Data Sfârșit</th>
                    <th>Scopul</th>
                    <th>Denumirea Medicamentului</th>
                    <th>Acțiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($studii as $studiu): ?>
                    <tr>
                        <td><?= htmlspecialchars($studiu['RolDoctor']) ?></td>
                        <td><?= htmlspecialchars($studiu['Specializarea']) ?></td>
                        <td><?= htmlspecialchars($studiu['DataInceput']) ?></td>
                        <td><?= htmlspecialchars($studiu['DataSfarsit']) ?></td>
                        <td><?= htmlspecialchars($studiu['Scopul']) ?></td>
                        <td><?= htmlspecialchars($studiu['Medicament']) ?></td>
                        <td>
                            <button 
                                class="btn btn-danger btn-sm" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalDelete<?= htmlspecialchars($studiu['ID_Studiu'] . '_' . $studiu['ID_Doctor']) ?>">
                                Șterge
                            </button>

                            <!-- Modal pentru ștergere -->
                            <div 
                                class="modal fade" 
                                id="modalDelete<?= htmlspecialchars($studiu['ID_Studiu'] . '_' . $studiu['ID_Doctor']) ?>" 
                                tabindex="-1" 
                                aria-labelledby="modalLabel<?= htmlspecialchars($studiu['ID_Studiu'] . '_' . $studiu['ID_Doctor']) ?>" 
                                aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalLabel<?= htmlspecialchars($studiu['ID_Studiu'] . '_' . $studiu['ID_Doctor']) ?>">Confirmare Ștergere</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            Ești sigur că vrei să ștergi acest studiu?
                                        </div>
                                        <div class="modal-footer">
                                            <form method="POST">
                                                <input type="hidden" name="ID_Studiu" value="<?= htmlspecialchars($studiu['ID_Studiu']) ?>">
                                                <input type="hidden" name="ID_Doctor" value="<?= htmlspecialchars($studiu['ID_Doctor']) ?>">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                <button type="submit" name="delete_studiu_doctor" class="btn btn-danger">Confirmă</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h3 class="text-center mt-5">Nu participați la niciun studiu!</h3>
    <?php endif; ?>
</div>

<?php 
    include "layout/footer.php"; 
?>
