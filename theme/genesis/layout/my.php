<?php
    $hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
    $hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
    $showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
    $showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);

    /* Sidebar */
    if($hassidepre)
        $sidebar = "LEFT";
    else if($hassidepost)
        $sidebar = "RIGHT";
    else
        $sidebar = "NONE";

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

    <link rel="shortcut icon" href="<?php echo $OUTPUT->favicon(); ?>">
    
    <?php echo $OUTPUT->standard_head_html() ?>
    
    <noscript>
        <link rel="stylesheet" type="text/css" href="<?php echo $CFG->wwwroot;?>/theme/genesis/css/nojs.css" />
    </noscript>
</head>
<body>
    <?php echo $OUTPUT->standard_top_of_body_html(); ?>
    <?php include 'header.php'; ?>
    
    <?php echo $OUTPUT->slider('frontpage'); ?>
    
    <div id="contentarea" class="row">
        <div class="sklt-container">
            
            <?php if($sidebar == "LEFT") { ?>
                <div class="four columns alpha leftsidebar">
                    <?php echo $OUTPUT->blocks_for_region('side-pre'); ?>
                </div>
            <?php } ?>
            
            <!-- Courses Header -->
            <div class="<?php echo (($sidebar != "NONE")?"nine":"thirteen"); ?> columns <?php echo (($sidebar != "LEFT")?"alpha":""); ?>" id="featuredCourses">
                <?php echo get_string('mycourses','theme_genesis');?>
            </div>
            <div class="three columns <?php echo (($sidebar != "RIGHT")?"omega":""); ?>" id="allCourses">
                <a href="<?php echo $CFG->wwwroot; ?>/course/"><div><?php echo get_string('allcourses','theme_genesis');?></div></a>
            </div>
            
            <div class="<?php echo (($sidebar == "NONE")?"sixteen":"twelve"); ?> columns <?php echo (($sidebar == "LEFT")?"omega":"alpha"); ?> sklt-container">
                <!-- Courses List -->
                <?php 
                    echo $OUTPUT->mycourses($CFG,$sidebar);
                ?>
            </div>
            
            <?php if($sidebar == "RIGHT") { ?>
                <div class="four columns omega rightsidebar">
                    <?php echo $OUTPUT->blocks_for_region('side-post'); ?>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php include 'footer.php'; ?>
</body>

<?php 
    echo "<div style='display: none;'>".$OUTPUT->main_content()."</div>"; 
    echo $OUTPUT->standard_end_of_body_html();
?>