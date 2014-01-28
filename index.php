<?php

// @TODO : Gravatar for the picture profile
// @TODO : passer à true pour les erreurs
// @TODO : gestion des erreurs lors d'une connexion
// @TODO : permettre d'activer le cache via l'administration
// @TODO : possibilité de tags
// @TODO : scheduled publishing (see more on https://github.com/Circa75/dropplets/pull/300)
// @TODO : language support
// @TODO : fix bug user action

ini_set('session.use_cookies', 1);          // Use cookies to store session.
ini_set('session.use_only_cookies', 1);     // Force cookies for session (phpsessionID forbidden in URL)
ini_set('session.use_trans_sid', false);    // Prevent php to use sessionID in URL if cookies are disabled.

session_start();

require_once 'core/class.rain.tpl.php';
require_once 'core/functions.php';
require_once 'core/settings.php';

if(!file_exists(SETTINGS_FILE))
    require_once "core/install.php";

if (isset($_GET['action']))
    require_once "core/action.php";

if(isset($_POST) && !empty($_POST))
    require_once "core/save.php";

if(isset($_GET['filename']) && ($_GET['filename'] == 'rss' || $_GET['filename'] == 'atom')) {
    $filename = $_GET['filename'];
} else if(isset($_GET['filename']) && !empty($_GET['filename'])) {
    $filename = explode('/', secure($_GET['filename']));

    if(count($filename) >= 2 && $filename[count($filename) - 2] == "category") {
        $category = $filename[count($filename) - 1];
        $filename = NULL;
    } else if(count($filename) >= 2 && $filename[count($filename) - 2] == "tag") {
        $tag = $filename[count($filename) - 1];
        $filename = NULL;
    } else {
        $filename = POSTS_DIR . $filename[count($filename) - 1] . FILE_EXT; // Individual Post
    }
}

if ($filename == NULL)
    require_once "core/getAllPosts.php"; // Home page (all posts)
else if ($filename == 'rss' || $filename == 'atom')
    require_once "core/rssFeed.php"; // RSS feed
else
    require_once "core/getSinglePost.php"; // Single post page
