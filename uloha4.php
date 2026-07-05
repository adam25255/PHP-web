<?php
//vyziadanie common.php, include menu.phpo
require '_common.php';
pageHeader('Prepočet jednotiek', 4);
include 'menu.php';
//skupiny jednotiek, polia
$groups = [
    'objem'    => ['l', 'dl', 'ml'],
    'hmotnost' => ['kg', 'tona'],
    'dlzka'    => ['m', 'km', 'cm', 'mm'],
];

// koeficienty hlavnych a mensich jednotiek
$toBase = [
    'l'    => 1.0,      'dl'   => 0.1,    'ml'   => 0.001,
    'kg'   => 1.0,      'tona' => 1000.0,
    'm'    => 1.0,      'km'   => 1000.0, 'cm'   => 0.01,  'mm' => 0.001,
];

$unitLabels = [
    'l' => 'Liter (l)', 'dl' => 'Deciliter (dl)', 'ml' => 'Milililter (ml)',
    'kg' => 'Kilogram (kg)', 'tona' => 'Tona',
    'm' => 'Meter (m)', 'km' => 'Kilometer (km)', 'cm' => 'Centimeter (cm)', 'mm' => 'Milimeter (mm)',
];
//spracovanie skupiny jednotiek
function getGroup(string $unit): ?string {
    global $groups;
    foreach ($groups as $g => $units) {
        if (in_array($unit, $units, true)) return $g;
    }
    return null;
}
//nulovy koalescencny operator
$result   = null;
$error    = '';
$hodnota  = $_POST['hodnota'] ?? '';
$z        = $_POST['z']       ?? 'l';
$na       = $_POST['na']      ?? 'ml';
//osetrenie hodnot
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $h = filter_var($hodnota, FILTER_VALIDATE_FLOAT);
    if ($h === false) {
        $error = 'Zadajte platnú číselnú hodnotu!';
    } elseif (getGroup($z) !== getGroup($na)) {
        $error = 'Nesprávne zadané jednotky – nemožno previesť ' . htmlspecialchars($z) . ' na ' . htmlspecialchars($na) . '.';
    } else {
        $result = ($h * $toBase[$z]) / $toBase[$na];
    }
}

$allUnits = array_keys($unitLabels);
?>
<!-- kontajner samotne formulare-->

<div class="container">
    <div class="panel">
        <h2>Prepočet jednotiek</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Hodnota:</label>
                    <input type="text" name="hodnota" value="<?= htmlspecialchars($hodnota) ?>" placeholder="napr. 1.5">
                </div>
                <div class="form-group">
                    <label>Výsledok:</label>
                    <input type="text" readonly
                        value="<?= ($result !== null && !$error) ? rtrim(rtrim(number_format($result, 10, '.', ''), '0'), '.') : '' ?>">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Z jednotky:</label>
                    <select name="z">
                        <?php foreach ($allUnits as $u): ?>
                            <option value="<?= $u ?>" <?= $z === $u ? 'selected' : '' ?>><?= $unitLabels[$u] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Na jednotku:</label>
                    <select name="na">
                        <?php foreach ($allUnits as $u): ?>
                            <option value="<?= $u ?>" <?= $na === $u ? 'selected' : '' ?>><?= $unitLabels[$u] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($result !== null): ?>
                <div class="result">
                    <?= htmlspecialchars($hodnota) ?> <?= htmlspecialchars($z) ?> =
                    <?= rtrim(rtrim(number_format($result, 10, '.', ''), '0'), '.') ?> <?= htmlspecialchars($na) ?>
                </div>
            <?php endif; ?>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Prepočítať</button>
                <button type="reset" class="btn">Vymazať</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Skupiny jednotiek</h2>
        <table style="max-width: 400px;">
            <thead><tr><th>Skupina</th><th>Jednotky</th></tr></thead>
            <tbody>
                <tr><td>Objem</td><td>Liter, Deciliter, Milililter</td></tr>
                <tr><td>Hmotnosť</td><td>Kilogram, Tona</td></tr>
                <tr><td>Dĺžka</td><td>Meter, Kilometer, Centimeter, Milimeter</td></tr>
            </tbody>
        </table>
        <p><small style="color:#777">Kombinácia rôznych skupín zobrazí chybovú hlášku.</small></p>
    </div>
</div>

<?php pageFooter(); ?>
