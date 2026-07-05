<?php
//session start
session_start();
require '_common.php';
//funkcia testu
function getQuestions(): array {
    $img1 = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="380" height="140" style="background:#f8f8f8">'.
        '<rect width="380" height="140" fill="#f8f8f8" stroke="#ccc"/>'.
        '<text x="15" y="35" font-family="monospace" font-size="13" fill="#c0392b">&lt;?php</text>'.
        '<text x="15" y="60" font-family="monospace" font-size="13" fill="#333">  $x = 10;</text>'.
        '<text x="15" y="85" font-family="monospace" font-size="13" fill="#333">  $y = 5;</text>'.
        '<text x="15" y="110" font-family="monospace" font-size="13" fill="#333">  echo $x + $y;</text>'.
        '<text x="15" y="132" font-family="monospace" font-size="13" fill="#c0392b">?&gt;</text>'.
        '</svg>'
    );
    $img2 = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="380" height="110" style="background:#f8f8f8">'.
        '<rect width="380" height="110" fill="#f8f8f8" stroke="#ccc"/>'.
        '<text x="15" y="35" font-family="monospace" font-size="13" fill="#2980b9">&lt;!DOCTYPE html&gt;</text>'.
        '<text x="15" y="58" font-family="monospace" font-size="13" fill="#27ae60">&lt;html lang="sk"&gt;</text>'.
        '<text x="15" y="81" font-family="monospace" font-size="13" fill="#555">  &lt;body&gt;...&lt;/body&gt;</text>'.
        '<text x="15" y="104" font-family="monospace" font-size="13" fill="#27ae60">&lt;/html&gt;</text>'.
        '</svg>'
    );
    $img3 = 'data:image/svg+xml;utf8,' . rawurlencode(
        '<svg xmlns="http://www.w3.org/2000/svg" width="380" height="120" style="background:#f8f8f8">'.
        '<rect width="380" height="120" fill="#f8f8f8" stroke="#ccc"/>'.
        '<text x="15" y="32" font-family="monospace" font-size="13" fill="#333">$pole = [1, 2, 3, 4, 5];</text>'.
        '<text x="15" y="57" font-family="monospace" font-size="13" fill="#27ae60">foreach ($pole as $val) {</text>'.
        '<text x="15" y="82" font-family="monospace" font-size="13" fill="#555">    echo $val . " ";</text>'.
        '<text x="15" y="107" font-family="monospace" font-size="13" fill="#27ae60">}</text>'.
        '</svg>'
    );

    return [
        ['otazka'=>'Aký bude výstup tohto PHP kódu?','obrazok'=>$img1,
         'odpovede'=>['5','10','15','50'],'spravna'=>2],
        ['otazka'=>'Ktorý HTML tag sa používa na zobrazenie obrázku?','obrazok'=>$img2,
         'odpovede'=>['&lt;picture&gt;','&lt;img&gt;','&lt;image&gt;','&lt;src&gt;'],'spravna'=>1],
        ['otazka'=>'Čo vypíše tento foreach cyklus?','obrazok'=>$img3,
         'odpovede'=>['1,2,3,4,5','12345','1 2 3 4 5','[1,2,3,4,5]'],'spravna'=>2],
        ['otazka'=>'Čo reprezentuje symbol <b>$</b> pred názvom premennej v PHP?',
         'odpovede'=>['Premenná je číslo','Premenná je string','Prefix pre premennú v PHP','Cena v dolároch'],'spravna'=>2],
        ['otazka'=>'Aká funkcia vracia dĺžku reťazca v PHP?',
         'odpovede'=>['count()','size()','strlen()','length()'],'spravna'=>2],
        ['otazka'=>'Čo znamená skratka CSS?',
         'odpovede'=>['Computer Style Sheets','Cascading Style Sheets','Creative Style Syntax','Custom Style Script'],'spravna'=>1],
        ['otazka'=>'Ktorý cyklus v PHP sa vždy vykoná aspoň raz?',
         'odpovede'=>['for','while','foreach','do-while'],'spravna'=>3],
        ['otazka'=>'Čo robí PHP 8 operátor <b>??</b> (nulový koalescenčný operátor)?',
         'odpovede'=>['Porovná dve hodnoty','Vráti pravý operand ak ľavý je NULL','Konvertuje na bool','Vynásobí hodnoty'],'spravna'=>1],
        ['otazka'=>'Aký SQL príkaz sa používa na výber dát z tabuľky?',
         'odpovede'=>['GET','FETCH','SELECT','EXTRACT'],'spravna'=>2],
        ['otazka'=>'Na akom porte štandardne beží HTTP server?',
         'odpovede'=>['21','22','443','80'],'spravna'=>3],
    ];
}
//ziskanie udajov, kontrola sporavnosti
$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'start' || !isset($_SESSION['quiz'])) {
    $questions = getQuestions();
    if (isset($_POST['shuffle_q'])) shuffle($questions);
    if (isset($_POST['shuffle_a'])) {
        foreach ($questions as &$q) {
            $pairs = array_map(null, $q['odpovede'], range(0, count($q['odpovede'])-1));
            shuffle($pairs);
            $q['odpovede'] = array_column($pairs, 0);
            $origRight = $q['spravna'];
            foreach ($pairs as $ni => $pair) {
                if ($pair[1] === $origRight) { $q['spravna'] = $ni; break; }
            }
        }
        unset($q);
    }
    $_SESSION['quiz'] = ['questions'=>$questions,'current'=>0,'answers'=>[],'started'=>true];
    header('Location: uloha7.php');
    exit;
}

