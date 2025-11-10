<?php
return [

    /* ------------ INDEX.PHP ------------ */
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

    /* ------------ PLAY.PHP ------------ */
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

    /* ------------ GAMEOVER.PHP ------------ */
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

    /* ------------ RANKING.PHP ------------ */
    'ranking' => [
        'title' => 'Players Ranking',
        'name' => 'Name',
        'score' => 'Score',
        'time' => 'Time',
        'date' => 'Date',
        'back' => 'ESCAPE',
    ],

    /* ------------ GIRATINA.PHP ------------ */
    'giratina' => [
        'title' => 'Catch Giratina!',
        'instruction' => 'Click on Giratina before it escapes!',
        'caught' => 'You caught Giratina! +100 points 🎉',
        'redirecting' => 'Redirecting to the game...',
        'bonus' => 'Giratina Bonus',
    ],

    /* ------------ ADMIN/LOGIN.PHP ------------ */
    'admin_login' => [
        'title' => 'Admin Access',
        'username' => 'Username:',
        'password' => 'Password:',
        'login' => 'Login',
        'error' => 'Incorrect credentials',
    ],

    /* ------------ ADMIN/INDEX.PHP ------------ */
    'admin_index' => [
        'title' => 'Administration Panel',
        'create' => 'Create sentence',
        'edit' => 'Edit sentence',
        'delete' => 'Delete sentence',
        'logout' => 'Log out',
    ],

    /* ------------ ADMIN/CREATE_SENTENCE.PHP ------------ */
    'admin_create' => [
        'title' => 'Create New Sentence',
        'text' => 'Sentence text:',
        'image' => 'Image name:',
        'difficulty' => 'Difficulty:',
        'save' => 'Save',
        'back' => 'Back',
    ],

    /* ------------ ADMIN/DELETE_SENTENCE.PHP ------------ */
    'admin_delete' => [
        'title' => 'Delete Sentence',
        'confirm' => 'Are you sure you want to delete this sentence?',
        'yes' => 'Yes',
        'no' => 'No',
    ],

    /* ------------ ERRORS/403 y 404 ------------ */
    'error403' => [
        'title' => '403 – Access Denied',
        'message' => 'You do not have permission to access this page.',
        'back' => 'Return to home',
    ],

    'error404' => [
        'title' => '404 – Page Not Found',
        'message' => 'The page you are looking for does not exist.',
        'back' => 'Return to home',
    ],

    /* ------------ HOTKEYS ------------ */
    'hotkeys' => [
        'play' => 'p',
        'save' => 'y',
        'no' => 'n',
        'back' => 'esc'
    ]
];
