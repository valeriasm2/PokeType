<?php
if (!isset($_POST['score']) || !isset($_POST['name'])) {
    header("HTTP/1.1 403 Forbidden");
    include(__DIR__ . "/errors/error403.php");
    exit;
}
$score = intval($_POST['score']);
$name = htmlspecialchars($_POST['name']);
?>

<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="gameover-container">
        <h1>Game Over!</h1>
        <p>Vols registrar el teu rècord de <?= $score ?> punts?</p>
        <button id="save-btn">Sí</button>
        <a href="index.php"><button>No</button></a>
    </div>

    <script>
        const player = {
            name: "<?= $name ?>",
            score: <?= $score ?>
        };
        document.getElementById("save-btn").addEventListener("click", () => {
            let ranking = JSON.parse(localStorage.getItem("ranking") || "[]");
            ranking.push(player);
            // Ordenar de mayor a menor
            ranking.sort((a, b) => b.score - a.score);
            localStorage.setItem("ranking", JSON.stringify(ranking));
            window.location.href = "ranking.php";
        });
    </script>
</body>

</html>