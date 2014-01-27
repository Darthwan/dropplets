<?php

// @TODO : Gravatar for the picture profile
// @TODO : passer à true pour les erreurs
// @TODO : gestion des erreurs lors d'une connexion
// @TODO : permettre d'activer le cache via l'administration
// @TODO : possibilité de tags
// @TODO : scheduled publishing (see more on https://github.com/Circa75/dropplets/pull/300)
// @TODO : language support

ini_set('session.use_cookies', 1);          // Use cookies to store session.
ini_set('session.use_only_cookies', 1);     // Force cookies for session (phpsessionID forbidden in URL)
ini_set('session.use_trans_sid', false);    // Prevent php to use sessionID in URL if cookies are disabled.

session_start();

require_once 'core/class.rain.tpl.php';
require_once 'core/functions.php';
require_once 'core/settings.php';

if(!file_exists(SETTINGS_FILE))
    require_once "core/install.php";

/*-----------------------------------------------------------------------------------*/
/* User Machine
/*-----------------------------------------------------------------------------------*/

// Password hashing via phpass.
$hasher = new PasswordHash(8,FALSE);

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    switch ($action) {
        // Logging in.
        case 'login':
            if ((isset($_POST['password'])) && $hasher->CheckPassword($_POST['password'], PASSWORD)) {
                $_SESSION['user'] = true;

                // Redirect if authenticated.
                header('Location: ./');
                exit();
            } else {
                // Display error if not authenticated.
                $login_error = 'Nope, try again!';
            }
            break;
        // Logging out.
        case 'logout':
            session_unset();
            session_destroy();

            // Redirect to dashboard on logout.
            header('Location: ./');
            exit();
            break;

        // Fogot password.
        case 'forgot':
            // The verification file.
            $verification_file = "./verify.php";

            // If verified, allow a password reset.
            if (!isset($_GET["verify"])) {
                $code = sha1(md5(rand()));

                $verify_file_contents[] = "<?php";
                $verify_file_contents[] = "\$verification_code = \"" . $code . "\";";
                file_put_contents($verification_file, implode("\n", $verify_file_contents));

                $recovery_url = sprintf("%s/index.php?action=forgot&verify=%s,", $blog_url, $code);
                $message      = sprintf("To reset your password go to: %s", $recovery_url);

                $headers[] = "From: " . $blog_email;
                $headers[] = "Reply-To: " . $blog_email;
                $headers[] = "X-Mailer: PHP/" . phpversion();

                mail($blog_email, $blog_title . " - Recover your password", $message, implode("\r\n", $headers));
                $login_error = "Details on how to recover your password have been sent to your email.";

            // If not verified, display a verification error.
            } else {
                include($verification_file);

                if ($_GET["verify"] == $verification_code) {
                    $_SESSION["user"] = true;
                    unlink($verification_file);
                } else {
                    $login_error = "That's not the correct recovery code!";
                }
            }
            break;
        // Invalidation
        case 'invalidate':
            if (!$_SESSION['user']) {
                $login_error = 'Nope, try again!';
            } else {
                if (!file_exists($upload_dir . 'cache/')) {
                    return;
                }

                $files = glob($upload_dir . 'cache/*');
                foreach ($files as $file) {
                    if (is_file($file))
                        unlink($file);
                }
            }
            header('Location: ' . './');
            break;
    }
    define('LOGIN_ERROR', $login_error);
}

/*-----------------------------------------------------------------------------------*/
/* Post action
/*-----------------------------------------------------------------------------------*/

if(isset($_POST) && !empty($_POST)) {
    require_once "core/save.php";
}


/*-----------------------------------------------------------------------------------*/
/* Reading File Names
/*-----------------------------------------------------------------------------------*/

$category = NULL;
$tag      = NULL;
$filename = NULL;

if(isset($_GET['filename']) && ($_GET['filename'] == 'rss' || $_GET['filename'] == 'atom')) {
    $filename = $_GET['filename'];
} else if(isset($_GET['filename']) && !empty($_GET['filename'])) {
    //Filename can be /some/blog/post-filename.md We should get the last part only
    $filename = explode('/', $_GET['filename']);

    // File name could be the name of a category
    if(count($filename) >= 2 && $filename[count($filename) - 2] == "category") {
        $category = $filename[count($filename) - 1];
        $filename = NULL;
    } else if(count($filename) >= 2 && $filename[count($filename) - 2] == "tag") {
        // File name could be the name of a tag
        $tag = $filename[count($filename) - 1];
        $filename = NULL;
    } else {
        // Individual Post
        $filename = POSTS_DIR . $filename[count($filename) - 1] . FILE_EXT;
    }
}

