<?php

/**
 * Makes our changes to the CSS
 *
 * @param string $css
 * @param theme_config $theme
 * @return string 
 */
function genesis_process_css($css, $theme) {

    $themecolor = $theme->settings->themecolor;
    
    switch ($themecolor) {
        case 'blue':
            $color1 = "#00A5B6";
            $color2 = "#003050";
            $color3 = "#0094A3";
            $color4 = "#CADD09";
            $themespriteposition = "-25px";
            break;
        case 'green':
            $color1 = "#5DBB71";
            $color2 = "#27736F";
            $color3 = "#4F9F60";
            $color4 = "#EDDC2A";
            $themespriteposition = "-50px";
            break;
        case 'orange':
            $color1 = "#D58303";
            $color2 = "#5F6366";
            $color3 = "#C28425";
            $color4 = "#FED060";
            $themespriteposition = "-75px";
            break;
    }
    
    $css = str_replace("[[setting:color1]]", $color1, $css);
    $css = str_replace("[[setting:color2]]", $color2, $css);
    $css = str_replace("[[setting:color3]]", $color3, $css);
    $css = str_replace("[[setting:color4]]", $color4, $css);
    $css = str_replace("[[setting:themespriteposition]]", $themespriteposition, $css);
    
    $css = str_replace("[[setting:logourl]]", $theme->settings->logourl, $css);
    $css = str_replace("[[setting:footermod_aboutus_whitelogo]]", $theme->settings->footermod_aboutus_whitelogo, $css);

    // Return the CSS
    return $css;
}

?>