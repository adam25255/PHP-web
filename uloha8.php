<!-- vyziadanie common.php, include menu zaciatok suboru -->

<?php
require '_common.php';
pageHeader('Citáty dňa', 8);
include 'menu.php';
//temy citatov, dni
$temy = [
    'zivot'       => 'O živote',
    'laska'       => 'O láske',
    'priatelstvo' => 'O priateľstve',
    'zamyslenie'  => 'Citáty na zamyslenie',
];
$dni = ['Pondelok','Utorok','Streda','Štvrtok','Piatok','Sobota','Nedeľa'];
//
function nacitajCitat(string $tema, int $denIndex, bool $nahodny = false): string {
    $subor = __DIR__ . "/citaty/{$tema}.txt";
    if (!file_exists($subor)) return 'Súbor citátov nebol nájdený.';
    $riadky = array_values(array_filter(
        array_map('trim', file($subor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES))
    ));
    if (!$riadky) return 'Prázdny súbor.';
    return $nahodny ? $riadky[array_rand($riadky)] : $riadky[$denIndex % count($riadky)];
}
//ziskanie dna
$den    = $_POST['den']   ?? (string)((int)date('N') - 1);
$tema   = $_POST['tema']  ?? 'zivot';
$citat  = null;
$akcia  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcia  = $_POST['akcia'] ?? '';
    $denIdx = (int)$den;

    if ($akcia === 'aktualny') {
        $denIdx = (int)date('N') - 1;
        $den    = (string)$denIdx;
        $citat  = nacitajCitat($tema, $denIdx, nahodny: true);
    } else {
        $citat = match($tema) {
            'zivot'       => nacitajCitat('zivot',       $denIdx),
            'laska'       => nacitajCitat('laska',       $denIdx),
            'priatelstvo' => nacitajCitat('priatelstvo', $denIdx),
            'zamyslenie'  => nacitajCitat('zamyslenie',  $denIdx),
            default       => 'Neznáma téma.',
        };
    }
}
?>
<!-- oblsast ulohy vyziadanie udajov zobrazenie citatu-->

<div class="container" style="max-width:700px">
    <div class="panel">
        <h2>Výber citátu podľa dňa a témy</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Deň v týždni:</label>
                    <select name="den">
                        <?php foreach ($dni as $i => $d): ?>
                            <option value="<?= $i ?>" <?= (string)$i === $den ? 'selected' : '' ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Téma citácie:</label>
                    <select name="tema">
                        <?php foreach ($temy as $k => $l): ?>
                            <option value="<?= $k ?>" <?= $tema === $k ? 'selected' : '' ?>><?= htmlspecialchars($l) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($citat !== null): ?>
                <div class="result" style="font-size:1rem; line-height:1.6; margin-bottom:10px;">
                    "<?= htmlspecialchars($citat) ?>"
                </div>
                <p style="font-size:0.8rem; color:#777; margin-bottom:10px;">
                    Deň: <?= $dni[(int)$den] ?> · Téma: <?= htmlspecialchars($temy[$tema] ?? '') ?>
                    · Súbor: citaty/<?= htmlspecialchars($tema) ?>.txt
                    <?= $akcia === 'aktualny' ? '(náhodný citát)' : '' ?>
                </p>
            <?php endif; ?>

            <div class="btn-group">
                <button type="submit" name="akcia" value="cituj" class="btn btn-primary">Cituj</button>
                <button type="submit" name="akcia" value="aktualny" class="btn">Aktuálny deň</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Obsah externých súborov</h2>
        <?php foreach ($temy as $k => $l): ?>
            <details style="margin-bottom:8px;">
                <summary style="cursor:pointer; font-weight:bold; padding:4px 0;">
                    📄 citaty/<?= $k ?>.txt – <?= htmlspecialchars($l) ?>
                </summary>
                <div style="margin-top:6px; padding:8px; background:#f8f8f8; border:1px solid #ddd; border-radius:3px;">
                <?php
                $subor = __DIR__ . "/citaty/{$k}.txt";
                if (file_exists($subor)) {
                    $lines = file($subor, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    foreach ($lines as $li => $line) {
                        echo '<div style="font-family:monospace; font-size:0.85rem;"><small style="color:#aaa">' . ($li+1) . '.</small> ' . htmlspecialchars(trim($line)) . '</div>';
                    }
                }
                ?>
                </div>
            </details>
        <?php endforeach; ?>
    </div>
<!-- preformatovana vzorka kodu-->

    <div class="panel">
        <h2>Výraz match (PHP 8)</h2>
<pre>
$citat = match($tema) {
    'zivot'       => nacitajCitat('zivot',       $denIdx),
    'laska'       => nacitajCitat('laska',       $denIdx),
    'priatelstvo' => nacitajCitat('priatelstvo', $denIdx),
    'zamyslenie'  => nacitajCitat('zamyslenie',  $denIdx),
    default       => 'Neznáma téma.',
};
</pre>
    </div>
</div>

<?php pageFooter(); ?>