/*-----------------------------------------------------------------------------------*/
/* The Home Page (All Posts)
/*-----------------------------------------------------------------------------------*/

if ($filename == NULL) {
    $page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) ? $_GET['page'] : 1;
    $offset = ($page - 1) * POSTS_PER_PAGE;

    // Index page cache file name, will be used if index_cache = "on"
    $cachefile = CACHE_DIR . ($category ? $category : "index") . $page . '.html';
    // @TODO : même chose avec les tags ?

    // If index cache file exists, serve it directly wihout getting all posts
    if(!isset($_SESSION['user'])) {
        if (file_exists($cachefile) && CACHE_NOTE != 'off') {
            include $cachefile;
            exit;
        }
    }

    if($category)
        $all_posts = get_posts_for_category($category);
    elseif($tag)
        $all_posts = get_posts_for_tag($tag);
    else
        $all_posts = get_all_posts();

    $total = round(count($all_posts) / POSTS_PER_PAGE);
    $posts = (PAGINATION_ON_OFF != "off")  ? array_slice($all_posts, $offset, (POSTS_PER_PAGE > 0) ? POSTS_PER_PAGE : null) : $all_posts;

    if($posts) {
        ob_start();
        $content = '';
        foreach($posts as $post) {
            // Get the post status.
            if (secure(trim(strtolower($post['post_status']))) == 'draft') continue;

            // Get the posts tags.
            $post_tags = array();
            foreach($post['post_tags'] as $tag) {
                $post_tags[] = array(
                    'name' => trim($tag),
                    'url'  => BLOG_URL . 'tag/' . urlencode(trim(strtolower($tag))),
                );
            }

            // Get the post link.
            if ($category)
                $post_link = trim(strtolower($post['post_category'])) . '/' . str_replace(FILE_EXT, '', $post['fname']);
            else
                $post_link = BLOG_URL . str_replace(FILE_EXT, '', $post['fname']);

            // Get the post title.
            $post_title             = str_replace(array("\n",'<h1>','</h1>'), '', $post['post_title']);
            $post_author            = $post['post_author'];
            $post_author_twitter    = $post['post_author_twitter'];
            $published_iso_date     = $post['post_date'];
            $published_date         = date_format(date_create($published_iso_date), $date_format);
            $post_category          = $post['post_category'];
            $post_category_link     = BLOG_URL . 'category/' . urlencode(trim(strtolower($post_category)));
            $post_status            = $post['post_status'];
            $post_content           = $post['post_content'];
            $post_intro             = $post['post_intro'];
            $post_name              = $post['fname'];

            // Get the post image url.
            $image = str_replace(array(FILE_EXT), '', POSTS_DIR.$post['fname']).'.jpg';

            if (file_exists($image))
                $post_image = BLOG_URL.str_replace(array(FILE_EXT, './'), '', POSTS_DIR . $post['fname']).'.jpg';
            else
                $post_image = get_twitter_profile_img($post_author_twitter);

            // Get the milti-post template file.
            include $posts_file;
        }

        echo $content;
        $content = ob_get_contents();

        // Get the site title
        $page_title = BLOG_TITLE;
        $blog_image = 'https://api.twitter.com/1/users/profile_image?screen_name='.$blog_twitter.'&size=bigger';

        // Get the page description and author meta.
        $get_page_meta[] = '<meta name="description" content="' . $meta_description . '">';
        $get_page_meta[] = '<meta name="author" content="' . BLOG_TITLE . '">';

        // Get the Twitter card.
        $get_page_meta[] = '<meta name="twitter:card" content="summary">';
        $get_page_meta[] = '<meta name="twitter:site" content="' . $blog_twitter . '">';
        $get_page_meta[] = '<meta name="twitter:title" content="' . BLOG_TITLE . '">';
        $get_page_meta[] = '<meta name="twitter:description" content="' . $meta_description . '">';
        $get_page_meta[] = '<meta name="twitter:creator" content="' . $blog_twitter . '">';
        $get_page_meta[] = '<meta name="twitter:image:src" content="' . $blog_image . '">';
        $get_page_meta[] = '<meta name="twitter:domain" content="' . BLOG_URL . '">';

        // Get the Open Graph tags.
        $get_page_meta[] = '<meta property="og:type" content="website">';
        $get_page_meta[] = '<meta property="og:title" content="' . BLOG_TITLE . '">';
        $get_page_meta[] = '<meta property="og:site_name" content="' . BLOG_TITLE . '">';
        $get_page_meta[] = '<meta property="og:url" content="' .BLOG_URL . '">';
        $get_page_meta[] = '<meta property="og:description" content="' . $meta_description . '">';
        $get_page_meta[] = '<meta property="og:image" content="' . $blog_image . '">';

        // Get all page meta.
        $page_meta = implode("\n", $get_page_meta);

        ob_end_clean();
    } else {
        ob_start();

        // Define the site title.
        $page_title = $error_title;
        $page_meta = '';

        // Get the 404 page template.
        include $not_found_file;

        //Get the contents
        $content = ob_get_contents();

        //Flush the buffer so that we dont get the page 2x times
        ob_end_clean();
    }

    ob_start();

    // Get the index template file.
    if(isset($index_file))
        include_once $index_file;

    //Now that we have the whole index page generated, put it in cache folder
    if (CACHE_NOTE != 'off') {
        $fp = fopen($cachefile, 'w');
        fwrite($fp, ob_get_contents());
        fclose($fp);
    }
}

