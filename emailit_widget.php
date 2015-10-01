<?php
/*  Copyright YEAR  E-MAILiT  (email :  support@e-mailit.com)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */


/*
  Plugin Name: Share Buttons by E-MAILiT
  Plugin URI: http://www.e-mailit.com
  Description: The most flexible sharing tools, including E-MAILiT's global mobile-optimized sharing menu, Twitter, Facebook, WhatsApp, SMS & many more.  [<a href="options-general.php?page=emailit_admin_panel.php">Settings</a>]
  Author: E-MAILiT
  Version: 8.0
  Author URI: http://www.e-mailit.com
 */
defined('ABSPATH') or die('No direct access permitted');

define('EMAILIT_VERSION', '8.0');

include_once plugin_dir_path(__FILE__) . '/include/emailit_admin_panel.php';
include_once plugin_dir_path(__FILE__) . '/include/emailit_options.php';

add_action('admin_init', 'emailit_admin_init');
add_action('widgets_init', 'emailit_widget_init');
add_action('wp_head', 'add_script');
add_filter('get_the_excerpt', 'emailit_display_excerpt', 11);
add_action('plugins_loaded', 'emailit_update');

add_action('admin_notices', 'emailit_admin_notices');
add_action('admin_init', 'emailit_nag_ignore');
function emailit_admin_init() {
    register_setting('emailit_options', 'emailit_options');
}

function emailit_admin_notices() {
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    if (!get_user_meta($user_id, 'emailit_ignore_notice4')) {
        parse_str($_SERVER['QUERY_STRING'], $params);
        echo '<div class="updated"><p>';
        printf(__('IMPORTANT ANNOUNCEMENT. We redesigned our service to make it faster, easier to use, and mobile-friendly. It\'s a whole new service, new exciting features have arrived and we would like to kindly inform you that you must re-built your sharing button settings to get the most from the new E-MAILiT powerful social sharing tools. | <a href="%1$s">Hide Notice</a>'), '?' .http_build_query(array_merge($params, array('emailit_nag_ignore4'=>'0'))));
        echo "</p></div>";
    }
}

function emailit_nag_ignore() {
    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;
    /* If user clicks to ignore the notice, add that to their user meta */
    if (isset($_GET['emailit_nag_ignore4']) && '0' == $_GET['emailit_nag_ignore4']) {
        add_user_meta($user_id, 'emailit_ignore_notice4', 'true', true);
    }
}

function add_script() {
    $emailit_options = get_option('emailit_options');

    //Creates Emailit script
    $outputValue = "<script type='text/javascript'>\r\n";
    $configValues = array();
    if (!$emailit_options["display_counter"] == 'true')
        $configValues[] = "display_counter:false";
    else
        $configValues[] = "display_counter:true";
    if ($emailit_options["TwitterID"] != "")
        $configValues[] = "TwitterID:'" . $emailit_options["TwitterID"] . "'";
    if ($emailit_options['follow_services'] != "")
        $configValues[] = "follow_services:" . $emailit_options["follow_services"];
    if ($emailit_options['thanks_message'] != "")
        $configValues[] = "thanks_message:'" . $emailit_options["thanks_message"] . "'";
    if ($emailit_options['back_color'] != "")
        $configValues[] = "back_color:'" . $emailit_options["back_color"] . "'";
    if ($emailit_options['text_color'] != "")
        $configValues[] = "text_color:'" . $emailit_options["text_color"] . "'";
    if ($emailit_options['text_display'] != "Share" && $emailit_options['text_display'] != "") {
        $configValues[] = "text_display:'" . $emailit_options["text_display"] . "'";
    }
    if ($emailit_options['mobile_bar'] != "")
        $configValues[] = "mobile_bar:true";
    else
        $configValues[] = "mobile_bar:false";
    if ($emailit_options['after_share_dialog'] != "")
        $configValues[] = "after_share_dialog:true";
    else
        $configValues[] = "after_share_dialog:false";    
    if ($emailit_options['display_ads'] != "")
        $configValues[] = "display_ads:true";
    else
        $configValues[] = "display_ads:false";
     if ($emailit_options['hover_pinit'] != "")
        $configValues[] = "hover_pinit:true";
    else
        $configValues[] = "hover_pinit:false";   
    if ($emailit_options['ad_url'] != "")
        $configValues[] = "ad_url:'" . $emailit_options["ad_url"] . "'";
    if ($emailit_options["follow_after_share"] == 'no')
        $configValues[] = "follow_after_share:false";


    if ($emailit_options['open_on'] != "") {
        $configValues[] = "open_on:'" . $emailit_options["open_on"] . "'";
    }
    if ($emailit_options['auto_popup'] && $emailit_options['auto_popup'] != "0") {
        $configValues[] = "auto_popup:" . $emailit_options["auto_popup"] * 1000;
    }
    $outputValue .= "var e_mailit_config = {" . implode(",", $configValues) . "};";
    $outputValue .= "(function() {	var b=document.createElement('script');	
                        b.type='text/javascript';b.async=true;\r\n	
                        b.src='//e-mailit.com/widget/menu3x/js/button.js';\r\n	
                        var c=document.getElementsByTagName('head')[0];	c.appendChild(b) })()";
    $outputValue .= "</script>" . PHP_EOL;
    echo $outputValue;
}

