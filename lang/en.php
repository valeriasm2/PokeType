<?php
return [
    'lang_names' => ['ca' => 'Català', 'es' => 'Español', 'en' => 'English'],

    'index' => [
        'welcome' => 'Welcome to Poketype!',
        'description' => 'A game to learn Pokémon types and improve your typing speed.',
        'name_label' => 'Name:',
        'difficulty' => 'Difficulty:',
        'difficulty_facil' => 'Easy',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Hard',
        'play' => 'Play',
        'logout' => 'Log out',
        'error_empty' => '⚠️ Name field cannot be empty',
        'permadeath_label' => 'Permadeath mode',
        'permadeath_info' => "Permadeath Mode:\nIf you enable it, you only have 5 lives and the game ends when you spend them. You may receive a bonus for playing in this mode.",
        'permadeath_confirm' => 'You have selected Permadeath Mode: only 5 lives. Do you want to continue?',
        'noscript' => '⚠️ This game requires JavaScript to work. Please enable JavaScript in your browser. ⚠️',
        'language' => 'Language:',
    ],

    'play' => [
        'difficulty_label' => 'Selected difficulty',
        'difficulty_facil' => 'Easy',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Hard',
        'timer' => '⏱',
        'countdown_start' => 'Starting...',
        'progress' => 'Sentence %d of %d',
        'phraseCompleted' => '✅ Sentence completed!',
        'back' => 'Back',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀',
        'write_phrase' => 'WRITE THE FOLLOWING PHRASE:',
        'permadeath_off' => '(permadeath off)',
        'permadeath_warning' => '⚠️ {lives} lives left',
        'permadeath_gameover' => '💀 Permadeath: no lives left. Game over.',
    ],

    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Game results:',
        'hits' => 'Hits',
        'difficultyBonus' => 'Difficulty bonus',
        'bonusGiratina' => 'Giratina bonus',
        'timeBonus' => 'Time bonus',
        'comboMultiplier' => 'Combo multiplier',
        'totalTime' => 'Total time',
        'finalScore' => 'Final score',
        'scoreUnit' => 'points',
        'retry' => 'Play again',
        'save' => 'Save score?',
        'yes' => 'Yes',
        'no' => 'No',
        'bonus' => 'Permadeath bonus applied',
        'permadeath_dead' => 'Permadeath: no lives left. Bonus not applied.',
        'permadeath_completed' => 'Permadeath enabled: game completed in permadeath.',
    ],

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'ranking' => [
        'title' => 'Players Ranking',
        'name' => 'Name',
        'score' => 'Score',
        'time' => 'Time',
        'date' => 'Date',
        'back' => 'ESCAPE',
    ],

    'admin' => [
        'title' => 'Admin Panel',
        'welcome' => 'Welcome',
        'language_label' => 'Language:',
        'list' => 'List sentences',
        'hide' => 'Hide sentences',
        'add_sentence' => 'Add sentence',
        'logout' => 'Log out',
        'filter_by_level' => 'Show by difficulty:',
        'table_phrase' => 'Sentence',
        'table_image' => 'Image',
        'table_delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete this sentence?',
        'pagination_prev' => '« Previous',
        'pagination_next' => 'Next »',
        'pagination_page_of' => 'Page {current} of {total}',
        'error_read' => 'Error reading or decoding the phrases file.',
        'msgs' => [
            'frase_eliminada' => 'Sentence deleted successfully.',
            'error_datos' => 'Error: incomplete data to delete the sentence.',
            'error_archivo_no_encontrado' => 'Error: phrases file not found.',
            'error_permiso_escritura' => 'Error: no write permission on the file.',
            'error_json' => 'Error: malformed phrases file.',
            'error_frase_no_encontrada' => 'Error: sentence not found.',
            'error_guardado' => 'Error: could not save the file.'
        ],
        'create' => [
            'title' => 'Add Sentence',
            'back_to_panel' => 'Back to panel',
            'difficulty_label' => 'Difficulty level:',
            'difficulty_placeholder' => 'Select a level',
            'phrase_label' => 'Sentence:',
            'image_label' => 'Image (optional):',
            'submit_add' => 'Add sentence',
            'errors' => [
                'required' => 'Error: you must select a level and write a sentence.',
                'file_malformed' => 'Error: malformed phrases file.',
                'save_error' => 'Error: could not save the file.',
                'image_error' => 'Error: could not save the image.'
            ]
        ],
        'login' => [
            'title' => 'Admin Login',
            'user_label' => 'User:',
            'pass_label' => 'Password:',
            'enter' => 'Enter',
            'errors' => [
                'empty' => 'All fields are required.',
                'user' => 'User not found.',
                'pass' => 'Incorrect password.',
                'server' => 'Could not read server credentials.'
            ]
        ]
    ],
];
