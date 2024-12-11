<?php

echo '<link rel="stylesheet" type="text/css" href="stilizare_butoane.css">';
echo '<script src="functionare_butoane.js"></script>';

function obtine_id_pacient($conexiune_bd) {
    $ID_Pacient=null;

    // Ne vom folosi de emailul și telefonul din sesiunea curentă
    $email=$_SESSION['Email'] ?? null;
    $telefon=$_SESSION['Telefon'] ?? null;

    if($email || $telefon){
        // Construim interogarea SQL în funcție de ce date sunt disponibile
        $query="SELECT ID_Pacient FROM pacienti WHERE ";
        $conditii=[];
        $parametri=[];
        $tipuri="";

        if($email){
            $conditii[]="Emailul = ?";
            $parametri[]=$email;
            $tipuri.="s";
        }

        if($telefon){
            $conditii[]="Telefonul = ?";
            $parametri[]=$telefon;
            $tipuri.="s";
        }

        // Acum putem să adăugăm condițiile în query
        $query.=implode(" AND ", $conditii); 
        $stmt=$conexiune_bd->prepare($query);
        $stmt->bind_param($tipuri, ...$parametri);

        $stmt->execute();
        $stmt->bind_result($ID_Pacient);
        $stmt->fetch();
        $stmt->close();
    }

    // Se va returna ID-ul dacă a fost găsit sau null, în cazul contrar
    return $ID_Pacient; 
}

function sterge_testare_selectata($conexiune_bd, $ID_Testare) {
    $stmt=$conexiune_bd->prepare("DELETE FROM testare_pacient WHERE ID_Testare = ?");
    $stmt->bind_param('i', $ID_Testare);

    if($stmt->execute()){
        echo "<div class='alert alert-success'>Testarea a fost ștearsă cu succes!</div>";
    } else{
        echo "<div class='alert alert-danger'>Eroare la ștergerea testării: " . htmlspecialchars($stmt->error) . "</div>";
    }

    $stmt->close();
}

function sterge_medicament_selectat($conexiune_bd, $ID_Medicament) {
    $ID_Pacient=obtine_id_pacient($conexiune_bd);

    $stmt=$conexiune_bd->prepare("DELETE FROM pacient_medicament WHERE ID_Medicament = ? AND ID_Pacient= ?");
    $stmt->bind_param('ii', $ID_Medicament, $ID_Pacient);

    if($stmt->execute()){
        echo "<div class='alert alert-success'>Medicamentul a fost șters cu succes!</div>";
    } else{
        echo "<div class='alert alert-danger'>Eroare la ștergerea medicamentului: " . htmlspecialchars($stmt->error) . "</div>";
    }

    $stmt->close();
}
function sterge_pacient_din_pacienti($conexiune_bd) {
    $ID_Pacient=obtine_id_pacient($conexiune_bd);

    if($ID_Pacient){
        // Ștergerea din tabela consultatie bazată pe ID_Testare asociat cu ID_Pacient
        $stmt=$conexiune_bd->prepare("
            DELETE FROM consultatie
            WHERE ID_Testare IN (
                SELECT ID_Testare 
                FROM testare_pacient 
                WHERE ID_Pacient = ?
            )");
        $stmt->bind_param('i', $ID_Pacient);
        if(!$stmt->execute()){
            echo "<div class='alert alert-danger'>Eroare la ștergerea consultațiilor: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();

        // Ștergerea din tabela pacient_medicament
        $stmt=$conexiune_bd->prepare("DELETE FROM pacient_medicament WHERE ID_Pacient = ?");
        $stmt->bind_param('i', $ID_Pacient);
        if(!$stmt->execute()){
            echo "<div class='alert alert-danger'>Eroare la ștergerea medicamentelor pacientului: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();

        // Ștergerea din tabela testare_pacient
        $stmt=$conexiune_bd->prepare("DELETE FROM testare_pacient WHERE ID_Pacient = ?");
        $stmt->bind_param('i', $ID_Pacient);
        if(!$stmt->execute()){
            echo "<div class='alert alert-danger'>Eroare la ștergerea testărilor pacientului: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();

        // Ștergerea din tabela pacienti
        $stmt=$conexiune_bd->prepare("DELETE FROM pacienti WHERE ID_Pacient = ?");
        $stmt->bind_param('i', $ID_Pacient);
        if($stmt->execute()){
            echo "<div class='alert alert-success'>Pacientul și toate datele asociate au fost șterse cu succes!</div>";
        } else{
            echo "<div class='alert alert-danger'>Eroare la ștergerea pacientului: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

function stergere_testare_pacient($conexiune_bd) {
    $ID_Pacient=obtine_id_pacient($conexiune_bd);

    if($ID_Pacient){
        $stmt=$conexiune_bd->prepare("SELECT ID_Testare FROM testare_pacient WHERE ID_Pacient = ?");
        $stmt->bind_param('i', $ID_Pacient);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){
            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalGeneral'>Șterge testare</button>";
            echo "<div class='modal fade' id='modalGeneral' tabindex='-1' aria-hidden='true'>";
            echo "<div class='modal-dialog'><div class='modal-content'>";
            echo "<div class='modal-header'><h5 class='modal-title'>Alege testarea de șters</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button></div>";
            echo "<div class='modal-body'><form method='POST'>";

            while($row=$result->fetch_assoc()){
                echo "<label class='radio-label'><input type='radio' name='ID_Testare' value='{$row['ID_Testare']}' class='radio-select'> Testare ID: {$row['ID_Testare']}</label><br>";
            }

            echo "<button type='submit' class='btn btn-danger btn-disabled mt-3' name='delete_testare_pacient' disabled>Șterge</button>";
            echo "</form></div></div></div></div>";
        } else{
            echo "<div class='alert alert-warning'>Nu există testări asociate acestui pacient!</div>";
        }
        $stmt->close();
    } else{
        echo "<div class='alert alert-danger'>Pacientul nu a fost găsit!</div>";
    }
}

function stergere_medicament_pacient($conexiune_bd) {
    $ID_Pacient=obtine_id_pacient($conexiune_bd);

    if($ID_Pacient){
        $stmt=$conexiune_bd->prepare("SELECT ID_Medicament FROM pacient_medicament WHERE ID_Pacient = ?");
        $stmt->bind_param('i', $ID_Pacient);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){
            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalGeneral'>Șterge medicament</button>";
            echo "<div class='modal fade' id='modalGeneral' tabindex='-1' aria-hidden='true'>";
            echo "<div class='modal-dialog'><div class='modal-content'>";
            echo "<div class='modal-header'><h5 class='modal-title'>Alege medicamentul de șters</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button></div>";
            echo "<div class='modal-body'><form method='POST'>";

            while($row=$result->fetch_assoc()){
                echo "<label class='radio-label'><input type='radio' name='ID_Medicament' value='{$row['ID_Medicament']}' class='radio-select'> Medicament ID: {$row['ID_Medicament']}</label><br>";
            }

            echo "<button type='submit' class='btn btn-danger btn-disabled mt-3' name='delete_medicament_pacient' disabled>Șterge</button>";
            echo "</form></div></div></div></div>";
        } else {
            echo "<div class='alert alert-warning'>Nu există medicamente asociate acestui pacient!</div>";
        }
            $stmt->close();
        } else{
            echo "<div class='alert alert-danger'>Pacientul nu a fost găsit!</div>";
    }
}
?>
