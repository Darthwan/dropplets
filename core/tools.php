<?php

/*-----------------------------------------------------------------------------------*/
/* If Logged Out, Get the Login Form
/*-----------------------------------------------------------------------------------*/

$login_error = LOGIN_ERROR;

if (!isset($_SESSION['user'])) { ?>

<div class="dp-panel-wrapper <?php if($_COOKIE['dp-panel']) { echo($_COOKIE['dp-panel']); } ?>" id="dp-dropplets">
    <div class="dp-panel">
        <div class="dp-row profile">
            <div class="dp-icon">
                <img src="<?php echo get_twitter_profile_img(BLOG_TWITTER); ?>" alt="<?php echo(BLOG_TITLE); ?>" />
            </div>

            <div class="dp-content">
                <span class="title">Hey There!</span>
                <a class="dp-button dp-button-dark dp-close dp-icon-close" href="#dp-dropplets"></a>
            </div>
        </div>

        <div class="dp-row dp-editable dp-editable-dark">
            <div class="dp-icon dp-icon-key"></div>

            <div class="dp-content">
                <form method="POST" action="?action=login">
                    <label>Enter Your Password</label>
                    <input type="password" name="password" id="password">
                    <button class="dp-icon-checkmark" type="submit" name="submit" value="submit"></button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php

/*-----------------------------------------------------------------------------------*/
/* Otherwise, Get the Toolbar
/*-----------------------------------------------------------------------------------*/

} else { ?>

<div class="dp-panel-wrapper <?php if($_COOKIE['dp-panel']) { echo($_COOKIE['dp-panel']); } ?>" id="dp-dropplets">
    <div class="dp-panel">
        <div class="dp-row profile">
            <div class="dp-icon">
                <img src="<?php echo get_twitter_profile_img(BLOG_TWITTER); ?>" alt="<?php echo(BLOG_TITLE); ?>" />
            </div>

            <div class="dp-content">
                <span class="title">Welcome Back!</span>
                <a class="dp-button dp-button-dark dp-close  dp-icon-close" href="#dp-dropplets"></a>
            </div>
        </div>

        <div class="dp-row">
            <div class="dp-icon dp-icon-dropplets"></div>
            <div class="dp-content">Publish or Update Posts</div>
            <label class="dp-link" for="postfiles"></label>
            <input style="display: none;" type="file" name="postfiles" id="postfiles" class="postfiles" multiple="multiple" />
        </div>

        <form method="POST" action="./core/save.php">
            <div class="dp-row">
                <div class="dp-icon dp-icon-settings"></div>
                <div class="dp-content">Blog Settings</div>
                <a class="dp-link dp-toggle collapsed" href="#dp-settings"></a>
                <button class="dp-button dp-button-submit" type="submit" name="submit" value="submit">k</button>
            </div>

            <div class="dp-sub-panel" id="dp-settings">
                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Password</label>
                        <input type="password" name="password" id="password" value="">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Language</label>
                        <input type="text" name="blog_language" id="blog_language" value="<?php echo BLOG_LANGUAGE; ?>">
                    </div>
                </div>
            </div>

            <div class="dp-row">
                <div class="dp-icon dp-icon-profile dp-icon-large"></div>
                <div class="dp-content">Blog Profile</div>
                <a class="dp-link dp-toggle" href="#dp-profile"></a>
                <button class="dp-button dp-button-submit" type="submit" name="submit" value="submit">k</button>
            </div>

            <div class="dp-sub-panel" id="dp-profile">
                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Email</label>
                        <input type="text" name="blog_email" id="blog_email" value="<?php echo BLOG_EMAIL; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Twitter</label>
                        <input type="text" name="blog_twitter" id="blog_twitter" value="<?php echo BLOG_TWITTER; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Google+</label>
                        <input type="text" name="blog_google" id="blog_google" value="<?php echo BLOG_GOOGLE; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Facebook</label>
                        <input type="text" name="blog_facebook" id="blog_facebook" value="<?php echo BLOG_FACEBOOK; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Flattr</label>
                        <input type="text" name="blog_flattr" id="blog_flattr" value="<?php echo BLOG_FLATTR; ?>">
                    </div>
                </div>
            </div>

            <div class="dp-row">
                <div class="dp-icon dp-icon-text"></div>
                <div class="dp-content">Blog Meta</div>
                <a class="dp-link dp-toggle" href="#dp-meta-text"></a>
                <button class="dp-button dp-button-submit" type="submit" name="submit" value="submit">k</button>
            </div>

            <div class="dp-sub-panel" id="dp-meta-text">
                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Title</label>
                        <input type="text" name="blog_title" id="blog_title" value="<?php echo BLOG_TITLE; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Blog Description</label>
                        <textarea name="meta_description" id="meta_description" rows="6" placeholder="Add your site description here... just a short sentence that describes what your blog is going to be about."><?php echo META_DESCRIPTION; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="dp-row">
                <div class="dp-icon dp-icon-text"></div>
                <div class="dp-content">Intro Text</div>
                <a class="dp-link dp-toggle" href="#dp-intro-text"></a>
                <button class="dp-button dp-button-submit" type="submit" name="submit" value="submit">k</button>
            </div>

            <div class="dp-sub-panel" id="dp-intro-text">
                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Intro Title</label>
                        <input type="text" name="intro_title" id="intro_title" value="<?php echo INTRO_TITLE; ?>">
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Intro Text</label>
                        <textarea name="intro_text" id="intro_text" rows="12"><?php echo INTRO_TEXT; ?></textarea>
                    </div>
                </div>
            </div>

            <div class="dp-row">
                <div class="dp-icon dp-icon-code"></div>
                <div class="dp-content">Code Injection</div>
                <a class="dp-link dp-toggle" href="#dp-injection"></a>
                <button class="dp-button dp-button-submit" type="submit" name="submit" value="submit">k</button>
            </div>

            <div class="dp-sub-panel" id="dp-injection">
                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Header Injection</label>
                        <textarea class="dp-code" name="header_inject" id="header_inject" rows="12"><?php echo HEADER_INJECT; ?></textarea>
                    </div>
                </div>

                <div class="dp-row dp-editable">
                    <div class="dp-icon dp-icon-edit"></div>

                    <div class="dp-content">
                        <label>Footer Injection</label>
                        <textarea class="dp-code" name="footer_inject" id="footer_inject" rows="12"><?php echo FOOTER_INJECT; ?></textarea>
                    </div>
                </div>
            </div>
        </form>

        <div class="dp-row">
            <div class="dp-icon dp-icon-large dp-icon-grid"></div>
            <div class="dp-content">Installed Templates</div>
            <a class="dp-link dp-toggle" href="#dp-templates"></a>
        </div>

        <div class="dp-sub-panel" id="dp-templates">
            <div class="dp-row dp-templates">
                <?php get_installed_templates('all'); ?>
            </div>
        </div>

        <div class="dp-row">
            <div class="dp-icon dp-icon-key"></div>
            <div class="dp-content">Log Out</div>
            <a class="dp-link" href="?action=logout" title="Logout"></a>
        </div>
    </div>
</div>

<div id="dp-uploaded"></div>

<?php }

