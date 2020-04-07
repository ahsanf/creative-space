<?php
class theme_genesis_core_renderer extends core_renderer {
    private function footermod_aboutus(){
        $logourl = get_config('theme_genesis', 'footermod_aboutus_whitelogo');
        $text = get_config('theme_genesis', 'footermod_aboutus_text');
        
        $content = '<div class="footermod footermod_aboutus">';
        
        if(!$logourl || trim($logourl)=="")
            $content .= '<div id="defaultlogowhite"></div>';
        else{
            $content .= '<div id="logowhite"></div>';
        }
        
        $content .= "<p>".$text."</p>";
        
        $content .= "</div>";
        
        return $content;
    }

    private function footermod_links(){
        $links = json_decode(get_config('theme_genesis', 'footermod_links'));
        
        $content = '<div class="footermod footermod_links">'; 
        $content .= '<p class="title">Links</p>';
        
        $content .= '<ul class="links">';
        
        for($x=0;$x<sizeof($links);$x++){
            $content .= '<li><a target="blank" href="'.$links[$x]->link.'">'.$links[$x]->text.'</a></li>';
        }
        
        $content .= '</ul>';
        $content .= '</div>';
        
        return $content;
    }
    
    private function footermod_contactinfo(){
        $address = get_config('theme_genesis', 'footermod_contact_address');
        $city = get_config('theme_genesis', 'footermod_contact_city');
        $phone = get_config('theme_genesis', 'footermod_contact_phone');
        $mail = get_config('theme_genesis', 'footermod_contact_mail');
        
        $content = '<div class="footermod footermod_contactinfo">';
        
        $content .= '<p class="title">'.get_string('contactinfo','theme_genesis').'</p>';
        
        $content .= '<ul class="contactinfos">';
        $content .= '<li class="address_icon">'.$address.'</li>';
        $content .= '<li class="city_icon">'.$city.'</li>';
        $content .= '<li class="phone_icon">'.$phone.'</li>';
        $content .= '<li class="mail_icon">'.$mail.'</li>';
        $content .= '</ul>';
        
        $content .= "</div>";
        
        return $content;
    }
    
    private function footermod_image(){
        $title = get_config('theme_genesis', 'footermod_image_title');
        $src = get_config('theme_genesis', 'footermod_image_url');
        
        $content = '<div class="footermod footermod_image">';
        
        $content .= '<p class="title">'.$title.'</p>';
        $content .= '<div class="image"><img src="'.$src.'"/></div>';
        
        $content .= "</div>";
        
        return $content;
    }
    
    protected function footermod($modulearea){
        $module = get_config("theme_genesis","footer".$modulearea);
        if(trim($module)!=""){
            $module = "footermod_".$module;
            return $this->$module();
        }else{
            return ' ';
        }
    }
    
