<?php

ob_start();

// Define the post file.
if(file_exists($filename))
    $fcontents = file($filename);

$slug_array = explode("/", $filename);
$slug_len = count($slug_array);

// This was hardcoded array index, it should always return the last index.
$slug = str_replace(array(FILE_EXT), '', $slug_array[$slug_len - 1]);

// Define the cached file.
$cachefile = CACHE_DIR . $slug . '.html';

// If index cache file exists, serve it directly wihout getting all posts
if ((file_exists($cachefile) && !isset($_SESSION['user']) && CACHE_NOTE != 'off') || (isset($_SESSION['user']) && CACHE_ADMIN == "on")) {
    include_once $cachefile;
    exit();
}

// If there's no file for the selected permalink, grab the 404 page template.
if (!file_exists($filename)) {

    //Change the cache file to 404 page.
    $cachefile = CACHE_DIR . '404.html';

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
    if (CACHE_POST != 'off') {
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
    if (CACHE_POST != 'off') {
        $fp = fopen($cachefile, 'w');
        fwrite($fp, ob_get_contents());
        fclose($fp);
    }
}
