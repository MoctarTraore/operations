<?php
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['nature']) && isset($_GET['devise'])) {
    $nature_id = $_GET['nature'];
    $devise_code = $_GET['devise'];

    try {
        // Récupérer les opérations archivées avec la nature du produit
        $query = "
            SELECT o.id, o.donneur, o.objet, o.montant_devise, o.montant_cfa, o.devise_code, np.nom AS nature_nom, o.updated_at
            FROM operations o
            JOIN natures_produit np ON o.nature_id = np.id
            WHERE o.nature_id = :nature_id AND o.devise_code = :devise_code AND o.is_archived = 1
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nature_id', $nature_id, PDO::PARAM_INT);
        $stmt->bindParam(':devise_code', $devise_code, PDO::PARAM_STR);
        $stmt->execute();

        $operations = $stmt->fetchAll();
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo "Nature et devise non spécifiées.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Archives des Opérations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1>Archives des Opérations</h1>

        <a href="operations_list.php?nature=<?= htmlspecialchars($nature_id) ?>&devise=<?= htmlspecialchars($devise_code) ?>" class="btn btn-secondary mb-3">Retour</a>
        <a href="index.php" class="btn btn-primary mb-3">Accueil</a>

        <?php if (!empty($operations)): ?>
            <p><strong>Nature :</strong> <?= htmlspecialchars($operations[0]['nature_nom']) ?></p>
            <p><strong>Devise :</strong> <?= htmlspecialchars($devise_code) ?></p>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Client Donneur d'Ordre</th>
                    <th>Objet du Règlement</th>
                    <th>Devise</th>
                    <th>Montant (Devise)</th>
                    <th>Montant (CFA)</th>
                    <th>Date d'Archivage</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($operations)): ?>
                    <?php foreach ($operations as $operation): ?>
                        <tr>
                            <td><?= htmlspecialchars($operation['donneur']) ?></td>
                            <td><?= htmlspecialchars($operation['objet']) ?></td>
                            <td><?= htmlspecialchars($operation['devise_code']) ?></td>
                            <td><?= number_format($operation['montant_devise'], 2) ?></td>
                            <td><?= number_format($operation['montant_cfa'], 2) ?></td>
                            <td><?= htmlspecialchars($operation['updated_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">Aucune opération archivée.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="" class="btn btn-primary mb-3">Générer le Borderau</
</body>
</html>
