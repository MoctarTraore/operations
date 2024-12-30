<?php
require_once __DIR__ . '/../config/db.php';
session_start(); // Démarre la session

if (isset($_GET['operations']) && isset($_GET['devise_code'])) {
    $operationIds = explode(',', $_GET['operations']);
    $deviseCode = htmlspecialchars($_GET['devise_code']);

    try {
        // Récupérer toutes les natures disponibles
        $naturesQuery = "SELECT id, nom FROM natures_produit";
        $naturesStmt = $pdo->prepare($naturesQuery);
        $naturesStmt->execute();
        $allNatures = $naturesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Requête pour regrouper les opérations par nature
        $query = "
        SELECT o.nature_id, np.nom AS nature_nom, SUM(o.montant_devise) AS total_montant_devise, 
               SUM(o.montant_cfa) AS total_montant_cfa, o.devise_code
        FROM operations o
        JOIN natures_produit np ON o.nature_id = np.id
        WHERE o.id IN (" . implode(',', array_fill(0, count($operationIds), '?')) . ") 
          AND o.devise_code = ?
        GROUP BY o.nature_id, o.devise_code
    ";

        $stmt = $pdo->prepare($query);
        $stmt->execute([...$operationIds, $deviseCode]);

        $bordereaux = $stmt->fetchAll();

        
        foreach ($allNatures as $nature) {
            $found = false;
            foreach ($bordereaux as $bordereau) {
                if ($bordereau['nature_id'] == $nature['id']) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                
                $bordereaux[] = [
                    'nature_id' => $nature['id'],
                    'nature_nom' => $nature['nom'],
                    'total_montant_devise' => 0,
                    'total_montant_cfa' => 0,
                    'devise_code' => $deviseCode
                ];
            }
        }

        // Sauvegarde des bordereaux dans la session
        $_SESSION['bordereaux'] = $bordereaux;

        
        header('Location: bordereaux.php');
        exit(); 

    } catch (PDOException $e) {
        die("Erreur : " . $e->getMessage());
    }
} else {
    echo "Paramètres manquants.";
}
