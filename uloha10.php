<?php
//start relacie vyziadanie common.php
session_start();
require '_common.php';
pageHeader('Výsledky študentov', 10);
//include menu
include 'menu.php';

if (!isset($_SESSION['studenti'])) {
    $_SESSION['studenti'] = [];
}

$error = '';
$msg   = '';
//pridavanie studentov
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $akcia = $_POST['akcia'] ?? '';

    if ($akcia === 'pridaj') {
        $sid  = strtoupper(trim($_POST['sid'] ?? ''));
        $body = trim($_POST['body'] ?? '');

        if ($sid === '' || $body === '') {
            $error = 'Zadajte ID študenta aj bodový výsledok!';
        } elseif (filter_var($body, FILTER_VALIDATE_INT) === false) {
            $error = 'Bodový výsledok musí byť celé číslo!';
        } else {
            if (!isset($_SESSION['studenti'][$sid])) {
                $_SESSION['studenti'][$sid] = [];
            }
            $_SESSION['studenti'][$sid][] = (int)$body;
            $msg = "Pridaný výsledok pre {$sid}: {$body} bodov.";
        }
    } elseif ($akcia === 'reset') {
        $_SESSION['studenti'] = [];
        $msg = 'Záznamy vymazané.';
    }
}

$studenti = $_SESSION['studenti'];

// prehlad pre textove pole
$prehladLines = [];
foreach ($studenti as $sid => $scores) {
    $prehladLines[] = $sid . ': ' . implode(', ', $scores);
}
$prehlad = implode("\n", $prehladLines);

// statistiky
$vsetky = [];
foreach ($studenti as $sid => $scores) {
    foreach ($scores as $s) $vsetky[] = ['id' => $sid, 'body' => $s];
}
usort($vsetky, fn($a, $b) => $b['body'] <=> $a['body']);
$top3    = array_slice($vsetky, 0, 3);
$celkom  = count($vsetky);
$priemer = $celkom > 0 ? array_sum(array_column($vsetky, 'body')) / $celkom : 0;

$vyhodnocovat = isset($_POST['akcia']) && $_POST['akcia'] === 'vyhodnot';
?>
<!-- formulare s ulohou-->

<div class="container">
    <div class="panel">
        <h2>Pridanie výsledku študenta</h2>
        <form method="post">
            <div class="form-row">
                <div class="form-group">
                    <label>ID študenta:</label>
                    <input type="text" name="sid" placeholder="napr. S2024001" style="text-transform:uppercase;">
                </div>
                <div class="form-group">
                    <label>Bodový výsledok:</label>
                    <input type="number" name="body" placeholder="napr. 85" min="0" max="100">
                </div>
            </div>
            <?php if ($error): ?><p style="color:red"><?= htmlspecialchars($error) ?></p><?php endif; ?>
            <?php if ($msg):  ?><p style="color:green"><?= htmlspecialchars($msg) ?></p><?php endif; ?>
            <div class="btn-group">
                <button type="submit" name="akcia" value="pridaj"   class="btn btn-primary">Pridaj</button>
                <button type="submit" name="akcia" value="vyhodnot" class="btn btn-success">Vyhodnoť</button>
                <button type="submit" name="akcia" value="reset"    class="btn btn-danger"
                    onclick="return confirm('Vymazať všetky záznamy?')">Reset</button>
            </div>
        </form>
    </div>

    <div class="panel">
        <h2>Všetky záznamy (<?= count($studenti) ?> študentov, <?= $celkom ?> pokusov celkom)</h2>
        <textarea readonly rows="8" style="font-family:monospace;"><?= htmlspecialchars($prehlad) ?></textarea>
    </div>

    <?php if ($vyhodnocovat || $celkom > 0): ?>
    <div class="panel">
        <h2>Vyhodnotenie</h2>
        <table style="max-width:350px; margin-bottom:20px;">
            <tr><th>Celkom pokusov</th><td><?= $celkom ?></td></tr>
            <tr><th>Počet študentov</th><td><?= count($studenti) ?></td></tr>
            <tr><th>Priemerné body</th><td><?= round($priemer, 2) ?></td></tr>
        </table>

        <?php if (!empty($top3)): ?>
        <h3 style="margin-bottom:10px;">Top 3 výsledky</h3>
        <table>
            <thead>
                <tr><th>Poradie</th><th>ID Študenta</th><th>Body</th></tr>
            </thead>
            <tbody>
                <?php foreach ($top3 as $i => $v): ?>
                <tr>
                    <td><?= ['🥇','🥈','🥉'][$i] ?? ($i+1) ?>.</td>
                    <td><strong><?= htmlspecialchars($v['id']) ?></strong></td>
                    <td><?= $v['body'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<?php pageFooter(); ?>
