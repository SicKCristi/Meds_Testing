<?php

echo '<link rel="stylesheet" type="text/css" href="stilizare_butoane.css">';
echo '<script src="functionare_butoane.js"></script>';

function obtine_id_doctor($conexiune_bd) {
    $ID_Doctor=null;

    // Ne vom folosi de emailul și telefonul din sesiunea curentă
    $email=$_SESSION['Email'] ?? null;
    $telefon=$_SESSION['Telefon'] ?? null;

    if($email || $telefon){
        // Construim interogarea SQL în funcție de ce date sunt disponibile
        $query="SELECT ID_Doctor FROM doctor WHERE ";
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
        $stmt->bind_result($ID_Doctor);
        $stmt->fetch();
        $stmt->close();
    }

    // Se va returna ID-ul dacă a fost găsit sau null, în cazul contrar
    return $ID_Doctor; 
}

function sterge_consultatie_selectata($conexiune_bd, $ID_Consultatie) {
    $stmt=$conexiune_bd->prepare("DELETE FROM consultatie WHERE ID_Consultatie = ?");
    $stmt->bind_param('i', $ID_Consultatie);

    if($stmt->execute()){
        echo "<div class='alert alert-success'>Consultația a fost ștearsă cu succes!</div>";
    } else{
        echo "<div class='alert alert-danger'>Eroare la ștergerea consultației: " . htmlspecialchars($stmt->error) . "</div>";
    }

    $stmt->close();
}

function sterge_studiu_selectat($conexiune_bd, $ID_Studiu) {
    $ID_Doctor=obtine_id_doctor($conexiune_bd); // Obținem ID_Doctor din sesiunea curentă

    if($ID_Doctor){
        // Se va șterge înregistrarea doar dacă ID_Studiu și ID_Doctor corespund valorilor date
        $stmt=$conexiune_bd->prepare("DELETE FROM studiu_doctor WHERE ID_Studiu = ? AND ID_Doctor = ?");
        $stmt->bind_param('ii', $ID_Studiu, $ID_Doctor);

        if($stmt->execute()){
            echo "<div class='alert alert-success'>Studiul a fost șters cu succes pentru acest doctor!</div>";
        } else{
            echo "<div class='alert alert-danger'>Eroare la ștergerea studiului: " . htmlspecialchars($stmt->error) . "</div>";
        }

        $stmt->close();
    } else{
        echo "<div class='alert alert-danger'>Doctorul nu a fost găsit!</div>";
    }
}

function sterge_doctor_din_doctor($conexiune_bd) {
    $ID_Doctor=obtine_id_doctor($conexiune_bd);

    if($ID_Doctor){
        // Ștergerea din tabela consultatie
        $stmt=$conexiune_bd->prepare("DELETE FROM consultatie WHERE ID_Doctor = ?");
        $stmt->bind_param('i', $ID_Doctor);
        $stmt->execute();
        $stmt->close();

        // Ștergerea din tabela studiu_doctor
        $stmt=$conexiune_bd->prepare("DELETE FROM studiu_doctor WHERE ID_Doctor = ?");
        $stmt->bind_param('i', $ID_Doctor);
        $stmt->execute();
        $stmt->close();

        // Ștergerea din tabela doctor
        $stmt=$conexiune_bd->prepare("DELETE FROM doctor WHERE ID_Doctor = ?");
        $stmt->bind_param('i', $ID_Doctor);
        if($stmt->execute()){
            echo "<div class='alert alert-success'>Doctorul și toate datele asociate au fost șterse cu succes!</div>";
        } else{
            echo "<div class='alert alert-danger'>Eroare la ștergerea doctorului: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    } else{
        echo "<div class='alert alert-danger'>Doctorul nu a fost găsit pe baza emailului și telefonului din sesiune!</div>";
    }
}

function stergere_doctor_studiu($conexiune_bd) {
    $ID_Doctor=obtine_id_doctor($conexiune_bd);

    if($ID_Doctor){
        $stmt=$conexiune_bd->prepare("SELECT ID_Studiu FROM studiu_doctor WHERE ID_Doctor = ?");
        $stmt->bind_param('i', $ID_Doctor);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows>0){
            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalGeneral'>Șterge studiu</button>";
            echo "<div class='modal fade' id='modalGeneral' tabindex='-1' aria-hidden='true'>";
            echo "<div class='modal-dialog'><div class='modal-content'>";
            echo "<div class='modal-header'><h5 class='modal-title'>Alege studiul de șters</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button></div>";
            echo "<div class='modal-body'><form method='POST'>";

            while($row=$result->fetch_assoc()){
                echo "<label class='radio-label'><input type='radio' name='ID_Studiu' value='{$row['ID_Studiu']}' class='radio-select'> Studiu ID: {$row['ID_Studiu']}</label><br>";
            }

            echo "<button type='submit' class='btn btn-danger btn-disabled mt-3' name='delete_studiu_doctor' disabled>Șterge</button>";
            echo "</form></div></div></div></div>";
        } else{
            echo "<div class='alert alert-warning'>Nu există studii asociate acestui doctor!</div>";
        }
        $stmt->close();
    } else{
        echo "<div class='alert alert-danger'>Doctorul nu a fost găsit!</div>";
    }
}

function stergere_consultatie($conexiune_bd) {
    $ID_Doctor=obtine_id_doctor($conexiune_bd);

    if($ID_Doctor){
        $stmt=$conexiune_bd->prepare("SELECT ID_Consultatie FROM consultatie WHERE ID_Doctor = ?");
        $stmt->bind_param('i', $ID_Doctor);
        $stmt->execute();
        $result=$stmt->get_result();

        if($result->num_rows>0){
            echo "<button type='button' class='btn btn-primary' data-bs-toggle='modal' data-bs-target='#modalGeneral'>Șterge consultație</button>";
            echo "<div class='modal fade' id='modalGeneral' tabindex='-1' aria-hidden='true'>";
            echo "<div class='modal-dialog'><div class='modal-content'>";
            echo "<div class='modal-header'><h5 class='modal-title'>Alege consultația de șters</h5>";
            echo "<button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button></div>";
            echo "<div class='modal-body'><form method='POST'>";

            while($row=$result->fetch_assoc()){
                echo "<label class='radio-label'><input type='radio' name='ID_Consultatie' value='{$row['ID_Consultatie']}' class='radio-select'> Consultație ID: {$row['ID_Consultatie']}</label><br>";
            }

            echo "<button type='submit' class='btn btn-danger btn-disabled mt-3' name='delete_consultatie' disabled>Șterge</button>";
            echo "</form></div></div></div></div>";
        } else{
            echo "<div class='alert alert-warning'>Nu există consultații asociate acestui doctor!</div>";
        }
        $stmt->close();
    } else{
        echo "<div class='alert alert-danger'>Doctorul nu a fost găsit!</div>";
    }
}
?>
