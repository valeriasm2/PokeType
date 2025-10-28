<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Atrapa a Giratina</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="laberinto-bg">
  <div class="giratina-container">
    <h1>¡Atrapa a Giratina antes de que escape!</h1>
    <div id="giratina"></div>

    <!-- Contenedor del resultado limpio -->
    <div id="resultado">
      <img src="https://media.tenor.com/0GRl16naN8YAAAAj/pokemon-nintendo.gif" alt="Giratina atrapado">
      <p>¡Has atrapado a Giratina! +999 puntos 🎉</p>
    </div>
  </div>

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

    const intervalo = setInterval(moverGiratina, 700);

    giratina.addEventListener('click', () => {
      atrapado = true;
      clearInterval(intervalo);
      giratina.style.display = 'none';
      resultado.classList.add('mostrar'); // mostramos el contenedor suavemente

      setTimeout(() => {
        window.location.href = 'play.php?bonus=100';
      }, 5000);
    });

    moverGiratina();
  </script>
</body>
</html>