    protected function linkbox($CFG,$sidebar){
        $linkboxitems = json_decode(get_config('theme_genesis', 'linkboxdata'));
        $content = '';
        
        $inline = 0;
        if($sidebar == "NONE")
            $inline = 4;
        else
            $inline = 3;
        
        for($x=1;$x<=sizeof($linkboxitems);$x++){
            $align = "";
            if($x % $inline == 1)
                $align = "alpha";
            else if($x % $inline == 0)
                $align = "omega";
            
            $content .= '<div class="four columns linkbox '.$align.'">
                            <div class="linkboxicon '.$linkboxitems[$x-1]->icon.'icon"></div>
                            <p class="title">'.$linkboxitems[$x-1]->title.'</p>
                            <p class="description">'.$linkboxitems[$x-1]->text.'</p>
                            <a href="'.$linkboxitems[$x-1]->link.'"><div class="readmore">'.get_string('readmore','theme_genesis').'</div></a>
                        </div>';
        }
        return $content;
    }
    protected function mycourses($CFG,$sidebar){
        $mycourses = enrol_get_users_courses($_SESSION['USER']->id);
        
        $courselist = array();
        foreach ($mycourses as $key=>$val){
            $courselist[] = $val->id;
        }
        
        $content = '';
        
        $coursesinline = 0;
        
        if($sidebar == "NONE")
            $coursesinline = 4;
        else
            $coursesinline = 3;
        
        for($x=1;$x<=sizeof($courselist);$x++){
            $course = get_course($courselist[$x-1]);
            $title = $course->fullname;
            
            if ($course instanceof stdClass) {
                require_once($CFG->libdir. '/coursecatlib.php');
                $course = new course_in_list($course);
            }

            $url = $CFG->wwwroot."/theme/genesis/pix/coursenoimage.jpg";
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if (!$isimage) {
                    $url = $CFG->wwwroot."/theme/genesis/pix/coursenoimage.jpg";
                }
            }
            
            $align = "";
            if($x % $coursesinline == 1)
                $align = "alpha";
            else if($x % $coursesinline == 0)
                $align = "omega";
                    
            $content .= '<div class="four columns course '.$align.'">
                            <ul class="grid cs-style-3">
                                <li>
                                    <figure>
                                        <img src="'.$url.'" alt="'.$title.'">
                                        <figcaption>
                                            <h3>'.$title.'</h3>
                                            <a href="'.$CFG->wwwroot.'/course/view.php?id='.$courselist[$x-1].'">Take a look</a>
                                        </figcaption>
                                    </figure>
                                </li>
                            </ul>
                         </div>';
        }
                    
