<?php
session_start();
require_once __DIR__ . '/../config/db.php';

if (!isset($_GET['operations']) || empty($_GET['operations'])) {
    die('Aucune opération sélectionnée.');
}

$operationIds = explode(',', $_GET['operations']);
$bordereaux = [];

try {
    $query = "
        SELECT o.id, o.donneur, o.objet, o.montant_devise, o.montant_cfa, o.devise_code, np.nom AS nature_nom
        FROM operations o
        JOIN natures_produit np ON o.nature_id = np.id
        WHERE o.id IN (" . implode(',', array_fill(0, count($operationIds), '?')) . ")
    ";

    $stmt = $pdo->prepare($query);
    foreach ($operationIds as $index => $id) {
        $stmt->bindValue($index + 1, $id, PDO::PARAM_INT);
    }
    $stmt->execute();

    $bordereaux = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}

$_SESSION['bordereaux'] = $bordereaux;
header('Location: cc139.php');
exit;
?>