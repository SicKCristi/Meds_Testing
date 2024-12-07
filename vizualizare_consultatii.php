<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();
    $Email_utilizator=$_SESSION['Email'] ?? null;

    // Interogarea 1: Datele despre consultațiile efectuate de un doctor
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

    // Interogarea 2: Datele asociate cu pacienții dintr-o consultație
    if(!empty($consultatii)){
        $ids_consultatii=implode(',', array_keys($consultatii));
        $query_pacienti="
            SELECT 
                C.ID_Consultatie,
                TP.DataInrolarii,
                TP.Statusul,
                P.Numele,
                P.Prenumele
            FROM consultatie AS C
                JOIN testare_pacient AS TP ON C.ID_Testare=TP.ID_Testare
                JOIN pacienti AS P ON TP.ID_Pacient=P.ID_Pacient
            WHERE C.ID_Consultatie IN ($ids_consultatii)";
        $stmt_pacienti=$conexiune_bd->prepare($query_pacienti);
        $stmt_pacienti->execute();
        $rezultat_pacienti=$stmt_pacienti->get_result();
    
        $pacienti_consultatii=[];
        while($rand=$rezultat_pacienti->fetch_assoc()){
            $pacienti_consultatii[$rand['ID_Consultatie']][]=$rand;
        }
        $stmt_pacienti->close();
    } else{
        $pacienti_consultatii=[];
    }
?>

<div class="container py-5">
    <h2>Consultațiile efectuate</h2>

    <?php if (!empty($consultatii)): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ID Consultație</th>
                    <th>Data Consultației</th>
                    <th>Data Înrolării</th>
                    <th>Statusul</th>
                    <th>Nume Pacient</th>
                    <th>Prenume Pacient</th>
                    <th>Acțiune</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultatii as $id_consultatie => $consultatie): ?>
                    <?php if (!empty($pacienti_consultatii[$id_consultatie])): ?>
                        <?php foreach ($pacienti_consultatii[$id_consultatie] as $pacient): ?>
                            <tr>
                                <td><?= htmlspecialchars($consultatie['ID_Consultatie']) ?></td>
                                <td><?= htmlspecialchars($consultatie['DataConsultatie']) ?></td>
                                <td><?= htmlspecialchars($pacient['DataInrolarii']) ?></td>
                                <td><?= htmlspecialchars($pacient['Statusul']) ?></td>
                                <td><?= htmlspecialchars($pacient['Numele']) ?></td>
                                <td><?= htmlspecialchars($pacient['Prenumele']) ?></td>
                                <td>
                                    <button 
                                        class="btn btn-danger btn-sm" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalDelete<?= $consultatie['ID_Consultatie'] ?>">
                                        Șterge
                                    </button>

                                    <!-- Modal pentru ștergere -->
                                    <div 
                                        class="modal fade" 
                                        id="modalDelete<?= $consultatie['ID_Consultatie'] ?>" 
                                        tabindex="-1" 
                                        aria-labelledby="modalLabel<?= $consultatie['ID_Consultatie'] ?>" 
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="modalLabel<?= $consultatie['ID_Consultatie'] ?>">Confirmare Ștergere</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Ești sigur că vrei să ștergi consultația cu ID-ul <strong><?= $consultatie['ID_Consultatie'] ?></strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                    <form method="POST">
                                                        <input type="hidden" name="ID_Consultatie" value="<?= $consultatie['ID_Consultatie'] ?>">
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
                                    data-bs-target="#modalDelete<?= $consultatie['ID_Consultatie'] ?>">
                                    Șterge
                                </button>

                                <!-- Modal pentru ștergere -->
                                <div 
                                    class="modal fade" 
                                    id="modalDelete<?= $consultatie['ID_Consultatie'] ?>" 
                                    tabindex="-1" 
                                    aria-labelledby="modalLabel<?= $consultatie['ID_Consultatie'] ?>" 
                                    aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="modalLabel<?= $consultatie['ID_Consultatie'] ?>">Confirmare Ștergere</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Ești sigur că vrei să ștergi consultația cu ID-ul <strong><?= $consultatie['ID_Consultatie'] ?></strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                                                <form method="POST">
                                                    <input type="hidden" name="ID_Consultatie" value="<?= $consultatie['ID_Consultatie'] ?>">
                                                    <button type="submit" name="delete_consultatie" class="btn btn-danger">Confirmă</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <h3 class="text-center mt-5">Nu există consultații efectuate!</h3>
    <?php endif; ?>
</div>

<?php
    // Funcția pentru ștergerea interogării specifice
    if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['delete_consultatie'])){
        $ID_Consultatie=$_POST['ID_Consultatie'] ?? null;
        $message='';
        if($ID_Consultatie){
            $stmt_delete=$conexiune_bd->prepare("DELETE FROM consultatie WHERE ID_Consultatie = ?");
            $stmt_delete->bind_param('i', $ID_Consultatie);
            if($stmt_delete->execute()){
                $message='Consultația a fost ștearsă cu succes!';
            } else{
                $message='Eroare la ștergerea consultației!';
            }
            $stmt_delete->close();
        }
    }
?>

<?php 
    if(!empty($message)): ?>
    <div class="alert alert-info" role="alert">
        <?= htmlspecialchars($message) ?>
    </div>
<?php 
    endif;
?>

<?php
    include "layout/footer.php"
?>