/*-----------------------------------------------------------------------------------*/
/* Show/Hide Tool Cards Script
/*-----------------------------------------------------------------------------------*/

?>

<a class="dp-open dp-icon-dropplets" id="dp-dropplets-icon" href="#dp-dropplets"></a>

<script type="text/javascript" src="<?php echo(BLOG_URL); ?>core/includes/js/cookies.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $(".dp-open").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).animate({left:"0"}, 200);
            $.cookies.set('dp-panel', 'open');
            $("body").css({ overflowY: 'hidden' });
            return false;
        });

        $(".dp-close").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).animate({left:"-300px"}, 200);
            $.cookies.set('dp-panel', 'closed');
            $("body").css({ overflowY: 'auto' });
            return false;
        });

        $(".dp-toggle").click(function(){
            var myelement = $(this).attr("href")
            $(myelement).toggle();
            $(this).next('button.dp-button-submit').toggle();
            return false;
        });

        // For Input Labels
        $('input, textarea').focus(function () {
            $(this).prev('label').hide(200);
        })
        .blur(function () {
            $(this).prev('label').show(200);
        });
    });
</script>

<?php

/*-----------------------------------------------------------------------------------*/
/* Get the Uploader Script if Logged In
/*-----------------------------------------------------------------------------------*/

if (isset($_SESSION['user'])) { ?>

<script type="text/javascript" src="<?php echo(BLOG_URL); ?>core/includes/js/uploader.js"></script>
<script type="text/javascript">
    jQuery(document).ready(function($) {
        $('.postfiles').liteUploader(
		{
			script: './core/includes/uploader.php',
			maxSizeInBytes: 1048576,
			typeMessage: '',
			before: function ()
			{
				$('#details').html('');
				$('#response').html('Uploading...');
			},
			each: function (file, errors)
			{
				var i, errorsDisp = '';

				if (errors.length > 0)
				{
					$('#response').html('One or more files did not pass validation');

					for (i = 0; i < errors.length; i++)
					{
						errorsDisp += '<br />' + errors[i].message;
					}
				}

				$('#details').append('<p>Name: ' + file.name + ' Type: ' + file.type + ' Size:' + file.size + errorsDisp + '</p>');
			},
			success: function (response)
			{
				$('#dp-uploaded').html(response);
				window.setTimeout(function(){location.reload()},2000)
			}
		});
    });
</script>

<?php } ?>