        return $content;
    }
    
    protected function featuredcourses($CFG,$sidebar){
        $featuredcourses = get_config('theme_genesis', 'featuredcourses');
        $courselist = json_decode($featuredcourses);
        $content = '';
        
        $coursesinline = 0;
        
        if($sidebar == "NONE")
            $coursesinline = 4;
        else
            $coursesinline = 3;
        
        for($x=1;$x<=sizeof($courselist);$x++){
            $course = get_course($courselist[$x-1]);
            $title = $course->fullname;
            
            if ($course instanceof stdClass) {
                require_once($CFG->libdir. '/coursecatlib.php');
                $course = new course_in_list($course);
            }

            $url = $CFG->wwwroot."/theme/genesis/pix/coursenoimage.jpg";
            foreach ($course->get_course_overviewfiles() as $file) {
                $isimage = $file->is_valid_image();
                $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                        '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                        $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
                if (!$isimage) {
                    $url = $CFG->wwwroot."/theme/genesis/pix/coursenoimage.jpg";
                }
            }
            
            $align = "";
            if($x % $coursesinline == 1)
                $align = "alpha";
            else if($x % $coursesinline == 0)
                $align = "omega";
                    
            $content .= '<div class="four columns course '.$align.'">
                            <ul class="grid cs-style-3">
                                <li>
                                    <figure>
                                        <img src="'.$url.'" alt="'.$title.'">
                                        <figcaption>
                                            <h3>'.$title.'</h3>
                                            <a href="'.$CFG->wwwroot.'/course/view.php?id='.$courselist[$x-1].'">Take a look</a>
                                        </figcaption>
                                    </figure>
                                </li>
                            </ul>
                         </div>';
        }
                    
        return $content;
    }
    
    protected function menu(){
        $menuitems = json_decode(get_config('theme_genesis', 'menudata'));
        $content = '<nav id="menu">';
        
        $currentpage = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        
        for($x=0;$x<sizeof($menuitems);$x++){
            $content .= '<a href="'.$menuitems[$x]->link.'"><span data-hover="'.$menuitems[$x]->text.'">'.$menuitems[$x]->text.'</span></a>';
        }
        
        $content .= '</nav>';
        return $content;
    }

    protected function searchbar($wwwroot){
        $searchbar = get_config('theme_genesis', 'searchbar');

        if($searchbar && isloggedin()){
            $content = '<div id="sb-search" class="sb-search">
                            <form action="'.$wwwroot.'/admin/search.php" method="GET">
                                <input class="sb-search-input" placeholder="'.get_string('search','theme_genesis').' ..." type="text" value="" name="query" id="search">
                                <input class="sb-search-submit" type="submit" value="">
                                <span class="sb-icon-search"></span>
                            </form>
                        </div>';
        }else{
            $content = " ";
        }
        
        return $content;
    }
    
    public function logo(){
        $logourl = get_config('theme_genesis', 'logourl');
        $content = '';
        if(!$logourl || trim($logourl)=="")
            $content = '<div id="defaultlogo"></div>';
        else{
            $content = '<div id="logo"></div>';
        }
        return $content;
    }

    public function favicon() {
        $faviconurl = get_config('theme_genesis', 'faviconurl');
        if(!$faviconurl || trim($faviconurl)=="")
            $faviconurl = $this->page->theme->pix_url('favicon', 'theme');
        return $faviconurl;
    }

    protected function copyright(){
        $copyright = get_config('theme_genesis', 'copyright');
        return $copyright;
    }

    protected function socialicons($area){
        $hassocialicons = get_config('theme_genesis', $area.'socialicon');
        
        $social_facebook = get_config('theme_genesis','social_facebook');
        $social_twitter = get_config('theme_genesis','social_twitter');
        $social_gplus = get_config('theme_genesis','social_gplus');
        $social_youtube = get_config('theme_genesis','social_youtube');
        $social_vimeo =  get_config('theme_genesis','social_vimeo');
        $social_wordpress = get_config('theme_genesis','social_wordpress');
        $social_pinterest = get_config('theme_genesis','social_pinterest');
        $social_flickr = get_config('theme_genesis','social_flickr');
        $social_rss = get_config('theme_genesis','social_rss');
        $social_dribbble = get_config('theme_genesis','social_dribbble');
        $social_linkedin = get_config('theme_genesis','social_linkedin');
        $social_tumblr = get_config('theme_genesis','social_tumblr');
        $social_behance = get_config('theme_genesis','social_behance');
        
        $content = '';
        
        if($hassocialicons){
            if(isset($social_facebook) && trim($social_facebook)!="")
                $content .= '<a href="'.$social_facebook.'" target="blank"><div class="facebooksocial iconsocial"></div></a>';
            if(isset($social_twitter) && trim($social_twitter)!="")
                $content .= '<a href="'.$social_twitter.'" target="blank"><div class="twittersocial iconsocial"></div></a>';
            if(isset($social_gplus) && trim($social_gplus)!="")
                $content .= '<a href="'.$social_gplus.'" target="blank"><div class="gplussocial iconsocial"></div></a>';
            if(isset($social_youtube) && trim($social_youtube)!="")
                $content .= '<a href="'.$social_youtube.'" target="blank"><div class="youtubesocial iconsocial"></div></a>';
            if(isset($social_vimeo) && trim($social_vimeo)!="")
                $content .= '<a href="'.$social_vimeo.'" target="blank"><div class="vimeosocial iconsocial"></div></a>';
            if(isset($social_wordpress) && trim($social_wordpress)!="")
                $content .= '<a href="'.$social_wordpress.'" target="blank"><div class="wordpresssocial iconsocial"></div></a>';
            if(isset($social_pinterest) && trim($social_pinterest)!="")
                $content .= '<a href="'.$social_pinterest.'" target="blank"><div class="pinterestsocial iconsocial"></div></a>';
            if(isset($social_flickr) && trim($social_flickr)!="")
                $content .= '<a href="'.$social_flickr.'" target="blank"><div class="flickrsocial iconsocial"></div></a>';
            if(isset($social_rss) && trim($social_rss)!="")
                $content .= '<a href="'.$social_rss.'" target="blank"><div class="rsssocial iconsocial"></div></a>';
            if(isset($social_dribbble) && trim($social_dribbble)!="")
                $content .= '<a href="'.$social_dribbble.'" target="blank"><div class="dribbblesocial iconsocial"></div></a>';
            if(isset($social_linkedin) && trim($social_linkedin)!="")
                $content .= '<a href="'.$social_linkedin.'" target="blank"><div class="linkedinsocial iconsocial"></div></a>';
            if(isset($social_tumblr) && trim($social_tumblr)!="")
                $content .= '<a href="'.$social_tumblr.'" target="blank"><div class="tumblrsocial iconsocial"></div></a>';            
            if(isset($social_behance) && trim($social_behance)!="")
                $content .= '<a href="'.$social_behance.'" target="blank"><div class="behancesocial iconsocial"></div></a>';            
        } 
        
        $content .= ' ';
        
        return $content;
    }

    protected function slider($pagelayout){
        if($pagelayout == 'frontpage')
            $slidertype = get_config('theme_genesis', 'slidermode');
        else
            $slidertype = 'banner';
        
        $sliderpattern = "slider".get_config('theme_genesis', 'sliderpattern').get_config('theme_genesis', 'themecolor');
        $slideritems = json_decode(get_config('theme_genesis', 'slideshowdata'));
        
        $content = '<div id="sliderarea" class="row '.$sliderpattern.'">
                        <div class="shadow1"></div>';

        switch ($slidertype) {
            case 'slideshow':
                $content .= '<div id="slider1" class="da-slider">';

                for($x=0;$x<sizeof($slideritems);$x++)
                {
                    $content .= '<div class="da-slide">';
                    $content .= '<h2>'.$slideritems[$x]->title.'</h2>';
                    $content .= '<p>'.$slideritems[$x]->description.'</p>';
                    $content .= '<a href="'.$slideritems[$x]->link.'" class="da-link">Read more</a>';
                    $content .= '<div class="da-img"><img src="'.$slideritems[$x]->image.'" alt="image01" /></div>';
                    $content .= '</div>';
                }

                $content .= '<nav class="da-arrows">
                                <span class="da-arrows-prev"></span>
                                <span class="da-arrows-next"></span>
                            </nav>';

                $content .= '</div>';
                break;
            case 'banner':
                $content .= '<div id="sliderbanner" class="'.$sliderpattern.'"></div>';

            break;

            default: $content .= '';
                break;
        }

        $content .= '</div>';

        return $content;
    }

    protected function forcefooter(){
        echo '<style type="text/css">
                 @media screen and (min-height: 730px){
                    #footer,#footerend{
                        position: absolute !important;
                    }
                 }
             </style>';
    }
    
    protected function render_navigation_node(navigation_node $item) {
        $content = $item->get_content();
        $title = $item->get_title();
        if ($item->icon instanceof renderable && !$item->hideicon) {
            if(trim($content) == 'Genesis')
                $item->icon->pix = 'g/genesis_'.get_config('theme_genesis','themecolor');
            $icon = $this->render($item->icon);
            if(trim($content) == 'Genesis')
                $content = '<b>Genesis</b>';
            $content = $icon.$content; // use CSS for spacing of icons
        }
        if ($item->helpbutton !== null) {
            $content = trim($item->helpbutton).html_writer::tag('span', $content, array('class'=>'clearhelpbutton', 'tabindex'=>'0'));
        }
        if ($content === '') {
            return '';
        }
        if ($item->action instanceof action_link) {
            $link = $item->action;
            if ($item->hidden) {
                $link->add_class('dimmed');
            }
            if (!empty($content)) {
                // Providing there is content we will use that for the link content.
                $link->text = $content;
            }
            $content = $this->render($link);
        } else if ($item->action instanceof moodle_url) {
            $attributes = array();
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            $content = html_writer::link($item->action, $content, $attributes);

        } else if (is_string($item->action) || empty($item->action)) {
            $attributes = array('tabindex'=>'0'); //add tab support to span but still maintain character stream sequence.
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            $content = html_writer::tag('span', $content, $attributes);
        }
        return $content;
    }
}
?>
