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
        'difficulty_label' => 'Dificultad seleccionada',
        'difficulty_facil' => 'Fácil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'timer' => '⏱',
        'countdown_start' => 'Comenzando...',
        'progress' => 'Frase %d de %d',
        'phraseCompleted' => '✅ ¡Frase completada!',
        'back' => 'Volver',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀'
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
    /* ------------ GIRATINA.PHP ------------ */
    'giratina' => [
        'title' => '¡Atrapa a Giratina!',
        'instruction' => '¡Haz clic sobre Giratina antes de que escape!',
        'caught' => '¡Has atrapado a Giratina! +100 puntos 🎉',
        'redirecting' => 'Redirigiendo al juego...',
        'bonus' => 'Bonus Giratina',
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
        'title' => "Panel de Administrador",
        'create' => 'Agregar frase',
        'edit' => 'Editar frase',
        'delete' => 'Borrar frase',
        'logout' => 'Cerrar sesión',
        'list_sentences' => 'Listar frases',
        'hide_sentences' => 'Ocultar frases',
        'difficulty' => 'Mostrar según nivel de dificultad:',
        'paginator'      => 'Siguiente',
    ],
    'messages' => [
        'frase_eliminada' => 'Frase eliminada correctamente.',
        'error_datos' => 'Error: datos incompletos para eliminar la frase.',
        'error_archivo_no_encontrado' => 'Error: archivo de frases no encontrado.',
        'error_permiso_escritura' => 'Error: sin permiso de escritura en el archivo.',
        'error_json' => 'Error: archivo de frases mal formado.',
        'error_frase_no_encontrada' => 'Error: frase no encontrada.',
        'error_guardado' => 'Error: no se pudo guardar el archivo.',
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
