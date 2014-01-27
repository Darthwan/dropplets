<?php

include 'includes/feedwriter.php';
include 'includes/markdown.php';
include 'includes/phpass.php';
include 'includes/actions.php';

/*-----------------------------------------------------------------------------------*/
/* Modifying string
/*-----------------------------------------------------------------------------------*/

function settingsFormat($name, $value) {
    return sprintf("\$%s = \"%s\";", $name, $value);
}

function settingsFormatPass($name, $value) {
    return sprintf("\$%s = '%s';", $name, $value);
}

function secure($str) {
    return htmlspecialchars($str);
}

/*-----------------------------------------------------------------------------------*/
/* Get URL blog
/*-----------------------------------------------------------------------------------*/

function getUrlBlog() {
    // Get the components of the current url.
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on') ? 'https://' : 'http://';
    $domain   = $_SERVER['SERVER_NAME'];
    $port     = $_SERVER['SERVER_PORT'];
    $path     = $_SERVER['REQUEST_URI'];

    // Check if running on alternate port.
    if ($protocol == "https://") {
        if ($port == 443)
            $url = $protocol . $domain;
        else
            $url = $protocol . $domain . ":" . $port;
    } elseif ($protocol == "http://") {
        if ($port == 80)
            $url = $protocol . $domain;
        else
            $url = $protocol . $domain . ":" . $port;
    }

    return array('url' => $url . $path, 'domain' => $domain);
}

/*-----------------------------------------------------------------------------------*/
/* Get All Posts Function
/*-----------------------------------------------------------------------------------*/

function get_all_posts($options = array()) {
    if($handle = opendir(POSTS_DIR)) {
        $files = array();
        $filetimes = array();
        $post_dates = array();

        while (false !== ($entry = readdir($handle))) {
            if(substr(strrchr($entry, '.'), 1) == ltrim(FILE_EXT, '.')) {

                // Define the post file.
                $fcontents = file(POSTS_DIR.$entry);

                // Define the post title.
                $post_title = Markdown($fcontents[0]);

                // Define the post author.
                $post_author = str_replace(array("\n", '-'), '', $fcontents[1]);

                // Define the post author Twitter account.
                $post_author_twitter = str_replace(array("\n", '- '), '', $fcontents[2]);

                // Define the published date.
                $post_date = str_replace('-', '', $fcontents[3]);

                // Define the post category.
                $post_category = str_replace(array("\n", '-'), '', $fcontents[4]);

                // Early return if we only want posts from a certain category
                if(isset($options['category']) && $options["category"] && $options["category"] != trim(strtolower($post_category)))
                    continue;

                // Get the posts tags.
                $post_tags = explode('|', trim(str_replace(array("\n", '-'), '', $fcontents[6])));
                $post_tags = array_map('trim', $post_tags);

                if(isset($options['tag']) && $options['tag'] && !in_array($options["tag"], $post_tags))
                    continue;

                // Define the post status.
                $post_status = str_replace(array("\n", '- '), '', $fcontents[5]);

                // Define the post content
                $post_content = Markdown(join('', array_slice($fcontents, 7, count($fcontents) - 1)));

                // Define the post intro.
                $post_intro = Markdown($fcontents[8]);

                // Pull everything together for the loop.
                $files[] = array(
                    'fname' => $entry,
                    'post_title' => $post_title,
                    'post_author' => $post_author,
                    'post_author_twitter' => $post_author_twitter,
                    'post_date' => $post_date,
                    'post_category' => $post_category,
                    'post_status' => $post_status,
                    'post_tags' => $post_tags,
                    'post_intro' => $post_intro,
                    'post_content' => $post_content,
                    'post_name' => $entry
                );

                $post_dates[] = $post_date;
                $post_titles[] = $post_title;
                $post_authors[] = $post_author;
                $post_authors_twitter[] = $post_author_twitter;
                $post_categories[] = $post_category;
                $post_statuses[] = $post_status;
                $post_tags[] = $post_tags;
                $post_intros[] = $post_intro;
                $post_contents[] = $post_content;
                $post_name[] = $entry;
            }
        }

        array_multisort($post_dates, SORT_DESC, $files);
        return $files;

    } else {
        return false;
    }
}

