<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title><?php echo $page_title; ?></title>

        <!-- Metadata -->
        <?php if(isset($âge_meta)) echo $page_meta; ?>

        <!-- Cascading Style Sheet -->
        <link rel="stylesheet" href="<?php echo BLOG_URL; ?>templates/base/base.css">
        <link rel="stylesheet" href="<?php echo $template_dir_url; ?>styles/style.css">
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
        <?php if(IS_HOME) { ?>
        <article class="home">
            <div class="row">
                <div class="meta">
                    <div class="thumbnail">
                        <a href="<?php echo BLOG_URL; ?>" title="Back home!"><img src="<?php echo get_twitter_profile_img($blog_twitter); ?>" alt="profile" /></a>
                    </div>
                </div>

                <div class="post">
                    <h2><?php echo $intro_title; ?></h2>

                    <p><?php echo nl2br($intro_text); ?></p>

                    <p class="post-social">
                        <?php if($blog_google != "") { ?><span><a href="https://twitter.com/<?php echo $blog_twitter; ?>" title="&#64;<?php echo $blog_twitter; ?>"><i class="icon-twitter"></i></a></span><?php } ?>
                        <?php if($blog_google != "") { ?><span><a href="https://plus.google.com/u/0/<?php echo $blog_google; ?>" title="Google+"><i class="icon-google"></i></a></span><?php } ?>
                        <?php if($blog_facebook != "") { ?><span><a href="https://facebook.com/<?php echo $blog_facebook; ?>" title="Facebook"><i class="icon-facebook"></i></a></span><?php } ?>
                        <?php if($blog_flattr != "") { ?><span><a href="https://flattr.com/submit/auto?user_id=<?php echo $blog_flattr; ?>&amp;url=<?php echo BLOG_URL; ?>&amp;title=<?php echo $blog_title; ?>&amp;language=<?php echo $blog_language; ?>&amp;category=text" title="Flattr"><i class="icon-flattr"></i></a></span><?php } ?>
                        <span><a href="<?php echo BLOG_URL; ?>rss" title="Flux RSS"><i class="icon-rss"></i></a></span>
                    </p>
                </div>
            </div>
        </article>
        <?php } ?>

        <?php echo $content; ?>

        <?php
            $string = '';
            if(INFINITE_SCROLL == off && $total <= POSTS_PER_PAGE) {
                $string .= "<ul style=\"list-style:none; width:400px; margin:15px auto;\">";

                for ($i = 1; $i<=$total;$i++) {
                    if ($i == $page) {
                        $string .= "<li style=\"display: inline-block; margin:5px;\" ><a href=\"\" class=\"button active\">".$i."</a></li>";
                    } else {
                        $string .=  "<li style='display: inline-block; margin:5px;'><a href=\"?page=".$i."\" class=\"button\">".$i."</a></li>";
                    }
                }

                $string .= "</ul>";
            }
            echo $string;
        ?>

        <!-- jQuery & Required Scripts -->
    <script src="<?php echo BLOG_URL; ?>core/includes/js/jquery-1.10.2.min.js"></script>

    <?php if (INFINITE_SCROLL !== "off") { ?>
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

    <!-- User Footer Injection -->
    <?php echo FOOTER_INJECT; ?>

    <!-- Tools -->
    <?php include('./core/tools.php'); ?>

    <!-- Plugin Footer Injection -->
    <?php action::run('dp_footer'); ?>
    </body>
</html>
