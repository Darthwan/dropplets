<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">

        <title><?php echo($page_title); ?></title>

        <?php echo($page_meta); ?>
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="stylesheet" href="<?php echo($template_dir_url); ?>styles/subdiv.css">
        <link rel="stylesheet" href="<?php echo($template_dir_url); ?>styles/style.css">

        <link rel="stylesheet" href="<?php echo BLOG_URL; ?>templates/base/base.css">
        <link rel="shortcut icon" href="<?php echo BLOG_URL; ?>templates/base/favicon.ico">

        <!-- RSS Feed Links -->
        <link rel="alternate" type="application/rss+xml" title="Subscribe using RSS" href="<?php echo BLOG_URL; ?>rss" />
        <link rel="alternate" type="application/atom+xml" title="Subscribe using Atom" href="<?php echo BLOG_URL; ?>atom" />

        <!-- User Header Injection -->
        <?php echo HEADER_INJECT; ?>

        <!-- Plugin Header Injection -->
        <?php action::run('dp_header'); ?>
    </head>

    <body>
        <?php if($is_home) { ?>
        <article class="home">
            <div class="row">
                <div class="meta">
                    <div class="thumbnail">
                        <img src="<?php echo get_twitter_profile_img($blog_twitter); ?>" alt="profile" />
                    </div>
                </div>

                <div class="post">
                    <h2><?php echo($blog_title); ?></h2>

                    <p><?php echo($intro_text); ?></p>

                    <p class="post-social">
                        <span><a href="mailto:<?php echo($blog_email); ?>?subject=Hello"><?php echo($blog_email); ?></a></span>
                        <?php if($blog_flattr != "") { ?><span><a href="https://flattr.com/submit/auto?user_id=<?php echo($blog_flattr); ?>&amp;url=<?php echo BLOG_URL; ?>&amp;title=<?php echo($blog_title); ?>&amp;language=<?php echo $blog_language; ?>&amp;category=text">Flattr</a></span><?php } ?>
                        <span><a href="https://twitter.com/<?php echo($blog_twitter); ?>">&#64;<?php echo($blog_twitter); ?></a></span>
                        <?php if($blog_google != "") { ?><span><a href="https://plus.google.com/u/0/<?php echo($blog_google); ?>">Google+</a></span><?php } ?>
                        <?php if($blog_facebook != "") { ?><span><a href="https://facebook.com/<?php echo($blog_facebook); ?>">Facebook</a></span><?php } ?>
                    </p>
                </div>
            </div>
        </article>
        <?php } ?>

        <?php echo($content); ?>

        <!-- jQuery & Required Scripts -->
    <script src="<?php echo(BLOG_URL); ?>core/includes/js/jquery-1.10.2.min.js"></script>

    <?php if (PAGINATION_ON_OFF !== "off") { ?>
    <!-- Post Pagination -->
    <script>
        var infinite = true;
        var next_page = 2;
        var loading = false;
        var no_more_posts = false;
        $(function() {
            function load_next_page() {
                $.ajax({
                    url: "index.php?page=" + next_page,
                    success: function (res) {
                        next_page++;
                        var result = $.parseHTML(res);
                        var articles = $(result).filter(function() {
                            return $(this).is('article');
                        });
                        if (articles.length < 2) {  //There's always one default article, so we should check if  < 2
                            no_more_posts = true;
                        }  else {
                            $('body').append(articles.slice(1));
                        }
                        loading = false;
                    }
                });
            }

            $(window).scroll(function() {
                var when_to_load = $(window).scrollTop() * 0.32;
                if (infinite && (loading != true && !no_more_posts) && $(window).scrollTop() + when_to_load > ($(document).height()- $(window).height() ) ) {
                    // Sometimes the scroll function may be called several times until the loading is set to true.
                    // So we need to set it as soon as possible
                    loading = true;
                    setTimeout(load_next_page,500);
                }
            });
        });
    </script>
    <?php } ?>

    <!-- Tools -->
    <?php include('./core/tools.php'); ?>

    <!-- User Footer Injection -->
    <?php echo FOOTER_INJECT; ?>

    <!-- Plugin Footer Injection -->
    <?php action::run('dp_footer'); ?>
    </body>
</html>
