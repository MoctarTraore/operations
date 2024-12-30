<?php
require_once __DIR__ . '/../config/db.php';


$date_debut = isset($_GET['date_debut']) ? $_GET['date_debut'] : date('Y-m-d');
$date_fin = isset($_GET['date_fin']) ? $_GET['date_fin'] : date('Y-m-d');

try {
    
    $query = "
        SELECT o.id, o.donneur, o.objet, o.montant_devise, o.montant_cfa, o.devise_code, np.nom AS nature_nom
        FROM operations o
        JOIN natures_produit np ON o.nature_id = np.id
        WHERE o.is_archived = 0
          AND DATE(o.created_at) BETWEEN :date_debut AND :date_fin
    ";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':date_debut', $date_debut, PDO::PARAM_STR);
    $stmt->bindParam(':date_fin', $date_fin, PDO::PARAM_STR);
    $stmt->execute();

    $operations = $stmt->fetchAll();

    
    $devisesQuery = "SELECT * FROM devises";
    $devisesStmt = $pdo->query($devisesQuery);
    $devises = $devisesStmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Opérations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Liste des Opérations</h1>

        <form method="GET" class="mb-4">
            <div class="row">
                <div class="col-md-5">
                    <label for="date_debut" class="form-label">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" class="form-control" value="<?= htmlspecialchars($date_debut) ?>">
                </div>
                <div class="col-md-5">
                    <label for="date_fin" class="form-label">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="form-control" value="<?= htmlspecialchars($date_fin) ?>">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrer</button>
                </div>
            </div>
        </form>

        <div class="mb-3">
            <label for="devise_code" class="form-label">Devise</label>
            <select name="devise_code" id="devise_code" class="form-select" required>
                <option value="">-- Sélectionnez --</option>
                <?php foreach ($devises as $devise): ?>
                    <option value="<?= htmlspecialchars($devise['code']) ?>"> <?= htmlspecialchars($devise['code']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <button class="btn btn-success mt-3" onclick="generateBorderau()">Générer le Bordereau</button>
        <button class="btn btn-primary mt-3" onclick="generateCC139()">Générer le cc139</button>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th><input type="checkbox" onclick="toggleSelectAll(this)"></th>
                    <th>Nature</th>
                    <th>Client Donneur d'Ordre</th>
                    <th>Objet du Règlement</th>
                    <th>Devise</th>
                    <th>Montant (Devise)</th>
                    <th>Montant (CFA)</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($operations)): ?>
                    <?php foreach ($operations as $operation): ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="operation-checkbox" value="<?= htmlspecialchars($operation['id']) ?>">
                            </td>
                            <td><?= htmlspecialchars($operation['nature_nom']) ?></td>
                            <td><?= htmlspecialchars($operation['donneur']) ?></td>
                            <td><?= htmlspecialchars($operation['objet']) ?></td>
                            <td><?= htmlspecialchars($operation['devise_code']) ?></td>
                            <td><?= number_format($operation['montant_devise'], 2) ?></td>
                            <td><?= number_format($operation['montant_cfa'], 2) ?></td>
                            <td>
                                <button class="btn btn-danger btn-sm"
                                    onclick="confirmArchive(<?= htmlspecialchars(json_encode(['id' => $operation['id'], 'donneur' => $operation['donneur']])) ?>)">
                                    <i class="bi bi-archive"></i> Archiver
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Aucune opération trouvée pour la période sélectionnée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmArchive(operation) {
            const confirmed = confirm(`Voulez-vous vraiment archiver l'opération avec le donneur "${operation.donneur}" ? Cette action est irréversible.`);
            if (confirmed) {
                window.location.href = `archive_operation.php?id=${operation.id}`;
            }
        }

        function toggleSelectAll(source) {
            const checkboxes = document.querySelectorAll('.operation-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = source.checked);
        }

        function generateBorderau() {
            const selectedOperations = Array.from(document.querySelectorAll('.operation-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedOperations.length === 0) {
                alert('Veuillez sélectionner au moins une nature de produit.');
                return;
            }

            const deviseCode = document.getElementById('devise_code').value;

            if (!deviseCode) {
                alert('Veuillez sélectionner une devise.');
                return;
            }

            const url = `generer_borderaux.php?operations=${selectedOperations.join(',')}&devise_code=${deviseCode}`;
            const printWindow = window.open(url, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }

        function generateCC139() {
            const selectedOperations = Array.from(document.querySelectorAll('.operation-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedOperations.length === 0) {
                alert('Veuillez sélectionner au moins une nature de produit.');
                return;
            }

            const url = `generer_cc139.php?operations=${selectedOperations.join(',')}`;
            const printWindow = window.open(url, '_blank');
            printWindow.onload = function() {
                printWindow.print();
            };
        }
    </script>
</body>
</html>
