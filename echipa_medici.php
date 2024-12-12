<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    // Determină sortarea
    $order_by=$_GET['order_by'] ?? 'asc';
    $sort_query=$order_by==='desc' 
    ? "ORDER BY NumeDoctor DESC, PrenumeDoctor DESC" 
    : "ORDER BY NumeDoctor ASC, PrenumeDoctor ASC";

    // Interogarea medicilor
    $query_medici="
        SELECT 
            NumeDoctor,
            PrenumeDoctor,
            Specializarea,
            Spitalul,
            Emailul,
            Telefonul
        FROM doctor
        $sort_query";
    $rezultat_medici=$conexiune_bd->query($query_medici);
?>

<div class="container py-5">

    <!-- Lista cu paginile pentru informații suplimentare -->
    <div class="dropdown mb-4">
        <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownInformatii" data-bs-toggle="dropdown" aria-expanded="false">
            Lista cu informații suplimentare
        </button>
        <ul class="dropdown-menu" aria-labelledby="dropdownInformatii">
            <li><a class="dropdown-item" href="numar_consultatii_per_medic.php">Numărul de consultații pentru fiecare medic</a></li>
            <li><a class="dropdown-item" href="medici_cu_studii_cu_medicamente_dupa_un_an_dat.php">Medici cu studii după un an dat</a></li>
            <li><a class="dropdown-item" href="medicii_cu_cele_mai_multe_studii.php">Medicii cu cele mai multe studii</a></li>
            <li><a class="dropdown-item" href="medici_la_studii_clinice_dupa_o_data_specificata.php">Medici la studii clinice după o dată</a></li>
            <li><a class="dropdown-item" href="medici_cu_studii_cu_medicamente_produse_de_un_anumit_producator.php">Medici la studii cu medicamente produse de un producător</a></li>
            <li><a class="dropdown-item" href="medicii_cu_cele_mai_multe_consultatii.php">Medicii cu cele mai multe consultații</a></li>
        </ul>
    </div>

    <h2>Tabelul cu medicii</h2>

    <div class="mb-3">
        <a href="?order_by=asc" class="btn btn-success <?= $order_by === 'asc' ? 'disabled' : '' ?>">Sortare alfabetică</a>
        <a href="?order_by=desc" class="btn btn-warning <?= $order_by === 'desc' ? 'disabled' : '' ?>">Sortare invers alfabetică</a>
    </div>

    <?php if($rezultat_medici && $rezultat_medici->num_rows>0): ?>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nume</th>
                    <th>Prenume</th>
                    <th>Specializarea</th>
                    <th>Spitalul</th>
                    <th>Email</th>
                    <th>Telefon</th>
                </tr>
            </thead>
            <tbody>
                <?php while($rand = $rezultat_medici->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($rand['NumeDoctor']) ?></td>
                        <td><?= htmlspecialchars($rand['PrenumeDoctor']) ?></td>
                        <td><?= htmlspecialchars($rand['Specializarea']) ?></td>
                        <td><?= htmlspecialchars($rand['Spitalul']) ?></td>
                        <td><?= htmlspecialchars($rand['Emailul']) ?></td>
                        <td><?= htmlspecialchars($rand['Telefonul']) ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-center fs-4">Nu există medici în baza de date.</p>
    <?php endif; ?>
</div>

<?php
    include "layout/footer.php";
?>
