<?php
    if(isloggedin())
        redirect ($CFG->wwwroot);
    echo $OUTPUT->doctype();
?>

<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" lang="en" <?php echo $OUTPUT->htmlattributes(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" lang="en" <?php echo $OUTPUT->htmlattributes(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" lang="en" <?php echo $OUTPUT->htmlattributes(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html lang="en" <?php echo $OUTPUT->htmlattributes(); ?>> <!--<![endif]-->
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?php echo $PAGE->title; ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link href='http://fonts.googleapis.com/css?family=Oxygen:400,300' rel='stylesheet' type='text/css'>
    
    <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <link rel="shortcut icon" href="<?php echo $OUTPUT->pix_url('favicon', 'theme'); ?>">
    
    <?php echo $OUTPUT->standard_head_html() ?>
    
    <noscript>
        <link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot;?>/theme/genesis/css/nojs.css" />
    </noscript>
</head>
<body>
    <?php echo $OUTPUT->standard_top_of_body_html(); ?>
    <?php include 'header.php'; ?>
    
    <div id="contentarea" class="row">
        <div class="sklt-container">
            <div class="full columns">
                <br>
                <center>
                    <a href="<?php echo $CFG->wwwroot; ?>">
                        <?php echo $OUTPUT->logo(); ?>
                    </a>
                </center>
                <br>
            </div>
        </div>
        <div class="sklt-container">
            <div class="full columns">
                <div class="loginbox">
                    <form method="post"  action="<?php echo $CFG->wwwroot; ?>/login/index.php">
                        <div class="leftarea">
                            <p><?php echo get_string('login','theme_genesis');?></p>
                            <div class="clear"></div>
                            <div class="inputarea">
                                <div>
                                    <label for="name"><?php echo get_string('username','theme_genesis');?></label>
                                    <input type="text" name="username"/>
                                </div>
                                <div>
                                    <label for="password"><?php echo get_string('password','theme_genesis');?></label>
                                    <input type="password" name="password"/>
                                </div>
                            </div>
                            <div class="remember">
                                <input type="checkbox" name="rememberusername" value="1"/>
                                <label><?php echo get_string('remember','theme_genesis');?></label>
                            </div>
                            <a href="forgot_password.php" style="float: right;"><?php echo get_string('forgotuser','theme_genesis');?></a>
                        </div>
                        <input type="submit" value=">"/>
                    </form>
                    <?php if(isset($_POST['username']) || isset($_POST['password'])){ ?>
                        <div class="error">
                            <?php echo get_string("invalidlogin"); ?>
                        </div>
                    <?php } ?>
                </div>
                <br>
                <div class="shadow2"></div>
                <br><br>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
<?php 
    echo "<div style='display: none;'>".$OUTPUT->main_content()."</div>";
    echo $OUTPUT->standard_end_of_body_html();
    echo $OUTPUT->forcefooter();
?>