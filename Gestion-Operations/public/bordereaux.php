<?php
session_start(); // Démarre la session pour accéder aux variables de session

if (!isset($_SESSION['bordereaux']) || empty($_SESSION['bordereaux'])) {
    die('Aucun bordereau généré.');
}

$bordereaux = $_SESSION['bordereaux']; // Récupère les bordereaux depuis la session
$deviseCode = isset($bordereaux[0]['devise_code']) ? $bordereaux[0]['devise_code'] : 'Non spécifiée';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bordereau</title>
</head>
<body>

    <h3>RÈGLEMENT DES OPERATIONS COURANTES</h3>

    <p>(Bordereau de dépôt du dossier de demande de couverture au guichet de la BCEAO)</p>
    
    Nom de la Banque : <strong>SGBCI</strong>

    <p>Sollicite auprès de la BCEAO un transfert d'un montant de <strong><?= htmlspecialchars($deviseCode) ?>: </strong></p>
    <p>Destiné à la couverture de : position extérieure débitrice règlements à effectuer</p>
    <p>Mode de couverture : au cours fixing des transferts par cotation de la salle des marchés de la BCEAO</p>
    <table>
        <thead>
            <tr>
                <th rowspan="2">Nature de l'opération</th>
                <th colspan="2">Total des ordres de la clientèle</th>
            </tr>
            <tr>
                <th> <strong>En FCFA</strong></th>
                <th> <strong>En devise(<?= htmlspecialchars($deviseCode) ?>)</strong></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($bordereaux)): ?>
                <?php foreach ($bordereaux as $bordereau): ?>
                    <tr>
                        <td><?= htmlspecialchars($bordereau['nature_nom']) ?></td>
                        <td><?= number_format($bordereau['total_montant_cfa'], 2, ',', ' ') ?></td>
                        <td><?= number_format($bordereau['total_montant_devise'], 2, ',', ' ') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">Aucun bordereau disponible.</td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total">
                <td>Total général</td>
                <td>
                    <?php 
                        $totalCfa = array_sum(array_column($bordereaux, 'total_montant_cfa'));
                        echo number_format($totalCfa, 0, ',', ' '); 
                    ?>
                </td>
                <td>
                    <?php 
                        $totalDevise = array_sum(array_column($bordereaux, 'total_montant_devise'));
                        echo number_format($totalDevise, 2, ',', ' '); 
                    ?>
                </td>
            </tr>
        </tfoot>
    </table>

    <table>
        <tr>
            <td>Signatures autorisées de la Banque</td>
            <td>Cachet de la BCEAO</td>
        </tr>
        <tr>
            <td rowspan="4" style="height: 80px;"></td>
            <td rowspan="4" style="height: 80px;"></td>
        </tr>
    </table>

    <p>Sort réservé à la demande<p>

    <table>
        <tr>
            <td colspan="5" class="element">Au guichet de la BCEAO</td>
            <td colspan="3" class="element">Après instruction du dossier</td>
        </tr>
        <tr>
            <td colspan="3" class="element">□ Reçu</td>
            <td colspan="2" class="element">□ Rejeté</td>
            <td colspan="2" class="element">□ Accepté</td>
            <td class="element">□ Rejeté</td>
        </tr>
        <tr>
            <td colspan="5" class="element">Motif du rejet*</td>
            <td colspan="3" rowspan="8" class="element">Motif du rejet* :</td>
        </tr>
        <tr>
            <td rowspan="7" colspan="5">
                <p class="element">□ dossier incomplet</p>
                <p class="element">□ non-respect de l'ordre de classement</p>
                <p class="element">□ pièces justificatives non conformes</p>
                <p class="element">□ Autre : ….........................................................	</p>
            </td>
        </tr>
    </table>

    <p>* à renseigner uniquement en cas de rejet</p>
    <p>Date réception à la BCEAO : …........../................./.............</p>

</body>
<style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        td {
            font-size: 13px;
        }
        .total {
            font-weight: bold;
        }
        .element {
            text-align: left;
        }
    </style>
</html>
