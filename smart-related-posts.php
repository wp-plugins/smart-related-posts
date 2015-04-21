<?php
/*
Plugin Name: Smart Related Post
Description: 
Version: 1.0
Author: Pedro Escudero
Author URI: http://es.linkedin.com/in/pedroescuderozumel/es
Plugin URI: http://github.com/smart-related-post
License: GPL2
*/

// Check for existing class
if ( ! class_exists( 'wordpress_smart_related_post' ) ) {
/**
	 * Main Class
	 */
	class wordpress_smart_related_post  {

		/**
		 * Class constructor: initializes class variables and adds actions and filters.
		 */
		public function __construct() {
			$this->wordpress_smart_related_post();
		}

		public function wordpress_smart_related_post() {
			register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );
			register_deactivation_hook( __FILE__, array( __CLASS__, 'deactivation' ) );

			// Register admin only hooks
			if(is_admin()) {
				$this->register_admin_hooks();
			}
                        
                        // Register global hooks
			$this->register_global_hooks();
		}
                /**
		 * Registers global hooks.
		 */
		public function register_global_hooks() {
			add_action('admin_enqueue_scripts', array($this,'add_css'));
      		add_action( 'the_content', array($this,'add_related_after_content') );
      		add_action( 'wp_head', array($this,'add_css') );
                   
		} 
                /**
		 * Add CSS needed for the plugin
		 */
		public function add_css() {
		    wp_register_style('wordpress_smart_related_post', plugins_url('style.css', __FILE__));
        	wp_enqueue_style( 'wordpress_smart_related_post' );
		}  
                
                
	                  
		/**
		 * Handles activation tasks, such as registering the uninstall hook.
		 */
		public function activation() {
			register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
                       
                        
		}
               
		/**
		 * Handles deactivation tasks, such as deleting plugin options.
		 */
		public function deactivation() {

		}

		/**
		 * Handles uninstallation tasks, such as deleting plugin options.
		 */
		public function uninstall() {
			
		}

		/**
		 * Registers admin only hooks.
		 */
		public function register_admin_hooks() {
			
			// Add Settings Link
			add_action('admin_menu', array($this, 'admin_menu'));

			// Add settings link to plugins listing page
			add_filter('plugin_action_links', array($this, 'plugin_settings_link'), 2, 2);

			
		}

		/**
		 * Admin: add settings link to plugin management page
		 */
		public function plugin_settings_link($actions, $file) {
			if(false !== strpos($file, 'wordpress_smart_related_post')) {
				$actions['settings'] = '<a href="options-general.php?page=wordpress_smart_related_post">Settings</a>';
			}
			return $actions;
		}

		/**
		 * Admin: add Link to sidebar admin menu
		 */
		public function admin_menu() {
			
			add_options_page('Wordpress Smart Related Post Options', 'Wordpress Smart Related Post', 'manage_options', 'wordpress_smart_related_post', array($this, 'settings_page'));
		}
                        
		/**
		 * Admin: settings page
		 */
		public function settings_page() {
			if ( !current_user_can( 'manage_options' ) )  {
				wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
			} ?>
                          
			<div class="wrap">

				<?php screen_icon(); ?>
				<h2>Wordpress Smart Related Post</h2>

				<hr/>

				
                                
				<h2>Description</h2>
				<p>
				 This plugin provides you a related list of post and the end of your entries. 
				</p>
                                <p>
                                    If this plugin has been useful, you may see my professional profile in <a href="http://es.linkedin.com/in/pedroescuderozumel/es" target="_blank">Linkedin</a> or follow me work at <a target="_blank" href="https://github.com/PedroEscudero">github</a>. Do you have any suggestion about this plugin? Please <a href="mailto:pedroescudero@gmail.com">write me</a>.
                                </p>
                                
                                
			</div>
			<?php
		}
    
    public function add_related_after_content() {
      $content = get_the_content();
    
    $content = wpautop(wptexturize($content));
    global $table_prefix;
    global $wpdb;
    $post_id = get_the_id();
    $table_post = $table_prefix . "posts";
    $table_relationships = $table_prefix . "term_relationships";
    $table_terms = $table_prefix . "terms" ;
    $categories = get_the_category( $post_id );
    $posts_array = array();
    foreach ( $categories as $category ):
    	
    	$term_id =$category -> term_id;
        $search = "SELECT * FROM $table_post 
        LEFT JOIN $table_relationships ON $table_relationships.object_id = $table_post.ID
        LEFT JOIN $table_terms ON $table_terms.term_id =  $table_relationships.term_taxonomy_id
        WHERE post_status = 'publish' && post_type ='post' && term_id = '$term_id'";
        
        $result = $wpdb->get_results( $search );
        foreach ( $result as $row ):
        
        	if (!in_array($row -> ID , $posts_array)){
        		array_push($posts_array, $row -> ID);
        	}
        endforeach;
    endforeach;
    
     
    echo $content;
    shuffle ( $posts_array );
    $max_related = 8; 
    $cont_related = 0;
    echo "<h1 id='title_related'>Related Posts</h1>";
    while ($cont_related < $max_related)
      		{
			    $post_selected = get_post ( $posts_array[$cont_related] );
			    $permalink = get_permalink( $id_post );
			    $related_text = $this -> chop_text ( $post_selected-> post_content , 43);
			    $image = $this -> first_post_image ( $post_selected );
			 
			    echo "<p><a class = 'link_related' href='{$permalink}'><strong>" . $post_selected -> post_title . "</strong>:<span class = 'related_text'> $related_text ...</span><br/></a></p>";
			    $cont_related ++;
	}
	echo "<br/><br/>";

    }

    function chop_text ( $content, $words_number ) {
    	 $content = strip_tags ( $content );
		 $content = explode(' ', $content);
		 $content = array_slice($content, 0, $words_number);
		 $content = implode(' ', $content);
		 return $content;
	} 

	

  } // End main class

	// Initialize Class
	new wordpress_smart_related_post();
}

?>
