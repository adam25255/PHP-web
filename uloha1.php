<!-- vyziadanie common.php -->
<?php require '_common.php'; pageHeader('Horizontálne menu', 1); ?>
<?php include 'menu.php'; ?>
<!-- popis ulohy -->
<div class="container">
    <div class="panel">
        <h2>Úloha 1 – Horizontálne prechodové menu</h2>
        <p>Menu je uložené v súbore <code>menu.php</code> a načítané pomocou <code>include 'menu.php'</code>.</p>
        <p>Každá položka <em>Príklad X</em> má náhodne vygenerovanú farbu cez PHP funkciu <code>rand()</code>
           a veľkosť písma <code>font-size: 20px</code>. Rok sa zobrazuje dynamicky.</p>
    </div>

    <div class="panel">
        <h2>Stred stránky</h2>
        <div style="text-align: center; padding: 20px 0;">
            <h1 style="font-size: 1.6rem; color: #2c3e50;">Úlohy z predmetu Pokročilé internetové technológie</h1>
            <p>Adam Panák</p>
            <p><?= date('Y') ?></p>
        </div>
    </div>
<!-- preformatovana oblast so zdrojovym kodom -->
    <div class="panel">
        <h2>Zdrojový kód menu.php (ukážka)</h2>
<pre>
for ($i = 1; $i &lt;= 12; $i++):
    $r = rand(30, 220);
    $g = rand(30, 220);
    $b = rand(30, 220);
?&gt;
&lt;a href="uloha&lt;?= $i ?&gt;.php"
   style="color: rgb(&lt;?= $r ?&gt;, &lt;?= $g ?&gt;, &lt;?= $b ?&gt;);
          font-size: 20px;"&gt;Príklad &lt;?= $i ?&gt;&lt;/a&gt;
&lt;?php endfor; ?&gt;
</pre>
    </div>
</div>

<?php pageFooter(); ?>
