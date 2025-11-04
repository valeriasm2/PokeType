<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Game Over</title>
    <link rel="stylesheet" href="styles.css?1762107115">
</head>

<body>
    <audio id="button-sound" src="boton.mp3" preload="auto"></audio>

    <div class="gameover-container">
        <h1>Game Over!</h1>
        <p>Vols registrar el teu rècord de 4 punts?</p>
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

        // Funciones para los botones
        const saveRecord = () => {
            playSound(() => {
                const player = {
                    name: "a",
                    score: 4
                };
                let ranking = JSON.parse(localStorage.getItem("ranking") || "[]");
                ranking.push(player);
                ranking.sort((a, b) => b.score - a.score);
                localStorage.setItem("ranking", JSON.stringify(ranking));

                // Guardar el último jugador para resaltarlo en ranking.php
                sessionStorage.setItem("lastPlayer", player.name);

                window.location.href = "ranking.php";
            });
        };

        const goToIndex = () => {
            playSound(() => {
                window.location.href = "index.php";
            });
        };

        // Eventos de click
        document.getElementById("save-btn").addEventListener("click", saveRecord);
        document.getElementById("no-btn").addEventListener("click", goToIndex);

        // Eventos de teclado: solo S y N
        document.addEventListener("keydown", (e) => {
            const key = e.key.toLowerCase();
            if (key === "s") {
                saveRecord();
            } else if (key === "n") {
                goToIndex();
            }
        });
    </script>
</body>

</html>