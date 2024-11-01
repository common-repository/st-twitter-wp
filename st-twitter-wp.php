<?php
/**
 * Plugin Name: ST Twitter
 
 * Plugin URI: http://beautiful-templates.com/
 
 * Description: Using this shortcode you can show twitter followers anywhere on your wordpress. Download and install this ST Twitter  and see just how great it is and how easy it is to use. Full support and even help installing it are available upon request. 
 * Author: Beautiful Templates Team
 
 * Version: 1.0.0
 
 * Author URI: http://beautiful-templates.com/
 
 */

/* 

*Directory

*/
define('ST_ROOT',dirname(__FILE__).'/');
define('ST_CSS_URL',  trailingslashit(plugins_url('/css/', __FILE__) ));
define('ST_BUTTON_URL',  trailingslashit(plugins_url('/editor_button/', __FILE__) ));
define('ST_LANG_URL',  trailingslashit(plugins_url('/languages/', __FILE__) ));
class st_Twitter_wp {
    function __construct() {
        $this->action();
        $this->shortcode();
        $this->st_tw_reg_act_hook();
    }
    function action() {
        // Active notice 
        $twitter_ops = get_option( 'st_twitter_admin_notice' );  
        if ( $twitter_ops == 'TRUE' && is_admin() ) :
            add_action('admin_notices', array($this, 'st_twitter_activation_notice'));
            update_option('st_twitter_admin_notice','FALSE');
        endif;
        add_action( 'admin_menu', array($this, 'st_register_admin_menu_page') );
        add_action( 'wp_enqueue_scripts', array($this, "add_scripts") );
        add_action( 'admin_enqueue_scripts', array($this, 'admin_style') );
        add_action( 'init', array(&$this, 'add_button') );
        add_action( 'plugins_loaded', array($this, 'st_tw_wp_init') );
    }
    
