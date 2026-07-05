<?php
//start relacie vyziadanie common.php
session_start();
require '_common.php';
pageHeader('Knižný zoznam', 11);
//include menu
include 'menu.php';

if (!isset($_SESSION['knihy'])) {
    $_SESSION['knihy'] = [
        'romány' => [
            'Vojnaámier'     => ['autor' => 'Lev Tolstoj',        'cena' => 12.99, 'strany' => 1392, 'popis' => 'Epický román zo života ruskej aristokracie.'],
            'MajstermArgita' => ['autor' => 'Michail Bulgakov',    'cena' => 9.50,  'strany' => 480,  'popis' => 'Satirický román o diablovi v Moskve.'],
            'DonKichot'      => ['autor' => 'Miguel de Cervantes', 'cena' => 8.90,  'strany' => 1074, 'popis' => 'Dobrodružstvá rytiera smutnej postavy.'],
        ],
        'detské' => [
            'MalyPrinc'   => ['autor' => 'Antoine de Saint-Exupéry', 'cena' => 6.50, 'strany' => 96,  'popis' => 'Filozofická rozprávka o malnom princovi.'],
            'HobitatAlebo'=> ['autor' => 'J.R.R. Tolkien',           'cena' => 11.0, 'strany' => 310, 'popis' => 'Dobrodružstvo hobbita Bilba Vrecúška.'],
        ],
        'encyklopédie' => [
            'Zem'        => ['autor' => 'DK Publishing',      'cena' => 33.00, 'strany' => 448, 'popis' => 'Komplexná encyklopédia o planéte Zem.'],
            'Vesmir'     => ['autor' => 'National Geographic', 'cena' => 45.00, 'strany' => 520, 'popis' => 'Encyklopédia vesmíru a astronómie.'],
            'Prirodopis'  => ['autor' => 'David Attenborough', 'cena' => 28.00, 'strany' => 360, 'popis' => 'Encyklopédia živočíchov a prírody.'],
        ],
    ];
}

$knihy   = &$_SESSION['knihy'];
$atribs  = ['autor' => 'Autor', 'cena' => 'Cena (€)', 'strany' => 'Počet strán', 'popis' => 'Popis'];
$msg     = '';
$error   = '';
//operacie s formularom
$selKat   = $_POST['kategoria'] ?? array_key_first($knihy);
$selKniha = $_POST['kniha']     ?? '';
$selAtrib = $_POST['atribut']   ?? 'autor';
$akcia    = $_POST['akcia']     ?? '';
$novaHod  = $_POST['nova_hodnota'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $akcia) {
    if (!$selKat || !$selKniha || !$selAtrib) {
        $error = 'Vyberte kategóriu, knihu a atribút!';
    } elseif ($akcia === 'zmenit') {
        if (trim($novaHod) === '') {
            $error = 'Zadajte novú hodnotu!';
        } else {
            $stara = $knihy[$selKat][$selKniha][$selAtrib] ?? '–';
            $nova  = in_array($selAtrib, ['cena','strany']) ? (float)$novaHod : trim($novaHod);
            $knihy[$selKat][$selKniha][$selAtrib] = $nova;
            $msg = "Zmenené [{$selKat} → {$selKniha} → {$atribs[$selAtrib]}]: '{$stara}' → '{$nova}'";
        }
    } elseif ($akcia === 'vymazat') {
        $stara = $knihy[$selKat][$selKniha][$selAtrib] ?? '–';
        unset($knihy[$selKat][$selKniha][$selAtrib]);
        $msg = "Vymazaný kľúč '{$selAtrib}' (hodnota: '{$stara}') z [{$selKat} → {$selKniha}]";
    }
}
//osetrenie 
$aktualnaHodnota = ($selKat && $selKniha && $selAtrib)
    ? ($knihy[$selKat][$selKniha][$selAtrib] ?? '– atribút neexistuje –')
    : null;
?>
<!-- kontajner s ulohou -->

