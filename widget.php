<?php
class st_twitter_widget extends WP_Widget {
    function st_twitter_widget() {
        parent::WP_Widget( $id = 'st_twitter_widget', $name = 'ST Twitter Widget' , $options = array( 'description' => 'Grab your tweets from twitter.' ) );
    }
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['st_tw_title'] = strip_tags( $new_instance['st_tw_title'] );
        $instance['st_tw_username'] = $new_instance['st_tw_username'];
        $instance['st_tw_limit'] = (int)$new_instance['st_tw_limit'];
        $instance['st_tw_ft'] = $new_instance['st_tw_ft'];
        $instance['st_tw_dfullname'] = !empty( $new_instance['st_tw_dfullname'] ) ? 2 : 0;
        $instance['st_tw_dscreenname'] = !empty( $new_instance['st_tw_dscreenname'] ) ? 2 : 0;
        $instance['st_tw_dreply'] = !empty( $new_instance['st_tw_dreply'] ) ? 2 : 0;
        $instance['st_tw_dretweet'] = !empty( $new_instance['st_tw_dretweet'] ) ? 2 : 0;
        $instance['st_tw_dfavorite'] = !empty( $new_instance['st_tw_dfavorite'] ) ? 2 : 0;
        
        return $instance;
    } // END UPDATE
    function form( $instance ) {
        $st_tw_setting = get_option('st_tw_settings',true);
        if( count( $st_tw_setting['config'] ) < 4 ) :
            echo 'Please configure your twitter API setting first from <a href="'.site_url().'/wp-admin/admin.php?page=st_twitter_wp">here</a>';
            return;
        endif;
        $instance = wp_parse_args( (array) $instance, array( 'title' => '') );
        $instance['st_tw_title'] = esc_attr( $instance['st_tw_title'] );
        $instance['st_tw_username'] = ( $instance['st_tw_username'] ? $instance['st_tw_username'] : '' );
        $instance['st_tw_limit'] = ( $instance['st_tw_limit'] ? $instance['st_tw_limit'] : 5 );
        $instance['st_tw_ft'] = ( $instance['st_tw_ft'] ? $instance['st_tw_ft'] : '' );
        $instance['st_tw_dfullname'] = ( $instance['st_tw_dfullname'] ? (bool) $instance['st_tw_dfullname'] : false );
        $instance['st_tw_dscreenname'] = ( $instance['st_tw_dscreenname'] ? (bool) $instance['st_tw_dscreenname'] : false );
        $instance['st_tw_dreply'] = ( $instance['st_tw_dreply'] ? (bool) $instance['st_tw_dreply'] : false );
        $instance['st_tw_dretweet'] = ( $instance['st_tw_dretweet'] ? (bool) $instance['st_tw_dretweet'] : false );
        $instance['st_tw_dfavorite'] = ( $instance['st_tw_dfavorite'] ? (bool) $instance['st_tw_dfavorite'] : false );
?>
        <div>
            <p>
                <label for="<?php echo $this->get_field_id('st_tw_title'); ?>"><?php _e('Title:'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_tw_title')?>" value="<?php echo esc_attr( $instance['st_tw_title'] ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('st_tw_username'); ?>"><?php _e('Twitter Username:'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_tw_username')?>" value="<?php echo esc_attr( $instance['st_tw_username'] ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('st_tw_limit'); ?>"><?php _e('Twitter Limit:'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_tw_limit')?>" value="<?php echo esc_attr( $instance['st_tw_limit'] ); ?>" />
            </p>
            <p>
                <label for="<?php echo $this->get_field_id('st_tw_ft'); ?>"><?php _e('Footer Text:'); ?></label>
                <input class="widefat" type="text" name="<?php echo $this->get_field_name('st_tw_ft')?>" value="<?php echo esc_attr( $instance['st_tw_ft'] ); ?>" />
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_tw_dfullname')?>"<?php checked( $instance['st_tw_dfullname'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_tw_dfullname'); ?>"><?php _e('Display Fullname'); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_tw_dscreenname')?>"<?php checked( $instance['st_tw_dscreenname'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_tw_dscreenname'); ?>"><?php _e('Display Screen Name'); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_tw_dreply')?>"<?php checked( $instance['st_tw_dreply'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_tw_dreply'); ?>"><?php _e('Display Reply'); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_tw_dretweet')?>"<?php checked( $instance['st_tw_dretweet'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_tw_dretweet'); ?>"><?php _e('Display Retweet'); ?></label>
            </p>
            <p>
                <input type="checkbox" class="checkbox" name="<?php echo $this->get_field_name('st_tw_dfavorite')?>"<?php checked( $instance['st_tw_dfavorite'] ); ?> />
                <label for="<?php echo $this->get_field_id('st_tw_dfavorite'); ?>"><?php _e('Display Favorite'); ?></label>
            </p>
        </div>
<?php        
    } // END FORM
    function widget( $args, $instance ) {
        global $st_Twitter_wp;
        extract( $args, EXTR_SKIP );
        $getTitle = $instance['st_tw_title'];
        $getUser = $instance['st_tw_username'];
        $getCount = $instance['st_tw_limit'];
        $getFooter = $instance['st_tw_ft'];
        $getFullname = $instance['st_tw_dfullname'];
        $getScreenname = $instance['st_tw_dscreenname'];
        $display_favorite = $instance['st_tw_dfavorite'];
        $display_reply = $instance['st_tw_dreply'];
        $display_retweet = $instance['st_tw_dretweet'];
        
        echo $before_widget;
        $st_tw_setting = get_option('st_tw_settings',true);
        if( count( $st_tw_setting['config'] ) == 4 ) :
            echo $st_Twitter_wp->st_show_tweet( $getUser, $getCount, $getTitle, $getFooter, $getFullname, $getScreenname, $display_favorite, $display_reply, $display_retweet );
        else :
            echo "<p>Please configure your twitter API settings</p>";
        endif;
        echo $after_widget;
    } // END WIDGET
} // END WIDGET
add_action( 'widgets_init', create_function( '', 'register_widget("st_twitter_widget");' ) );