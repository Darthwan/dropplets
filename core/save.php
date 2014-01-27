<?php

// Get submitted setup values.
if(isset($_POST["blog_email"]))        $blog_email = secure($_POST["blog_email"]);
if(isset($_POST["blog_twitter"]))      $blog_twitter = secure($_POST["blog_twitter"]);
if(isset($_POST["blog_google"]))       $blog_google = secure($_POST["blog_google"]);
if(isset($_POST["blog_facebook"]))     $blog_facebook = secure($_POST["blog_facebook"]);
if(isset($_POST["blog_flattr"]))       $blog_flattr = secure($_POST["blog_flattr"]);
if(isset($_POST["blog_url"]))          $blog_url = secure($_POST["blog_url"]);
if(isset($_POST["blog_title"]))        $blog_title = secure($_POST["blog_title"]);
if(isset($_POST["blog_language"]))     $blog_language = secure($_POST["blog_language"]);
if(isset($_POST["language_rss"]))      $language_rss = secure($_POST["language_rss"]);
if(isset($_POST["meta_description"]))  $meta_description = secure($_POST["meta_description"]);
if(isset($_POST["intro_title"]))       $intro_title = secure($_POST["intro_title"]);
if(isset($_POST["intro_text"]))        $intro_text = secure($_POST["intro_text"]);
if(isset($_POST["template"]))          $template = secure($_POST["template"]);

if(isset($_POST["header_inject"]))
    $header_inject = secure($_POST["header_inject"]);
else
    $header_inject = "";

if(isset($_POST["footer_inject"]))
    $footer_inject = secure($_POST["footer_inject"]);
else
    $footer_inject = "";

// There must always be a $password, but it can be changed optionally in the
// settings, so you might not always get it in $_POST.
if (!isset($password) || !empty($_POST["password"]))
    $password = $hasher->HashPassword($_POST["password"]);

// Output submitted setup values.
$config[] = "<?php";
$config[] = "// See core/settings.php for more";
$config[] = settingsFormat("blog_email", $blog_email);
$config[] = settingsFormat("blog_twitter", $blog_twitter);
$config[] = settingsFormat("blog_google", $blog_google);
$config[] = settingsFormat("blog_facebook", $blog_facebook);
$config[] = settingsFormat("blog_flattr", $blog_flattr);
$config[] = settingsFormat("blog_url", $blog_url);
$config[] = settingsFormat("blog_title", $blog_title);
$config[] = settingsFormat("blog_language", $blog_language);
$config[] = settingsFormat("language_rss", $language_rss);
$config[] = settingsFormat("meta_description", $meta_description);
$config[] = settingsFormat("intro_title", $intro_title);
$config[] = settingsFormat("intro_text", $intro_text);
$config[] = settingsFormatPass("password", $password);
$config[] = settingsFormat("header_inject", $header_inject);
$config[] = settingsFormat("footer_inject", $footer_inject);
$config[] = settingsFormat("template", $template);

// Create the settings file.
file_put_contents(SETTINGS_FILE, implode("\n", $config));

// Generate the .htaccess file on initial setup only.
if(!file_exists(HTACCESS_FILE)) {
    // Get subdirectory
    $dir = str_replace('core/save.php', '', $_SERVER["REQUEST_URI"]);

    // Parameters for the htaccess file.
    $htaccess[] = "AddDefaultCharset utf-8";
    $htaccess[] = "AddType text/plain .md";
    $htaccess[] = "Options -Indexes";
    $htaccess[] = "# Pretty Permalinks";
    $htaccess[] = "RewriteRule ^(images)($|/) - [L]";
    $htaccess[] = "RewriteCond %{REQUEST_URI} !^action=logout [NC]";
    $htaccess[] = "RewriteCond %{REQUEST_URI} !^action=login [NC]";
    $htaccess[] = "Options +FollowSymLinks -MultiViews";
    $htaccess[] = "RewriteEngine on";
    $htaccess[] = "RewriteBase " . $dir;
    $htaccess[] = "RewriteCond %{REQUEST_URI} !index\.php";
    $htaccess[] = "RewriteCond %{REQUEST_FILENAME} !-f";
    $htaccess[] = "RewriteRule ^(.*)$ index.php?filename=$1 [NC,QSA,L]";

    // Generate the .htaccess file.
    file_put_contents(HTACCESS_FILE, implode("\n", $htaccess));
}

if (!file_exists(CACHE_DIR)) {
    mkdir(CACHE_DIR, 0755, TRUE);
}

header("Location: " . $blog_url);
