<?php
return [
    'lang_names' => ['ca' => 'Català', 'es' => 'Español', 'en' => 'English'],

    'index' => [
        'welcome' => 'Benvingut a Poketype!',
        'description' => 'Un joc per aprendre els tipus de Pokémon i millorar la teva velocitat d’escriptura.',
        'name_label' => 'Nom:',
        'difficulty' => 'Dificultat:',
        'difficulty_facil' => 'Fàcil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'play' => 'Jugar',
        'logout' => 'Tancar sessió',
        'error_empty' => '⚠️ El camp nom no pot estar buit',
        'permadeath_label' => 'Mode permadeath',
        'permadeath_info' => "Mode Permadeath:\nSi l'activas només tens 5 vides i la partida s'acaba quan te les gastes. Pots rebre un bonus per jugar en aquest mode.",
        'permadeath_confirm' => 'Has seleccionat Mode Permadeath: només 5 vides. Vols continuar?',
        'noscript' => '⚠️ Aquest joc necessita JavaScript per funcionar. Si us plau, habilita JavaScript al teu navegador. ⚠️',
        'language' => 'Idioma:',
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
        'back' => 'Enrere',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀',
        'write_phrase' => 'ESCRIU LA FRASE SEGÜENT:',
        'permadeath_off' => '(permadeath desactivat)',
        'permadeath_warning' => '⚠️ Queden {lives} vides',
        'permadeath_gameover' => '💀 Permadeath: no queden vides. Fi de la partida.',
    ],

    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Resultat de la partida:',
        'hits' => 'Encerts',
        'difficultyBonus' => 'Bonus per dificultat',
        'bonusGiratina' => 'Bonus Giratina',
        'timeBonus' => 'Bonus per temps',
        'comboMultiplier' => 'Multiplicador de combo',
        'totalTime' => 'Temps total',
        'finalScore' => 'Puntuació final',
        'scoreUnit' => 'punts',
        'retry' => 'Torna a jugar',
        'save' => 'Guardar puntuació?',
        'yes' => 'Sí',
        'no' => 'No',
        'bonus' => 'Bonus permadeath aplicat',
        'permadeath_dead' => 'Permadeath: no queden vides. No s’aplica el bonus.',
        'permadeath_completed' => 'Permadeath activat: partida completada en permadeath.',
    ],

    'hotkeys' => [
        'play' => 'j',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'ranking' => [
        'title' => 'Rànking de jugadors',
        'name' => 'Jugador',
        'score' => 'Punts',
        'time' => 'Temps',
        'date' => 'Data',
        'back' => 'ESCAPE',
    ],

    'admin' => [
        'title' => "Panell d’Administrador",
        'welcome' => 'Benvingut',
        'language_label' => 'Idioma:',
        'list' => 'Llistar frases',
        'hide' => 'Ocultar frases',
        'add_sentence' => 'Afegir frase',
        'logout' => 'Tancar sessió',
        'filter_by_level' => 'Mostra segons nivell de dificultat:',
        'table_phrase' => 'Frase',
        'table_image' => 'Foto',
        'table_delete' => 'Esborra',
        'confirm_delete' => 'Segur que vols eliminar aquesta frase?',
        'pagination_prev' => '« Anterior',
        'pagination_next' => 'Següent »',
        'pagination_page_of' => 'Pàgina {current} de {total}',
        'error_read' => 'Error al llegir o decodificar el fitxer de frases.',
        'msgs' => [
            'frase_eliminada' => 'Frase eliminada correctament.',
            'error_datos' => 'Error: dades incompletes per eliminar la frase.',
            'error_archivo_no_encontrado' => 'Error: fitxer de frases no trobat.',
            'error_permiso_escritura' => 'Error: sense permís d\'escriptura al fitxer.',
            'error_json' => 'Error: fitxer de frases mal format.',
            'error_frase_no_encontrada' => 'Error: frase no trobada.',
            'error_guardado' => 'Error: no s\'ha pogut guardar el fitxer.'
        ],
        'create' => [
            'title' => 'Afegir Frase',
            'back_to_panel' => 'Tornar al panell',
            'difficulty_label' => 'Nivell de dificultat:',
            'difficulty_placeholder' => 'Selecciona un nivell',
            'phrase_label' => 'Frase:',
            'image_label' => 'Imatge (opcional):',
            'submit_add' => 'Afegir frase',
            'errors' => [
                'required' => 'Error: has de seleccionar un nivell i escriure una frase.',
                'file_malformed' => 'Error: fitxer de frases mal format.',
                'save_error' => 'Error: no s\'ha pogut guardar el fitxer.',
                'image_error' => 'Error: no s\'ha pogut guardar la imatge.'
            ]
        ],
        'login' => [
            'title' => 'Login Administrador',
            'user_label' => 'Usuari:',
            'pass_label' => 'Contrasenya:',
            'enter' => 'Entrar',
            'errors' => [
                'empty' => 'Tots els camps són obligatoris.',
                'user' => 'Usuari no trobat.',
                'pass' => 'Contrasenya incorrecta.',
                'server' => 'No s\'han pogut llegir les credencials del servidor.'
            ]
        ]
    ],
];
