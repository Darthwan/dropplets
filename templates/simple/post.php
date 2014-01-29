<article class="single <?php echo $post_status; ?>">
    <div class="row">
        <div class="one-quarter meta">
            <div class="thumbnail">
                <a href="<?php echo $blog_url; ?>" title="Back home!"><img src="<?php echo $post_image; ?>" alt="<?php echo $post_title; ?>" /></a>
            </div>

            <ul>
                <li>Written by <?php echo $post_author; ?></li>
                <li><?php echo $published_date; ?></li>
                <li>About <a href="<?php echo $post_category_link; ?>"><?php echo $post_category; ?></a></li>
                <li>Tags: <?php for($i = 0, $c = count($post_tags); $i < $c-1; $i++) { ?>
                    <a href="<?php echo($post_tags[$i]['url']); ?>"><?php echo($post_tags[$i]['name']); ?></a>,
                <?php } ?>
                    <a href="<?php echo($post_tags[count($post_tags)-1]['url']); ?>"><?php echo($post_tags[count($post_tags)-1]['name']); ?></a>
                </li>
                <li><a href="https://twitter.com/<?php echo $post_author_twitter; ?>">&#64;<?php echo $post_author_twitter; ?></a></li>
                <li></li>
            </ul>
        </div>

        <div class="three-quarters post">
            <h2><?php echo $post_title; ?></h2>
            <?php echo $post_content; ?>

            <ul class="actions">
                <li><a class="button" href="https://twitter.com/intent/tweet?screen_name=<?php echo $post_author_twitter; ?>&amp;text=<?php echo transformSpace($post_title); ?>%20<?php echo $post_link; ?>" data-dnt="true">Comment on Twitter</a></li>
                <li><a class="button" href="<?php echo $post_name; ?>">Source ".md"</a></li>
                <?php if($blog_flattr != "") { ?><li><a class="button" href="https://flattr.com/submit/auto?user_id=<?php echo transformSpace($blog_flattr); ?>&amp;url=<?php echo $post_link; ?>&amp;title=<?php echo transformSpace($post_title); ?>&amp;language=<?php echo $blog_language; ?>&amp;category=text">Flattr me!</a></li><?php } ?>
                <li><a class="button" href="<?php echo $blog_url; ?>">More Articles</a></li>
            </ul>
            <ul class="actions">
                <li><a class="button" href="https://twitter.com/intent/tweet?text=&quot;<?php echo transformSpace($post_title); ?>&quot;%20<?php echo $post_link; ?>%20via%20&#64;<?php echo $post_author_twitter; ?>" data-dnt="true">Share on Twitter</a></li>
                <li><a class="button" href="https://www.facebook.com/sharer.php?u=<?php echo $post_link; ?>&amp;t=<?php echo transformSpace($post_title); ?>" data-dnt="true">Share on Facebook</a></li>
                <li><a class="button" href="https://plus.google.com/share?url=<?php echo $post_link; ?>&amp;hl=fr" data-dnt="true">Share on Google+</a></li>
            </ul>

            <div class="clear"></div>
            <footer>
                <?php echo $blog_title; ?> is published under the terms of the Creative Commons Attribution - ShareAlike (<a href="http://creativecommons.org/licenses/by-sa/3.0/">learn more</a>).<br />
                Powered by a <a href="https://github.com/Hennek/dropplets">fork</a> of <a href="http://dropplets.com/">Dropplets</a>.<br />
                <a href="https://github.com/Hennek/dropplets/issues">Signaler un problème</a> !
            </footer>
        </div>
    </div>
</article>
