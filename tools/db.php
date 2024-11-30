<?php

function getDatabaseConnection(){
    $nume_server="localhost";
    $nume_utilizator="root";
    $parola="cristi2003";
    $baza_de_date="evidenta_testare";

    // Creaza conexiunea
    $conexiune= new mysqli($nume_server,$nume_utilizator,$parola,$baza_de_date);
    if($conexiune->connect_error){
        die("Eroare la conectarea cu baza de data MySQL:" . $conexiune->connect_error);
    }

    return $conexiune;
}

?>