<div class="container">
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">

        <div class="panel">
            <h2>Výber a operácia</h2>
            <form method="post" id="bookForm">
                <div class="form-group">
                    <label>Kategória (combobox):</label>
                    <select name="kategoria" id="selKat" onchange="updateKnihy()">
                        <?php foreach (array_keys($knihy) as $kat): ?>
                            <option value="<?= htmlspecialchars($kat) ?>"
                                <?= $selKat === $kat ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($kat)) ?> (<?= count($knihy[$kat]) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Kniha (listbox):</label>
                    <select name="kniha" id="selKniha" size="5" style="height:120px;">
                        <?php if ($selKat && isset($knihy[$selKat])): ?>
                            <?php foreach (array_keys($knihy[$selKat]) as $k): ?>
                                <option value="<?= htmlspecialchars($k) ?>"
                                    <?= $selKniha === $k ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($k) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Atribút (combobox):</label>
                    <select name="atribut">
                        <?php foreach ($atribs as $k => $l): ?>
                            <option value="<?= $k ?>" <?= $selAtrib === $k ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($aktualnaHodnota !== null && $akcia): ?>
                <div class="form-group">
                    <label>Aktuálna hodnota:</label>
                    <input type="text" readonly value="<?= htmlspecialchars((string)$aktualnaHodnota) ?>">
                </div>
                <?php endif; ?>

                <div class="form-group" id="groupNova" style="display:<?= $akcia === 'zmenit' ? 'block' : 'none' ?>;">
                    <label>Nová hodnota (pre ZMENIŤ):</label>
                    <input type="text" name="nova_hodnota" value="<?= htmlspecialchars($novaHod) ?>" placeholder="zadajte novú hodnotu">
                </div>

                <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
                <?php if ($msg):   ?><p style="color:green; font-size:0.85rem;"><?= htmlspecialchars($msg) ?></p><?php endif; ?>

                <div class="btn-group">
                    <button type="submit" name="akcia" value="zobrazit" class="btn" onclick="hideNova()">Zobraziť</button>
                    <button type="submit" name="akcia" value="zmenit"   class="btn btn-primary" onclick="showNova()">Zmeniť</button>
                    <button type="submit" name="akcia" value="vymazat"  class="btn btn-danger"
                        onclick="hideNova(); return confirm('Naozaj vymazať?')">Vymazať</button>
                </div>
            </form>
        </div>

        <div>
            <?php foreach ($knihy as $kat => $zbierka): ?>
            <div class="panel" style="margin-bottom:15px;">
                <h2>📚 <?= htmlspecialchars(ucfirst($kat)) ?></h2>
                <?php foreach ($zbierka as $kn => $info): ?>
                    <div style="padding:6px 8px; border:1px solid #ddd; border-radius:3px; margin-bottom:6px;
                        <?= ($selKniha === $kn && $selKat === $kat) ? 'border-color:#2c3e50; background:#f0f4f8;' : '' ?>">
                        <strong><?= htmlspecialchars($kn) ?></strong><br>
                        <?php foreach ($info as $attr => $val): ?>
                            <small style="color:#666; font-family:monospace;">
                                <?= htmlspecialchars($atribs[$attr] ?? $attr) ?>: <?= htmlspecialchars((string)$val) ?>
                            </small><br>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<!-- skript-->

<script>
const bookData = <?= json_encode(array_map('array_keys', $knihy)) ?>;
function updateKnihy() {
    const kat = document.getElementById('selKat').value;
    const sel = document.getElementById('selKniha');
    sel.innerHTML = '';
    (bookData[kat] || []).forEach(k => {
        const o = document.createElement('option');
        o.value = k; o.textContent = k;
        sel.appendChild(o);
    });
}
function showNova() { document.getElementById('groupNova').style.display = 'block'; }
function hideNova() { document.getElementById('groupNova').style.display = 'none'; }
</script>

<?php pageFooter(); ?>
