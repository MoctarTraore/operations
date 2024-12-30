<?php
require_once __DIR__ . '/../config/db.php';

if (isset($_GET['id'])) {
    $operation_id = $_GET['id'];

    try {
        // Mettre à jour l'opération pour l'archiver
        $query = "
            UPDATE operations
            SET is_archived = 1, archived_at = NOW()
            WHERE id = :operation_id
        ";

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':operation_id', $operation_id, PDO::PARAM_INT);
        $stmt->execute();

        // Rediriger après archivage
        header("Location: operations_list.php?nature={$_GET['nature']}&devise={$_GET['devise']}");
        exit;
    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo "Opération non spécifiée.";
    exit;
}
?>
