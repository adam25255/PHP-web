<?php
//vyzadovanie common.php, include menu
require '_common.php';
pageHeader('Transformácia textu', 9);
include 'menu.php';

function transformujText(string $vstup): array {
    $kroky = [];
    $kroky['0. Vstup'] = $vstup;

    // zrusenie zalomenia riadkov
    $t = str_replace(["\r\n", "\r", "\n"], '', $vstup);
    $kroky['1. Zrušenie zalomení'] = $t;

    // odstranenie makcenov dlznov
    $from = ['á','é','í','ó','ú','ý','ä','ľ','ĺ','ŕ','č','š','ž','ň','ď','ť','ř',
             'Á','É','Í','Ó','Ú','Ý','Ä','Ľ','Ĺ','Ŕ','Č','Š','Ž','Ň','Ď','Ť','Ř'];
    $to   = ['a','e','i','o','u','y','a','l','l','r','c','s','z','n','d','t','r',
             'A','E','I','O','U','Y','A','L','L','R','C','S','Z','N','D','T','R'];
    $t = str_replace($from, $to, $t);
    $kroky['2. Bez diakritiky'] = $t;

    // sucasna zamena cez placeholder
    $t = str_replace(['e','E','a','A','u','U'], ["\x01","\x02","\x03","\x04","\x05","\x06"], $t);
    $t = str_replace(["\x01","\x02","\x03","\x04","\x05","\x06"], ['a','A','e','E','h','H'], $t);
    $kroky['3. Zámena e↔a, u→h'] = $t;

    // odstranenie criarok, buodiek medzier, zatvoriek
    $t = str_replace([' ', ',', '.', '(', ')'], '', $t);
    $kroky['4. Bez medzier, čiarok, bodiek, zátvoriek'] = $t;

    // odstranenie tretieho pismena
    $chars = mb_str_split($t);
    $r = [];
    foreach ($chars as $idx => $ch) {
        if ($idx % 3 !== 0) $r[] = $ch;
    }
    $t = implode('', $r);
    $kroky['5. Odstránenie každého 3. znaku (od 1.)'] = $t;

    // otocenie textu
    $t = implode('', array_reverse(mb_str_split($t)));
    $kroky['6. Otočenie textu'] = $t;

    // vlozenie medzier
    $chars = mb_str_split($t);
    $parts = [];
    $pos = 0; $size = 1;
    while ($pos < count($chars)) {
        $parts[] = implode('', array_slice($chars, $pos, $size));
        $pos += $size;
        $size++;
    }
    $t = implode(' ', $parts);
    $kroky['7. Inkrementálne medzery (1, 2, 3, …)'] = $t;

    //male pismena
    $t = mb_strtolower($t);
    $kroky['8. Malé písmená → vysledok.txt'] = $t;

    return [$t, $kroky];
}
//vstup,vystup formular, subory
$inputFile  = __DIR__ . '/citaj.txt';
$outputFile = __DIR__ . '/vysledok.txt';
$citajObsah = file_exists($inputFile) ? file_get_contents($inputFile) : '';
$vstupText  = '';
$vysledok   = '';
$kroky      = [];
$error      = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vstupText = $_POST['vstup'] ?? '';
    if (trim($vstupText) === '') {
        $error = 'Textové pole je prázdne!';
    } else {
        [$vysledok, $kroky] = transformujText($vstupText);
        file_put_contents($outputFile, $vysledok . "\n");
    }
}
?>

<div class="container">
    <div class="panel">
        <h2>Transformácia textu – citaj.txt → vysledok.txt</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>Vstupný text (citaj.txt):</label>
                    <textarea name="vstup" rows="6"><?= htmlspecialchars($vstupText ?: $citajObsah) ?></textarea>
                </div>
                <div class="form-group">
                    <label>Výsledok (vysledok.txt):</label>
                    <textarea readonly rows="6" style="background:#eee;"><?= htmlspecialchars($vysledok) ?></textarea>
                </div>
            </div>
            <?php if ($error): ?>
                <p style="color:red"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Konvertuj</button>
                <button type="button" class="btn" onclick="document.querySelector('textarea[name=vstup]').value=<?= json_encode($citajObsah) ?>">
                    Načítaj citaj.txt
                </button>
            </div>
        </form>
    </div>

    <?php if (!empty($kroky)): ?>
        <!-- vysvetlenie krokov -->

    <div class="panel">
        <h2>Jednotlivé kroky transformácie</h2>
        <table>
            <thead><tr><th>Krok</th><th>Výsledok</th></tr></thead>
            <tbody>
            <?php foreach ($kroky as $label => $val): ?>
                <tr>
                    <td style="white-space:nowrap; font-weight:bold; width:40%;"><?= htmlspecialchars($label) ?></td>
                    <td style="font-family:monospace; font-size:0.85rem; word-break:break-all;"><?= htmlspecialchars($val) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php if (file_exists($outputFile)): ?>
    <div class="panel">
        <h2>Obsah vysledok.txt</h2>
        <div class="result"><?= htmlspecialchars(file_get_contents($outputFile)) ?></div>
    </div>
    <?php endif; ?>
</div>

<?php pageFooter(); ?>
