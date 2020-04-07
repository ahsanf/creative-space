<?php
    include("../../../config.php");
    
    #Loading Settings
    
    /* General Settings */
    $themecolor         = get_config("theme_genesis","themecolor");
    $logourl            = get_config("theme_genesis","logourl");
    $faviconurl         = get_config("theme_genesis","faviconurl");
    $generalsidebar     = get_config("theme_genesis","generalsidebar");
    
    /* Header Settings */
    $headersocialicon   = get_config("theme_genesis","headersocialicon");
    $menudata           = json_decode(get_config("theme_genesis","menudata"));
    $searchbar          = get_config("theme_genesis","searchbar");
    
    /* Footer Settings */
    $footermodule1      = get_config('theme_genesis','footermodule1');
    $footermodule2      = get_config('theme_genesis','footermodule2');
    $footermodule3      = get_config('theme_genesis','footermodule3');
    $copyright          = get_config('theme_genesis','copyright');
    $footersocialicon   = get_config("theme_genesis","footersocialicon");
    
    /* Footer Modules */
    #About Us
    $footermod_aboutus_whitelogo = get_config('theme_genesis','footermod_aboutus_whitelogo');
    $footermod_aboutus_text      = get_config('theme_genesis','footermod_aboutus_text');
    #Links
    $footermod_links             = json_decode(get_config('theme_genesis','footermod_links'));
    #Contact Info
    $footermod_contact_address   = get_config('theme_genesis','footermod_contact_address');
    $footermod_contact_city      = get_config('theme_genesis','footermod_contact_city');
    $footermod_contact_phone     = get_config('theme_genesis','footermod_contact_phone');
    $footermod_contact_mail      = get_config('theme_genesis','footermod_contact_mail');
    #Image
    $footermod_image_title       = get_config('theme_genesis','footermod_image_title');
    $footermod_image_url         = get_config('theme_genesis','footermod_image_url');
    
    /* Frontpage */
    $slidermode                  = get_config('theme_genesis','slidermode');
    $slideshowdata               = json_decode(get_config('theme_genesis','slideshowdata'));
    $sliderpattern               = get_config('theme_genesis','sliderpattern');
    $frontpagesidebar            = get_config('theme_genesis','frontpagesidebar');
    $showfeaturedcourses         = get_config('theme_genesis','showfeaturedcourses');
    $featuredcourses             = json_decode(get_config('theme_genesis','featuredcourses'));
    $showlinkboxes               = get_config('theme_genesis','showlinkboxes');
    $linkboxdata                 = json_decode(get_config('theme_genesis','linkboxdata'));
    
    /* Social Media */
    $social_rss         = get_config('theme_genesis','social_rss');
    $social_twitter     = get_config('theme_genesis','social_twitter');
    $social_dribbble    = get_config('theme_genesis','social_dribbble');
    $social_vimeo       = get_config('theme_genesis','social_vimeo');
    $social_facebook    = get_config('theme_genesis','social_facebook');
    $social_youtube     = get_config('theme_genesis','social_youtube');
    $social_flickr      = get_config('theme_genesis','social_flickr');
    $social_gplus       = get_config('theme_genesis','social_gplus');
    $social_linkedin    = get_config('theme_genesis','social_linkedin');
    $social_tumblr      = get_config('theme_genesis','social_tumblr');
    $social_behance     = get_config('theme_genesis','social_behance');
    $social_wordpress   = get_config('theme_genesis','social_wordpress');
    $social_pinterest   = get_config('theme_genesis','social_pinterest');
    
    
    /* Get course list */
    $courses = get_courses("all","c.fullname ASC");
    $courseList = array();
    $c=0;
    foreach($courses as $key=>$value){
        if($value->id != 1){
            $courseList[$c]["id"] = $value->id;
            $courseList[$c]["fullname"] = $value->fullname;
            $c++;
        }
    }
    
    
    $PAGE->set_pagelayout('admin');
    $PAGE->set_context(context_system::instance());
    $PAGE->set_title('Settings');
    $PAGE->set_url($CFG->wwwroot."/theme/genesis/settings/index.php");
    echo $OUTPUT->header();
    
    $hassidepre = $PAGE->blocks->region_has_content('side-pre', $OUTPUT);
    $hassidepost = $PAGE->blocks->region_has_content('side-post', $OUTPUT);
    $showsidepre = $hassidepre && !$PAGE->blocks->region_completely_docked('side-pre', $OUTPUT);
    $showsidepost = $hassidepost && !$PAGE->blocks->region_completely_docked('side-post', $OUTPUT);
    $courseheader = $coursecontentheader = $coursecontentfooter = $coursefooter = '';
    if (empty($PAGE->layout_options['nocourseheaderfooter'])) {
        $courseheader = $OUTPUT->course_header();
        $coursecontentheader = $OUTPUT->course_content_header();
        if (empty($PAGE->layout_options['nocoursefooter'])) {
            $coursecontentfooter = $OUTPUT->course_content_footer();
            $coursefooter = $OUTPUT->course_footer();
        }
    }
    
    /* Sidebar */
    if($hassidepre)
        $sidebar = "LEFT";
    else if($hassidepost)
        $sidebar = "RIGHT";
    else
        $sidebar = "NONE";

    echo $OUTPUT->doctype();
