<?php
return [

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

    'ranking' => [
        'title' => 'Ranking de Jugadores',
        'name' => 'Nombre',
        'score' => 'Puntuación',
        'time' => 'Tiempo',
        'date' => 'Fecha',
        'back' => 'ESCAPE',
    ],

    'giratina' => [
        'title' => '¡Atrapa a Giratina!',
        'instruction' => '¡Haz clic en Giratina antes de que escape!',
        'caught' => '¡Has atrapado a Giratina! +100 puntos 🎉',
        'redirecting' => 'Redirigiendo al juego...',
        'bonus' => 'Bonus Giratina',
    ],

    'admin_login' => [
        'title' => 'Acceso Administrador',
        'user' => 'Usuario:',
        'pass' => 'Contraseña:',
        'enter' => 'Entrar',
        'error' => 'Credenciales incorrectas'
    ],

    'admin_index' => [
        'title' => "Panel de Administración",
        'create' => 'Crear frase',
        'edit' => 'Editar frase',
        'delete' => 'Eliminar frase',
        'logout' => 'Cerrar sesión',
        'list_sentences' => 'Listar frases',
        'hide_sentences' => 'Ocultar frases',
        'difficulty' => 'Mostrar según nivel de dificultad:',
        'paginator' => 'Siguiente',
        'levels' => ['facil'=>'Fácil','normal'=>'Normal','dificil'=>'Difícil'],
        'select_level' => 'Selecciona un nivel',
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

    'admin_create' => [
        'title'        => 'Crear Nueva Frase',
        'text'         => 'Texto de la frase:',
        'image'        => 'Nombre de la imagen:',
        'difficulty'   => 'Dificultad:',
        'save'         => 'Guardar',
        'back'         => 'Volver',
        'select_level' => 'Selecciona un nivel',
        'success'      => 'Frase agregada correctamente.',
        'success_lang' => 'Frase agregada correctamente en el idioma: {lang}',
        'info_lang'    => 'La frase se guardará en la lista del idioma seleccionado:',
        'select_file'  => 'No se ha seleccionado ningún archivo',
    ],

    'admin_delete' => [
        'title' => 'X',
        'confirm' => '¿Seguro que quieres eliminar esta frase?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    'error403' => [
        'title' => '403 – Acceso Denegado',
        'message' => 'No tienes permiso para acceder a esta página.',
        'back' => 'Volver al inicio',
    ],

    'error404' => [
        'title' => '404 – Página no encontrada',
        'message' => 'La página que buscas no existe.',
        'back' => 'Volver al inicio',
    ],

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'lang_names' => ['ca'=>'Català','es'=>'Español','en'=>'English'],
];
