<?php
return [

    /* ------------ INDEX.PHP ------------ */
    'index' => [
        'welcome' => 'Benvingut a Poketype!',
        'description' => 'Un joc per aprendre els tipus de Pokémon i millorar la teva velocitat d\'escriptura.',
        'name_label' => 'Nom:',
        'difficulty' => 'Dificultat:',
        'difficulty_facil' => 'Fàcil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'play' => 'Jugar',
        'error_empty' => '⚠️ El camp nom no pot estar buit',
        'logout' => 'Tancar sessió',
    ],

    /* ------------ PLAY.PHP ------------ */
    'play' => [
        // Etiqueta de dificultad
        'difficulty_label' => 'Dificultat seleccionada',
        'difficulty_facil' => 'Fàcil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'timer' => '⏱', 
        'countdown_start' => 'Començant...',
        'progress' => 'Frase %d de %d',
        'phraseCompleted' => '✅ Frase completada!',
        'back' => 'Tornar',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀'
    ],

    /* ------------ GAMEOVER.PHP ------------ */
    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Resultat de la partida:',
        'hits' => 'Encerts',
        'difficultyBonus' => 'Bonus per dificultat',
        'bonusGiratina' => 'Bonus Giratina',
        'timeBonus' => 'Bonus per temps',
        'totalTime' => 'Temps total',
        'finalScore' => 'Puntuació final',
        'scoreUnit' => 'punts',
        'retry' => 'Torna a jugar',
        'save' => 'Guardar puntuació?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    /* ------------ GIRATINA.PHP ------------ */
    'giratina' => [
        'title' => 'Atrapa en Giratina!',
        'instruction' => 'Fes clic sobre en Giratina abans que fugi!',
        'caught' => 'Has atrapat en Giratina! +100 punts 🎉',
        'redirecting' => 'Redirigint al joc...',
        'bonus' => 'Bonificació Giratina',
    ],
    
    /* ------------ RANKING.PHP ------------ */
    'ranking' => [
        'title' => 'Rànquing de Jugadors',
        'name' => 'Nom',
        'score' => 'Puntuació',
        'time' => 'Temps',
        'date' => 'Data',
        'back' => 'ESCAPE',
    ],

    /* ------------ ADMIN/LOGIN.PHP ------------ */
    'admin_login' => [
        'title' => 'Accés Administratiu',
        'username' => 'Usuari:',
        'password' => 'Contrasenya:',
        'login' => 'Entrar',
        'error' => 'Credencials incorrectes',
    ],

    /* ------------ ADMIN/INDEX.PHP ------------ */
    'admin_index' => [
        'title' => 'Panell d\'Administració',
        'create' => 'Crear frase',
        'edit' => 'Editar frase',
        'delete' => 'Eliminar frase',
        'logout' => 'Tancar sessió',
    ],

    /* ------------ ADMIN/CREATE_SENTENCE.PHP ------------ */
    'admin_create' => [
        'title' => 'Crear Nova Frase',
        'text' => 'Text de la frase:',
        'image' => 'Nom de la imatge:',
        'difficulty' => 'Dificultat:',
        'save' => 'Desar',
        'back' => 'Tornar',
    ],

    /* ------------ ADMIN/DELETE_SENTENCE.PHP ------------ */
    'admin_delete' => [
        'title' => 'Eliminar Frase',
        'confirm' => 'Segur que vols eliminar aquesta frase?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    /* ------------ ERRORS/403 y 404 ------------ */
    'error403' => [
        'title' => '403 – Accés denegat',
        'message' => 'No tens permís per accedir a aquesta pàgina.',
        'back' => 'Tornar a l\'inici',
    ],

    'error404' => [
        'title' => '404 – Pàgina no trobada',
        'message' => 'La pàgina que busques no existeix.',
        'back' => 'Tornar a l\'inici',
    ],

    /* ------------ HOTKEYS ------------ */
    'hotkeys' => [
        'play' => 'j',
        'save' => 'd',
        'no' => 'n',
        'back' => 'esc'
    ]
];