if ($action === 'answer' && isset($_SESSION['quiz'])) {
    $answered = isset($_POST['odpoved']) ? (int)$_POST['odpoved'] : -1;
    $_SESSION['quiz']['answers'][$_SESSION['quiz']['current']] = $answered;
    $_SESSION['quiz']['current']++;
    if ($_SESSION['quiz']['current'] >= count($_SESSION['quiz']['questions'])) {
        header('Location: uloha7.php?action=result');
        exit;
    }
    header('Location: uloha7.php');
    exit;
}

if ($action === 'reset') {
    unset($_SESSION['quiz']);
    header('Location: uloha7.php');
    exit;
}

pageHeader('Testovací kvíz', 7);
//include menu.php
include 'menu.php';

// vysledok
if ($action === 'result' && isset($_SESSION['quiz'])) {
    $quiz    = $_SESSION['quiz'];
    $total   = count($quiz['questions']);
    $correct = 0;
    foreach ($quiz['answers'] as $qi => $ans) {
        if ($ans === $quiz['questions'][$qi]['spravna']) $correct++;
    }
    $pct = round($correct / $total * 100);
    ?>
    <div class="container" style="max-width:700px">
        <div class="panel" style="text-align:center">
            <h2>Výsledok kvízu</h2>
            <p style="font-size:1.3rem; margin:15px 0;">
                Správne: <strong><?= $correct ?> / <?= $total ?></strong> (<?= $pct ?>%)
            </p>
            <p><?= $correct >= 8 ? '🏆 Výborný výsledok!' : ($correct >= 5 ? '👍 Dobrý výkon.' : '📚 Treba viac trénovať.') ?></p>
        </div>
        <div class="panel">
            <h2>Podrobné výsledky</h2>
            <?php foreach ($quiz['questions'] as $qi => $q): ?>
                <?php $ans = $quiz['answers'][$qi] ?? -1; $ok = ($ans === $q['spravna']); ?>
                <div style="padding:8px; border-left:4px solid <?= $ok ? '#27ae60' : '#e74c3c' ?>; margin-bottom:8px; background:#f9f9f9;">
                    <strong><?= $qi+1 ?>. <?= $q['otazka'] ?></strong><br>
                    <span style="color:<?= $ok ? '#27ae60' : '#e74c3c' ?>">
                        <?= $ok ? '✓ Správne' : '✗ Nesprávne' ?>
                    </span>
                    <?php if (!$ok): ?>
                        – správna odpoveď: <strong><?= $q['odpovede'][$q['spravna']] ?></strong>
                        | vaša odpoveď: <?= $ans >= 0 ? $q['odpovede'][$ans] : 'nezodpovedaná' ?>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="btn-group">
            <a href="uloha7.php?action=reset" class="btn btn-primary">Nový kvíz</a>
            <a href="index.php" class="btn">Späť na prehľad</a>
        </div>
    </div>
    <?php
    pageFooter();
    exit;
}

// --- Štartovacia obrazovka ---
if (!isset($_SESSION['quiz']['started'])): ?>
<div class="container" style="max-width:500px">
    <div class="panel">
        <h2>Testovací kvíz – PHP a web</h2>
        <p style="margin-bottom:15px;">10 otázok o PHP a webových technológiách. Môžete náhodne premiešať otázky aj odpovede.</p>
        <form method="post" action="uloha7.php">
            <input type="hidden" name="action" value="start">
            <div class="form-group">
                <label><input type="checkbox" name="shuffle_q" checked style="width:auto; margin-right:6px;"> Náhodné poradie otázok</label>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="shuffle_a" checked style="width:auto; margin-right:6px;"> Náhodné poradie odpovedí</label>
            </div>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Spustiť kvíz</button>
            </div>
        </form>
    </div>
</div>
<?php
else:
    $quiz  = $_SESSION['quiz'];
    $ci    = $quiz['current'];
    $q     = $quiz['questions'][$ci];
    $total = count($quiz['questions']);
    $pct   = round(($ci / $total) * 100);
?>
<div class="container" style="max-width:600px">
    <!-- postup v teste -->
    <p style="margin-bottom:8px;">Otázka <?= $ci+1 ?> z <?= $total ?></p>
    <div style="background:#ddd; border-radius:3px; height:8px; margin-bottom:20px;">
        <div style="background:#2c3e50; height:8px; border-radius:3px; width:<?= $pct ?>%;"></div>
    </div>

    <div class="panel">
        <?php if (!empty($q['obrazok'])): ?>
            <img src="<?= $q['obrazok'] ?>" alt="Otázka"
                 style="max-width:100%; display:block; margin-bottom:15px; border:1px solid #ccc;">
        <?php endif; ?>

        <p style="font-size:1.05rem; font-weight:bold; margin-bottom:15px;"><?= $q['otazka'] ?></p>
<!-- formular -->

        <form method="post" action="uloha7.php">
            <input type="hidden" name="action" value="answer">
            <?php foreach ($q['odpovede'] as $ai => $odp): ?>
                <div style="margin-bottom:8px;">
                    <label style="font-weight:normal; cursor:pointer;">
                        <input type="radio" name="odpoved" value="<?= $ai ?>" required style="width:auto; margin-right:8px;">
                        <?= $odp ?>
                    </label>
                </div>
            <?php endforeach; ?>
            <div class="btn-group" style="margin-top:15px;">
                <button type="submit" class="btn btn-primary">Odpoveď</button>
                <a href="uloha7.php?action=reset" class="btn" style="text-decoration:none; padding:6px 14px;">Reštart</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php pageFooter(); ?>
