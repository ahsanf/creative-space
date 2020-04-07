<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Parent theme: Bootstrapbase by Bas Brands
 * Built on: Essential by Julian Ridden
 *
 * @package   theme_lambda
 * @copyright 2014 redPIthemes
 *
 */

$haslogo = (!empty($PAGE->theme->settings->logo));

$hasheaderprofilepic = (empty($PAGE->theme->settings->headerprofilepic)) ? false : $PAGE->theme->settings->headerprofilepic;

$checkuseragent = '';
if (!empty($_SERVER['HTTP_USER_AGENT'])) {
    $checkuseragent = $_SERVER['HTTP_USER_AGENT'];
}
$username = get_string('username');
if (strpos($checkuseragent, 'MSIE 8')) {$username = str_replace("'", "&prime;", $username);}
?>

<?php if($PAGE->theme->settings->socials_position==1) { ?>
    	<div class="container-fluid socials-header"> 
    	<?php require_once(dirname(__FILE__).'/socials.php');?>
        </div>
<?php
} ?>

    <header id="page-header" class="clearfix">
       
    <div class="container-fluid">    
    <div class="row-fluid">
    <!-- HEADER: LOGO AREA -->
        	
            <?php if (!$haslogo) { ?>
            	<div class="span6">
              		<h1 id="title" style="line-height: 2em"><?php echo $SITE->shortname; ?></h1>
                </div>
            <?php } else { ?>
                <div class="logo-header">
                	<a class="logo" href="<?php echo $CFG->wwwroot; ?>" title="<?php print_string('home'); ?>">
                    <?php 
					echo html_writer::empty_tag('img', array('src'=>$PAGE->theme->setting_file_url('logo', 'logo'), 'class'=>'logo', 'alt'=>'logo'));
					?>
                    </a>
                </div>
            <?php } ?>      	
            
            <div class="login-header">
            <div class="profileblock">
            
            <?php 
	function get_content () {
	global $USER, $CFG, $SESSION, $COURSE;
	$wwwroot = '';
	$signup = '';}

	if (empty($CFG->loginhttps)) {
		$wwwroot = $CFG->wwwroot;
	} else {
		$wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
	}

		if (!isloggedin() or isguestuser()) { ?>
		<form class="navbar-form pull-right" method="post" action="<?php echo $wwwroot; ?>/login/index.php?authldap_skipntlmsso=1">
		<div id="block-login">
		<label id="user"><i class="fa fa-user"></i></label>	
		<input id="inputName" class="span2" type="text" name="username" placeholder="<?php echo $username; ?>" style="margin-bottom:10px;">
		<label id="pass"><i class="fa fa-key"></i></label>        
		<input id="inputPassword" class="span2" type="password" name="password" id="password" placeholder="<?php echo get_string('password'); ?>">        
		<input type="submit" id="submit" name="submit" value=""/>
		</div>
		</form>
        
	<?php } else { 

 		echo '<div id="loggedin-user">';		
		echo $OUTPUT->user_menu();
		echo $OUTPUT->user_picture($USER, array('size' => 80, 'class' => 'welcome_userpicture'));		
		echo '</div>';

	}?>

	</div>
	</div>
            
    </div>
    </div>
               
</header>

<header role="banner" class="navbar">
    <nav role="navigation" class="navbar-inner">
        <div class="container-fluid">
            <a class="brand" href="<?php echo $CFG->wwwroot;?>"><?php echo $SITE->shortname; ?></a>
            <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="nav-collapse collapse">
                <?php echo $OUTPUT->custom_menu(); ?>
                <div class="nav-divider-right"></div>
                <ul class="nav pull-right">
                    <li><?php echo $OUTPUT->page_heading_menu(); ?></li>
                </ul>
                
                <form id="search" action="<?php echo $CFG->wwwroot;?>/course/search.php" method="GET">
                <div class="nav-divider-left"></div>							
					<input id="coursesearchbox" type="text" onFocus="if(this.value =='<?php echo get_string('searchcourses'); ?>' ) this.value=''" onBlur="if(this.value=='') this.value='<?php echo get_string('searchcourses'); ?>'" value="<?php echo get_string('searchcourses'); ?>" name="search">
					<input type="submit" value="">							
				</form>
                
            </div>
        </div>
    </nav>
</header>