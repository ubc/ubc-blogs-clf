<?php  

/*
  Plugin Name: UBC Blogs Website
  Plugin URI:  http://blogs.ubc.ca Blogs website | Note: This plugin will only work on wp-hybrid-clf theme
  Version: 1.0
  Author: David Brabbins
  Licence: GPLv2
  Author URI: http://blogs.ubc.ca
 */

Class UBC_BLOGS_Theme_Options {
    static $prefix;
    static $faculty_main_homepage;
    static $add_script;

    /**
     * init function.
     * 
     * @access public
     * @return void	
     */
    static function init() {
    
        self::$prefix = 'wp-hybrid-clf'; // function hybrid_get_prefix() is not available within the plugin
        
        self::$faculty_main_homepage = 'http://www.blogs.ubc.ca';
        
        $theme = wp_get_theme();
        
        if( "UBC Collab" != $theme->name )
          return true;

        add_action( 'admin_init',array(__CLASS__, 'admin' ), 1);

        add_filter( 'ubc_collab_default_theme_options', array(__CLASS__, 'default_values'), 10,1 );

        add_filter( 'ubc_collab_theme_options_validate', array(__CLASS__, 'validate'), 10, 2 );  

        add_action( 'wp_head', array( __CLASS__,'wp_head' ) );

        add_action( 'wp_footer', array( __CLASS__,'blogs_enqueue_script' ), 10, 1 );
        //reg and enque
        add_action('admin_enqueue_scripts', array(__CLASS__,'blogs_enqueue' ) );
        //Adds Stylesheet
        add_action('wp_enqueue_scripts', array(__CLASS__, 'blogs_theme_styles'));    

        add_filter('wp-hybrid-clf_after_header', array(__CLASS__,'output_blogs_featured_img'), 11, 3);

        add_action( 'wp_footer', array(__CLASS__,'blogs_global_js'), 10, 1 );

    }
     
    /**
     * foe_uploader_options_enqueue_scripts function.
     * 
     * @access static
     * @return void
     */
    static function blogs_enqueue() {
      $screen = get_current_screen();
      //wp_die( print_r( $screen, true ) ); // $screen->id
        if( $screen->id !== 'appearance_page_theme_options' )
          return;
      wp_register_style('blogs-theme-option-style', plugins_url('/css/style.css', __FILE__) );
      wp_register_script('blogs-theme-option-script', plugins_url('/js/script.js', __FILE__) );

      // Enqueue scripts and styles
      wp_enqueue_style('blogs-theme-option-style');
      wp_enqueue_script('blogs-theme-option-script');
      wp_enqueue_script('blogs-upload');     
    
      if(function_exists( 'wp_enqueue_media' )) :
            wp_enqueue_media();
          else :
                wp_enqueue_style('thickbox');
                wp_enqueue_script('media-upload');
                wp_enqueue_script('thickbox');
      endif;

  }

  static function blogs_global_js() {
    wp_register_script('blogs-theme-global-js', plugins_url('/js/blogs-global-js.js', __FILE__) );
    wp_enqueue_script('blogs-theme-global-js');   
  }

  /**
   * print_script function.
   * 
   * @access public
   * @static
   * @return void
   */
  static function print_script() {
    if ( ! self::$add_script )
      return;
                
    wp_print_scripts( 'ubc-collab-blogs' );
  }  

    /**
    * Style Guide page creator
    * Create page, store page id, display link in theme options
    */
    function style_guide_creator()  {

        //$page_url = $echo_guide_url;

     

    }   
    /**
     * admin function.
     * 
     * @access public
     * @return void
     */
    static function admin(){
        
        //Add Blogs Options tab in the theme options
        add_settings_section(
                'blogs-options', // Unique identifier for the settings section
                'BLOGS SETTINGS', // Section title
                '__return_false', // Section callback (we don't want anything)
                'theme_options' // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
        );
          //BLOGS opt in stylesheet
          add_settings_field(
              'stylesheet-opt', // Unique identifier for the field for this section
              'Blogs Stylesheet', // Setting field label
              array(__CLASS__,'blogs_style_option'), // Function that renders the settings field
              'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
              'blogs-options' // Settings section. Same as the first argument in the add_settings_section() above
          );
  				//Add Colour options
  				add_settings_field(
  						'blogs-colours', // Unique identifier for the field for this section
  						'Colour Options', // Setting field label
  						array(__CLASS__,'blogs_colour_options'), // Function that renders the settings field
  						'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
  						'blogs-options' // Settings section. Same as the first argument in the add_settings_section() above
  				);
          //Add Colour options
          add_settings_field(
              'banner-settings', // Unique identifier for the field for this section
              'Banner Settings', // Setting field label
              array(__CLASS__,'banner_options'), // Function that renders the settings field
              'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
              'blogs-options' // Settings section. Same as the first argument in the add_settings_section() above
          );
  				//Add Hardcoded list
  				//add_settings_field(
  						//'foe-hardcoded-options', // Unique identifier for the field for this section
  						//'Hardcoded Features and Resources', // Setting field label
  						//array(__CLASS__,'blogs_hardcoded_options'), // Function that renders the settings field
  						//'theme_options', // Menu slug, used to uniquely identify the page; see ubc_collab_theme_options_add_page()
  						//'blogs-options' // Settings section. Same as the first argument in the add_settings_section() above
  				//);  
    }  

    /**
     *  BLOGS opt in stylesheet.
     * Display colour options for BLOGS specific template
     * @access public
     * @return void
     */   
    static function blogs_style_option(){ ?>
      <div class="explanation"><a href="#" class="explanation-help">Info</a>
        <div>Turn off Blogs new Stylesheet.
        </div>
      </div>
      <div id="education-unit-colour-box">
        <?php UBC_Collab_Theme_Options::checkbox( 'blogs-stylesheet', 1, 'Use new Blogs stylesheet?' ); ?><br /> 
        <small>Check off to use the stylesheet.</small>  
      </div>
      <br/>

      <?php 
        }
      /**
       *  blogs_colour_options.
       * Display colour options for BLGOS specific template
       * @access public
       * @return void
       */   
      static function blogs_colour_options(){ ?>
      <div class="explanation"><a href="#" class="explanation-help">Info</a>
        <div> Allows quick colour changes.
        </div>
      </div>
      <div id="education-unit-colour-box">
        Read more about <a href="http://clf.educ.ubc.ca/design-style-guide/clf-specifications/#contrast" target="_blank">colour contrast</a> and <a href="http://clf.educ.ubc.ca/design-style-guide/clf-specifications/#contrast" target="_blank">web accesibility</a>.
        <div class="education-colour-item main-color"><br />
          <div class="picker"><b>MAIN COLOUR</b><br /><small>Changes the color for all header tags, links, subnavigation active/current page, .btn (only the .btn), tab hover and active, and accordion hover.</small> <br /><br /></div>
          <?php  UBC_Collab_Theme_Options::text( 'blogs-main-colour' ); ?>
        </div>
        <br/>
        <div class="education-colour-item secondary-color">
          <div class="picker"><b>SECONDARY COLOUR</b><br /><small>Changes the hover colour for page and post text links, and subnavigation section active.</small><br /><br /></div>
          <?php  UBC_Collab_Theme_Options::text( 'blogs-gradient-colour' ); ?>
        </div>
      </div>
      <br/>
      <div class="education-colour-item">
        <div class="picker"><b>SUBNAVIGATION SECTION COLOUR</b><br /><br /></div>
                          <?php  UBC_Collab_Theme_Options::text( 'blogs-hover-colour' ); ?>
                        </div>
      <br/>

      <?php 
        }
      /**
       *  banner_options.
       * Display colour options for BLOGS specific template
       * @access public
       * @return void
       */   
      static function banner_options(){ ?>
      <div class="explanation"><a href="#" class="explanation-help">Info</a>
        <div><b>Adjust the featured image banner settings.</b>
          Featured images will automatically show up on <b>Pages</b> only if the image is set, unless the images dimensions do not meet the settings set below.</br /><br />
          <b>The settings will allow you set the desired width and height.</b><br /><br />
          If the image dimensions do not meet the set width or height, the image will not show up.<br /><br />
          <strong>Defaults</strong><br />
          <strong>Width:</strong> 1500px<br />
          <strong>Height:</strong> 350px
        </div>
      </div>
      <div class="banner-options">
       <p><small>The input values will only save as numbers.</small></p>
        <div class="banner-input"><strong>Width: </strong><?php  UBC_Collab_Theme_Options::text( 'banner-width' ); ?> px
        </div>
        <div class="banner-input"><strong>Height: </strong> <?php  UBC_Collab_Theme_Options::text( 'banner-height' ); ?> px
        </div>

      </div>

      <br/>
  <?php 
      } 
		
    /**
     * Blogs_hardcoded_options.
     * Display Hardcoded info and Faculty Resources
     * @access public
     * @return void
     */      
    static function blogs_hardcoded_options(){ ?>
      <div class="explanation"><a href="#" class="explanation-help">Info</a>
        <div>The following are the description of hardcoded items and resources for a Faculty of Education Theme.</div>
      </div>
      <div id="hardcoded-box"> <b>The following features are hardcoded:</b><br />
        <?php
         
            ?>
      </div>
<?php

    UBC_BLOGS_Theme_Options::blogs_defaults();
    }    
    
    static function blogs_defaults(){
    }
	
    /*********** 
     * Default Options
     * 
     * Returns the options array for Blogs fields.
     *
     * @since ubc-clf 1.0
     */
    static function default_values( $options ) {

            if (!is_array($options)) { 
                    $options = array();
            }

            $defaults = array(
                'blogs-main-colour'			         =>  "#166a9a",
                'blogs-gradient-colour'	         =>  "#3f7ea7",
                'blogs-hover-colour'		         =>  "#E7F4FC",
                'open-sans-add'                  =>  "0",
                'blogs-stylesheet'               =>  1,
                'banner-width'                   =>  "1500",
                'banner-height'                  =>  "350"
            );

            $options = array_merge( $options, $defaults );

            return $options;
    }  
	
	/**
	 * Sanitize and validate form input. Accepts an array, return a sanitized array.
	 *
	 *
	 * @todo set up Reset Options action
	 *
	 * @param array $input Unknown values.
	 * @return array Sanitized theme options ready to be stored in the database.
	 *
	 */
	static function validate( $output, $input ) {

		// Grab default values as base
		$starter = UBC_BLOGS_Theme_Options::default_values( array() );

	    // Validate Unit Colour Options A, B, and C

      $starter['blogs-main-colour'] = UBC_Collab_Theme_Options::validate_text($input['blogs-main-colour'], $starter['blogs-main-colour'] );
      $starter['blogs-gradient-colour'] = UBC_Collab_Theme_Options::validate_text($input['blogs-gradient-colour'], $starter['blogs-gradient-colour'] );
      $starter['blogs-hover-colour'] = UBC_Collab_Theme_Options::validate_text($input['blogs-hover-colour'], $starter['blogs-hover-colour'] );
      $starter['page_url_value'] = UBC_Collab_Theme_Options::validate_text($input['page_url_value'], $starter['page_url_value'] );
      $starter['blogs-stylesheet'] = (bool)$input['blogs-stylesheet'];

      $starter['banner-width'] = UBC_Collab_Theme_Options::validate_text(intval($input['banner-width']), $starter['banner-width']);
      $starter['banner-height'] = UBC_Collab_Theme_Options::validate_text(intval($input['banner-height']), $starter['banner-height'] );
     // $output = strip_tags($output);
			$output = array_merge($output, $starter);

       // $string1 = $output;
        //$string = strip_tags($string1);

           return $output;            
        }
		
	 /**
     * blogs_theme_styles
     * Adds the Faculty of Blogs Stylesheet
     */         
		static function blogs_theme_styles()  {


			  wp_register_style( 'blogs-clf', plugins_url('/css/global.css', __FILE__, true ) );
		    if (UBC_Collab_Theme_Options::get( 'blogs-stylesheet') == 1) : 
			  // enqueing:
			  wp_enqueue_style( 'blogs-clf' );
        endif;


			  
		}	
	 /**
     * blogs_enqueue_script
     * Adds the Faculty of BLOGS Stylesheet
     */         
		static function blogs_enqueue_script()  { 
			  // enqueing:
			  wp_enqueue_script( 'blogs-enq-script' );
			  
		}
							
   /**
   * output_blogs_featured_img
   * Adds featured images to WP pages
   */         
   static function output_blogs_featured_img(){

     if ( is_page() && ! is_front_page() && ! is_home() ) {

          if (has_post_thumbnail()) {

            $image_url = wp_get_attachment_image_src(get_post_thumbnail_id(),'full', true);
            $image_size = wp_get_attachment_image_src(get_post_thumbnail_id(),'full', true);
              if ($image_size[1] >= UBC_Collab_Theme_Options::get( 'banner-width') && $image_size[2] == UBC_Collab_Theme_Options::get( 'banner-height')) :
                echo "<div class=\"header-img-container\"><img class=\"special-featured-images\" src=\"" . $image_url[0] ."\" title=\"" . the_title_attribute('echo=0') . "\" alt=\"" . the_title_attribute('echo=0') . "\" /></div>";
              endif;
				
			} else {
                    echo "";
                }
            }
        }
      /*** Start
      **/
      /**
     * wp_head
     * Appends some of the dynamic css and js to the wordpress header
     */        
        static function wp_head(){ 


          // PHP will try to detect the middle count to help color contrast with background colors
          $hexcolor = UBC_Collab_Theme_Options::get('blogs-gradient-colour');
            $r = hexdec(substr($hexcolor,0,2));
            $g = hexdec(substr($hexcolor,2,2));
            $b = hexdec(substr($hexcolor,4,2));
            $yiq = (($r*299)+($g*587)+($b*114))/1000;

            $newColor = ($yiq >= 128) ? '#000' : 'white';
            //echo "hex: ".$hexcolor; echo " YIQ: ".$yiq;
          ?>
<style type="text/css" media="screen">
/*-- Typography  ---------------------------*/
#content h1, #content h2, #content h3, #content h4, #content h5, #content h6, #content h1 a, #content h2 a, #content h3 a, #content h4 a, #content h5 a, #content h6 a, #content a, #content .hero-unit h1, #frontpage-siderbar .tab-pane a {
 color:<?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
}
#content a:hover {
 color: <?php echo UBC_Collab_Theme_Options::get('blogs-gradient-colour')?>;
}
/*-- Sidebar Navigation  ---------------------------*/
				/*-- Current Pages  ---------------------------*/