    // Active Notice
    function st_twitter_activation_notice(){
        echo '<div class="updated" style="background-color: #53be2a; border-color:#199b57">            
                <p>Thank you for installing <strong>ST Twitter WP</strong> !</p>
            </div>';
    }
    function st_twitter_activate(){
        update_option('st_twitter_admin_notice','TRUE');
    }
    function st_tw_reg_act_hook() {
        register_activation_hook( __FILE__, array(&$this, 'st_twitter_activate') );
    }
    /* 

    *Admin Menu Item
    
    */
    function st_register_admin_menu_page(){
        add_options_page( 'ST Twitter WP', 'ST Twitter WP', 'manage_options', 'st_twitter_wp', array(&$this, 'st_admin_menu_page') ); 
    }
    function st_admin_menu_page() {
        $st_get_opts = get_option('st_tw_settings',true);
        $st_config = $st_get_opts['config'];
        $consumer_key = $st_config['consumer_key'];
        $consumer_secret = $st_config['consumer_secret'];
        $access_token = $st_config['access_token'];
        $access_token_secret = $st_config['access_token_secret'];
    
        if( $_SERVER['REQUEST_METHOD'] == 'POST' ) :
            $consumer_key = trim($_POST['st_consumer_key']);
            $consumer_secret = trim($_POST['st_consumer_secret']);
            $access_token = trim($_POST['st_access_token']);
            $access_token_secret = trim($_POST['st_access_token_secret']);
    
            $st_conf = array();
            $st_conf['config'] = array(
                'consumer_key' => $consumer_key,
                'consumer_secret' => $consumer_secret,
                'access_token' => $access_token,
                'access_token_secret' => $access_token_secret
            );
            update_option('st_tw_settings',$st_conf);
            $this->save_code('css/style.css', $_POST['customcss']);
        endif;
    ?>
    <div class="st-twitter-admin">
        <h2><?php _e('ST Twitter WP', 'st-twitter-wp')?></h2>
        <div class="main box left">
            <h3 class="box-title"><div class="dashicons dashicons-admin-generic"></div><?php _e('Global Setting', 'st-twitter-wp')?></h3>  
            <div class="content">  	
                <form method="post" action="">
                    <div>
                        <?php add_thickbox(); ?>
                        <p class="getapi"><a class="thickbox" href="<?php echo plugins_url( '/doc/index.html', __FILE__ ); ?>?TB_iframe=true&width=1000&height=600">How to get API information ?</a></p>
                    </div>
                    <div class="col">
                        <div class='z-label'><p><?php _e('Consumer Key', 'st-twitter-wp')?></p></div>
                        <input type='text' name="st_consumer_key" id="st_consumer_key" value="<?php echo $consumer_key ?>"/>
                    </div>
                    <div class="col">
                        <div class='z-label'><p><?php _e('Consumer Secret', 'st-twitter-wp')?></p></div>
                        <input type='text' name="st_consumer_secret" id="st_consumer_secret" value="<?php echo $consumer_secret ?>"/>
                    </div>
                    <div class="col">
                        <div class='z-label'><p><?php _e('Access Token', 'st-twitter-wp')?></p></div>
                        <input type='text'name="st_access_token" id="st_access_token" value="<?php echo $access_token ?>"/>
                    </div>
                    <div class="col">
                        <div class='z-label'><p><?php _e('Access Token Secret', 'st-twitter-wp')?></p></div>
                        <input  type='text' name="st_access_token_secret" id="st_access_token_secret" value="<?php echo $access_token_secret ?>"/>
                    </div>
                    <div class="col">
                        <div class="z-label" style="vertical-align: top;"><p><?php _e('Custom Css', 'st-twitter-wp')?></p></div>
                        <textarea name="customcss"><?php $this->load_code('style.css');?></textarea>
                    </div>
                    <div class="col">
                        <input type='submit' name="st_save_settings" id="st_save_settings" value="save"/>
                    </div>
                </form>
            </div>
        </div>
        <div class="main box right">
            <?php $this->st_copyright();?>
        </div>   
    </div>
    <script>
    jQuery(document).ready(function($) {
        $(window).load(function() {
      		var feedURL = 'http://beautiful-templates.com/evo/category/products/feed/';
        	$.ajax({
    	        type: "GET",
    	        url: document.location.protocol + '//ajax.googleapis.com/ajax/services/feed/load?v=1.0&num=1000&callback=?&q=' + encodeURIComponent(feedURL),
    	        dataType: 'json',
    	        success: function(xml){
    	            var item = xml.responseData.feed.entries;
    	            
    	            var html = "<ul>";
    	            $.each(item, function(i, value){
    	            	html+= '<li><a href="'+value.link+'">'+value.title+'</a></li>';
    	            	if (i===9){
    	            		return false;
    	            	}
    	            });
    	             html+= "</ul>";
    	             $('.st_load_rss').html(html);
    	        }
    	        
    	    });
      });
    });
    </script>
<?php
    } // END FORM SETTING
    function st_twitter_setting( $url, $username ) {
        require_once(ST_ROOT.'/tmh/tmhOAuth.php');
        require_once(ST_ROOT.'/tmh/tmhUtilities.php');
    
        $get_opts = get_option('st_tw_settings',true);
        $st_config = $get_opts['config'];
        $consumer_key = $st_config['consumer_key'];
        $consumer_secret = $st_config['consumer_secret'];
        $access_token = $st_config['access_token'];
        $access_token_secret = $st_config['access_token_secret'];
        
        $st_opts = new tmhOAuth(array(
    			'consumer_key'    => $consumer_key,
    			'consumer_secret' => $consumer_secret,
    			'user_token'      => $access_token,
    			'user_secret'     => $access_token_secret,
    			'curl_ssl_verifypeer' => FALSE
        ));
    
        $st_opts->request(
    			'GET',
    			$url,
    			array(
    				'screen_name' => $username
    			)
        );
    
    	$response = $st_opts->response['response'];
        //$response_details = json_decode( $response );
        return $response;
    } // END st_twitter_setting
    function entry_content( $origTweet, $entities ) {
	   
        if( $entities == null ){ return $origTweet; }
        
        foreach( $entities->urls as $url ) :
        	$index[$url->indices[0]] = "<a href=\"".$url->url."\">".$url->url."</a>";
        	$endEntity[(int)$url->indices[0]] = (int)$url->indices[1];
        endforeach;
        foreach( $entities->hashtags as $hashtag ) :
        	$index[$hashtag->indices[0]] = "<a href=\"http://twitter.com/#!/search?q=%23".$hashtag->text."\">#".$hashtag->text."</a>";
        	$endEntity[$hashtag->indices[0]] = $hashtag->indices[1];
        endforeach;
        foreach( $entities->user_mentions as $user_mention ):
        	$index[$user_mention->indices[0]] = "<a href=\"http://twitter.com/".$user_mention->screen_name."\">@".$user_mention->screen_name."</a>";
        	$endEntity[$user_mention->indices[0]] = $user_mention->indices[1];
        endforeach;
        $fixedTweet="";
        for($i=0;$i<iconv_strlen($origTweet, "UTF-8" );$i++):
        	if(isset($index[(int)$i]) && iconv_strlen($index[(int)$i], "UTF-8" )>0) :
        		$fixedTweet .= $index[(int)$i];
        		$i = $endEntity[(int)$i]-1;
       	    else :
        		$fixedTweet .= iconv_substr( $origTweet,$i,1, "UTF-8" );
        	endif;
        endfor;
        return $fixedTweet;
    } // END ENTRY_CONTENT
    function settime( $time, $showFull = false ) {
        $set_datetime = new DateTime($time, new DateTimeZone('America/New_York'));
        if( $showFull == false ) :
            $datetime = date("M d", $set_datetime->format('U'));    
        else :
            $datetime = date("d F Y, H:i:s (e)", $set_datetime->format('U')); 
        endif;
        return $datetime;
    } // END SETTIME
    function shortcode() {
        add_shortcode( 'STtwitter' , array(&$this, 'st_twitter_shortcode') );
    }
    function st_twitter_shortcode( $atts ) {

        extract( shortcode_atts( array(
        
        			'username' => '',
        			'count' => '',
        			'title' => '',
                    'footer' => '',
                    'template' => '',
                    'display_screenname' => 2,
                    'display_fullname' => 1,
                    'display_favorite' => 2,
                    'display_reply' => 2,
                    'display_retweet' => 2
        
        ), $atts));
        
        //////
        $getUser = $username ? $username : 'cooltemplates';
        $getCount = $count ? $count : '3';
        $getTitle = $title ? $title : 'Twitter';
        $getTemplate = $template ? $template : 0;
        $getFooter = ( ( $footer ) ? $footer : '' );
        $getFullname = $display_fullname;
        $getScreenname = $display_screenname;
        /////
        switch ( $template ) :
            case 0:
                $getTemplateClass = 'default';
            break;
            
            case 1:
                $getTemplateClass = 'empty';
            break;
            
            default:
                $getTemplateClass = 'default';
        endswitch;    
        return $this->st_show_tweet( $getUser, $getCount, $getTitle, $getFooter, $getFullname, $getScreenname, $display_favorite, $display_reply, $display_retweet );
    }
    function st_show_tweet( $getUser, $getCount, $getTitle, $getFooter, $getFullname, $getScreenname, $display_favorite, $display_reply, $display_retweet ) {
        /** URL for REST request **/
        $url = "https://api.twitter.com/1.1/statuses/user_timeline.json";
        //$getLink = 'http://'.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
        $getConf = $this->st_twitter_setting( $url, $getUser );
        
        $tweets = json_decode( $getConf );
        //echo "<pre>";
        //var_dump($tweets->errors[0]->message); echo "</pre>";die();
        if( isset($tweets->errors[0]->message) && $tweets->errors[0]->message !== '' ) {
        	$html = "<h3>Sorry, there was a problem.</h3><p>Twitter returned the following error message:</p><p><em>".$tweets->errors[0]->message."</em></p>";exit();	
        } else {
        	$i=1;
            // SET STYLE
            $html  = "<div class='st-twitter-template default row'>";
            $html .= "<div class='head row'>";
            $html .= "<h3>".( empty( $getTitle ) ? 'Tweets Recent' : $getTitle)."</h3>";
            //$html .= "<div class='intro'>";
            //$html .= "<div class='img-intro col-sm-5'><div class='wrap'><img src='".$tweets[0]->user->profile_image_url."'/></div></div>";
            //$html .= "<div class='info'></div>";
            //$html .= "</div>";
            //$html .= "<div class='row'>";
            //$html .= "<div class='status'>";
            //$html .= "<div class='col-sm-4'><span class='num'>".$tweets[0]->user->statuses_count."</span><span class='text'>tweets</span></div>";
            //$html .= "<div class='col-sm-4'><span class='num'>".$tweets[0]->user->friends_count."</span><span class='text'>following</span></div>";
            //$html .= "<div class='col-sm-4'><span class='num'>".$tweets[0]->user->followers_count."</span><span class='text'>followers</span></div>";
            //$html .= "</div>";
            $html .= "</div>";
            $html .= "<ul class='tweet-content'>";
            // Start
            
    		foreach( $tweets as $tweet ){
    			
    			if( $i > $getCount ){ break; }
                // Continue
    			if( strlen( $tweet->in_reply_to_screen_name )){ continue;}
                
                $html .= "<li><div class='entry-wrap'><div class='main'>";
                
                // Header
                $html .= "<div class='entry-header profile'><div class='profile-authorDetails'>";
                $html .= "<a class='profile-originalAuthorLink' href='http://twitter.com/".$getUser."'>";
                
                    // Avatar
                    $html .= "<img class='profile-avatar' src='" . $tweet->user->profile_image_url . "' />";
                    // Portfolio Name
                    $html .= "<span class='profile-name pullLeft'>";
                        if( $getFullname == 2 ) :
                        $html .= "<b class='profile-fullname'>".$tweet->user->name."</b>";
                        endif;
                        if( $getScreenname == 2 ) :
                            $html .= "<span class='profile-screenname dir' dir='ltr'>
                                          <span class='at'>@</span>".$tweet->user->screen_name."
                                      </span>";
                        endif;
                    $html .= "</span>";
                    
                $html .= "</a>";
                $html .= "<span class='pullLeft'>&nbsp;·&nbsp;</span>";
                // Time
                $html .= "<span class='pullLeft'>
                              <a class='profile-time' title='".$this->settime( $tweet->created_at, true )."'>
                                  <span>
                                      ".$this->settime( $tweet->created_at, false )."
                                  </span>
                              </a>
                          </span>";
                $html .= "</div></div>"; // End Head
                
                // ENTRY CONTENT
                
                $html .= "<div class='entry-content'>
                            <p class='text dir' dir='ltr'>&quot;" . $this->entry_content( $tweet->text, $tweet->entities ) . "&quot;</p>";
                            if( $display_reply == 2 || $display_retweet == 2 || $display_favorite == 2 ) :
                                $html .= "<ul class='ul-action'>";
                                    if( $display_reply == 2 ) :
                                        $html .= "<li class='li-action reply'><a target='_top' href='https://twitter.com/intent/tweet?in_reply_to=".$tweet->id_str."' class='reply-action web-intent' title='Reply'><i class='ic-reply ic-mask'></i><b>Reply</b></a></li>";
                                    endif;
                                    if($display_retweet == 2 ) :
                                        $html .= "<li class='li-action'><a href='https://twitter.com/intent/retweet?tweet_id=".$tweet->id_str."' class='retweet-action web-intent' title='Retweet'><i class='ic-retweet ic-mask'></i><b>Retweet</b></a></li>";
                                    endif;
                                    if( $display_favorite == 2 ) :
                                        $html .= "<li class='li-action'><a href='https://twitter.com/intent/favorite?tweet_id=".$tweet->id_str."' class='favorite-action web-intent' title='Favorite'><i class='ic-fav ic-mask'></i><b>Favorite</b></a></li>";
                                    endif;
                                $html .= "</ul>";
                            endif;
                        $html .= "</div>";
                $html .= "</div></div></li>";// END ENTRY WRAP
    			$i++;
    		} // END FOREACH
            
            $html .= "</ul>";
            if( $getFooter && $getFooter != '' ) {
                $html .= "<div class='footer'><a target='_blank' href='http://twitter.com/".$getUser."'>". $getFooter ."</a></div>"; 
            }
            // END STYLE
            $html .= "</div>";
        } // END errors
        return $html;
    }
    function st_copyright() {
?>
    <h3 class="box-title"><div class="dashicons dashicons-sos"></div><?php _e('Abouts', 'st-twitter-wp')?></h3>
    <div class="st-box">
    	<div class="box-content">
    		<div class="st-row">
    			Hi,</br></br>We are Beautiful-Templates and we provide Wordpress Themes & Plugins, Joomla Templates & Extensions.</br>Thank you for using our products. Let drop us feedback to improve products & services.</br></br>Best regards,</br> Beautiful Templates Team
    		</div>
    	</div>
    	<div class="st-row st-links">
    		<div class="col col-8 links">
    			<ul>
    				<li>
    					<a href="http://beautiful-templates.com/" target="_blank"> <?php _e('Home', 'st-twitter-wp')?></a>
    				</li>
    				<li>
    					<a href="http://beautiful-templates.com/amember/" target="_blank"><?php _e('Submit Ticket', 'st-twitter-wp')?></a>
    				</li>
    				<li>
    					<a href="http://beautiful-templates.com/evo/forum/" target="_blank"><?php _e('forum', 'st-twitter-wp')?></a>
    				</li>
    				<li>
    					<?php add_thickbox(); ?>
    					<a href="<?php echo plugins_url( '/doc/index.html', __FILE__ ); ?>?TB_iframe=true&width=1000&height=600" class="thickbox"><?php _e('Document', 'st-twitter-wp')?></a>
    				</li>
    			</ul>
    		</div>
    		<div class="col col-2 social">
    			<ul>
    				<li>
    					<a href="https://www.facebook.com/beautifultemplates/" target="_blank"><div class="dashicons dashicons-facebook-alt"></div></a>
    				</li>
    				<li>
    					<a href="https://twitter.com/cooltemplates/" target="_blank"><div class="dashicons dashicons-twitter"></div></a>
    				</li>
    			</ul>
    		</div>
    	</div>
    </div>
    <div class="st-box st-rss">
    	<div class="box-content">
    		<div class="st-row st_load_rss">
    			<span class="spinner" style="display:block;"></span>
    		</div>
    	</div>
    </div>
    <?php
    } // END ABOUTS
    function load_code( $file ) {
        $filename = ST_CSS_URL . trim($file);
        if( @file_get_contents($filename) == true ) :
            $code = @file_get_contents($filename);
            echo $code;
        else :
            return false;
        endif;
    }
    function save_code( $file, $content ) {
        if ( current_user_can('edit_plugins') ) :
            $filename = ST_ROOT . $file;
            $setContent = wp_unslash($content); // Remove slashes from a string or array of strings.
            if( is_writeable( $filename ) ) :
                $setfile = fopen($filename, "w+") or die("Unable to open file!");
                if( $setfile !== false ) :
                    fwrite($setfile, urldecode($setContent));
                    fclose($setfile);
                endif;
            endif;
        else :
            wp_die('<p>'.__('You do not have sufficient permissions to edit plugin for this site.').'</p>');
        endif;
    }
    function st_tw_wp_init() {
        $plugin_dir = basename(dirname(__FILE__)).'/languages/';
        load_plugin_textdomain( 'st-twitter-wp', false, $plugin_dir );
    }
    function add_button() {  
        if ( current_user_can('edit_posts') &&  current_user_can('edit_pages') ) :  
            add_filter('mce_external_plugins', array(&$this, 'add_plugin'));  
            add_filter('mce_buttons', array(&$this, 'register_button'));  
        endif;  
    }
    function add_plugin($plugin_array) { 
        $path = ST_BUTTON_URL . 'editor_button.js';
        $plugin_array['STtwitterShortcodeEditorButton'] = $path;  
        return $plugin_array;  
    }
    function register_button($buttons) {  
        array_push($buttons, "STtwitterShortcode");  
        return $buttons;  
    }  
    function add_scripts() {
        wp_register_script( 'Tweet-js', 'http://platform.twitter.com/widgets.js', array('jquery') );
        wp_enqueue_script('Tweet-js');
        $this->add_style();
    }
    function add_style() {
        wp_register_style( 'st-twitter-style',ST_CSS_URL.'style.css' );
        wp_enqueue_style( 'st-twitter-style' );
    }
    function admin_style() {
        wp_register_style( 'stt-admin-style', ST_CSS_URL.'admin.css' );
        wp_enqueue_style( 'stt-admin-style' );
    }
} // END CLASS st_Twitter_wp
$st_Twitter_wp = new st_Twitter_wp;
require_once(ST_ROOT . 'widget.php');