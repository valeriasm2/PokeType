<?php
return [

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

    'play' => [
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

    'ranking' => [
        'title' => 'Rànquing de Jugadors',
        'name' => 'Nom',
        'score' => 'Puntuació',
        'time' => 'Temps',
        'date' => 'Data',
        'back' => 'ESCAPE',
    ],

    'giratina' => [
        'title' => 'Atrapa en Giratina!',
        'instruction' => 'Fes clic sobre en Giratina abans que fugi!',
        'caught' => 'Has atrapat en Giratina! +100 punts 🎉',
        'redirecting' => 'Redirigint al joc...',
        'bonus' => 'Bonificació Giratina',
    ],

    'admin_login' => [
        'title' => 'Accés Administrador',
        'user' => 'Usuari:',
        'pass' => 'Contrasenya:',
        'enter' => 'Entrar',
        'error' => 'Credencials incorrectes'
    ],

    'admin_index' => [
        'title' => "Panell d'Administració",
        'create' => 'Crear frase',
        'edit' => 'Editar frase',
        'delete' => 'Eliminar frase',
        'logout' => 'Tancar sessió',
        'list_sentences' => 'Llistar frases',
        'hide_sentences' => 'Ocultar frases',
        'difficulty' => 'Mostra segons nivell de dificultat:',
        'paginator' => 'Següent',
        'levels' => ['facil'=>'Fàcil','normal'=>'Normal','dificil'=>'Difícil'],
        'select_level' => 'Selecciona un nivell',
    ],

    'messages' => [
        'frase_eliminada' => 'Frase eliminada correctament.',
        'error_datos' => 'Error: dades incompletes per eliminar la frase.',
        'error_archivo_no_encontrado' => 'Error: fitxer de frases no trobat.',
        'error_permiso_escritura' => 'Error: sense permís d\'escriptura al fitxer.',
        'error_json' => 'Error: fitxer de frases mal format.',
        'error_frase_no_encontrada' => 'Error: frase no trobada.',
        'error_guardado' => 'Error: no s\'ha pogut guardar el fitxer.',
    ],

    'admin_create' => [
        'title'        => 'Crear Nova Frase',
        'text'         => 'Text de la frase:',
        'image'        => 'Nom de la imatge:',
        'difficulty'   => 'Dificultat:',
        'save'         => 'Desar',
        'back'         => 'Tornar',
        'select_level' => 'Selecciona un nivell',
        'success'      => 'Frase afegida correctament.',
        'success_lang' => 'Frase afegida correctament a l\'idioma: {lang}',
        'info_lang'    => 'La frase es guardarà a la llista de l\'idioma seleccionat:',
        'select_file'  => 'No s\'ha triat cap fitxer',
    ],

    'admin_delete' => [
        'title' => 'X',
        'confirm' => 'Segur que vols eliminar aquesta frase?',
        'yes' => 'Sí',
        'no' => 'No',
    ],

    'error403' => [
        'title' => '403 – Accés Denegat',
        'message' => 'No tens permís per accedir a aquesta pàgina.',
        'back' => 'Tornar a l\'inici',
    ],

    'error404' => [
        'title' => '404 – Pàgina no trobada',
        'message' => 'La pàgina que busques no existeix.',
        'back' => 'Tornar a l\'inici',
    ],

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'lang_names' => ['ca'=>'Català','es'=>'Español','en'=>'English'],
];