.sidenavigation .accordion-group .accordion-heading.active, .sidenavigation .accordion-group .accordion-heading.active a, .sidenavigation .accordion-group .accordion-heading.active .accordion-toggle, .accordion.sidenav .single a.opened, .simple-custom-menu a .current-post-ancestor a, .simple-custom-menu .current-menu-parent a, .simple-custom-menu.current-post-parent a, .simple-custom-menu .active a, .sidenav .accordion-group .accordion-inner>a.opened, .sidenav .accordion>a.opened {
 background: <?php echo UBC_Collab_Theme_Options::get('blogs-gradient-colour')?>;
}
.sidebar .in .accordion-inner {
background-color: <?php echo UBC_Collab_Theme_Options::get('blogs-hover-colour')?>;
}
#primary-secondary .in .accordion-inner a.opened {
  background: <?php echo UBC_Collab_Theme_Options::get('blogs-gradient-colour')?>;
  color: <?php echo $newColor ?>;
}
/*-- Hover Pages  ---------------------------*/
.sidenav .single a:hover, .simple-custom-menu.sidenav .menu-item a:hover, .sidenav .accordion-inner a:hover, .sidenav .single a:hover, .simple-custom-menu.sidenav .menu-item a:hover, .sidenav .accordion-inner a:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('blogs-gradient-colour')?>;
}
.sidenav .accordion-heading:hover, .sidenav .accordion-heading a:hover, .sidenav .accordion-heading:hover a:focus, .sidenav .accordion-heading:hover a:active, .sidenav .accordion-heading:hover .accordion-toggle {
 background: <?php echo UBC_Collab_Theme_Options::get('blogs-gradient-colour')?>!important;
}
.sidenav .accordion-heading .accordion-toggle:hover {
 background: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>!important;
}
/*-- Accordion Hover  ---------------------------*/
#content .accordion-heading a:hover, #content .accordion-heading a:active, #content .accordion-heading a:focus {
 background-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;

}
/*-- tab Hover  ---------------------------*/
#content .nav-tabs>.active>a, #content .nav-tabs>li>a:hover, #content .nav-tabs>li>a:focus {
	background-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
	border-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;	
}
/*-- Buttons  ---------------------------*/
#content .btn {
  border-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
  color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
}
#content .btn:hover, #content .btn:focus {
  background-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
  border-color: <?php echo UBC_Collab_Theme_Options::get('blogs-main-colour')?>;
}
</style>
<?php 
    } 
}

UBC_BLOGS_Theme_Options::init();
//var_dump( get_option( 'ubc-collab-theme-options' ));


