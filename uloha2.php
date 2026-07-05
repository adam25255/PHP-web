<!-- vyziadanie common.php -->
<?php
require '_common.php';
pageHeader('Rozvrh hodín', 2);
//include
include 'menu.php';
//funlcia casy v rozvrhu
function getScheduleTimes(): array {
    $times = [];
    $hour = 8; $min = 0;
    $breaks = [15, 20, 15, 15, 30, 15, 15];
    for ($i = 0; $i < 8; $i++) {
        $start = sprintf('%02d:%02d', $hour, $min);
        $endMin = $min + 45;
        $endHour = $hour + intdiv($endMin, 60);
        $endMin  = $endMin % 60;
        $end = sprintf('%02d:%02d', $endHour, $endMin);
        $times[] = "$start – $end";
        if ($i < 7) {
            $total = $endHour * 60 + $endMin + $breaks[$i];
            $hour  = intdiv($total, 60);
            $min   = $total % 60;
        }
    }
    return $times;
}
//pole dni, casov
$days  = ['Pondelok', 'Utorok', 'Streda', 'Štvrtok', 'Piatok'];
$times = getScheduleTimes();
$rozvrh = null;
$vstup  = '';
$error  = '';
//vstup
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vstup = trim($_POST['vstup'] ?? '');
    if ($vstup === '') {
        $error = 'Zadajte vstup pre rozvrh.';
    } else {
        $dayParts = explode('/', $vstup);
        $rozvrh = [];
        foreach ($dayParts as $idx => $dayStr) {
            if ($idx >= 5) break;
            $subjects = explode(',', $dayStr);
            $row = [];
            for ($p = 0; $p < 8; $p++) {
                $row[] = trim($subjects[$p] ?? '–');
            }
            $rozvrh[] = $row;
        }
        while (count($rozvrh) < 5) $rozvrh[] = array_fill(0, 8, '–');
    }
}
//formulare, oblast rozvrhu
?>
<div class="container">
    <div class="panel">
        <h2>Generátor rozvrhu hodín</h2>
        <form method="post">
            <div class="form-group">
                <label>Vstup – predmety oddelené čiarkou, dni lomítkom:</label>
                <input type="text" name="vstup" value="<?= htmlspecialchars($vstup) ?>"
                    placeholder="MAT,SJL,ANJ,DEJ,MAT,TEV,–,–/FYZ,CHE,BIO,MAT,ANJ,–,–,SJL">
                <small style="color:#777">Formát: <code>DEJ,SJL,MAT/TEV,FYZ,–/...</code> – voľná hodina = –</small>
            </div>
            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="btn-group">
                <button type="submit" class="btn btn-primary">Vytvoriť</button>
                <button type="reset" class="btn">Vymazať</button>
            </div>
        </form>
    </div>

    <?php if ($rozvrh): ?>
    <div class="panel">
        <h2>Rozvrh hodín</h2>
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Deň</th>
                        <?php foreach ($times as $i => $t): ?>
                            <th><?= $i+1 ?>. hodina<br><small style="font-weight:normal"><?= $t ?></small></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($days as $di => $day): ?>
                    <tr>
                        <td><strong><?= $day ?></strong></td>
                        <?php foreach ($rozvrh[$di] as $s): ?>
                            <td style="text-align:center; <?= $s === '–' ? 'color:#aaa' : '' ?>">
                                <?= htmlspecialchars($s) ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <div class="panel">
        <h2>Časy hodín a prestávok</h2>
        <table style="max-width:300px">
            <thead><tr><th>Hodina</th><th>Čas</th></tr></thead>
            <tbody>
            <?php foreach ($times as $i => $t): ?>
                <tr><td><?= $i+1 ?>. hodina</td><td><?= $t ?></td></tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <small style="color:#777">
            Prestávky: 1.=15min, 2.=20min, 3.=15min, 4.=15min, 5.=30min, 6.=15min, 7.=15min
        </small>
    </div>
</div>

<?php pageFooter(); ?>
