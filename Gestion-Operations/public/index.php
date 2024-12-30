<?php
require_once __DIR__ . '/../config/db.php';

try {
    
    $natures = $pdo->query("SELECT * FROM natures_produit")->fetchAll();
    $devises = $pdo->query("SELECT * FROM devises")->fetchAll();
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Opérations</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h1>Gestion des Opérations</h1>
        <form id="selectionForm">
            <div class="mb-3">
                <label for="nature" class="form-label">Nature de Produit</label>
                <select name="nature" id="nature" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach ($natures as $nature): ?>
                        <option value="<?= htmlspecialchars($nature['id']) ?>"><?= htmlspecialchars($nature['nom']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="devise" class="form-label">Devise</label>
                <select name="devise" id="devise" class="form-select" required>
                    <option value="">-- Sélectionnez --</option>
                    <?php foreach ($devises as $devise): ?>
                        <option value="<?= htmlspecialchars($devise['code']) ?>"><?= htmlspecialchars($devise['code']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Champ pour la valeur du dollar, caché par défaut -->
            <div class="mb-3" id="valeurDollarDiv" style="display: none;">
                <label for="valeurDollar" class="form-label">Valeur du Dollar (USD)</label>
                <input type="number" step="0.01" class="form-control" id="valeurDollar" name="valeurDollar">
            </div>

            <button type="button" class="btn btn-primary" id="activerBtn">Activer</button>
            <button type="button" class="btn btn-danger" id="desactiverBtn" style="display: none;">Désactiver</button>
            <button type="button" class="btn btn-info" id="voirBtn">Voir</button>
        </form>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="operationsModal" tabindex="-1" aria-labelledby="operationsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="operationsModalLabel">Liste des Opérations</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6>Ajouter une Opération</h6>
                    <form id="operationForm">
                        <input type="hidden" name="nature_id" id="hiddenNatureId">
                        <input type="hidden" name="devise_code" id="hiddenDeviseCode">
                        <div class="mb-3">
                            <label for="donneur" class="form-label">Donneur d'Ordre</label>
                            <input type="text" class="form-control" id="donneur" name="donneur" required>
                        </div>
                        <div class="mb-3">
                            <label for="objet" class="form-label">Objet du Règlement</label>
                            <input type="text" class="form-control" id="objet" name="objet" required>
                        </div>
                        <div class="mb-3">
                            <label for="montantDevise" class="form-label">Montant (Devise)</label>
                            <input type="number" step="0.01" class="form-control" id="montantDevise" name="montantDevise" required>
                        </div>
                        <div class="mb-3">
                            <label for="montantCFA" class="form-label">Montant (CFA)</label>
                            <input type="number" class="form-control" id="montantCFA" name="montantCFA" readonly>
                        </div>
                        <button type="button" class="btn btn-success" id="ajouterOperationBtn">Ajouter</button>
                    </form>

                    <h6>Liste des Opérations</h6>
                    <table class="table table-bordered" id="operationsTable">
                        <thead>
                            <tr>
                                <th>Client donneur d'ordre</th>
                                <th>Objet du règlement</th>
                                <th>Montant (Devise)</th>
                                <th>Montant (CFA)</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let tauxDeConversion = 655.957;

        document.getElementById('devise').addEventListener('change', () => {
            const devise = document.getElementById('devise').value;


            if (devise === 'USD') {
                document.getElementById('valeurDollarDiv').style.display = 'block';
            } else {
                document.getElementById('valeurDollarDiv').style.display = 'none';
            }


            document.getElementById('valeurDollar').value = '';
        });


        document.getElementById('activerBtn').addEventListener('click', () => {
            const nature = document.getElementById('nature').value;
            const devise = document.getElementById('devise').value;

            if (nature && devise) {

                if (devise === 'USD') {
                    const valeurDollar = parseFloat(document.getElementById('valeurDollar').value);
                    if (!valeurDollar || valeurDollar <= 0) {
                        alert("Veuillez entrer une valeur valide pour le dollar (USD) avant de continuer.");
                        return;
                    }
                }


                document.getElementById('hiddenNatureId').value = nature;
                document.getElementById('hiddenDeviseCode').value = devise;

                // Réinitialiser les champs du formulaire dans la modal
                document.getElementById('donneur').value = '';
                document.getElementById('objet').value = '';
                document.getElementById('montantDevise').value = '';
                document.getElementById('montantCFA').value = '';

                const modal = new bootstrap.Modal(document.getElementById('operationsModal'));
                modal.show();

                document.getElementById('desactiverBtn').style.display = 'inline-block';
            } else {
                alert("Veuillez sélectionner une nature et une devise.");
            }
        });

        // Désactivation et réinitialisation des sélections
        document.getElementById('desactiverBtn').addEventListener('click', () => {
            document.getElementById('nature').value = '';
            document.getElementById('devise').value = '';
            document.getElementById('hiddenNatureId').value = '';
            document.getElementById('hiddenDeviseCode').value = '';
            document.getElementById('desactiverBtn').style.display = 'none';

            const modal = bootstrap.Modal.getInstance(document.getElementById('operationsModal'));
            modal.hide();

            location.reload();
        });

        // Calcul du montant en CFA
        document.getElementById('montantDevise').addEventListener('input', function () {
            const montantDevise = parseFloat(this.value) || 0;
            let montantCFA = montantDevise * tauxDeConversion;

            // Calcul basé sur la valeur du dollar
            if (document.getElementById('devise').value === 'USD') {
                const valeurDollar = parseFloat(document.getElementById('valeurDollar').value) || 1;
                montantCFA = montantDevise * valeurDollar;
            }

            document.getElementById('montantCFA').value = montantCFA.toFixed(2);
        });

        // Ajouter une opération
        document.getElementById('ajouterOperationBtn').addEventListener('click', () => {
            const nature_id = document.getElementById('hiddenNatureId').value;
            const devise_code = document.getElementById('hiddenDeviseCode').value;
            const donneur = document.getElementById('donneur').value;
            const objet = document.getElementById('objet').value;
            const montantDevise = document.getElementById('montantDevise').value;
            const montantCFA = document.getElementById('montantCFA').value;

            if (nature_id && devise_code && donneur && objet && montantDevise && montantCFA) {
                const data = {
                    nature_id,
                    devise_code,
                    donneur,
                    objet,
                    montantDevise,
                    montantCFA
                };

                fetch('operations.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(result => {
                        if (result.status === 'success') {
                            const tableBody = document.querySelector('#operationsTable tbody');
                            const newRow = `
                                <tr>
                                    <td>${donneur}</td>
                                    <td>${objet}</td>
                                    <td>${parseFloat(montantDevise).toFixed(2)}</td>
                                    <td>${parseFloat(montantCFA).toFixed(2)}</td>
                                </tr>
                            `;
                            tableBody.insertAdjacentHTML('beforeend', newRow);

                            document.getElementById('operationForm').reset();
                            document.getElementById('montantCFA').value = '';
                        } else {
                            alert(result.message);
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        alert("Une erreur s'est produite. Veuillez réessayer.");
                    });
            } else {
                alert("Veuillez remplir tous les champs.");
            }
        });

        // Redirection vers la page "Voir"
        document.getElementById('voirBtn').addEventListener('click', () => {
            const nature = document.getElementById('nature').value;
            const devise = document.getElementById('devise').value;
            window.location.href = `operations_list.php?nature=${nature}&devise=${devise}`;
        });
    </script>
</body>

</html>