/*-----------------------------------------------------------------------------------*/
/* RSS Feed
/*-----------------------------------------------------------------------------------*/

else if ($filename == 'rss' || $filename == 'atom') {
    if($filename == 'rss')
        $feed = new FeedWriter(RSS2);
    else
        $feed = new FeedWriter(ATOM);

    $feed->setTitle(BLOG_TITLE);
    $feed->setLink(BLOG_URL);

    if($filename == 'rss') {
        $feed->setDescription($meta_description);
        $feed->setChannelElement('language', LANGUAGE_RSS);
        $feed->setChannelElement('pubDate', date(DATE_RSS, time()));
    } else {
        $feed->setChannelElement('author', BLOG_TITLE.' - ' . BLOG_EMAIL);
        $feed->setChannelElement('updated', date(DATE_RSS, time()));
    }

    $posts = get_all_posts();

    if($posts) {
        $c = 0;
        foreach($posts as $post) {
            if($c < $feed_max_items) {
                $item = $feed->createNewItem();

                // Remove HTML from the RSS feed.
                $item->setTitle(substr($post['post_title'], 4, -6));
                $item->setLink(rtrim(BLOG_URL, '/') . '/' . str_replace(FILE_EXT, '', $post['fname']));
                $item->setDate($post['post_date']);

                // Remove Meta from the RSS feed.
                $remove_metadata_from = file(rtrim(POSTS_DIR, '/') . '/' . $post['fname']);

                if($filename == 'rss') {
                    $item->addElement('author', str_replace('-', '', $remove_metadata_from[1]) . ' - ' . BLOG_EMAIL);
                    $item->addElement('guid', rtrim(BLOG_URL, '/') . '/' . str_replace(FILE_EXT, '', $post['fname']));
                }

                // Remove the metadata from the RSS feed.
                unset($remove_metadata_from[0],
                      $remove_metadata_from[1],
                      $remove_metadata_from[2],
                      $remove_metadata_from[3],
                      $remove_metadata_from[4],
                      $remove_metadata_from[5]);
                $remove_metadata_from = array_values($remove_metadata_from);

                $item->setDescription(Markdown(implode($remove_metadata_from)));

                $feed->addItem($item);
                $c++;
            }
        }
    }

    $feed->genarateFeed();
}

/*-----------------------------------------------------------------------------------*/
/* Single Post Pages
/*-----------------------------------------------------------------------------------*/

