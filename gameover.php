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
    <link rel="stylesheet" href="styles.css<?php echo time(); ?>">
</head>

<body>
    <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

    <div class="gameover-container">
        <h1>Game Over!</h1>
        <p>Vols registrar el teu rècord de <?= $score ?> punts?</p>
        <button id="save-btn">Sí</button>
        <button id="no-btn">No</button>
    </div>

    <script src="music.js"></script>
    <script>
        const buttonSound = document.getElementById('button-sound');

        const playSound = (callback) => {
            buttonSound.currentTime = 0;
            buttonSound.volume = 1;
            buttonSound.play();
            setTimeout(callback, 800);
        }

        document.getElementById("save-btn").addEventListener("click", () => {
            playSound(() => {
                const player = {
                    name: "<?= $name ?>",
                    score: <?= $score ?>
                };
                let ranking = JSON.parse(localStorage.getItem("ranking") || "[]");
                ranking.push(player);
                ranking.sort((a, b) => b.score - a.score);
                localStorage.setItem("ranking", JSON.stringify(ranking));

                // Guardar el último jugador para resaltarlo en ranking.php
                sessionStorage.setItem("lastPlayer", player.name);

                window.location.href = "ranking.php";
            });
        });

        document.getElementById("no-btn").addEventListener("click", () => {
            playSound(() => {
                window.location.href = "index.php";
            });
        });
    </script>
</body>

</html>