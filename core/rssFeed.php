<?php

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
