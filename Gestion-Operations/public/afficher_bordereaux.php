<?php
require_once __DIR__ . '/../config/db.php';

try {
    // Requête pour récupérer les données des bordereaux
    $query = "
        SELECT b.*, np.nom AS nature_nom
        FROM bordereaux b
        JOIN natures_produit np ON b.nature_id = np.id
        ORDER BY b.created_at DESC
    ";

    $stmt = $pdo->prepare($query);
    $stmt->execute();

    // Récupérer les données
    $bordereaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