/*-----------------------------------------------------------------------------------*/
/* Get Posts for Selected Tag
/*-----------------------------------------------------------------------------------*/

function get_posts_for_tag($tag) {
    $tag = trim(strtolower($tag));
    return get_all_posts(array("tag" => $tag));
}

/*-----------------------------------------------------------------------------------*/
/* Get Posts for Selected Category
/*-----------------------------------------------------------------------------------*/

function get_posts_for_category($category) {
    $category = trim(strtolower($category));
    return get_all_posts(array("category" => $category));
}


/*-----------------------------------------------------------------------------------*/
/* Get Installed Templates
/*-----------------------------------------------------------------------------------*/

function get_installed_templates() {

    // The currently active template.
    $active_template = ACTIVE_TEMPLATE;

    // The templates directory.
    $templates_directory = './templates/';

    // Get all templates in the templates directory.
    $available_templates = glob($templates_directory . '*');

    foreach ($available_templates as $template):
        if($template != "./templates/base") {
            // Generate template names.
            $template_dir_name = substr($template, 12);

            // Template screenshots.
            $template_screenshot = '' . $templates_directory . '' . $template_dir_name . '/screenshot.jpg'; {
            ?>
<li<?php if($active_template == $template_dir_name) { ?> class="active"<?php } ?>>
<div class="shadow"></div>
<form method="post">
<img src="<?php echo $template_screenshot; ?>">
<input type="hidden" name="template" id="template" required readonly value="<?php echo $template_dir_name ?>">
<button class="<?php if ($active_template == $template_dir_name) :?>active<?php else : ?>activate<?php endif; ?>" type="submit" name="submit" value="submit"><?php if ($active_template == $template_dir_name) :?>t<?php else : ?>k<?php endif; ?></button>
</form>
</li>
<?php
            }
        }
    endforeach;
}

/*-----------------------------------------------------------------------------------*/
/* If is Home (Could use "is_single", "is_category" as well.)
/*-----------------------------------------------------------------------------------*/

// If is home.
function get_home() {
    $homepage = BLOG_URL;
    $tmp = explode('?', $_SERVER["REQUEST_URI"]);

    // Get the current page.
    $currentpage = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'on') ? 'https://' . $_SERVER["SERVER_NAME"] : 'http://' . $_SERVER["SERVER_NAME"];
    $currentpage .= $tmp[0];

    return $homepage == $currentpage;
}

/*-----------------------------------------------------------------------------------*/
/* Get Profile Image
/*-----------------------------------------------------------------------------------*/

function get_twitter_profile_img($username) {
	// Get the cached profile image.
	$profile_image = BLOG_URL . 'cache/'.$username.'.jpg';
    $test = './cache/'.$username.'.jpg';

	// Cache the image if it doesn't already exist.
	if (!file_exists($test)) {
	    $image_url = 'http://dropplets.com/profiles/?id='.$username.'';
	    $image = file_get_contents($image_url);
	    file_put_contents('./cache/'.$username.'.jpg', $image);
	}

	// Return the image URL.
	return $profile_image;
}

function get_gravatar_profile_img($username) {
    // Get the cached profile image.
    $profile_image = BLOG_URL . 'cache/'.$username.'.jpg';
    $test = './cache/'.$username.'.jpg';

    $default = "http://dropplets.com/favicon.png";
    $size = 40;

    // Cache the image if it doesn't already exist.
    if (!file_exists($test)) {
        $image_url = 'http://www.gravatar.com/avatar/' . md5(trim(strtolower($username))) . '?s=' . $size . '&r=pg&d=' . urlencode($default) . '&f=y';
        $image = file_get_contents($image_url);
        file_put_contents('./cache/'.$username.'.jpg', $image);
    }

    // Return the image URL.
    return $profile_image;
}

/*-----------------------------------------------------------------------------------*/
/* Include All Plugins in Plugins Directory
/*-----------------------------------------------------------------------------------*/

foreach(glob('./core/plugins/' . '*.php') as $plugin){
    include_once $plugin;
}
