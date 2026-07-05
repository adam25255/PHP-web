<?php
// _common.php spolocne funkcie, php subor vyzadovany ostatnymi podstrankami
//header
function pageHeader(string $title, int $taskNum): void {
    echo '<!DOCTYPE html>' . "\n";
    echo '<html lang="sk">' . "\n";
    echo '<head>' . "\n";
    echo '<meta charset="UTF-8">' . "\n";
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    echo "<title>Úloha {$taskNum} – " . htmlspecialchars($title) . "</title>\n";
    echo '<link rel="stylesheet" href="style.css">' . "\n";
    echo '</head>' . "\n";
    echo '<body>' . "\n";
    echo '<div class="page-header">';
    echo '<h1>Úloha ' . $taskNum . ' – ' . htmlspecialchars($title) . '</h1>';
    echo '<span class="meta">Adam Panák · PHP 8 · ' . date('Y') . '</span>';
    echo '</div>' . "\n";
}
//footer
function pageFooter(): void {
    echo '<footer class="page-footer">Adam Panák · Pokročilé internetové technológie · ' . date('Y') . '</footer>' . "\n";
    echo '</body>' . "\n";
    echo '</html>' . "\n";
}