function emailit_widget_init() {
    require_once('emailit_sidebar_widget.php');
    register_widget('EmailitSidebarWidget');
}

function emailit_display_excerpt($content) {
    $options = get_option('emailit_options');

    if ($options['emailit_showonexcerpts'] == true) {
        return emailit_display_button($content);
    } else
        return $content;
}

add_action('init', 'emailit_init');

function emailit_init() {
    $emailit_options = get_option('emailit_options');
    if (!isset($emailit_options['plugin_type']))
        $emailit_options['plugin_type'] = "content";

    if ($emailit_options['plugin_type'] == "content") {
        add_filter('the_content', 'emailit_display_button');
    } else {
        remove_filter('the_content', 'emailit_display_button');
    }
}

function emailit_display_button($content) {
    $emailit_options = get_option('emailit_options');

    if (is_home() || is_front_page())
        $display = (isset($emailit_options['emailit_showonhome']) && $emailit_options['emailit_showonhome'] == true ) ? true : false;
    elseif (is_archive() && !is_category())
        $display = (isset($emailit_options['emailit_showonarchives']) && $emailit_options['emailit_showonarchives'] == true ) ? true : false;
    // Cat
    elseif (is_category())
        $display = (isset($emailit_options['emailit_showoncats']) && $emailit_options['emailit_showoncats'] == true ) ? true : false;
    // Pages
    elseif (is_page())
        $display = (isset($emailit_options['emailit_showonpages']) && $emailit_options['emailit_showonpages'] == true) ? true : false;
    // Single pages (true by default and design)
    elseif (is_single())
        $display = (isset($emailit_options['emailit_showonposts']) && $emailit_options['emailit_showonposts'] == true) ? true : false;
    else
        $display = false;

    $custom_fields = get_post_custom($post->ID);
    if (isset($custom_fields['emailit_exclude']) && $custom_fields['emailit_exclude'][0] == 'true')
        $display = false;

    //an den prepei na mpei
    if (!$display)
        return $content;

    $url = get_permalink();
    $title = get_the_title();
    $outputValue = emailit_createButton($emailit_options, $url, $title);


    if ($emailit_options["button_position"] == 'top' || $emailit_options["button_position"] == 'both') {
        $content = $outputValue . $content;
    }
    if ($emailit_options["button_position"] == 'bottom' || $emailit_options["button_position"] == 'both') {
        $content = $content . $outputValue;
    }
    return $content;
}

function emailit_createButton($emailit_options, $url = null, $title = null) {

    $shared_url = $url != null ? "data-emailit-url='" . $url . "'" : "";
    $shared_title = $title != null ? "data-emailit-title='" . strip_tags($title) . "'" : "";

    //Creating div elements for e-mailit
    $style = $emailit_options["toolbar_type"];
    if ($emailit_options['back_color'] != "") {
        $style .= " no_bgr";
    }

    if (isset($emailit_options["circular"]) && $emailit_options["circular"] == "true") {
        $style .= " circular";
    }

    $outputValue = "<div class=\"e-mailit_toolbox $style \">" . PHP_EOL;
    if ($emailit_options["global_button"] === "first") {
        $outputValue .= "<div class=\"e-mailit_btn_EMAILiT\" $shared_url $shared_title></div>" . PHP_EOL;
    }

    $stand_alone_buttons = array_filter(explode(",", $emailit_options["default_buttons"]));

    foreach ($stand_alone_buttons as $stand_alone_button) {
        $outputValue .= "<div class=\"e-mailit_btn_$stand_alone_button\" $shared_url $shared_title></div>" . PHP_EOL;
    }
    if ($emailit_options["global_button"] === "last") {
        $outputValue .= "<div class=\"e-mailit_btn_EMAILiT\" $shared_url $shared_title></div>";
    }
    $outputValue .= "</div>" . PHP_EOL;
    return $outputValue;
}

add_action('wp_footer', 'emailit_addFloatingBar');

function emailit_addFloatingBar() {
    $emailit_options = get_option('emailit_options');

    $emailit_options['toolbar_type'] = str_replace("native", "large", $emailit_options['toolbar_type']);
    $emailit_options['toolbar_type'] = str_replace("small", "large", $emailit_options['toolbar_type']);
    $emailit_options['toolbar_type'] .= " " . $emailit_options['floating_bar'];
    
    if ($emailit_options['floating_bar'] !== "disabled") {
        $outputValue = emailit_createButton($emailit_options);
    }
    echo $outputValue;
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');

function add_action_links($links) {
    $mylink = '<a href="options-general.php?page=emailit_admin_panel.php">' . __('Settings') . '</a>';

    array_unshift($links, $mylink);
    return $links;
}

require_once('emailit_post_metabox.php');
?>