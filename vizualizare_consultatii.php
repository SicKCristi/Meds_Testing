<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'] ?? null;

    $alert_message=null;
    $alert_class="";

    // Interogarea 1, folosim tabelele utilizator, doctor și consultație
    $query_consultatii="
        SELECT 
            C.ID_Consultatie,
            C.DataConsultatie
        FROM utilizator AS U    JOIN doctor AS D ON U.Email=D.Emailul
                                JOIN consultatie AS C ON D.ID_Doctor=C.ID_Doctor
        WHERE U.Email=?";
    $stmt_consultatii=$conexiune_bd->prepare($query_consultatii);
    $stmt_consultatii->bind_param('s', $Email_utilizator);
    $stmt_consultatii->execute();
    $rezultat_consultatii=$stmt_consultatii->get_result();

    $consultatii=[];
    while($rand=$rezultat_consultatii->fetch_assoc()){
        $consultatii[$rand['ID_Consultatie']]=$rand;
    }
    $stmt_consultatii->close();

    // Interogarea 2, folosim tabelele consultație, testare_pacient, pacient pentru datele suplimentare
    $pacienti_consultatii=[];
    if(!empty($consultatii)){
        $ids_consultatii=implode(',', array_keys($consultatii));
        $query_pacienti="
            SELECT 
                C.ID_Consultatie,
                TP.DataInrolarii,
                TP.Statusul,
                P.Numele,
                P.Prenumele
            FROM consultatie AS C   JOIN testare_pacient AS TP ON C.ID_Testare=TP.ID_Testare
                                    JOIN pacienti AS P ON TP.ID_Pacient = P.ID_Pacient
            WHERE C.ID_Consultatie IN ($ids_consultatii)";
        $stmt_pacienti=$conexiune_bd->prepare($query_pacienti);
        $stmt_pacienti->execute();
        $rezultat_pacienti=$stmt_pacienti->get_result();

        while($rand=$rezultat_pacienti->fetch_assoc()){
            $pacienti_consultatii[$rand['ID_Consultatie']][]=$rand;
        }
        $stmt_pacienti->close();
    }

    // Funcția pentru ștergerea interogării specifice
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_consultatie'])){
        $ID_Consultatie=$_POST['ID_Consultatie'] ?? null;
        if($ID_Consultatie){
            $stmt_delete=$conexiune_bd->prepare("DELETE FROM consultatie WHERE ID_Consultatie = ?");
            $stmt_delete->bind_param('i', $ID_Consultatie);
            if($stmt_delete->execute()){
                $alert_message="Consultația a fost ștearsă cu succes!";
                $alert_class="alert-success";
                unset($consultatii[$ID_Consultatie]);
            } else{
                $alert_message="Eroare la ștergerea consultației!";
                $alert_class="alert-danger";
            }
            $stmt_delete->close();
        }
    }
?>

<div class="container py-5">
    <!-- Mesajul de alertă -->
    <?php if($alert_message): ?>
        <div class="alert <?= $alert_class ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($alert_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <h2>Consultațiile efectuate</h2>
    <?php if(!empty($consultatii)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Data Consultației</th>
                    <th>Data Înrolării</th>
                    <th>Statusul</th>
                    <th>Nume Pacient</th>
                    <th>Prenume Pacient</th>
                    <th>Acțiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($consultatii as $id_consultatie => $consultatie): ?>
                    <?php if(!empty($pacienti_consultatii[$id_consultatie])): ?>
                        <?php foreach($pacienti_consultatii[$id_consultatie] as $pacient): ?>
                            <tr>
                                <td><?= htmlspecialchars($consultatie['DataConsultatie']) ?></td>
                                <td><?= htmlspecialchars($pacient['DataInrolarii']) ?></td>
                                <td><?= htmlspecialchars($pacient['Statusul']) ?></td>
                                <td><?= htmlspecialchars($pacient['Numele']) ?></td>
                                <td><?= htmlspecialchars($pacient['Prenumele']) ?></td>
                                <td>
                                    <button 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDelete<?= $id_consultatie ?>">
                                        Șterge
                                    </button>

                                    <!-- Modal pentru ștergere -->
                                    <div 
                                        class="modal fade" 
                                        id="modalDelete<?= $id_consultatie ?>" 
                                        tabindex="-1" 
                                        aria-labelledby="modalLabel<?= $id_consultatie ?>" 
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel<?= $id_consultatie ?>">Confirmare</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Ești sigur că dorești să ștergi această consultație?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                    <form method="POST">
                                                        <input type="hidden" name="ID_Consultatie" value="<?= $id_consultatie ?>">
                                                        <button type="submit" name="delete_consultatie" class="btn btn-danger">Confirmă</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td><?= htmlspecialchars($consultatie['ID_Consultatie']) ?></td>
                            <td><?= htmlspecialchars($consultatie['DataConsultatie']) ?></td>
                            <td colspan="4" class="text-center">Nu există pacienți înregistrați</td>
                            <td>
                                <button 
                                    class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#modalDelete<?= $id_consultatie ?>">
                                    Șterge
                                </button>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h3 class="text-center mt-5">Nu există consultații efectuate!</h3>
    <?php endif; ?>

    <!-- Butonul care face legătura cu pagina vizualizare_studii.php -->
    <div class="mt-4">
        <a href="vizualizare_studii.php" class="btn btn-primary">Vizualizează Studiile</a>
    </div>
</div>

<?php 
    include "layout/footer.php";
?>
