<?php
    include "layout/header.php";

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

    $conditie_filtrare=[];

    if(!empty($_GET['filtru_nume'])){
        $filtru_nume=$conexiune_bd->real_escape_string($_GET['filtru_nume']);
        $conditie_filtrare[]="NumeDoctor LIKE '%$filtru_nume%'";
    }

    if(!empty($_GET['filtru_prenume'])){
        $filtru_prenume=$conexiune_bd->real_escape_string($_GET['filtru_prenume']);
        $conditie_filtrare[]="PrenumeDoctor LIKE '%$filtru_prenume%'";
    }

    if(!empty($_GET['filtru_specializare'])){
        $filtru_specializare=$conexiune_bd->real_escape_string($_GET['filtru_specializare']);
        $conditie_filtrare[]="Specializarea LIKE '%$filtru_specializare%'";
    }

    if(!empty($_GET['filtru_spital'])){
        $filtru_spital=$conexiune_bd->real_escape_string($_GET['filtru_spital']);
        $conditie_filtrare[]="Spitalul LIKE '%$filtru_spital%'";
    }

    $where_query=!empty($conditie_filtrare) ? "WHERE " . implode(" AND ", $conditie_filtrare) : "";

    $query_medici="
        SELECT 
            NumeDoctor,
            PrenumeDoctor,
            Specializarea,
            Spitalul,
            Emailul,
            Telefonul
        FROM doctor
        $where_query
        $sort_query";
    $rezultat_medici=$conexiune_bd->query($query_medici);
?>

<div class="container py-5">
    <!-- Sortare și filtrare -->
    <div class="mb-4">
        <div class="d-inline-block">
            <span class="fw-bold me-2">Filtrează după</span>
            <button class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                Filtrează după
            </button>
            <ul class="dropdown-menu">
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filtruNumeModal">Nume</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filtruPrenumeModal">Prenume</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filtruSpecializareModal">Specializare</button></li>
                <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#filtruSpitalModal">Spital</button></li>
            </ul>
        </div>
    </div>

    <div class="mb-4">
        <!-- Formular pentru sortare -->
        <form method="GET" class="d-inline-block">
            <label for="sort_by" class="form-label fw-bold">Sortează după:</label>
            <select name="sort_by" id="sort_by" class="form-select d-inline-block w-auto ms-2">
                <option value="nume_asc" <?= $sort_by === 'nume_asc' ? 'selected' : '' ?>>Nume (A-Z)</option>
                <option value="nume_desc" <?= $sort_by === 'nume_desc' ? 'selected' : '' ?>>Nume (Z-A)</option>
                <option value="specializare_asc" <?= $sort_by === 'specializare_asc' ? 'selected' : '' ?>>Specializare (A-Z)</option>
                <option value="specializare_desc" <?= $sort_by === 'specializare_desc' ? 'selected' : '' ?>>Specializare (Z-A)</option>
                <option value="spital_asc" <?= $sort_by === 'spital_asc' ? 'selected' : '' ?>>Spital (A-Z)</option>
                <option value="spital_desc" <?= $sort_by === 'spital_desc' ? 'selected' : '' ?>>Spital (Z-A)</option>
            </select>

            <!-- Menținerea filtrelor curente -->
            <?php if (!empty($_GET['filtru_nume'])): ?>
                <input type="hidden" name="filtru_nume" value="<?= htmlspecialchars($_GET['filtru_nume']) ?>">
            <?php endif; ?>
            <?php if (!empty($_GET['filtru_prenume'])): ?>
                <input type="hidden" name="filtru_prenume" value="<?= htmlspecialchars($_GET['filtru_prenume']) ?>">
            <?php endif; ?>
            <?php if (!empty($_GET['filtru_specializare'])): ?>
                <input type="hidden" name="filtru_specializare" value="<?= htmlspecialchars($_GET['filtru_specializare']) ?>">
            <?php endif; ?>
            <?php if (!empty($_GET['filtru_spital'])): ?>
                <input type="hidden" name="filtru_spital" value="<?= htmlspecialchars($_GET['filtru_spital']) ?>">
            <?php endif; ?>

            <button type="submit" class="btn btn-primary ms-2">Aplică</button>
        </form>

        <!-- Buton pentru resetarea efectelor filtrelor anteriore -->
        <?php if (!empty($conditie_filtrare)): ?>
            <form method="GET" class="d-inline-block">
                <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                <button type="submit" class="btn btn-warning ms-3">Resetează filtrul</button>
            </form>
        <?php endif; ?>
    </div>

    <h2>Tabelul cu medicii</h2>
    <?php if($rezultat_medici && $rezultat_medici->num_rows > 0): ?>
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

<!-- Modalul pentru nume -->
<div class="modal fade" id="filtruNumeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrare după nume</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="filtru_nume" placeholder="Introduceți numele">
                    
                    <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                    
                    <?php if(!empty($_GET['filtru_prenume'])): ?>
                        <input type="hidden" name="filtru_prenume" value="<?= htmlspecialchars($_GET['filtru_prenume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_specializare'])): ?>
                        <input type="hidden" name="filtru_specializare" value="<?= htmlspecialchars($_GET['filtru_specializare']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_spital'])): ?>
                        <input type="hidden" name="filtru_spital" value="<?= htmlspecialchars($_GET['filtru_spital']) ?>">
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modalul pentru prenume -->
<div class="modal fade" id="filtruPrenumeModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrare după prenume</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="filtru_prenume" placeholder="Introduceți prenumele">
                    
                    <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                    
                    <?php if(!empty($_GET['filtru_nume'])): ?>
                        <input type="hidden" name="filtru_nume" value="<?= htmlspecialchars($_GET['filtru_nume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_specializare'])): ?>
                        <input type="hidden" name="filtru_specializare" value="<?= htmlspecialchars($_GET['filtru_specializare']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_spital'])): ?>
                        <input type="hidden" name="filtru_spital" value="<?= htmlspecialchars($_GET['filtru_spital']) ?>">
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modalul pentru specializare -->
<div class="modal fade" id="filtruSpecializareModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrare după specializare</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="filtru_specializare" placeholder="Introduceți specializare">
                    
                    <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                    
                    <?php if(!empty($_GET['filtru_nume'])): ?>
                        <input type="hidden" name="filtru_nume" value="<?= htmlspecialchars($_GET['filtru_nume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_prenume'])): ?>
                        <input type="hidden" name="filtru_prenume" value="<?= htmlspecialchars($_GET['filtru_prenume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_spital'])): ?>
                        <input type="hidden" name="filtru_spital" value="<?= htmlspecialchars($_GET['filtru_spital']) ?>">
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modalul pentru spital -->
<div class="modal fade" id="filtruSpitalModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="GET">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrare după spital</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="text" class="form-control" name="filtru_spital" placeholder="Introduceți spitalul">
                    
                    <input type="hidden" name="sort_by" value="<?= htmlspecialchars($sort_by) ?>">
                    
                    <?php if(!empty($_GET['filtru_nume'])): ?>
                        <input type="hidden" name="filtru_nume" value="<?= htmlspecialchars($_GET['filtru_nume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_prenume'])): ?>
                        <input type="hidden" name="filtru_prenume" value="<?= htmlspecialchars($_GET['filtru_prenume']) ?>">
                    <?php endif; ?>
                    <?php if(!empty($_GET['filtru_specializare'])): ?>
                        <input type="hidden" name="filtru_specializare" value="<?= htmlspecialchars($_GET['filtru_specializare']) ?>">
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Anulează</button>
                    <button type="submit" class="btn btn-primary">Filtrează</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php
    include "layout/footer.php";
?>