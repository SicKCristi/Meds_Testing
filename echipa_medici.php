<?php
    include "layout/header.php";
    include "tools/db.php";

    $conexiune_bd=getDatabaseConnection();

    // Se va selecta modalitatea de sortare a tabelei de medici
    $sort_by=$_GET['sort_by'] ?? 'nume_asc';
    switch($sort_by){
        case 'nume_asc':
            $sort_query="ORDER BY NumeDoctor ASC, PrenumeDoctor ASC";
            break;
        case 'nume_desc':
            $sort_query="ORDER BY NumeDoctor DESC, PrenumeDoctor DESC";
            break;
        case 'specializare_asc':
            $sort_query="ORDER BY Specializarea ASC";
            break;
        case 'specializare_desc':
            $sort_query="ORDER BY Specializarea DESC";
            break;
        case 'spital_asc':
            $sort_query="ORDER BY Spitalul ASC";
            break;
        case 'spital_desc':
            $sort_query="ORDER BY Spitalul DESC";
            break;
        default:
            $sort_query="ORDER BY NumeDoctor ASC, PrenumeDoctor ASC";
            break;
    }

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

    <!-- Dropdown pentru informații suplimentare -->
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

    <div class="mb-4">
        <form method="GET" class="d-inline-block">
            <label for="sort_by" class="form-label fw-bold">Sortare după:</label>
            <select name="sort_by" id="sort_by" class="form-select d-inline-block w-auto ms-2">
                <option value="nume_asc" <?= $sort_by === 'nume_asc' ? 'selected' : '' ?>>Numele medicului (A-Z)</option>
                <option value="nume_desc" <?= $sort_by === 'nume_desc' ? 'selected' : '' ?>>Nume medicului (Z-A)</option>
                <option value="specializare_asc" <?= $sort_by === 'specializare_asc' ? 'selected' : '' ?>>Denumirea specializării (A-Z)</option>
                <option value="specializare_desc" <?= $sort_by === 'specializare_desc' ? 'selected' : '' ?>>Denumirea specializării (Z-A)</option>
                <option value="spital_asc" <?= $sort_by === 'spital_asc' ? 'selected' : '' ?>>Numele spitalului (A-Z)</option>
                <option value="spital_desc" <?= $sort_by === 'spital_desc' ? 'selected' : '' ?>>Numele spitalui (Z-A)</option>
            </select>
            <button type="submit" class="btn btn-primary ms-2">Aplică</button>
        </form>
    </div>

    <h2>Tabelul cu medicii</h2>
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
                <?php while($rand=$rezultat_medici->fetch_assoc()): ?>
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
