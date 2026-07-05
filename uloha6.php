<?php
//vyziadanie spolocnych funcii, include menu.php
require '_common.php';
pageHeader('Čínske znamenie', 6);
include 'menu.php';

// 2026l kon, index 6 
$cinske = ['Potkan','Byvol','Tiger','Zajac','Drak','Had','Kôň','Ovca','Opica','Kohút','Pes','Prasa'];
//pole lunarnychznameni
$lunarne = [
    ['Baran',      3, 21,  4, 19],
    ['Býk',        4, 20,  5, 20],
    ['Blíženec',   5, 21,  6, 20],
    ['Rak',        6, 21,  7, 22],
    ['Lev',        7, 23,  8, 22],
    ['Panna',      8, 23,  9, 22],
    ['Váhy',       9, 23, 10, 22],
    ['Škorpión',  10, 23, 11, 21],
    ['Strelec',   11, 22, 12, 21],
    ['Kozorožec', 12, 22,  1, 19],
    ['Vodnár',     1, 20,  2, 18],
    ['Ryby',       2, 19,  3, 20],
];
//funkcia cinske znamenie
function getCinskeZnamenie(int $year): string {
    global $cinske;
    $idx = ((($year - 2026) % 12) + 12) % 12;
    $idx = ($idx + 6) % 12;
    return $cinske[$idx];
}
//funkcia lunarne znamenie
function getLunarneZnamenie(int $month, int $day): string {
    global $lunarne;
    foreach ($lunarne as [$meno, $sm, $sd, $em, $ed]) {
        if ($sm <= $em) {
            if (($month === $sm && $day >= $sd) || ($month > $sm && $month < $em) || ($month === $em && $day <= $ed))
                return $meno;
        } else {
            if (($month === $sm && $day >= $sd) || ($month === $em && $day <= $ed))
                return $meno;
        }
    }
    return 'Neznáme';
}
//ziskanie dat z formulara
$result     = null;
$meno       = $_POST['meno']       ?? '';
$priezvisko = $_POST['priezvisko'] ?? '';
$datum      = $_POST['datum']      ?? '';
$pohlavie   = $_POST['pohlavie']   ?? 'muz';
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!trim($meno) || !trim($priezvisko) || !$datum) {
        $error = 'Vyplňte všetky polia!';
    } else {
        $dt = DateTime::createFromFormat('Y-m-d', $datum);
        if (!$dt) {
            $error = 'Neplatný dátum!';
        } else {
            $year  = (int)$dt->format('Y');
            $month = (int)$dt->format('m');
            $day   = (int)$dt->format('d');
            $chinaYear = ($month === 1) ? $year - 1 : $year;
            $result = [
                'meno'    => trim($meno) . ' ' . trim($priezvisko),
                'cinsk'   => getCinskeZnamenie($chinaYear),
                'lunar'   => getLunarneZnamenie($month, $day),
                'osloven' => ($pohlavie === 'zena') ? 'narodená' : 'narodený',
                'datum'   => $dt->format('d.m.Y'),
                'rok'     => $chinaYear,
            ];
        }
    }
}
?>
<!-- kontajner s ulohou -->

<div class="container">
    <div class="panel">
        <h2>Zistenie čínskeho a lunárneho znamenia</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Meno:</label>
                    <input type="text" name="meno" value="<?= htmlspecialchars($meno) ?>" placeholder="Ján">
                </div>
                <div class="form-group">
                    <label>Priezvisko:</label>
                    <input type="text" name="priezvisko" value="<?= htmlspecialchars($priezvisko) ?>" placeholder="Novák">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Dátum narodenia:</label>
                    <input type="date" name="datum" value="<?= htmlspecialchars($datum) ?>">
                </div>
                <div class="form-group">
                    <label>Pohlavie:</label>
                    <select name="pohlavie">
                        <option value="muz"  <?= $pohlavie === 'muz'  ? 'selected' : '' ?>>Muž</option>
                        <option value="zena" <?= $pohlavie === 'zena' ? 'selected' : '' ?>>Žena</option>
                    </select>
                </div>
            </div>
            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Zisti znamenie</button>
            </div>
        </form>
    </div>

    <?php if ($result): ?>
        <!-- tabulka s vysledkami samotna uloha -->

    <div class="panel">
        <h2>Výsledok</h2>
        <p style="font-size:1.1rem;">
            <strong><?= htmlspecialchars($result['meno']) ?></strong>
            ste <?= $result['osloven'] ?> v čínskom znamení
            <strong><?= htmlspecialchars($result['cinsk']) ?></strong>
            a lunárnom znamení
            <strong><?= htmlspecialchars($result['lunar']) ?></strong>.
        </p>
        <table style="max-width:400px; margin-top:10px;">
            <tr><th>Dátum</th><td><?= $result['datum'] ?></td></tr>
            <tr><th>Čínsky rok</th><td><?= $result['rok'] ?></td></tr>
            <tr><th>Čínske znamenie</th><td><?= htmlspecialchars($result['cinsk']) ?></td></tr>
            <tr><th>Lunárne znamenie</th><td><?= htmlspecialchars($result['lunar']) ?></td></tr>
        </table>
    </div>
    <?php endif; ?>
<!-- tabulka s vysvetlivkami -->

    <div class="panel">
        <h2>Čínsky horoskop – cyklus 12 zvierat</h2>
        <p>
        <?php
        $emojis = ['🐭','🐂','🐯','🐰','🐉','🐍','🐴','🐑','🐵','🐓','🐶','🐷'];
        foreach ($cinske as $i => $z) {
            $yr = 2026 + (($i - 6 + 12) % 12);
            echo $emojis[$i] . ' ' . $z . ' (' . $yr . ')&nbsp;&nbsp; ';
        }
        ?>
        </p>
        <p><small style="color:#777">Rok 2026 = Kôň. Cyklus sa opakuje každých 12 rokov.</small></p>
    </div>
</div>

<?php pageFooter(); ?>
