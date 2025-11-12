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
        'seconds' => 's',
        'countdown_start' => 'Començant...',
        'progress' => 'Frase %d de %d',
        'phraseCompleted' => 'Frase completada!',
        'back' => 'Tornar',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀',
        'write_phrase' => 'ESCRIU LA SEGÜENT FRASE:',
    ],

    'gameover' => [
        'title'           => 'Game Over',
        'results'         => 'Resultado de la partida:',
        'hits'            => 'Aciertos',
        'difficultyBonus' => 'Bonus por dificultad',
        'bonusGiratina'   => 'Bonus Giratina',
        'scoreUnit'       => 'puntos',
        'finalScore'      => 'Puntuación final',
        'yes'             => 'Sí',
        'no'              => 'No',
        // Textos de permadeath
        'permadeath_dead'   => '⚠️ Modo Permadeath activado: la partida terminó porque te quedaste sin vidas. No se aplica el bonus.',
        'permadeath_alive'  => '⚠️ Modo Permadeath activado: esta partida se completó en permadeath.',
        'permadeath_bonus'  => 'Bonus permadeath aplicado',
        // Otros textos si quieres traducir también
        'timeBonus'       => 'Bonus por tiempo',
        'comboMultiplier' => 'Multiplicador de combo',
        'totalTime'       => 'Tiempo total',
        'saveRecord'      => 'Guardar récord',
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
        'delete_phrase' => 'X',
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

    403 => [
        'code' => '403',
        'title' => 'Accés Prohibit',
        'msg1' => 'No pots visitar directament aquesta pàgina.',
        'msg2' => 'Un Pokémon guardià bloqueja el pas per protegir el joc.',
        'btn' => 'Tornar a l\'inici'
    ],
    
    404 => [
        'code' => '404',
        'title' => 'Pàgina no trobada',
        'msg1' => 'La ruta que busques no existeix. Sembla que un Pokémon trapella se la va emportar.',
        'msg2' => '',
        'btn' => 'Tornar a l\'inici',
        'btn2' => 'Veure rànquing'
    ],

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'lang_names' => ['ca'=>'Català','es'=>'Español','en'=>'English'],
];