?>


<script type="text/javascript" src="jquery.min.js"></script>
<script type="text/javascript" src="jquery.ba-hashchange.js"></script>
<script type="text/javascript" src="settings.js"></script>

<body>
    <form method="post" action="save.php">
        <div id="settingstopbar" class="row">
            <h1 class="title">
                Template Settings
                <input type="submit" class="button save" name="saveall" id="saveall" value="Save All" />
            </h1>				
        </div>

        <div id="settings">
            <ul>
                <li tab="tab1" class="first current">General</li>
                <li tab="tab2">Header</li>
                <li tab="tab3">Footer</li>
                <li tab="tab4">Footer Modules</li>
                <li tab="tab5">Frontpage</li>
                <li tab="tab6">Social Media</li>
            </ul>

            <!-- General -->
            <div class="tab-content">
                <h2 class="first">Layout Style</h2>
                <p>Choose a style.</p>
                <label><input type="radio" name="general[themecolor]" value="blue" <?php echo ((isset($themecolor) && $themecolor=="blue")?'checked="checked"':''); ?> />Blue</label>
                <label><input type="radio" name="general[themecolor]" value="orange" <?php echo ((isset($themecolor) && $themecolor=="orange")?'checked="checked"':''); ?> />Orange</label>
                <label><input type="radio" name="general[themecolor]" value="green" <?php echo ((isset($themecolor) && $themecolor=="green")?'checked="checked"':''); ?> />Green</label>

                <h2>Logo URL</h2>
                <p>Use a external link for include your logo image. (200x70)</p>
                <input type="text" name="general[logourl]" value="<?php echo ((isset($logourl))?$logourl:""); ?>" />

                <h2>Favicon URL</h2>
                <p>Use a external link for include your favicon image.</p>
                <input type="text" name="general[faviconurl]" value="<?php echo ((isset($faviconurl))?$faviconurl:""); ?>" />

                <h2>Sidebar</h2>
                <p>Choose the default side of your sidebar in general.</p>
                <label><input type="radio" name="general[generalsidebar]" value="side-pre"  <?php echo ((!isset($generalsidebar) || $generalsidebar!="side-post")?'checked="checked"':''); ?> />Left Bar </label>
                <label><input type="radio" name="general[generalsidebar]" value="side-post" <?php echo ((isset($generalsidebar) && $generalsidebar=="side-post")?'checked="checked"':''); ?> />Right Bar </label>
            </div>

            <!-- Header -->
            <div class="tab-content">					
                <h2 class="first">Header Social Icons</h2>
                <p>Select "ON" to enabled Social Icons on HEADER section and "OFF" to disabled.</p>
                <label><input type="radio" name="header[headersocialicon]" value="1" <?php echo ((isset($headersocialicon) && $headersocialicon=="1")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="header[headersocialicon]" value="0" <?php echo ((!isset($headersocialicon) || $headersocialicon=="0")?'checked="checked"':''); ?> />Off </label>

                <h2>Main Menu</h2>
                <p>Type your main menu texts and complete with they links below. <strong>Note:</strong> You can add a menu using the "Add Menu" button after the text boxes.</p>
                <table id="menu-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="6%">N&ordm;</th>
                        <th width="32%">Menu</th>
                        <th width="56%">Link (URL)</th>
                        <th width="6%">Remove</th>
                    </tr>
                    <?php for ($x=0;$x<sizeof($menudata);$x++) { ?>
                        <tr class="new-menu" data-number="<?php echo ($x+1); ?>">
                            <td class="nMenu"><?php echo ($x+1); ?></td>
                            <td>
                                <input type="text" class="menu" name="header[menudata][<?php echo $x; ?>][text]" value="<?php echo $menudata[$x]->text; ?>">
                            </td>
                            <td>
                                <input type="text" class="link" name="header[menudata][<?php echo $x; ?>][link]" value="<?php echo $menudata[$x]->link; ?>">
                            </td>
                            <td>
                                <div class="button remove" data-remove="new-menu-<?php echo $x; ?>">Remove</div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="button" id="add-new-menu">Add Menu</div>

                <h2>Search Bar</h2>
                <p>Select "ON" to enabled the Search Bar and "OFF" to disabled.</p>
                <label><input type="radio" name="header[searchbar]" value="1" <?php echo ((isset($searchbar) && $searchbar=="1")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="header[searchbar]" value="0" <?php echo ((!isset($searchbar) || $searchbar=="0")?'checked="checked"':''); ?> />Off </label>
            </div>

            <!-- Footer -->
            <div class="tab-content">
                <h2 class="first">Select Modules</h2>
                <p>Choose until 3 options of modules to be showing on FOOTER section.</p>
                <table id="select-modules-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Module 1</th>
                        <td width="70%">
                            <select name="footer[footermodule1]" id="module-1">
                                <option value="" <?php echo ((!isset($footermodule1) || trim($footermodule1)=="")?'selected="selected"':''); ?>>None</option>
                                <option value="aboutus" <?php echo ((isset($footermodule1) && trim($footermodule1)=="aboutus")?'selected="selected"':''); ?>>About Us</option>
                                <option value="links" <?php echo ((isset($footermodule1) && trim($footermodule1)=="links")?'selected="selected"':''); ?>>Links</option>
                                <option value="contactinfo" <?php echo ((isset($footermodule1) && trim($footermodule1)=="contactinfo")?'selected="selected"':''); ?>>Contact Info</option>
                                <option value="image" <?php echo ((isset($footermodule1) && trim($footermodule1)=="image")?'selected="selected"':''); ?>>Image</option>						
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Module 2</th>
                        <td>
                            <select name="footer[footermodule2]" id="module-2">
                                <option value="" <?php echo ((!isset($footermodule2) || trim($footermodule2)=="")?'selected="selected"':''); ?>>None</option>
                                <option value="aboutus" <?php echo ((isset($footermodule2) && trim($footermodule2)=="aboutus")?'selected="selected"':''); ?>>About Us</option>
                                <option value="links" <?php echo ((isset($footermodule2) && trim($footermodule2)=="links")?'selected="selected"':''); ?>>Links</option>
                                <option value="contactinfo" <?php echo ((isset($footermodule2) && trim($footermodule2)=="contactinfo")?'selected="selected"':''); ?>>Contact Info</option>
                                <option value="image" <?php echo ((isset($footermodule2) && trim($footermodule2)=="image")?'selected="selected"':''); ?>>Image</option>						
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>Module 3</th>
                        <td>
                            <select name="footer[footermodule3]" id="module-3">
                                <option value="" <?php echo ((!isset($footermodule3) || trim($footermodule3)=="")?'selected="selected"':''); ?>>None</option>
                                <option value="aboutus" <?php echo ((isset($footermodule3) && trim($footermodule3)=="aboutus")?'selected="selected"':''); ?>>About Us</option>
                                <option value="links" <?php echo ((isset($footermodule3) && trim($footermodule3)=="links")?'selected="selected"':''); ?>>Links</option>
                                <option value="contactinfo" <?php echo ((isset($footermodule3) && trim($footermodule3)=="contactinfo")?'selected="selected"':''); ?>>Contact Info</option>
                                <option value="image" <?php echo ((isset($footermodule3) && trim($footermodule3)=="image")?'selected="selected"':''); ?>>Image</option>						
                            </select>
                        </td>
                    </tr>
                </table>

                <h2>Footer Text / Copyright</h2>
                <p>Type the texts you want view on the last bar.</p>
                <table id="footer-text" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Copyright</th>
                        <td width="70%">
                            <input type="text" name="footer[copyright]" value="<?php echo ((isset($copyright))?$copyright:''); ?>" />
                        </td>
                    </tr>
                </table>

                <h2>Footer Social Icons</h2>
                <p>Select "ON" to enabled Social Icons on FOOTER section and "OFF" to disabled.</p>
                <label><input type="radio" name="footer[footersocialicon]" value="1" <?php echo ((isset($footersocialicon) && $footersocialicon=="1")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="footer[footersocialicon]" value="0" <?php echo ((!isset($footersocialicon) || $footersocialicon=="0")?'checked="checked"':''); ?> />Off </label>
            </div>

            <!-- Footer Modules -->
            <div class="tab-content">
                <h2 class="first">About Us</h2>
                <p>Talk about you. The text and image (200x70) bellow will be on module "About Us" on footer.</p>
                <table id="about-us-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Use an Image (URL): </th>
                        <td width="70%"><input type="text" class="name" name="footermod_aboutus[footermod_aboutus_whitelogo]" value="<?php echo ((isset($footermod_aboutus_whitelogo))?$footermod_aboutus_whitelogo:''); ?>"  /></td>
                    </tr>
                    <tr>
                        <th style="vertical-align: top;">Description: </textarea></th>
                        <td><textarea name="footermod_aboutus[footermod_aboutus_text]"><?php echo ((isset($footermod_aboutus_text))?$footermod_aboutus_text:''); ?></textarea></td>
                    </tr>
                </table>

                <h2>Links</h2>
                <p>Insert useful links on your footer section.</p>
                <table id="link-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="6%">N&ordm;</th>
                        <th width="32%">Link Title</th>
                        <th width="56%">Link URL</th>
                        <th width="6%">Remove</th>
                    </tr>
                    <?php for ($x=0;$x<sizeof($footermod_links);$x++) { ?>
                        <tr class="new-link" data-number="<?php echo ($x+1); ?>">
                            <td class="nLink"><?php echo ($x+1); ?></td>
                            <td>
                                <input type="text" class="menu" name="footermod_links[footermod_links][<?php echo $x; ?>][text]" value="<?php echo $footermod_links[$x]->text; ?>">
                            </td>
                            <td>
                                <input type="text" class="link" name="footermod_links[footermod_links][<?php echo $x; ?>][link]" value="<?php echo $footermod_links[$x]->link; ?>">
                            </td>
                            <td>
                                <div class="button remove" data-remove="new-menu-<?php echo $x; ?>">Remove</div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="button" id="add-new-link"><i class="icon-plus"></i> Add Link</div>

                <h2>Contact Info</h2>
                <p>Type your contact informations.</p>
                <table id="contact-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Address:</th>
                        <td width="70%">
                            <input type="text" name="footermod_contact[footermod_contact_address]" value="<?php echo ((isset($footermod_contact_address))?$footermod_contact_address:''); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th>City:</th>
                        <td><input type="text" name="footermod_contact[footermod_contact_city]" value="<?php echo ((isset($footermod_contact_city))?$footermod_contact_city:''); ?>" /></td>
                    </tr>
                    <tr>
                        <th>Phone Number:</th>
                        <td><input type="text" name="footermod_contact[footermod_contact_phone]" value="<?php echo ((isset($footermod_contact_phone))?$footermod_contact_phone:''); ?>" /></td>
                    </tr>
                    <tr>
                        <th>E-mail:</th>
                        <td><input type="text" name="footermod_contact[footermod_contact_mail]" value="<?php echo ((isset($footermod_contact_mail))?$footermod_contact_mail:''); ?>" /></td>
                    </tr>
                </table>


                <h2>Image</h2>
                <p>You can use an image on footer as some feature of your business.</p>
                <table id="image-module" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Image Title:</th>
                        <td width="70%"><input type="text" name="footermod_image[footermod_image_title]" value="<?php echo ((isset($footermod_image_title))?$footermod_image_title:''); ?>" /></td>
                    </tr>
                    <tr>
                        <th>Image URL:</th>
                        <td><input type="text" name="footermod_image[footermod_image_url]" value="<?php echo ((isset($footermod_image_url))?$footermod_image_url:''); ?>" /></td>
                    </tr>
                </table>
            </div>

            <!-- Frontpage -->
            <div class="tab-content">
                <h2 class="first">Slideshow</h2>
                <p>Select "ON" to enabled Slideshow on your FRONT PAGE of "OFF" to disabled.</p>
                <label><input type="radio" name="frontpage[slidermode]" value="slideshow"  <?php echo ((isset($slidermode) && $slidermode=="slideshow")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="frontpage[slidermode]" value="banner" <?php echo ((!isset($slidermode) || $slidermode!="slideshow")?'checked="checked"':''); ?> />Off </label>

                <h3>Slider Content</h3>
                <p>Type all informations about your conten to each slider. <strong>Note:</strong> You can add a slider using the "Add Slider" button after the text boxes.</p>
                <table id="slide-list" cellpadding="0" cellspacing="0">
                    <?php for ($x=0;$x<sizeof($slideshowdata);$x++) { ?>
                        <tr class="new-slide<?php echo (($x==0)?" first":""); ?>" data-number="<?php echo ($x+1); ?>">
                            <td>
                                <label class="left first">Title:
                                    <input type="text" class="name" name="frontpage[slideshowdata][<?php echo $x; ?>][title]" value="<?php echo $slideshowdata[$x]->title; ?>">
                                </label>
                                <label class="left">Link (URL):
                                    <input type="text" class="link" name="frontpage[slideshowdata][<?php echo $x; ?>][link]" value="<?php echo $slideshowdata[$x]->link; ?>">
                                </label><label class="left">Image:
                                    <input type="text" class="imag" name="frontpage[slideshowdata][<?php echo $x; ?>][image]" value="<?php echo $slideshowdata[$x]->image; ?>">
                                </label>
                            </td>
                            <td>
                                <label>Description:
                                    <textarea class="desc" name="frontpage[slideshowdata][<?php echo $x; ?>][description]"><?php echo $slideshowdata[$x]->description; ?></textarea>
                                </label>
                                <div class="button remove" data-remove="new-slide-<?php echo $x; ?>">Remove slide item</div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="button" id="add-new-slide">Add Slide</div>

                <h2>Background Pattern</h2>
                <p>Choose a pattern to use as slideshow background.</p>
                <table id="background-pattern-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="30%">Choose Pattern: </th>
                        <td width="70%">
                            <select name="frontpage[sliderpattern]" id="bg-pattern">
                                <option value="arches" <?php echo ((isset($sliderpattern) && trim($sliderpattern)=="arches")?'selected="selected"':''); ?>>Arches</option>
                                <option value="escheresque" <?php echo ((isset($sliderpattern) && trim($sliderpattern)=="escheresque")?'selected="selected"':''); ?>>Escheresque</option>
                                <option value="pinstripedsuit" <?php echo ((isset($sliderpattern) && trim($sliderpattern)=="pinstripedsuit")?'selected="selected"':''); ?>>Pinstriped Suit Info</option>
                                <option value="color" <?php echo ((isset($sliderpattern) && trim($sliderpattern)=="color")?'selected="selected"':''); ?>>Theme Color</option>						
                                <option value="waves" <?php echo ((!isset($sliderpattern) || trim($sliderpattern)=="waves")?'selected="selected"':''); ?>>Waves</option>
                            </select>
                        </td>
                    </tr>
                </table>

                <h2>Sidebar</h2>
                <p>Choose the default side of your sidebar.</p>
                <label><input type="radio" name="frontpage[frontpagesidebar]" value="side-pre" <?php echo ((isset($frontpagesidebar) && $frontpagesidebar=="side-pre"))?'checked="checked"':''; ?> />Left Bar </label>
                <label><input type="radio" name="frontpage[frontpagesidebar]" value="side-post" <?php echo ((isset($frontpagesidebar) && $frontpagesidebar=="side-post"))?'checked="checked"':''; ?> />Right Bar </label>
                <label><input type="radio" name="frontpage[frontpagesidebar]" value="" <?php echo ((!isset($frontpagesidebar) || trim($frontpagesidebar)==""))?'checked="checked"':''; ?>/>None </label>

                <h2>Featured Courses</h2>
                <p>Select "ON" to enabled Featured Courses on Frontpage site or "OFF" to disabled.</p>
                <label><input type="radio" name="frontpage[showfeaturedcourses]" value="1" <?php echo ((isset($showfeaturedcourses) && $showfeaturedcourses=="1")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="frontpage[showfeaturedcourses]" value="0" <?php echo ((!isset($showfeaturedcourses) || $showfeaturedcourses=="0")?'checked="checked"':''); ?> />Off </label>

                <h3>Select your features</h3>
                <p>Select your featured courses bellow to show on Frontpage.</p>
                <table id="featured-course-list" cellpadding="0" cellspacing="0">
                    <tr>
                        <th width="6%">N&ordm;</th>
                        <th width="76%">Name of Course</th>
                        <th width="18%">Remove</th>
                    </tr>
                    <?php for($x=0;$x<sizeof($featuredcourses);$x++){ ?>
                    
                        <tr class="new-fcourse" data-number="<?php echo $x+1; ?>" style="">
                            <td class="nFCourse"><?php echo $x+1; ?></td>
                            <td>
                                <select class="name" name="frontpage[featuredcourses][<?php echo $x; ?>]">
                                    <?php for($y=0;$y<sizeof($courseList);$y++){ ?>
                                        <option value="<?php echo $courseList[$y]["id"] ?>" <?php echo (($courseList[$y]["id"]==$featuredcourses[$x])?'selected="selected"':''); ?> ><?php echo $courseList[$y]["fullname"]?></option>
                                    <?php } ?>
                                </select>
                            </td>	
                            <td>
                                <div class="button remove" data-remove="new-fcourse-<?php echo $x; ?>">Remove</div>
                            </td>
                        </tr>
                    
                    <?php } ?>
                </table>
                <div class="button" id="add-new-fcourse">Add Featured Course</div>

                <h2>Link Box</h2>
                <p>Use our predefined icons.</p>
                <label><input type="radio" name="frontpage[showlinkboxes]" value="1" <?php echo ((isset($showlinkboxes) && $showlinkboxes =="1")?'checked="checked"':''); ?> />On </label>
                <label><input type="radio" name="frontpage[showlinkboxes]" value="0" <?php echo ((!isset($showlinkboxes) || $showlinkboxes=="0")?'checked="checked"':''); ?> />Off </label>

                <h3>Link Box Content</h3>
                <p>Type all informations about your conten to each Link Box. <strong>Note:</strong> You can add a Link Box using the "Add Link Box" button after the text boxes.</p>
                <table id="linkbox-list" cellpadding="0" cellspacing="0">
                    <?php for($x=0;$x<sizeof($linkboxdata);$x++){ ?>
                        <tr class="new-linkbox <?php echo ($x==0?'first':''); ?>" data-number="<?php echo $x+1; ?>">
                            <td>
                                <label class="left first">Title:
                                    <input type="text" class="menu" name="frontpage[linkboxdata][<?php echo $x;?>][title]" value="<?php echo $linkboxdata[$x]->title;?>">
                                </label>
                                <label class="left">Link (URL):
                                    <input type="text" class="link" name="frontpage[linkboxdata][<?php echo $x;?>][link]" value="<?php echo $linkboxdata[$x]->link;?>">	
                                </label>
                                <label class="left">Image: 		
                                    <select class="icon" name="frontpage[linkboxdata][<?php echo $x;?>][icon]">
                                        <option value="airplane" <?php echo (($linkboxdata[$x]->icon=='airplane')?'selected="selected"':'');?>>Airplane</option>			
                                        <option value="book" <?php echo (($linkboxdata[$x]->icon=='book')?'selected="selected"':'');?>>Book</option>			
                                        <option value="calendar" <?php echo (($linkboxdata[$x]->icon=='calendar')?'selected="selected"':'');?>>Calendar</option>			
                                        <option value="camera" <?php echo (($linkboxdata[$x]->icon=='camera')?'selected="selected"':'');?>>Camera</option>			
                                        <option value="card" <?php echo (($linkboxdata[$x]->icon=='card')?'selected="selected"':'');?>>Card</option>			
                                        <option value="clock" <?php echo (($linkboxdata[$x]->icon=='clock')?'selected="selected"':'');?>>Clock</option>			
                                        <option value="cloud" <?php echo (($linkboxdata[$x]->icon=='cloud')?'selected="selected"':'');?>>Cloud</option>			
                                        <option value="coffee" <?php echo (($linkboxdata[$x]->icon=='coffee')?'selected="selected"':'');?>>Coffee</option>			
                                        <option value="communication" <?php echo (($linkboxdata[$x]->icon=='communication')?'selected="selected"':'');?>>Communication</option>			
                                        <option value="cross" <?php echo (($linkboxdata[$x]->icon=='cross')?'selected="selected"':'');?>>Cross</option>			
                                        <option value="education" <?php echo (($linkboxdata[$x]->icon=='education')?'selected="selected"':'');?>>Education</option>			
                                        <option value="heart" <?php echo (($linkboxdata[$x]->icon=='heart')?'selected="selected"':'');?>>Heart</option>			
                                        <option value="image" <?php echo (($linkboxdata[$x]->icon=='image')?'selected="selected"':'');?>>Image</option>			
                                        <option value="link" <?php echo (($linkboxdata[$x]->icon=='link')?'selected="selected"':'');?>>Link</option>			
                                        <option value="location" <?php echo (($linkboxdata[$x]->icon=='location')?'selected="selected"':'');?>>Location</option>			
                                        <option value="locked" <?php echo (($linkboxdata[$x]->icon=='locked')?'selected="selected"':'');?>>Locked</option>			
                                        <option value="male" <?php echo (($linkboxdata[$x]->icon=='male')?'selected="selected"':'');?>>Male</option>			
                                        <option value="members" <?php echo (($linkboxdata[$x]->icon=='members')?'selected="selected"':'');?>>Members</option>			
                                        <option value="music" <?php echo (($linkboxdata[$x]->icon=='music')?'selected="selected"':'');?>>Music</option>			
                                        <option value="pen" <?php echo (($linkboxdata[$x]->icon=='pen')?'selected="selected"':'');?>>Pen</option>			
                                        <option value="phone" <?php echo (($linkboxdata[$x]->icon=='phone')?'selected="selected"':'');?>>Phone</option>			
                                        <option value="rate" <?php echo (($linkboxdata[$x]->icon=='rate')?'selected="selected"':'');?>>Rate</option>			
                                        <option value="smartphone" <?php echo (($linkboxdata[$x]->icon=='smartphone')?'selected="selected"':'');?>>Smartphone</option>			
                                        <option value="stats" <?php echo (($linkboxdata[$x]->icon=='stats')?'selected="selected"':'');?>>Stats</option>			
                                        <option value="technology" <?php echo (($linkboxdata[$x]->icon=='technology')?'selected="selected"':'');?>>Technology</option>			
                                        <option value="tick">Tick</option>			
                                        <option value="wrench">Wrench</option>		
                                    </select>	
                                </label>
                            </td>	
                            <td>
                                <label>Description: 		
                                    <textarea class="desc" name="frontpage[linkboxdata][<?php echo $x;?>][text]"><?php echo $linkboxdata[$x]->text;?></textarea>	
                                </label>
                                <div class="button remove" data-remove="new-linkbox-<?php echo $x;?>">Remove linkbox item</div>
                            </td>
                        </tr>
                    <?php } ?>
                </table>
                <div class="button" id="add-new-linkbox">Add Link Box</div>
            </div>
            <div class="tab-content">
                <h2 class="first">Social Media</h2>
                <p>Type your links and share with people.</p>
                <table id="social-list" cellpadding="0" cellspacing="0">
                    <tr><th width="30%">Network Name:</th><th width="70%">Your Link</th></tr>
                    <tr>
                        <td class="lbl"><icon class="social rss">Rss</icon></td>
                        <td><input type="text" name="social[social_rss]" value="<?php echo ((isset($social_rss))?$social_rss:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social twitter">Twitter</icon></td>
                        <td><input type="text" name="social[social_twitter]" value="<?php echo ((isset($social_twitter))?$social_twitter:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social dribbble">Dribbble</icon></td>
                        <td><input type="text" name="social[social_dribbble]" value="<?php echo ((isset($social_dribbble))?$social_dribbble:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social vimeo">Vimeo</icon></td>
                        <td><input type="text" name="social[social_vimeo]" value="<?php echo ((isset($social_vimeo))?$social_vimeo:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social facebook">Facebook</icon></td>
                        <td><input type="text" name="social[social_facebook]" value="<?php echo ((isset($social_facebook))?$social_facebook:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social youtube">Youtube</icon></td>
                        <td><input type="text" name="social[social_youtube]" value="<?php echo ((isset($social_youtube))?$social_youtube:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social flickr">Flickr</icon></td>
                        <td><input type="text" name="social[social_flickr]" value="<?php echo ((isset($social_flickr))?$social_flickr:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social googleplus">Google+</icon></td>
                        <td><input type="text" name="social[social_gplus]" value="<?php echo ((isset($social_gplus))?$social_gplus:''); ?>" /></td>

                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social linkedin">Linked In</icon></td>
                        <td><input type="text" name="social[social_linkedin]" value="<?php echo ((isset($social_linkedin))?$social_linkedin:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social tumblr">Tumblr</icon></td>
                        <td><input type="text" name="social[social_tumblr]" value="<?php echo ((isset($social_tumblr))?$social_tumblr:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social behance">Behance</icon></td>
                        <td><input type="text" name="social[social_behance]" value="<?php echo ((isset($social_behance))?$social_behance:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social wordpress">Wordpress</icon></td>
                        <td><input type="text" name="social[social_wordpress]" value="<?php echo ((isset($social_wordpress))?$social_wordpress:''); ?>" /></td>
                    </tr>
                    <tr>
                        <td class="lbl"><icon class="social pinterest">Pinterest</icon></td>
                        <td><input type="text" name="social[social_pinterest]" value="<?php echo ((isset($social_pinterest))?$social_pinterest:''); ?>" /></td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
</body>
</html>

<?php 
    echo $OUTPUT->footer(); 
?>