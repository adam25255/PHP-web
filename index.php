
 <!-- doctype hlavicka--> 
<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Cvičenia 2026 – Adam Panák</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="page-header">
    <h1>PHP Cvičenia 2026</h1>
    <span class="meta">Adam Panák · Pokročilé internetové technológie</span>
</div>

<div class="hero">
    <h2>Úlohy z predmetu Pokročilé internetové technológie</h2>
    <p>Adam Panák &nbsp;|&nbsp; <?= date('Y') ?></p>
</div>
<!-- rozpis uloh asociativne pole -->
<div class="task-grid">
<?php
$ulohy = [
    1  => ['Horizontálne menu',     'Prechodové menu, výpis Príklad 1–12 s náhodnou farbou, dynamický rok, include.'],
    2  => ['Rozvrh hodín',          'Generátor rozvrhu hodín z formátu DEJ,SJL/MAT,TEV s časmi hodín.'],
    3  => ['Násobky čísel',         'Výpis násobkov v rozsahu pomocou cyklu while-do a rekurzívnej funkcie.'],
    4  => ['Prepočet jednotiek',    'Konvertor l/dl/ml, kg/tona, m/km/cm/mm s ošetrením nelogických kombinácií.'],
    5  => ['Geometrické telesá',    'Objem a povrch kocky, hranola, kvádra, ihlana, valca – jedna funkcia.'],
    6  => ['Čínske znamenie',       'Čínske a lunárne znamenie podľa mena a dátumu narodenia.'],
    7  => ['Testovací kvíz',        '10 otázok s obrázkami, radio tlačidlá, náhodné premiešanie, vyhodnotenie.'],
    8  => ['Citáty dňa',            'Citáty podľa dňa a témy, výraz match, externé súbory, tlačidlo Aktuálny.'],
    9  => ['Transformácia textu',   'Séria reverzných operácií nad súborom citaj.txt → vysledok.txt.'],
    10 => ['Výsledky študentov',    'Evidencia ID a bodov, uloženie viacerých pokusov, top 3, priemer.'],
    11 => ['Knižný zoznam',         'CRUD nad asociatívnym poľom kníh – combobox, listbox, zobraziť/zmeniť/vymazať.'],
    12 => ['Hra – Hádanie farieb',  'Hádanie poradia 3 farieb pomocou datalistov. Nulový koalescenčný operátor.'],
];
foreach ($ulohy as $n => [$nazov, $popis]):
?>
    <div class="task-card">
        <div class="num">Úloha <?= $n ?></div>
        <h3><?= htmlspecialchars($nazov) ?></h3>
        <p><?= htmlspecialchars($popis) ?></p>
        <a href="uloha<?= $n ?>.php" target="_blank" class="btn-open">Otvoriť →</a>
    </div>
<?php endforeach; ?>
</div>
<!-- footer -->
<footer class="page-footer">Adam Panák · Pokročilé internetové technológie · <?= date('Y') ?></footer>
</body>
</html>
