document.addEventListener('DOMContentLoaded', () => {
    // ===== Música de fondo persistente =====
    let bgMusic = document.getElementById('bg-music');

    if (!bgMusic) {
        bgMusic = document.createElement('audio');
        bgMusic.id = 'bg-music';
        bgMusic.src = 'juego.mp3';
        bgMusic.loop = true;
        bgMusic.autoplay = true;
        document.body.appendChild(bgMusic);
    }

    // Ajustar volumen de fondo más suave
    bgMusic.volume = 0.2;

    // Continuar música desde localStorage si ya estaba sonando
    if (localStorage.getItem('musicPlaying') === 'true') {
        bgMusic.currentTime = parseFloat(localStorage.getItem('musicTime')) || 0;
        bgMusic.play();
    }

    // Guardar estado cada segundo
    setInterval(() => {
        localStorage.setItem('musicPlaying', !bgMusic.paused);
        localStorage.setItem('musicTime', bgMusic.currentTime);
    }, 1000);

    // ===== Sonido de clics más fuerte que la música =====
    const clickSound = document.createElement('audio');
    clickSound.id = 'click-sound';
    clickSound.src = 'vuttonfin.mp3';
    clickSound.preload = 'auto';
    clickSound.volume = 1.0; // más fuerte que la música de fondo
    document.body.appendChild(clickSound);

    // Asociar el sonido a todos los botones y enlaces importantes
    document.querySelectorAll('button, a#back-btn, a#easter-egg').forEach(elem => {
        elem.addEventListener('click', () => {
            clickSound.currentTime = 0;
            clickSound.play();
        });
    });
});
