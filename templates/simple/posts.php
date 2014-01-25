<article class="<?php echo($post_status); ?>">
    <div class="row">
        <div class="one-quarter meta">
            <div class="thumbnail">
                <a href="<?php echo($blog_url); ?>" title="Back home!"><img src="<?php echo($post_image); ?>" alt="<?php echo($post_title); ?>" /></a>
            </div>

            <ul>
                <li>Written by <?php echo($post_author); ?></li>
                <li><?php echo($published_date); ?></li>
                <li>About <a href="<?php echo($post_category_link); ?>"><?php echo($post_category); ?></a></li>
                <li><a href="https://twitter.com/<?php echo($post_author_twitter); ?>">&#64;<?php echo($post_author_twitter); ?></a></li>
                <li></li>
            </ul>
        </div>

        <div class="three-quarters post">
            <h2><a href="<?php echo($post_link); ?>"><?php echo($post_title); ?></a></h2>

            <?php echo($post_intro); ?>

            <ul class="actions">
                <li><a class="button" href="<?php echo($post_link); ?>">Continue Reading</a></li>
                <li><a class="button" href="https://twitter.com/intent/tweet?screen_name=<?php echo($post_author_twitter); ?>&text=<?php echo($post_link); ?>%20(cc%20&#64;<?php echo($post_author_twitter); ?>)" data-dnt="true">Comment on Twitter</a></li>
                <?php if ($category) { ?>
                <li><a class="button" href="<?php echo($blog_url); ?>">More Articles</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</article>
