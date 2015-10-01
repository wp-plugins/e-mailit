<?php
class emailit_post_metabox{
    function admin_init()
    {
        $screens = apply_filters('emailit_post_metabox_screens', array('post', 'page') );
        foreach($screens as $screen)
        {
        add_meta_box('emailit', 'E-MAILiT', array($this, 'post_metabox'), $screen, 'side', 'default'  );
        }
        add_action('save_post', array($this, 'save_post') );
        
        add_filter('default_hidden_meta_boxes', array($this,  'default_hidden_meta_boxes' )  );
    }
    function default_hidden_meta_boxes($hidden)
    {
        $hidden[] = 'emailit';
        return $hidden;
    }
    function post_metabox(){
        global $post_id;
        if ( is_null($post_id) )
            $checked = '';
        else
        {
            $custom_fields = get_post_custom($post_id);
            $checked = ( isset ($custom_fields['emailit_exclude'])   ) ? 'checked="checked"' : '' ;
        }
        wp_nonce_field('emailit_postmetabox_nonce', 'emailit_postmetabox_nonce');
        echo '<label for="emailit_show_option">';
        _e("Remove E-MAILiT:", 'myplugin_textdomain' );
        echo '</label> ';
        echo '<input type="checkbox" id="emailit_show_option" name="emailit_show_option" value="1" '.$checked.'>';
    }
    function save_post($post_id)
    {
    	global $post;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
            return;
        if ( ! isset($_POST['emailit_postmetabox_nonce'] ) ||  !wp_verify_nonce( $_POST['emailit_postmetabox_nonce'], 'emailit_postmetabox_nonce' ) ) 
            return;
        if ( ! isset($_POST['emailit_show_option']) )
        {
            delete_post_meta($post_id, 'emailit_exclude');
        }
        else
        {
        	delete_post_meta($post_id, 'emailit_exclude');
            $custom_fields = get_post_custom($post_id);
            if (! isset ($custom_fields['emailit_exclude'][0]) && ($post->post_type=="post")  )
            {
                add_post_meta($post_id, 'emailit_exclude', 'true');
            }
            else
            {
                update_post_meta($post_id, 'emailit_exclude', 'true' , $custom_fields['emailit_exclude'][0]  ); 
            }
        }
    }
}
$emailit_post_metabox = new emailit_post_metabox;
add_action('admin_init', array($emailit_post_metabox, 'admin_init'));
