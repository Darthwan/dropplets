<?php

// Get the components of the current url.
$url = getUrlBlog();
// Check if the install directory is writable.
$is_writable = (TRUE == is_writable(dirname(__FILE__) . '/'));

?><!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Let's Get Started</title>

        <link rel="stylesheet" href="templates/base/base.css" />
        <link rel="shortcut icon" href="templates/base/favicon.ico">
    </head>

    <body class="dp-install">
        <form method="post">
            <a class="dp-icon-dropplets-logo" href="http://dropplets.com"></a>

            <h2>Install Dropplets</h2>
            <p>Welcome to an easier way to blog.</p>

            <input type="password" name="password" id="password" required placeholder="Choose Your Password">
            <input type="password" name="password-confirmation" id="password-confirmation" required placeholder="Confirm Your Password" onblur="confirmPass()">

            <input hidden type="text" name="blog_email" id="blog_email" value="hi@dropplets.com">
            <input hidden type="text" name="blog_twitter" id="blog_twitter" value="dropplets">
            <input hidden type="text" name="blog_google" id="blog_google" value="">
            <input hidden type="text" name="blog_flattr" id="blog_flattr" value="">
            <input hidden type="text" name="blog_url" id="blog_url" value="<?php echo $url['url']; ?><?php if ($url['url'] == $url['domain']) { ?>/<?php } ?>">
            <input hidden type="text" name="template" id="template" value="simple">
            <input hidden type="text" name="blog_title" id="blog_title" value="Welcome to Dropplets">
            <input hidden type="text" name="blog_language" id="blog_language" value="en_US">
            <input hidden type="text" name="language_rss" id="language_rss" value="en-us">
            <input hidden type="text" name="intro_title" id="intro_title" value="Welcome to Dropplets">
            <textarea hidden name="meta_description" id="meta_description"></textarea>
            <textarea hidden name="intro_text" id="intro_text">In a flooded selection of overly complex solutions, Dropplets has been created in order to deliver a much needed alternative. There is something to be said about true simplicity in the design, development and management of a blog. By eliminating all of the unnecessary elements found in typical solutions, Dropplets can focus on pure design, typography and usability. Welcome to an easier way to blog.</textarea>

            <button type="submit" name="submit" value="submit">k</button>
        </form>

        <?php if (!$is_writable) { ?>
            <p class="alert">It seems that your config folder is not writable, please add the necessary permissions.</p>
        <?php } ?>

        <script>
            function confirmPass() {
                var pass = document.getElementById("password").value
                var confPass = document.getElementById("password-confirmation").value
                if(pass != confPass) {
                    alert('Your passwords do not match!');
                }
            }
        </script>
    </body>
</html>
