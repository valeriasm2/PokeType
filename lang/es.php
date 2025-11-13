<?php
return [
    'lang_names' => ['ca' => 'Català', 'es' => 'Español', 'en' => 'English'],

    'index' => [
        'welcome' => '¡Bienvenido a Poketype!',
        'description' => 'Un juego para aprender los tipos de Pokémon y mejorar tu velocidad de escritura.',
        'name_label' => 'Nombre:',
        'difficulty' => 'Dificultad:',
        'difficulty_facil' => 'Fácil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'play' => 'Jugar',
        'logout' => 'Cerrar sesión',
        'error_empty' => '⚠️ El campo nombre no puede estar vacío',
        'permadeath_label' => 'Modo permadeath',
        'permadeath_info' => "Modo Permadeath:\nSi lo activas solo tienes 5 vidas y la partida termina cuando las gastes. Puedes recibir un bonus por jugar en este modo.",
        'permadeath_confirm' => 'Has seleccionado Modo Permadeath: solo 5 vidas. ¿Quieres continuar?',
        'noscript' => '⚠️ Este juego necesita JavaScript para funcionar. Por favor, habilita JavaScript en tu navegador. ⚠️',
        'language' => 'Idioma:',
    ],

    'play' => [
        'difficulty_label' => 'Dificultad seleccionada',
        'difficulty_facil' => 'Fácil',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Difícil',
        'timer' => '⏱',
        'countdown_start' => 'Empezando...',
        'progress' => 'Frase %d de %d',
        'phraseCompleted' => '✅ ¡Frase completada!',
        'back' => 'Atrás',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀',
        'write_phrase' => 'ESCRIBE LA SIGUIENTE FRASE:',
        'permadeath_off' => '(permadeath desactivado)',
        'permadeath_warning' => '⚠️ Quedan {lives} vidas',
        'permadeath_gameover' => '💀 Permadeath: no quedan vidas. Fin de la partida.',
    ],

    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Resultado de la partida:',
        'hits' => 'Aciertos',
        'difficultyBonus' => 'Bonus por dificultad',
        'bonusGiratina' => 'Bonus Giratina',
        'timeBonus' => 'Bonus por tiempo',
        'comboMultiplier' => 'Multiplicador de combo',
        'totalTime' => 'Tiempo total',
        'finalScore' => 'Puntuación final',
        'scoreUnit' => 'puntos',
        'retry' => 'Jugar de nuevo',
        'save' => '¿Guardar puntuación?',
        'yes' => 'Sí',
        'no' => 'No',
        'bonus' => 'Bonus permadeath aplicado',
        'permadeath_dead' => 'Permadeath: te quedaste sin vidas. No se aplica el bonus.',
        'permadeath_completed' => 'Permadeath activado: partida completada en permadeath.',
    ],

    'hotkeys' => [
        'play' => 'j',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'ranking' => [
        'title' => 'Ranking de jugadores',
        'name' => 'Nombre',
        'score' => 'Puntos',
        'time' => 'Tiempo',
        'date' => 'Fecha',
        'back' => 'ESCAPE',
    ],

    'admin' => [
        'title' => 'Panel de Administrador',
        'welcome' => 'Bienvenido',
        'language_label' => 'Idioma:',
        'list' => 'Listar frases',
        'hide' => 'Ocultar frases',
        'add_sentence' => 'Añadir frase',
        'logout' => 'Salir',
        'filter_by_level' => 'Mostrar por nivel de dificultad:',
        'table_phrase' => 'Frase',
        'table_image' => 'Foto',
        'table_delete' => 'Borrar',
        'confirm_delete' => '¿Seguro que quieres eliminar esta frase?',
        'pagination_prev' => '« Anterior',
        'pagination_next' => 'Siguiente »',
        'pagination_page_of' => 'Página {current} de {total}',
        'error_read' => 'Error al leer o decodificar el archivo de frases.',
        'msgs' => [
            'frase_eliminada' => 'Frase eliminada correctamente.',
            'error_datos' => 'Error: datos incompletos para eliminar la frase.',
            'error_archivo_no_encontrado' => 'Error: archivo de frases no encontrado.',
            'error_permiso_escritura' => 'Error: sin permiso de escritura en el archivo.',
            'error_json' => 'Error: archivo de frases mal formado.',
            'error_frase_no_encontrada' => 'Error: frase no encontrada.',
            'error_guardado' => 'Error: no se pudo guardar el archivo.'
        ],
        'create' => [
            'title' => 'Añadir Frase',
            'back_to_panel' => 'Volver al panel',
            'difficulty_label' => 'Nivel de dificultad:',
            'difficulty_placeholder' => 'Selecciona un nivel',
            'phrase_label' => 'Frase:',
            'image_label' => 'Imagen (opcional):',
            'submit_add' => 'Añadir frase',
            'errors' => [
                'required' => 'Error: debes seleccionar un nivel y escribir una frase.',
                'file_malformed' => 'Error: archivo de frases mal formado.',
                'save_error' => 'Error: no se pudo guardar el archivo.',
                'image_error' => 'Error: no se pudo guardar la imagen.'
            ]
        ],
        'login' => [
            'title' => 'Login Administrador',
            'user_label' => 'Usuario:',
            'pass_label' => 'Contraseña:',
            'enter' => 'Entrar',
            'errors' => [
                'empty' => 'Todos los campos son obligatorios.',
                'user' => 'Usuario no encontrado.',
                'pass' => 'Contraseña incorrecta.',
                'server' => 'No se pudieron leer las credenciales del servidor.'
            ]
        ]
    ],
];
