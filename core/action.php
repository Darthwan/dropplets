<?php

$action = $_GET['action'];

switch ($action) {
    // Logging in.
    case 'login':
        if ((isset($_POST['password'])) && $hasher->CheckPassword($_POST['password'], PASSWORD)) {
            $_SESSION['user'] = true;

            // Redirect if authenticated.
            header('Location: ./');
            exit();
        } else {
            // Display error if not authenticated.
            $login_error = 'Nope, try again!';
        }
        break;

    // Logging out.
    case 'logout':
        session_unset();
        session_destroy();

        header('Location: ./');
        exit();
        break;

    // Fogot password.
    case 'forgot':
        // The verification file.
        $verification_file = "./verify.php";

        // If verified, allow a password reset.
        if (!isset($_GET["verify"])) {
            $code = sha1(md5(rand()));

            $verify_file_contents[] = "<?php";
            $verify_file_contents[] = "\$verification_code = \"" . $code . "\";";
            file_put_contents($verification_file, implode("\n", $verify_file_contents));

            $recovery_url = sprintf("%s/index.php?action=forgot&verify=%s,", $blog_url, $code);
            $message      = sprintf("To reset your password go to: %s", $recovery_url);

            $headers[] = "From: " . $blog_email;
            $headers[] = "Reply-To: " . $blog_email;
            $headers[] = "X-Mailer: PHP/" . phpversion();

            mail($blog_email, $blog_title . " - Recover your password", $message, implode("\r\n", $headers));
            $login_error = "Details on how to recover your password have been sent to your email.";

        // If not verified, display a verification error.
        } else {
            include($verification_file);

            if ($_GET["verify"] == $verification_code) {
                $_SESSION["user"] = true;
                unlink($verification_file);
            } else {
                $login_error = "That's not the correct recovery code!";
            }
        }
        break;

    // Invalidation
    case 'invalidate':
        if (!$_SESSION['user']) {
            $login_error = 'Nope, try again!';
        } else {
            if (!file_exists($upload_dir . 'cache/')) {
                return;
            }

            $files = glob($upload_dir . 'cache/*');
            foreach ($files as $file) {
                if (is_file($file))
                    unlink($file);
            }
        }
        header('Location: ' . './');
        exit();
        break;
}

define('LOGIN_ERROR', $login_error);
