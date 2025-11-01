<!DOCTYPE html>
<html lang="ca">

<head>
    <meta charset="UTF-8">
    <title>Rànking de rècords</title>
    <link rel="stylesheet" href="styles.css?<?php echo time(); ?>">

</head>

<body>
    <audio id="button-sound" src="media/boton.mp3" preload="auto"></audio>

    <div id="ranking-container">
        <h1>Rànking de rècords</h1>
        <table>
            <thead>
                <tr>
                    <th>Jugador</th>
                    <th>Punts</th>
                </tr>
            </thead>
            <tbody id="ranking-body">
            </tbody>
        </table>
        <a href="index.php" class="btn-link">Tornar al joc</a>
    </div>

    <script src="utils/music3.js"></script>
    <script>
        let ranking = JSON.parse(localStorage.getItem("ranking") || "[]");
        const tbody = document.getElementById("ranking-body");
        const lastPlayerName = sessionStorage.getItem("lastPlayer"); // jugador reciente

        ranking.forEach((p, i) => {
            let tr = document.createElement("tr");
            tr.className = i % 2 === 0 ? "even" : "odd";
            if (p.name === lastPlayerName) {
                tr.classList.add("highlight");
            }
            tr.innerHTML = `<td>${p.name}</td><td>${p.score}</td>`;
            tbody.appendChild(tr);
        });

        const buttonSound = document.getElementById('button-sound');
        const allButtons = document.querySelectorAll('button, .btn-link');

        allButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                buttonSound.currentTime = 0;
                buttonSound.volume = 1;
                buttonSound.play();
                const href = btn.getAttribute('href');
                if (href) {
                    setTimeout(() => {
                        window.location.href = href;
                    }, 800);
                } else {
                    const form = btn.closest('form');
                    if (form) setTimeout(() => form.submit(), 800);
                }
            });
        });
    </script>
</body>

</html>