<?php
//vyziadanie common.php, include menu.php
require '_common.php';
pageHeader('Geometrické telesá', 5);
include 'menu.php';
//uloha s telesami, funkccia, kontanta pi
define('PI_CONST', 3.14159265358979);

function geometria(string $teleso, string $typ, float $a, float $b = 0.0, float $vyska = 3.0): float {
    //match a vypocty
    return match($teleso) {
        'kocka'  => match($typ) {
            'objem'  => $a ** 3,
            'povrch' => 6 * $a ** 2,
            default  => 0.0,
        },
        'hranol' => match($typ) {
            'objem'  => $a * $a * $vyska,
            'povrch' => 2 * $a * $a + 4 * $a * $vyska,
            default  => 0.0,
        },
        'kvadr'  => match($typ) {
            'objem'  => $a * $b * $vyska,
            'povrch' => 2 * ($a * $b + $b * $vyska + $a * $vyska),
            default  => 0.0,
        },
        'ihlan'  => match($typ) {
            'objem'  => (1/3) * $a * $a * $vyska,
            'povrch' => $a * $a + 4 * (0.5 * $a * sqrt(($a/2)**2 + $vyska**2)),
            default  => 0.0,
        },
        'valec'  => match($typ) {
            'objem'  => PI_CONST * $a ** 2 * $vyska,
            'povrch' => 2 * PI_CONST * $a * ($a + $vyska),
            default  => 0.0,
        },
        default  => 0.0,
    };
}
//pole s volbami telies
$telesaOptions = [
    'kocka'  => 'Kocka',
    'hranol' => 'Hranol (štvorcový)',
    'kvadr'  => 'Kváder',
    'ihlan'  => 'Ihlan (štvorhranný)',
    'valec'  => 'Valec',
];
$typOptions = ['' => '-- zvoľte --', 'objem' => 'Objem', 'povrch' => 'Povrch'];
//nulovy koalescencny operator
$teleso = $_POST['teleso'] ?? 'kocka';
$typ    = $_POST['typ']    ?? '';
$a      = $_POST['a']      ?? '';
$b      = $_POST['b']      ?? '';
$vyska  = $_POST['vyska']  ?? '';
$result = null;
$error  = '';

$needsB     = ($teleso === 'kvadr');
$needsVyska = ($teleso !== 'kocka');
//osetrenie vstupu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $typ !== '') {
    $aVal     = filter_var($a,     FILTER_VALIDATE_FLOAT);
    $bVal     = $needsB     ? filter_var($b,     FILTER_VALIDATE_FLOAT) : 0.0;
    $vyskaVal = $needsVyska ? (($vyska !== '') ? filter_var($vyska, FILTER_VALIDATE_FLOAT) : 3.0) : 3.0;

    if ($aVal === false || ($needsB && $bVal === false)) {
        $error = 'Zadajte platné číselné hodnoty!';
    } elseif ($aVal <= 0 || ($needsB && $bVal <= 0)) {
        $error = 'Rozmery musia byť kladné čísla!';
    } else {
        if ($vyskaVal === false || $vyskaVal <= 0) $vyskaVal = 3.0;
        $result = geometria($teleso, $typ, (float)$aVal, (float)$bVal, (float)$vyskaVal);
    }
}
?>
<!-- kontajner s formularmi ulohy-->

<div class="container">
    <div class="panel">
        <h2>Výpočet objemu a povrchu geometrických telies</h2>
        <form method="post" id="geoForm">
            <div class="form-row">
                <div class="form-group">
                    <label>Teleso:</label>
                    <select name="teleso" id="selTeleso" onchange="updateForm()">
                        <?php foreach ($telesaOptions as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $teleso === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vypočítať:</label>
                    <select name="typ">
                        <?php foreach ($typOptions as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $typ === $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label id="labelA">Strana / polomer (a):</label>
                    <input type="number" name="a" value="<?= htmlspecialchars($a) ?>" step="any" min="0.001" placeholder="napr. 5">
                </div>
                <div class="form-group" id="groupB" style="display: <?= $needsB ? 'block' : 'none' ?>;">
                    <label>Šírka (b) – len pre kváder:</label>
                    <input type="number" name="b" value="<?= htmlspecialchars($b) ?>" step="any" min="0.001" placeholder="napr. 3">
                </div>
                <div class="form-group" id="groupVyska" style="display: <?= $needsVyska ? 'block' : 'none' ?>;">
                    <label>Výška (h) – ak prázdne, použije sa 3:</label>
                    <input type="number" name="vyska" value="<?= htmlspecialchars($vyska) ?>" step="any" min="0.001" placeholder="default: 3">
                </div>
            </div>

            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php elseif ($result !== null): ?>
                <div class="result">
                    <?= $telesaOptions[$teleso] ?> → <?= $typOptions[$typ] ?> = <?= round($result, 4) ?>
                    <?= $typ === 'objem' ? 'jednotiek³' : 'jednotiek²' ?>
                </div>
            <?php endif; ?>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Vypočítať</button>
                <button type="reset" class="btn">Vymazať</button>
            </div>
        </form>
    </div>
<!-- tabulka so vzorcami -->

    <div class="panel">
        <h2>Vzorce – funkcia geometria($teleso, $typ, $a, $b=0, $vyska=3)</h2>
        <table>
            <thead><tr><th>Teleso</th><th>Objem</th><th>Povrch</th></tr></thead>
            <tbody>
                <tr><td>Kocka</td><td>a³</td><td>6·a²</td></tr>
                <tr><td>Hranol (štvorcový)</td><td>a²·h</td><td>2a²+4a·h</td></tr>
                <tr><td>Kváder</td><td>a·b·h</td><td>2(ab+bh+ah)</td></tr>
                <tr><td>Ihlan (štvorhranný)</td><td>(a²·h)/3</td><td>a²+4·(a/2·sl)</td></tr>
                <tr><td>Valec</td><td>π·r²·h</td><td>2π·r(r+h)</td></tr>
            </tbody>
        </table>
        <p><small style="color:#777">Konštanta: <code>define('PI_CONST', 3.14159265358979);</code></small></p>
    </div>
</div>

<script>
function updateForm() {
    const t = document.getElementById('selTeleso').value;
    document.getElementById('groupB').style.display     = (t === 'kvadr') ? 'block' : 'none';
    document.getElementById('groupVyska').style.display = (t === 'kocka') ? 'none'  : 'block';
    const labels = {
        kocka:'Strana (a):', hranol:'Základňa (a):', kvadr:'Dĺžka (a):',
        ihlan:'Základňa (a):', valec:'Polomer (r):'
    };
    document.getElementById('labelA').textContent = labels[t] || 'Strana (a):';
}
</script>

<?php pageFooter(); ?>
