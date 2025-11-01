<?php
$rankingFile = __DIR__ . '/ranking.txt';
$ranking = [];
$lastPlayer = $_GET['last'] ?? '';
$lastScore  = isset($_GET['score']) ? intval($_GET['score']) : null;

if(file_exists($rankingFile)) {
    $lines = file($rankingFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach($lines as $line) {
        [$playerName, $score] = explode(":", $line);
        $ranking[] = ['name' => htmlspecialchars($playerName), 'score' => intval($score)];
    }
    usort($ranking, fn($a, $b) => $b['score'] <=> $a['score']);
}
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <title>Rànking de rècords</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">
</head>
<body>
    <div id="ranking-container">
        <h1>Rànking de rècords</h1>
        <table>
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Punts</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $highlighted = false; // resaltar solo la última partida
                foreach($ranking as $i => $p):
                    $highlight = (!$highlighted && $p['name'] === $lastPlayer && $p['score'] === $lastScore);
                    if($highlight) $highlighted = true;
                ?>
                <tr class="<?= $i % 2 === 0 ? 'even' : 'odd' ?> <?= $highlight ? 'highlight' : '' ?>">
                    <td><?= $p['name'] ?></td>
                    <td><?= $p['score'] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn-link">Tornar al joc</a>
    </div>
</body>
</html>
