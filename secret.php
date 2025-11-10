<?php
session_start();

// Detectar idioma: si ya hay en sesión, usamos ese; si no, español por defecto
$lang_code = isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es';
$lang = include "lang/{$lang_code}.php";

// Capturamos los parámetros del jugador para conservarlos
$name = isset($_GET['name']) ? htmlspecialchars($_GET['name']) : 'Jugador';
$difficulty = isset($_GET['difficulty']) ? htmlspecialchars($_GET['difficulty']) : 'facil';
?>
<!DOCTYPE html>
<html lang="<?= $lang_code ?>">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $lang['giratina']['title'] ?></title>
    <link rel="stylesheet" href="styles.css?<?= time(); ?>">
  </head>
  <body class="laberinto-bg">
    <div class="giratina-container">
      <h1><?= $lang['giratina']['instruction'] ?></h1>
      <div id="giratina"></div>

      <div id="resultado">
        <img src="https://media.tenor.com/0GRl16naN8YAAAAj/pokemon-nintendo.gif" alt="<?= $lang['giratina']['title'] ?>">
        <p><?= $lang['giratina']['caught'] ?></p>
      </div>
    </div>

    <script src="utils/music2.js"></script>
    <script>
      const giratina = document.getElementById('giratina');
      const resultado = document.getElementById('resultado');
      let atrapado = false;

      function moverGiratina() {
        if (atrapado) return;
        const limiteX = window.innerWidth - 150;
        const limiteY = window.innerHeight - 150;
        const x = Math.random() * limiteX;
        const y = Math.random() * limiteY;
        giratina.style.left = `${x}px`;
        giratina.style.top = `${y}px`;
      }

      const intervalo = setInterval(moverGiratina, 600);

      giratina.addEventListener('click', () => {
        atrapado = true;
        clearInterval(intervalo);
        giratina.style.display = 'none';
        resultado.classList.add('mostrar');

        setTimeout(() => {
          // Redirigimos a play.php pasando bonus + nombre + dificultad y conservando el idioma
          const url = `play.php?bonusGiratina=100&name=<?= urlencode($name) ?>&difficulty=<?= urlencode($difficulty) ?>&lang=<?= $lang_code ?>`;
          window.location.href = url;
        }, 3000);
      });

      moverGiratina();
    </script>
  </body>
</html>
