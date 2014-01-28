<?php

$page = (isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] > 1) ? $_GET['page'] : 1;
$offset = ($page - 1) * POSTS_PER_PAGE;

// Index page cache file name, will be used if index_cache = "on"
$cachefile = CACHE_DIR;
if($category)
    $cachefile .= $category;
else if($tag)
    $cachefile .= $tag;
else
    $cachefile .= 'index';
$cachefile .= $page . '.html';

// If index cache file exists, serve it directly wihout getting all posts
if ((file_exists($cachefile) && !isset($_SESSION['user']) && CACHE_NOTE != 'off') || (isset($_SESSION['user']) && CACHE_ADMIN == "on")) {
    include_once $cachefile;
    exit();
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
    $blog_image = 'https://api.twitter.com/1/users/profile_image?screen_name='.$blog_twitter.'&amp;size=bigger'; // @TODO change for 1.1

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
