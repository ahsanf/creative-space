<?php 
    $menubar = empty($PAGE->layout_options['nomenubar']);
    $noslider = !empty($PAGE->layout_options['noslider']);
    $search = empty($PAGE->layout_options['nosearch']);
    $topbutton = (isset($PAGE->layout_options['topbutton']))?$PAGE->layout_options['topbutton']:'logout';
?>

<div id="topbar" class="row">
    <div class="sklt-container">
        <div class="<?php echo ($search)?"nine":"fifteen"; ?> columns alpha">
            <?php echo $OUTPUT->socialicons('header'); ?>
        </div>
        <?php if($search){ ?>
            <div class="six columns omega">
                <?php echo $OUTPUT->searchbar($CFG->wwwroot); ?>
            </div>
        <?php } ?>
        <div class="one columns omega">
            <?php
                switch ($topbutton) {
                    case 'home':
                        echo '<div id="home" class="topbutton"><a href="'.$CFG->wwwroot.'">'.get_string('home','theme_genesis').'</a></div>';
                    break;
                    default:
                        if(isloggedin())
                            echo '<div id="logout" class="topbutton"><a href="'.$CFG->wwwroot.'/login/logout.php">'.get_string('logout','theme_genesis').'</a></div>';
                        else
                            echo '<div id="login" class="topbutton"><a href="'.$CFG->wwwroot.'/login">'.get_string('login','theme_genesis').'</a></div>';
                }
            ?>
        </div>
    </div>
    <?php if($noslider){ ?>
        <div class="shadow1"></div>
    <?php } ?>
</div>

<?php if($menubar) { ?>
    <div id="menubar" class="row">
        <div class="sklt-container">
            <div class="four columns">
                <a href="<?php echo $CFG->wwwroot; ?>">
                    <?php echo $OUTPUT->logo(); ?>
                </a>
            </div>
            <div class="twelve columns omega">
                <?php echo $OUTPUT->menu(); ?>
            </div>
        </div>
    </div>
<?php } ?>