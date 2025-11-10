<?php
return [

    /* ------------ INDEX.PHP ------------ */
    'index' => [
        'welcome' => '¡Bienvenido a Poketype!',
        'description' => 'Un juego para aprender los tipos de Pokémon y mejorar tu velocidad de escritura.',
        'name_label' => 'Nombre:',
        'difficulty' => 'Dificultad:',
        'difficulty_facil' => 'Fácil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'play' => 'Jugar',
        'error_empty' => '⚠️ El campo nombre no puede estar vacío',
        'logout' => 'Cerrar sesión',
    ],

    /* ------------ PLAY.PHP ------------ */
    'play' => [
        'difficulty' => 'Dificultad',
        'phraseCompleted' => '✅ ¡Frase completada!',
        'back' => 'Volver',
        'countdown_start' => 'Comenzando...',
        'escape' => 'ESCAPE'
    ],

    /* ------------ GAMEOVER.PHP ------------ */
    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Resultado de la partida:',
        'hits' => 'Aciertos',
        'difficultyBonus' => 'Bonus por dificultad',
        'bonusGiratina' => 'Bonus Giratina',
        'timeBonus' => 'Bonus por tiempo',
        'totalTime' => 'Tiempo total',
        'finalScore' => 'Puntuación final',
        'scoreUnit' => 'puntos',
        'retry' => 'Jugar de nuevo',
        'save' => '¿Guardar puntuación?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    /* ------------ RANKING.PHP ------------ */
    'ranking' => [
        'title' => 'Ranking de Jugadores',
        'name' => 'Nombre',
        'score' => 'Puntuación',
        'time' => 'Tiempo',
        'date' => 'Fecha',
        'back' => 'ESCAPE',
    ],

    /* ------------ ADMIN/LOGIN.PHP ------------ */
    'admin_login' => [
        'title' => 'Acceso Administrativo',
        'username' => 'Usuario:',
        'password' => 'Contraseña:',
        'login' => 'Entrar',
        'error' => 'Credenciales incorrectas',
    ],

    /* ------------ ADMIN/INDEX.PHP ------------ */
    'admin_index' => [
        'title' => 'Panel de Administración',
        'create' => 'Crear frase',
        'edit' => 'Editar frase',
        'delete' => 'Eliminar frase',
        'logout' => 'Cerrar sesión',
    ],

    /* ------------ ADMIN/CREATE_SENTENCE.PHP ------------ */
    'admin_create' => [
        'title' => 'Crear Nueva Frase',
        'text' => 'Texto de la frase:',
        'image' => 'Nombre de la imagen:',
        'difficulty' => 'Dificultad:',
        'save' => 'Guardar',
        'back' => 'Volver',
    ],

    /* ------------ ADMIN/DELETE_SENTENCE.PHP ------------ */
    'admin_delete' => [
        'title' => 'Eliminar Frase',
        'confirm' => '¿Seguro que deseas borrar esta frase?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    /* ------------ ERRORS/403 y 404 ------------ */
    'error403' => [
        'title' => '403 – Acceso denegado',
        'message' => 'No tienes permiso para acceder a esta página.',
        'back' => 'Volver al inicio',
    ],

    'error404' => [
        'title' => '404 – Página no encontrada',
        'message' => 'La página que buscas no existe.',
        'back' => 'Volver al inicio',
    ],

    /* ------------ TECLAS RÁPIDAS ------------ */
    'hotkeys' => [
        'play' => 'j',
        'save' => 's',
        'no' => 'n',
        'back' => 'esc'
    ]
];
