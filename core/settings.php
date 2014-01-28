<?php

header('Content-Type: text/html; charset=utf-8');
header("Set-Cookie: name=value; httpOnly");

DEFINE('HTACCESS_FILE', '.htaccess');
DEFINE('SETTINGS_FILE', 'config.php');

define('CACHE_ADMIN', 'off');               // Don't serve the cache if the user is admin
DEFINE('CACHE_POST', 'off');                // Cache the post
DEFINE('CACHE_NOTE', 'off');                // Cache the index

ini_set('display_errors', false);           // Display errors if there are any.

define('PAGINATION_ON_OFF', "on");          // Activate pagination system if it is on
define('INFINITE_SCROLL', "on");            // Infinite scroll works only if pagination is on.
define('POSTS_PER_PAGE', 4);

define('POST_DIR', 'posts/');
define('CACHE_DIR', 'cache/');
define('FILE_EXT', '.md');

if (glob(POST_DIR . '*.md') != false)
    define('POSTS_DIR', './posts/');
else
    define('POSTS_DIR', './posts/welcome/');

if(file_exists(SETTINGS_FILE)) {
    require_once SETTINGS_FILE;

    // Definitions from the included configs above.
    define('BLOG_EMAIL', $blog_email);
    define('BLOG_TWITTER', $blog_twitter);
    define('BLOG_GOOGLE', $blog_google);
    define('BLOG_FACEBOOK', $blog_facebook);
    define('BLOG_FLATTR', $blog_flattr);
    define('BLOG_URL', $blog_url);
    define('BLOG_TITLE', $blog_title);
    define('BLOG_LANGUAGE', $blog_language);
    define('LANGUAGE_RSS', $language_rss);
    define('META_DESCRIPTION', $meta_description);
    define('INTRO_TITLE', $intro_title);
    define('INTRO_TEXT', $intro_text);
    define('PASSWORD', $password);
    define('HEADER_INJECT', stripslashes($header_inject));
    define('FOOTER_INJECT', stripslashes($footer_inject));
    define('ACTIVE_TEMPLATE', $template);

    define('IS_HOME', get_home());

    // Get the active template directory.
    $template_dir     = './templates/' . ACTIVE_TEMPLATE . '/';
    $base_dir_url     = BLOG_URL . 'templates/base/';
    $template_dir_url = BLOG_URL . 'templates/' . ACTIVE_TEMPLATE . '/';
    $index_file     = $template_dir . 'note.php';
    $post_file      = $template_dir . 'post.php';
    $posts_file     = $template_dir . 'posts.php';
    $not_found_file = $template_dir . '404.php';
}

$feed_max_items = '10';
$date_format = 'F jS, Y';
$error_title = 'Sorry, But That&#8217;s Not Here';
$error_text  = 'Really sorry, but what you&#8217;re looking for isn&#8217;t here. Click the button below to find something else that might interest you.';

$category = NULL;
$tag      = NULL;
$filename = NULL;

// Password hashing via phpass.
$hasher = new PasswordHash(8, FALSE);
$login_error = '';
