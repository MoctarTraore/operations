<?php
require_once __DIR__ . '/../config/db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   
    $input = json_decode(file_get_contents('php://input'), true);

  
    if (
        isset($input['nature_id']) &&
        isset($input['devise_code']) &&
        isset($input['donneur']) &&
        isset($input['objet']) &&
        isset($input['montantDevise']) &&
        isset($input['montantCFA'])
    ) {
     
        $nature_id = htmlspecialchars($input['nature_id']);
        $devise_code = htmlspecialchars($input['devise_code']);
        $donneur = htmlspecialchars($input['donneur']);
        $objet = htmlspecialchars($input['objet']);
        $montantDevise = (float) $input['montantDevise'];
        $montantCFA = (float) $input['montantCFA'];

        try {
            
            $stmt = $pdo->prepare("
                INSERT INTO operations (nature_id, devise_code, donneur, objet, montant_devise, montant_cfa)
                VALUES (:nature_id, :devise_code, :donneur, :objet, :montantDevise, :montantCFA)
            ");

           
            $stmt->execute([
                ':nature_id' => $nature_id,
                ':devise_code' => $devise_code,
                ':donneur' => $donneur,
                ':objet' => $objet,
                ':montantDevise' => $montantDevise,
                ':montantCFA' => $montantCFA
            ]);

           
            echo json_encode([
                'status' => 'success',
                'message' => 'Opération ajoutée avec succès.'
            ]);
        } catch (PDOException $e) {
           
            echo json_encode([
                'status' => 'error',
                'message' => 'Erreur lors de l\'insertion des données : ' . $e->getMessage()
            ]);
        }
    } else {
       
        echo json_encode([
            'status' => 'error',
            'message' => 'Données manquantes. Veuillez remplir tous les champs requis.'
        ]);
    }
} else {
   
    echo json_encode([
        'status' => 'error',
        'message' => 'Méthode HTTP invalide. Utilisez POST.'
    ]);
}
