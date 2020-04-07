<?php
    $ADMIN->add('root', new admin_externalpage('genesis', 'Genesis', $CFG->wwwroot."/theme/genesis/settings/index.php"));
    
    /* Setting default settings */
    
    /* General */
    $themecolor = get_config('theme_genesis','themecolor');
    if(!isset($themecolor) || trim($themecolor) == "") set_config('themecolor','orange','theme_genesis');
    
    $generalsidebar = get_config('theme_genesis','generalsidebar');
    if(!isset($generalsidebar) || trim($generalsidebar) == "") set_config('generalsidebar','side-pre','theme_genesis');
    
    $logourl = get_config('theme_genesis','logourl');
    if(!isset($logourl) || trim($logourl) == "") set_config('logourl','','theme_genesis');
    
    $faviconurl = get_config('theme_genesis','faviconurl');
    if(!isset($faviconurl) || trim($faviconurl) == "") set_config('faviconurl','','theme_genesis');
    
    /* Frontpage */
    $frontpagesidebar = get_config('theme_genesis','frontpagesidebar');
    if(!isset($frontpagesidebar) || trim($frontpagesidebar) == "") set_config('frontpagesidebar','','theme_genesis');
   
    $featuredcourses = get_config('theme_genesis','featuredcourses');
    if(!isset($featuredcourses) || trim($featuredcourses) == "") set_config('featuredcourses','','theme_genesis');
    
    $showfeaturedcourses = get_config('theme_genesis','showfeaturedcourses');
    if(!isset($showfeaturedcourses) || trim($showfeaturedcourses) == "") set_config('showfeaturedcourses','0','theme_genesis');
        
    /* Linkbox */
    
    $linkboxdata = get_config('theme_genesis','linkboxdata');
    if(!isset($linkboxdata) || trim($linkboxdata) == "") set_config('linkboxdata','[{"icon":"heart","title":"Linkbox 1","text":"Pellentesque enim tellus, consectetur id erat auctor, rhoncus dapibus nibh. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis condimentum cursus nibh, sed tincidunt sem gravida congue.","link":"#"},{"icon":"camera","title":"Linkbox 2","text":"Pellentesque enim tellus, consectetur id erat auctor, rhoncus dapibus nibh. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis condimentum cursus nibh, sed tincidunt sem gravida congue.","link":"#"},{"icon":"link","title":"Linkbox 3","text":"Pellentesque enim tellus, consectetur id erat auctor, rhoncus dapibus nibh. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis condimentum cursus nibh, sed tincidunt sem gravida congue.","link":"#"},{"icon":"rate","title":"Linkbox 4","text":"Pellentesque enim tellus, consectetur id erat auctor, rhoncus dapibus nibh. Interdum et malesuada fames ac ante ipsum primis in faucibus. Duis condimentum cursus nibh, sed tincidunt sem gravida congue.","link":"#"}]','theme_genesis');
    
    $showlinkboxes = get_config('theme_genesis','showlinkboxes');
    if(!isset($showlinkboxes) || trim($showlinkboxes) == "") set_config('showlinkboxes','1','theme_genesis');    
    
    /* Footer */
    $copyright = get_config('theme_genesis','copyright');
    if(!isset($copyright) || trim($copyright) == "") set_config('copyright','All rights reserved  | Ararazu ®','theme_genesis'); 

    $footermodule1 = get_config('theme_genesis','footermodule1');
    if(!isset($footermodule1) || trim($footermodule1) == "") set_config('footermodule1','aboutus','theme_genesis');     
    
    $footermodule2 = get_config('theme_genesis','footermodule2');
    if(!isset($footermodule2) || trim($footermodule2) == "") set_config('footermodule2','links','theme_genesis');     
    
    $footermodule3 = get_config('theme_genesis','footermodule3');
    if(!isset($footermodule3) || trim($footermodule3) == "") set_config('footermodule3','contactinfo','theme_genesis');     
    
    /* Header */
    $searchbar = get_config('theme_genesis','searchbar');
    if(!isset($searchbar) || trim($searchbar) == "") set_config('searchbar','1','theme_genesis');
    
    $menudata = get_config('theme_genesis','menudata');
    if(!isset($menudata) || trim($menudata) == "") set_config('menudata','[{"text":"Link 1","link":"#"},{"text":"Link 2","link":"#"},{"text":"Link 3","link":"#"},{"text":"Link 4","link":"#"},{"text":"Link 5","link":"#"}]','theme_genesis');     
    
    $searchbar = get_config('theme_genesis','searchbar');
    if(!isset($searchbar) || trim($searchbar) == "") set_config('searchbar','1','theme_genesis');     
    
    /* Social Icons */
    
    $headersocialicon = get_config('theme_genesis','headersocialicon');
    if(!isset($headersocialicon) || trim($headersocialicon) == "") set_config('headersocialicon','1','theme_genesis');    
    
    $footersocialicon = get_config('theme_genesis','footersocialicon');
    if(!isset($footersocialicon) || trim($footersocialicon) == "") set_config('footersocialicon','1','theme_genesis');    
    
    
    /* Slider */
    
    $slidermode = get_config('theme_genesis','slidermode');
    if(!isset($slidermode) || trim($slidermode) == "") set_config('slidermode','banner','theme_genesis');    
    
    $sliderpattern = get_config('theme_genesis','sliderpattern');
    if(!isset($sliderpattern) || trim($sliderpattern) == "") set_config('sliderpattern','waves','theme_genesis');    
    
    $slideshowdata = get_config('theme_genesis','slideshowdata');
    if(!isset($slideshowdata) || trim($slideshowdata) == "") set_config('slideshowdata','','theme_genesis');    
    
    
    /* Footer modules */
    
    $footermod_aboutus_whitelogo = get_config('theme_genesis','footermod_aboutus_whitelogo');
    if(!isset($footermod_aboutus_whitelogo) || trim($footermod_aboutus_whitelogo) == "") set_config('footermod_aboutus_whitelogo','','theme_genesis');    
    
    $footermod_aboutus_text = get_config('theme_genesis','footermod_aboutus_text');
    if(!isset($footermod_aboutus_text) || trim($footermod_aboutus_text) == "") set_config('footermod_aboutus_text','Donec vitae eros sit amet nibh fringilla hendrerit non at odio. Sed eu lacus hendrerit, venenatis elit ac, mollis massa. Sed nec enim ac justo feugiat tincidunt vitae sed felis. Pellentesque tincidunt viverra justo, eget posuere sem facilisis sit amet.','theme_genesis');        

    $footermod_image_title = get_config('theme_genesis','footermod_image_title');
    if(!isset($footermod_image_title) || trim($footermod_image_title) == "") set_config('footermod_image_title','Image Title','theme_genesis');    

    $footermod_image_url = get_config('theme_genesis','footermod_image_url');
    if(!isset($footermod_image_url) || trim($footermod_image_url) == "") set_config('footermod_image_url','','theme_genesis');    

    $footermod_links = get_config('theme_genesis','footermod_links');
    if(!isset($footermod_links) || trim($footermod_links) == "") set_config('footermod_links','[{"text":"Facebook - Share this!","link":"https:\/\/www.facebook.com\/"},{"text":"Google","link":"https:\/\/www.google.com.br\/"},{"text":"Twitter - Follow us!","link":"https:\/\/twitter.com\/"},{"text":"Ararazu","link":"http:\/\/themeforest.net\/user\/ararazu"}]','theme_genesis');
  
    $footermod_contact_address = get_config('theme_genesis','footermod_contact_address');
    if(!isset($footermod_contact_address) || trim($footermod_contact_address) == "") set_config('footermod_contact_address','Address 42','theme_genesis');    

    $footermod_contact_city = get_config('theme_genesis','footermod_contact_city');
    if(!isset($footermod_contact_city) || trim($footermod_contact_city) == "") set_config('footermod_contact_city','Rio - Brazil','theme_genesis');    

    $footermod_contact_phone = get_config('theme_genesis','footermod_contact_phone');
    if(!isset($footermod_contact_phone) || trim($footermod_contact_phone) == "") set_config('footermod_contact_phone','+99 (99) 9999-9999','theme_genesis');    

    $footermod_contact_mail = get_config('theme_genesis','footermod_contact_mail');
    if(!isset($footermod_contact_mail) || trim($footermod_contact_mail) == "") set_config('footermod_contact_mail','email@email.com','theme_genesis');    
    
    /* Social Icons */
    
    $social_rss = get_config('theme_genesis','social_rss');
    if(!isset($social_rss) || trim($social_rss) == "") set_config('social_rss','','theme_genesis');    
    
    $social_twitter = get_config('theme_genesis','social_twitter');
    if(!isset($social_twitter) || trim($social_twitter) == "") set_config('social_twitter','','theme_genesis');    
    
    $social_dribbble = get_config('theme_genesis','social_dribbble');
    if(!isset($social_dribbble) || trim($social_dribbble) == "") set_config('social_dribbble','','theme_genesis');    
   
    $social_vimeo = get_config('theme_genesis','social_vimeo');
    if(!isset($social_vimeo) || trim($social_vimeo) == "") set_config('social_vimeo','','theme_genesis');  
    
    $social_facebook = get_config('theme_genesis','social_facebook');
    if(!isset($social_facebook) || trim($social_facebook) == "") set_config('social_facebook','','theme_genesis');
    
    $social_youtube = get_config('theme_genesis','social_youtube');
    if(!isset($social_youtube) || trim($social_youtube) == "") set_config('social_youtube','','theme_genesis');
    
    $social_flickr = get_config('theme_genesis','social_flickr');
    if(!isset($social_flickr) || trim($social_flickr) == "") set_config('social_flickr','','theme_genesis');
    
    $social_gplus = get_config('theme_genesis','social_gplus');
    if(!isset($social_gplus) || trim($social_gplus) == "") set_config('social_gplus','','theme_genesis');
    
    $social_linkedin = get_config('theme_genesis','social_linkedin');
    if(!isset($social_linkedin) || trim($social_linkedin) == "") set_config('social_linkedin','','theme_genesis');
    
    $social_tumblr = get_config('theme_genesis','social_tumblr');
    if(!isset($social_tumblr) || trim($social_tumblr) == "") set_config('social_tumblr','','theme_genesis');
    
    $social_behance = get_config('theme_genesis','social_behance');
    if(!isset($social_behance) || trim($social_behance) == "") set_config('social_behance','','theme_genesis');
    
    $social_wordpress = get_config('theme_genesis','social_wordpress');
    if(!isset($social_wordpress) || trim($social_wordpress) == "") set_config('social_wordpress','','theme_genesis');
    
    $social_pinterest = get_config('theme_genesis','social_pinterest');
    if(!isset($social_pinterest) || trim($social_pinterest) == "") set_config('social_pinterest','','theme_genesis');
    
    if(isset($_SERVER['QUERY_STRING']) && trim($_SERVER['QUERY_STRING']) == 'section=themesettinggenesis')
        redirect ($CFG->wwwroot.'/theme/genesis/settings/index.php');
?>