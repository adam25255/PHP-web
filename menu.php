<?php
// menu.php include dom ostatnych suborov, ovladanie cisla ulohy
?>
<nav class="main-menu">
<?php
//generovanie farbieb textu uloh
$current = basename($_SERVER['PHP_SELF']);
for ($i = 1; $i <= 12; $i++):
    $r = rand(30, 220);
    $g = rand(30, 220);
    $b = rand(30, 220);
    $active = ($current === "uloha{$i}.php") ? 'text-decoration:underline;font-weight:bold;' : '';
?>
    <a href="uloha<?= $i ?>.php"
       style="color: rgb(<?= $r ?>, <?= $g ?>, <?= $b ?>); font-size: 20px; <?= $active ?>">Príklad <?= $i ?></a>
<?php endfor; ?>
    <a href="index.php" class="nav-back">← Späť na prehľad</a>
</nav>
