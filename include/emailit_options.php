<?php

defined('ABSPATH') or die('No direct access permitted');


function emailit_update() {
    $emailit_version = get_option('emailit_version');
    //before 8.0
    if (!isset($emailit_version) || version_compare($emailit_version, '8.0', '<')) {
        delete_option('emailit_options');
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;

        delete_user_meta($user_id, 'emailit_ignore_notice');
        delete_user_meta($user_id, 'emailit_ignore_notice2');
        delete_user_meta($user_id, 'emailit_ignore_notice3');

        $default_options = array(
            'toolbar_type' => 'large',
            'global_button' => 'last',
            'open_on' => 'onclick',
            'text_display' => 'Share',
            'default_buttons' => 'Facebook,Twitter,Send_via_Email,Pinterest,LinkedIn',
            'emailit_showonhome' => 'true',
            'emailit_showonpages' => 'true',
            'emailit_showonposts' => 'true',
            'emailit_showonexcerpts' => 'true',
            'emailit_showonarchives' => 'true',
            'emailit_showoncats' => 'true',
            'button_position' => 'both',
            'follow_services'=>'{}',
            'floating_bar'=> 'disabled',
            'after_share_dialog' => 'true',
            'thanks_message' => 'Thanks for sharing!',
            'mobile_bar' => 'true',
            'display_ads'=>'true'
        );

        add_option('emailit_options', $default_options);
        add_option('emailit_version', EMAILIT_VERSION);
    }
    
    //after 8.0
    if (version_compare($emailit_version, EMAILIT_VERSION, '<')){
        update_option('emailit_version', EMAILIT_VERSION);
    }
}
