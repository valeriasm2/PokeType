<?php
return [

    'index' => [
        'welcome' => 'Welcome to Poketype!',
        'description' => 'A game to learn Pokémon types and improve your typing speed.',
        'name_label' => 'Name:',
        'difficulty' => 'Difficulty:',
        'difficulty_facil' => 'Easy',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Hard',
        'play' => 'Play',
        'error_empty' => '⚠️ The name field cannot be empty',
        'logout' => 'Log out',
    ],

    'play' => [
        'difficulty_label' => 'Selected difficulty',
        'difficulty_facil' => 'Easy',
        'difficulty_normal' => 'Normal',
        'difficulty_dificil' => 'Hard',
        'timer' => '⏱',
        'seconds' => 's',
        'countdown_start' => 'Starting...',
        'progress' => 'Sentence %d of %d',
        'phraseCompleted' => 'Sentence completed!',
        'back' => 'Back',
        'escape' => 'ESCAPE',
        'easter_egg' => '👀',
        'write_phrase' => 'TYPE THE FOLLOWING SENTENCE:',
    ],

    'gameover' => [
        'title'           => 'Game Over',
        'results'         => 'Game results:',
        'hits'            => 'Hits',
        'difficultyBonus' => 'Difficulty bonus',
        'bonusGiratina'   => 'Giratina bonus',
        'scoreUnit'       => 'points',
        'finalScore'      => 'Final score',
        'yes'             => 'Yes',
        'no'              => 'No',
        'permadeath_dead'   => '⚠️ Permadeath mode active: the game ended because you ran out of lives. No bonus applies.',
        'permadeath_alive'  => '⚠️ Permadeath mode active: this run was completed in permadeath.',
        'permadeath_bonus'  => 'Permadeath bonus applied',
        'timeBonus'       => 'Time bonus',
        'comboMultiplier' => 'Combo multiplier',
        'totalTime'       => 'Total time',
        'saveRecord'      => 'Save record',
    ],

    'ranking' => [
        'title' => 'Players Ranking',
        'name' => 'Name',
        'score' => 'Score',
        'time' => 'Time',
        'date' => 'Date',
        'back' => 'ESCAPE',
        'combo' => 'Combo',
        'permadeath' => 'Permadeath',
        'permadeath_flag' => 'Yes',
        'permadeath_flag_no' => 'No',
    ],

    'giratina' => [
        'title' => 'Catch Giratina!',
        'instruction' => 'Click on Giratina before it escapes!',
        'caught' => 'You caught Giratina! +100 points 🎉',
        'redirecting' => 'Redirecting to the game...',
        'bonus' => 'Giratina bonus',
    ],

    'admin_login' => [
        'title' => 'Admin Access',
        'user' => 'User:',
        'pass' => 'Password:',
        'enter' => 'Enter',
        'error' => 'Invalid credentials'
    ],

    'admin_index' => [
        'title' => 'Admin Panel',
        'create' => 'Create sentence',
        'edit' => 'Edit sentence',
        'delete' => 'Delete sentence',
        'delete_phrase' => 'X',
        'logout' => 'Log out',
        'list_sentences' => 'List sentences',
        'hide_sentences' => 'Hide sentences',
        'difficulty' => 'Show by difficulty level:',
        'paginator' => 'Next',
        'levels' => ['facil'=>'Easy','normal'=>'Normal','dificil'=>'Hard'],
        'select_level' => 'Select a level',
        'photo' => 'Photo',
        'confirm_delete' => 'Delete this sentence?',
        'paginator_prev' => 'Previous',
        'paginator_next' => 'Next',
        'paginator_page' => 'Page',
    ],

    'messages' => [
        'frase_eliminada' => 'Sentence deleted successfully.',
        'error_datos' => 'Error: incomplete data to delete the sentence.',
        'error_archivo_no_encontrado' => 'Error: sentences file not found.',
        'error_permiso_escritura' => 'Error: no write permission on the file.',
        'error_json' => 'Error: sentences file is malformed.',
        'error_frase_no_encontrada' => 'Error: sentence not found.',
        'error_guardado' => 'Error: could not save the file.',
    ],

    'admin_create' => [
        'title'        => 'Create New Sentence',
        'text'         => 'Sentence text:',
        'image'        => 'Image name:',
        'difficulty'   => 'Difficulty:',
        'save'         => 'Save',
        'back'         => 'Back',
        'select_level' => 'Select a level',
        'success'      => 'Sentence added successfully.',
        'success_lang' => 'Sentence added successfully in language: {lang}',
        'info_lang'    => 'The sentence will be saved in the selected language list:',
        'select_file'  => 'No file selected',
    ],

    'admin_delete' => [
        'title' => 'X',
        'confirm' => 'Are you sure you want to delete this sentence?',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    403 => [
        'code' => '403',
        'title' => 'Access Forbidden',
        'msg1' => 'You cannot visit this page directly.',
        'msg2' => 'A guardian Pokémon blocks the path to protect the game.',
        'btn' => 'Back to home'
    ],

    404 => [
        'code' => '404',
        'title' => 'Page not found',
        'msg1' => 'The path you are looking for does not exist. A mischievous Pokémon may have taken it.',
        'msg2' => '',
        'btn' => 'Back to home',
        'btn2' => 'View ranking'
    ],

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'lang_names' => ['ca'=>'Català','es'=>'Español','en'=>'English'],
];