else {
    ob_start();

    // Define the post file.
    if(file_exists($filename))
        $fcontents = file($filename);

    $slug_array = explode("/", $filename);
    $slug_len = count($slug_array);

    // This was hardcoded array index, it should always return the last index.
    $slug = str_replace(array(FILE_EXT), '', $slug_array[$slug_len - 1]);

    // Define the cached file.
    $cachefile = CACHE_DIR.$slug.'.html';

    // If there's no file for the selected permalink, grab the 404 page template.
    if (!file_exists($filename)) {

        //Change the cache file to 404 page.
        $cachefile = CACHE_DIR.'404.html';

        // Define the site title.
        $page_title = $error_title;

        // Get the 404 page template.
        include $not_found_file;

        // Get the contents.
        $content = ob_get_contents();

        // Flush the buffer so that we dont get the page 2x times.
        ob_end_clean();

        // Start new buffer.
        ob_start();

	      // Get the index template file.
        include_once $index_file;

        // Cache the post on if caching is turned on.
        if (CACHE_POST != 'off')
        {
            $fp = fopen($cachefile, 'w');
            fwrite($fp, ob_get_contents());
            fclose($fp);
        }

    // If there is a cached file for the selected permalink, display the cached post.
    } else if (file_exists($cachefile)) {

        // Define site title
        $page_title = str_replace('# ', '', $fcontents[0]);

        // Get the cached post.
        include $cachefile;

        exit;

    // If there is a file for the selected permalink, display and cache the post.
    } else {
        // Get the post title.
        $post_title = Markdown($fcontents[0]);
        $post_title = str_replace(array("\n",'<h1>','</h1>'), '', $post_title);

        // Get the post intro.
        $post_intro = htmlspecialchars($fcontents[8]);

        // Get the post author.
        $post_author = str_replace(array("\n", '-'), '', $fcontents[1]);

        // Get the post author Twitter ID.
        $post_author_twitter = str_replace(array("\n", '- '), '', $fcontents[2]);

        // Get the published date.
        $published_iso_date = str_replace('-', '', $fcontents[3]);

        // Generate the published date.
        $published_date = date_format(date_create($published_iso_date), $date_format);

        // Get the post category.
        $post_category = str_replace(array("\n", '-'), '', $fcontents[4]);

        // Get the post category link.
        $post_category_link = BLOG_URL . 'category/' . urlencode(trim(strtolower($post_category)));

        // Get the post tags.
        $temp_tags = explode('|', trim(str_replace(array("\n", '-'), '', $fcontents[6])));
        $post_tags = array();
        foreach($temp_tags as $tag) {
            $post_tags[] = array(
                'name' => trim($tag),
                'url'  => BLOG_URL . 'tag/' . urlencode(trim(strtolower($tag))),
            );
        }

        // Get the post status.
        $post_status = str_replace(array("\n", '- '), '', $fcontents[5]);

        // Get the post link.
        $post_link = BLOG_URL.str_replace(array(FILE_EXT, POSTS_DIR), '', $filename);

        $post_name = $filename;

        // Get the post image url.
        $image = str_replace(array(FILE_EXT), '', $filename).'.jpg';

        if (file_exists($image)) {
            $post_image = BLOG_URL.str_replace(array(FILE_EXT, './'), '', $filename).'.jpg';
        } else {
            $post_image = get_twitter_profile_img($post_author_twitter);
        }

        // Get the post content
        $file_array = array_slice(file($filename), 7);
        $post_content = Markdown(trim(implode("", $file_array)));

        // Get the site title.
        $page_title = str_replace('# ', '', $fcontents[0]);

        // Generate the page description and author meta.
        $get_page_meta[] = '<meta name="description" content="' . $post_intro . '">';
        $get_page_meta[] = '<meta name="author" content="' . $post_author . '">';

        // Generate the Twitter card.
        $get_page_meta[] = '<meta name="twitter:card" content="summary">';
        $get_page_meta[] = '<meta name="twitter:site" content="' . $blog_twitter . '">';
        $get_page_meta[] = '<meta name="twitter:title" content="' . $page_title . '">';
        $get_page_meta[] = '<meta name="twitter:description" content="' . $post_intro  . '">';
        $get_page_meta[] = '<meta name="twitter:creator" content="' . $post_author_twitter . '">';
        $get_page_meta[] = '<meta name="twitter:image:src" content="' . $post_image . '">';
        $get_page_meta[] = '<meta name="twitter:domain" content="' . $post_link . '">';

        // Get the Open Graph tags.
        $get_page_meta[] = '<meta property="og:type" content="article">';
        $get_page_meta[] = '<meta property="og:title" content="' . $page_title . '">';
        $get_page_meta[] = '<meta property="og:site_name" content="' . $page_title . '">';
        $get_page_meta[] = '<meta property="og:url" content="' . $post_link . '">';
        $get_page_meta[] = '<meta property="og:description" content="' . $post_intro . '">';
        $get_page_meta[] = '<meta property="og:image" content="' . $post_image . '">';

        // Generate all page meta.
        $page_meta = implode("\n", $get_page_meta);

        // Generate the post.
        $post = Markdown(join('', $fcontents));

        // Get the post template file.
        include $post_file;

        $content = ob_get_contents();
        ob_end_clean();
        ob_start();

        // Get the index template file.
        include_once $index_file;

        // Cache the post on if caching is turned on.
        if (CACHE_POST != 'off')
        {
            $fp = fopen($cachefile, 'w');
            fwrite($fp, ob_get_contents());
            fclose($fp);
        }
    }
}
