<?php
//start relacie, iclude menu, vyziadanie common.php
session_start();
require '_common.php';
pageHeader('Hra – Hádanie farieb', 12);
include 'menu.php';
//moznosti
$moznosti = ['Červená', 'Zelená', 'Modrá'];
$farbyCss = ['Červená' => '#e74c3c', 'Zelená' => '#27ae60', 'Modrá' => '#2980b9'];

if (!isset($_SESSION['hra_farby']) || isset($_GET['nova'])) {
    $tajne = [];
    for ($i = 0; $i < 3; $i++) $tajne[] = $moznosti[array_rand($moznosti)];
    $_SESSION['hra_farby'] = ['tajne' => $tajne, 'pokusy' => 0, 'vyhral' => false, 'historia' => []];
}

$hra    = &$_SESSION['hra_farby'];
$sprava = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$hra['vyhral']) {
    // nulovy koalescencny operator
    $had1 = $_POST['farba1'] ?? '';
    $had2 = $_POST['farba2'] ?? '';
    $had3 = $_POST['farba3'] ?? '';
    $had  = [$had1, $had2, $had3];
    $tajne = $hra['tajne'];
    $hra['pokusy']++;

    // presne zhody
    $presne = 0;
    for ($i = 0; $i < 3; $i++) {
        if (($had[$i] ?? '') === ($tajne[$i] ?? '')) $presne++;
    }

    // farby z pola spravna a nespravna, vyhodnotenie
    $tajneCopy = $tajne;
    $hadCopy   = $had;
    for ($i = 0; $i < 3; $i++) {
        if ($hadCopy[$i] === $tajneCopy[$i]) { $tajneCopy[$i] = null; $hadCopy[$i] = null; }
    }
    $zPola = 0;
    foreach ($hadCopy as $hf) {
        if ($hf === null) continue;
        $pos = array_search($hf, $tajneCopy, true);
        if ($pos !== false) { $zPola++; $tajneCopy[$pos] = null; }
    }

    if ($presne === 3) {
        $sprava = 'Uhádli ste všetky 3 farby na presnej pozícii! Gratulujem!';
        $hra['vyhral'] = true;
    } else {
        $casti = [];
        if ($zPola > 0)   $casti[] = 'Vybrali ste ' . $zPola . ' farbu' . ($zPola > 1 ? 'y' : '') . ' z poľa';
        if ($presne > 0)  $casti[] = 'uhádli ste presne ' . $presne . ' farbu' . ($presne > 1 ? 'y' : '');
        $sprava = $casti ? ucfirst(implode(', ', $casti)) . '.' : 'Žiadna farba nebola uhádnutá.';
    }

    $hra['historia'][] = [
        'pokus'  => $hra['pokusy'],
        'had'    => $had,
        'presne' => $presne,
        'zPola'  => $zPola,
        'sprava' => $sprava,
        'vyhral' => $hra['vyhral'],
    ];
}
?>
<!-- kontajner s ulohou, formulare-->

<div class="container" style="max-width:620px">
    <div class="panel">
        <h2>Hádanie poradia farieb</h2>
        <p>Hra náhodne vybrala 3 farby z možností: <strong>Červená</strong>, <strong>Zelená</strong>, <strong>Modrá</strong>.
           Hádaj ich presné poradie pomocou datalistov.
           Pokusy: <strong><?= $hra['pokusy'] ?></strong></p>

        <?php if (!$hra['vyhral']): ?>
        <form method="post">
            <datalist id="farbyList">
                <?php foreach ($moznosti as $f): ?>
                    <option value="<?= htmlspecialchars($f) ?>">
                <?php endforeach; ?>
            </datalist>

            <div class="form-row three" style="margin-bottom:15px;">
                <?php for ($i = 1; $i <= 3; $i++): ?>
                <div class="form-group">
                    <label>Pozícia <?= $i ?>:</label>
                    <input type="text" name="farba<?= $i ?>" list="farbyList"
                           placeholder="Farba..." autocomplete="off" required>
                </div>
                <?php endfor; ?>
            </div>
            <button type="submit" class="btn btn-primary">Skúsiť</button>
        </form>
        <?php else: ?>
            <p style="color:green; font-size:1.1rem; font-weight:bold;">
                🏆 Vyhral si za <?= $hra['pokusy'] ?> <?= $hra['pokusy'] === 1 ? 'pokus' : ($hra['pokusy'] < 5 ? 'pokusy' : 'pokusov') ?>!
            </p>
            <p>Tajné poradie bolo:
            <?php foreach ($hra['tajne'] as $f): ?>
                <strong style="color:<?= $farbyCss[$f] ?>"><?= htmlspecialchars($f) ?></strong>
            <?php endforeach; ?>
            </p>
        <?php endif; ?>

        <div class="btn-group" style="margin-top:15px;">
            <a href="uloha12.php?nova=1" class="btn">Nová hra</a>
        </div>
    </div>

    <?php if (!empty($hra['historia'])): ?>
    <div class="panel">
        <h2>História pokusov</h2>
        <table>
            <thead>
                <tr><th>#</th><th>Výber</th><th>Výsledok</th></tr>
            </thead>
            <tbody>
            <?php foreach (array_reverse($hra['historia']) as $h): ?>
                <tr style="<?= $h['vyhral'] ? 'background:#eafaf1;' : '' ?>">
                    <td><?= $h['pokus'] ?></td>
                    <td>
                        <?php foreach ($h['had'] as $hf): ?>
                            <span style="background:<?= $farbyCss[$hf] ?? '#888' ?>; color:white;
                                padding:2px 8px; border-radius:3px; margin-right:3px; font-size:0.82rem;">
                                <?= htmlspecialchars($hf) ?>
                            </span>
                        <?php endforeach; ?>
                    </td>
                    <td style="font-size:0.85rem;"><?= htmlspecialchars($h['sprava']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Nulový koalescenčný operátor ?? (PHP 8)</h2>
        <!-- preformatovany blok s kodom, nulovy koalescencny operator -->

<pre>
// Operator ?? vracia pravý operand ak ľavý je NULL
$had1 = $_POST['farba1'] ?? '';
$had2 = $_POST['farba2'] ?? '';
$had3 = $_POST['farba3'] ?? '';

// Je ekvivalentné s:
$had1 = isset($_POST['farba1']) ? $_POST['farba1'] : '';
</pre>
    </div>
</div>

<?php pageFooter(); ?>
