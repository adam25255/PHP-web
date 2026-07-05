<!-- vyziadanie common.php -->
<?php
require '_common.php';
//nasobky cisel tretia uloha
pageHeader('Násobky čísel', 3);
//include menu
include 'menu.php';
//funkcia hladania nasobku rekurzivne
function najdiNasobkyRekurzia(int $current, int $max, int $nasob, array &$vysledok = []): array {
    if ($current > $max) return $vysledok;
    if ($current % $nasob === 0) $vysledok[] = $current;
    return najdiNasobkyRekurzia($current + 1, $max, $nasob, $vysledok);
}
//metoda post z formulara
$result = null;
$method = '';
$error  = '';
$min    = $_POST['min']   ?? '';
$max    = $_POST['max']   ?? '';
$nasob  = $_POST['nasob'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $method   = $_POST['method'] ?? '';
    $minVal   = filter_var($min,   FILTER_VALIDATE_INT);
    $maxVal   = filter_var($max,   FILTER_VALIDATE_INT);
    $nasobVal = filter_var($nasob, FILTER_VALIDATE_INT);
//osetrovanie vstupu, do while cyklus
    if ($minVal === false || $maxVal === false || $nasobVal === false) {
        $error = 'Zadajte iba celé čísla!';
    } elseif ($nasobVal == 0) {
        $error = 'Násobok nesmie byť nula!';
    } elseif ($minVal > $maxVal) {
        $error = 'Dolná hranica musí byť menšia alebo rovná hornej!';
    } else {
        $cisla = [];
        if ($method === 'cyklus') {
            $i = $minVal;
            do {
                if ($i % $nasobVal === 0) $cisla[] = $i;
                $i++;
            } while ($i <= $maxVal);
        } else {
            $arr = [];
            $cisla = najdiNasobkyRekurzia($minVal, $maxVal, $nasobVal, $arr);
        }
        $result = $cisla;
    }
}
?>
<!-- formulare -->

<div class="container">
    <div class="panel">
        <h2>Násobky čísel v zadanom rozsahu</h2>
        <form method="post">
            <div class="form-row three">
                <div class="form-group">
                    <label>Dolná hranica:</label>
                    <input type="number" name="min" value="<?= htmlspecialchars($min) ?>" placeholder="napr. 1">
                </div>
                <div class="form-group">
                    <label>Horná hranica:</label>
                    <input type="number" name="max" value="<?= htmlspecialchars($max) ?>" placeholder="napr. 100">
                </div>
                <div class="form-group">
                    <label>Násobok čísla:</label>
                    <input type="number" name="nasob" value="<?= htmlspecialchars($nasob) ?>" placeholder="napr. 7">
                </div>
            </div>
            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="btn-group">
                <button type="submit" name="method" value="cyklus" class="btn btn-primary">Cyklus (do-while)</button>
                <button type="submit" name="method" value="funkcia" class="btn">Funkcia (rekurzia)</button>
            </div>
        </form>
    </div>

    <?php if ($result !== null && !$error): ?>
    <div class="panel">
        <h2>Výsledok – <?= $method === 'cyklus' ? 'Do-While cyklus' : 'Rekurzívna funkcia' ?></h2>
        <?php if (count($result) === 0): ?>
            <p>Žiadne násobky čísla <?= htmlspecialchars($nasob) ?> v rozsahu <?= htmlspecialchars($min) ?>–<?= htmlspecialchars($max) ?>.</p>
        <?php else: ?>
            <div class="result"><?= implode(' ', $result) ?></div>
            <p><small>Celkom: <strong><?= count($result) ?></strong> násobkov čísla <?= htmlspecialchars($nasob) ?> v rozsahu <?= htmlspecialchars($min) ?>–<?= htmlspecialchars($max) ?></small></p>
        <?php endif; ?>
    </div>

    <div class="panel">
        <h2>Použitý kód</h2>
        <?php if ($method === 'cyklus'): ?>
<!-- vzorka kodu-->

<pre>
// Do-While cyklus
$i = $minVal;
do {
    if ($i % $nasobVal === 0) $cisla[] = $i;
    $i++;
} while ($i &lt;= $maxVal);
</pre>
        <?php else: ?>
<pre>
// Rekurzívna funkcia
function najdiNasobkyRekurzia(int $current, int $max,
    int $nasob, array &amp;$vysledok = []): array
{
    if ($current > $max) return $vysledok;
    if ($current % $nasob === 0) $vysledok[] = $current;
    return najdiNasobkyRekurzia($current + 1, $max, $nasob, $vysledok);
}
</pre>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php pageFooter(); ?>
