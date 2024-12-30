<?php
session_start();

if (!isset($_SESSION['bordereaux']) || empty($_SESSION['bordereaux'])) {
    die('Aucun bordereau généré.');
}

$bordereaux = $_SESSION['bordereaux'];

function convertNumberToWords($number) {
    $frenchNumberWords = [
        0 => 'zéro', 1 => 'un', 2 => 'deux', 3 => 'trois', 4 => 'quatre', 5 => 'cinq',
        6 => 'six', 7 => 'sept', 8 => 'huit', 9 => 'neuf', 10 => 'dix',
        11 => 'onze', 12 => 'douze', 13 => 'treize', 14 => 'quatorze', 15 => 'quinze',
        16 => 'seize', 17 => 'dix-sept', 18 => 'dix-huit', 19 => 'dix-neuf', 20 => 'vingt',
        30 => 'trente', 40 => 'quarante', 50 => 'cinquante', 60 => 'soixante',
        70 => 'soixante-dix', 80 => 'quatre-vingt', 90 => 'quatre-vingt-dix',
        100 => 'cent', 1000 => 'mille', 1000000 => 'million', 1000000000 => 'milliard'
    ];

    if ($number < 21) {
        return $frenchNumberWords[$number];
    } elseif ($number < 100) {
        $tens = floor($number / 10) * 10;
        $units = $number % 10;
        return $frenchNumberWords[$tens] . ($units ? '-' . $frenchNumberWords[$units] : '');
    } elseif ($number < 1000) {
        $hundreds = floor($number / 100);
        $remainder = $number % 100;
        return ($hundreds > 1 ? $frenchNumberWords[$hundreds] . '-' : '') . 'cent' . ($remainder ? '-' . convertNumberToWords($remainder) : '');
    } elseif ($number < 1000000) {
        $thousands = floor($number / 1000);
        $remainder = $number % 1000;
        return ($thousands > 1 ? convertNumberToWords($thousands) . '-' : '') . 'mille' . ($remainder ? '-' . convertNumberToWords($remainder) : '');
    } elseif ($number < 1000000000) {
        $millions = floor($number / 1000000);
        $remainder = $number % 1000000;
        return convertNumberToWords($millions) . '-million' . ($remainder ? '-' . convertNumberToWords($remainder) : '');
    } else {
        $billions = floor($number / 1000000000);
        $remainder = $number % 1000000000;
        return convertNumberToWords($billions) . '-milliard' . ($remainder ? '-' . convertNumberToWords($remainder) : '');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
         @media print {
            @page {
                margin: 2cm 2cm;
            }

            body {
                margin: 2cm; 
                font-size: 12pt;
            }

            .element {
                text-align: right;
            }

            .flex-container {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .flex-container .sgci {
                font-size: 12pt;
                font-weight: bold;
                margin-left: 200px;
            }

            .flex-container .devise {
                font-weight: bold;
                margin-left: 300px;
            }   

            .flex-container .montant-devise {
                text-align: right;
            }

            .sg-ab {
                margin-left: 75px;
                margin-top: 15px;
            }

            .sgp {
                margin-left: 35px;
                margin-top: 10px;
            }

            .rib {
                margin-left: 60px;
            }

            .iban {
                margin-left: 45px;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
    <?php foreach ($bordereaux as $bordereau): ?>
    <div class="flex-container">
        <div class="sgci">SGCI</div>
        <div class="element">2631100A00030081</div>
    </div>
    <p class="montant-cfa"><?= number_format($bordereau['montant_cfa'], 0, ',', ' ') ?></p>
    <div class="flex-container">
        <div class="devise"><?= htmlspecialchars($bordereau['devise_code']) ?></div>
        <div class="montant-devise"><?= number_format($bordereau['montant_devise'], 2, ',', ' ') ?></div>
    </div>
    <p class="montant-cfa"><?= number_format($bordereau['montant_cfa'], 0, ',', ' ') ?></p>
    
    <p class="montant-lettre"><?= strtoupper(ucwords(convertNumberToWords((int)$bordereau['montant_cfa']))) ?> Francs CFA</p>

    <div class="sg-ab">SGBCI - ABIDJAN</div>
    <div class="sg-ab">SGBCI - ABIDJAN</div>
    <div class="sgp">SOCIETE GENERALE PARIS - SOGEFRPPXXX</div>
    <div class="rib">RIB : 3000 3069 9000 2013 7801 744</div>
    <div class="iban">IBAN : FR76 3000 3069 9000 2013 7801 744</div>

    <div>COUVERTURE POUR REGLEMENT FACTURE CLIENT</div>
    <div class="client"><?= htmlspecialchars($bordereau['donneur']) ?></div>
    <div class="flex-container">
        <div> POUR </div> 
        <div class="objet"><?= htmlspecialchars($bordereau['objet']) ?></div>
    </div> 

    <div class="flex-container">
        <div class="abj">Abidjan</div>
        <div class="date">/ /</div>
    </div>
    <hr>
    <?php endforeach; ?>
</body>
</html>
