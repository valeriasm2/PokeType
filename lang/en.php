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
        'error_empty' => '⚠️ Name field cannot be empty',
        'logout' => 'Log out',
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
        'easter_egg' => '👀'
    ],

    'gameover' => [
        'title' => 'Game Over',
        'results' => 'Game results:',
        'hits' => 'Hits',
        'difficultyBonus' => 'Difficulty bonus',
        'bonusGiratina' => 'Giratina bonus',
        'timeBonus' => 'Time bonus',
        'totalTime' => 'Total time',
        'finalScore' => 'Final score',
        'scoreUnit' => 'points',
        'retry' => 'Play again',
        'save' => 'Save score?',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    'ranking' => [
        'title' => 'Players Ranking',
        'name' => 'Name',
        'score' => 'Score',
        'time' => 'Time',
        'date' => 'Date',
        'back' => 'ESCAPE',
    ],

    'giratina' => [
        'title' => 'Catch Giratina!',
        'instruction' => 'Click on Giratina before it escapes!',
        'caught' => 'You caught Giratina! +100 points 🎉',
        'redirecting' => 'Redirecting to the game...',
        'bonus' => 'Giratina Bonus',
    ],

    'admin_login' => [
        'title' => 'Admin Access',
        'user' => 'Username:',
        'pass' => 'Password:',
        'enter' => 'Login',
        'error' => 'Incorrect credentials'
    ],

    'admin_index' => [
        'title' => "Administration Panel",
        'create' => 'Create sentence',
        'edit' => 'Edit sentence',
        'delete' => 'Delete sentence',
        'logout' => 'Log out',
        'list_sentences' => 'List sentences',
        'hide_sentences' => 'Hide sentences',
        'difficulty' => 'Show by difficulty level:',
        'paginator' => 'Next',
        'levels' => ['facil'=>'Easy','normal'=>'Normal','dificil'=>'Hard'],
        'select_level' => 'Select a level',
    ],

    'messages' => [
        'frase_eliminada' => 'Sentence deleted successfully.',
        'error_datos' => 'Error: incomplete data to delete the sentence.',
        'error_archivo_no_encontrado' => 'Error: sentences file not found.',
        'error_permiso_escritura' => 'Error: no write permission on file.',
        'error_json' => 'Error: malformed sentences file.',
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
        'info_lang'    => 'The sentence will be saved in the list for the selected language:',
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
        'msg1' => "You can't visit this page directly.",
        'msg2' => 'A guardian Pokémon blocks the way to protect the game.',
        'btn' => 'Return to Home'
    ],
    
    404 => [
        'code' => '404',
        'title' => 'Page Not Found',
        'msg1' => "The path you are looking for does not exist. It seems a mischievous Pokémon has taken it away.",
        'msg2' => '',
        'btn' => 'Return to Home',
        'btn2' => 'View Ranking'
    ],    

    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ],

    'lang_names' => ['ca'=>'Català','es'=>'Español','en'=>'English'],
];
