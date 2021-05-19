<?php

/* Manage features of Rich Text Editor field */
class CF7RT_Rich_Text_Editor{

	/* Intialize actions and filters required for fields */
	public function __construct(){
		add_action( 'init', array($this,'add_shortcode_rich_text_editor'),5 );

		add_filter( 'wpcf7_validate_rich_text_editor*', array($this, 'rich_text_editor_validation_filter'), 10, 2 );

		add_action( 'admin_init', array($this, 'add_tag_generator_rich_text_editor'), 20 );

		add_filter( 'wpcf7_mail_tag_replaced',array($this, 'replace_tag_rich_text_editor'),10,3);
				
	}

	/* Prevent replacing HTML tags */
	public function replace_tag_rich_text_editor($replaced, $submitted, $html){
		if($html){
			$replaced = $submitted;
		}
		return $replaced;
	}

	/* Add shortcode for Rich Text Editor field */
	public function add_shortcode_rich_text_editor() {
	    wpcf7_add_shortcode( array( 'rich_text_editor', 'rich_text_editor*' ),
	        array($this,'rich_text_editor_shortcode_handler'), true );
	}

	/* Display Rich Text Editor field on contact form with settings */
	public function rich_text_editor_shortcode_handler($tag){
		
	    $tag = new WPCF7_Shortcode( $tag );

	    if ( empty( $tag->name ) )
	        return '';

	    $validation_error = wpcf7_get_validation_error( $tag->name );

	    $class = wpcf7_form_controls_class( $tag->type );

	    if ( $validation_error )
	        $class .= ' wpcf7-not-valid';

	    $atts = array();

	    $atts['rows'] = $tag->get_rows_option( '10' );
	    $atts['class'] = $tag->get_class_option( $class );
	    $atts['id'] = $tag->get_option( 'id', 'id', true );
	    $atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );

	    if ( $tag->has_option( 'readonly' ) )
	        $atts['readonly'] = 'readonly';

	    if ( $tag->is_required() )
	        $atts['aria-required'] = 'true';

	    $atts['aria-invalid'] = $validation_error ? 'true' : 'false';

	    $value = (string) reset( $tag->values );

	    if ( '' !== $tag->content )
	        $value = $tag->content;

	    if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
	        $atts['placeholder'] = $value;
	        $value = '';
	    }

	    if ( wpcf7_is_posted() && isset( $_POST[$tag->name] ) )
	        $value = stripslashes_deep( $_POST[$tag->name] );

	    $atts['name'] = $tag->name;

	    $pre_formated_atts = $atts;
	    $atts = wpcf7_format_atts( $atts );

	    ob_start();
	    $settings = array( 
	        'media_buttons' => false ,
	        'textarea_name' => $tag->name,
	        'textarea_rows' => $pre_formated_atts['rows'],
	        'editor_class'=>"wpcf7_form_novalidate ".$tag->get_class_option( $class ),
	        'wpautop'=>false
	    );


	    wp_enqueue_script('jquery');
	    wp_editor( $value, $tag->name,$settings);
	    $rich_editor = ob_get_contents();

	    $html = '<span class="wpcf7-form-control-wrap '.$tag->name.'">'.$rich_editor.$validation_error.'</span>
	    <script type="text/javascript">
	    jQuery(document).ready(function() {
	        jQuery(".wpcf7-form").submit(function(e){
	            jQuery("#'.$tag->name.'").val(tinyMCE.get("'.$tag->name.'").getContent());
	            return true;
	        });
	    });
	    </script>';

	    ob_end_clean();

	    return $html;
	}

	/* Validate Rich Text Editor field */
	public function rich_text_editor_validation_filter( $result, $tag ) {
	    $tag = new WPCF7_Shortcode( $tag );

	    $type = $tag->type;
	    $name = $tag->name;

	    $value = isset( $_POST[$name] ) ? $_POST[$name] : '';
	    $results = $result;
	    $value = trim($value);
	    if ( 'rich_text_editor*' == $type ) {
        	if ( $tag->is_required() && empty($value) ) {
				$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
	        }
	    }

	    return $result;
	}

	/* Generate new field in contact form creation screen */
	public function add_tag_generator_rich_text_editor() {
	    if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;

	    $tag_generator = WPCF7_TagGenerator::get_instance();
	    $tag_generator->add( 'rich_text_editor', __( 'Rich Text Editor', 'cf7rt' ),
	        array($this,'settings_panel_rich_text_editor') );

	}

	/* Add settings for Rich Text Editor field */
	public function settings_panel_rich_text_editor( $contact_form,$args ='' ) {
		global $cf7rt,$cf7rt_rich_text_params;
        $args = wp_parse_args( $args, array() );
    	$type = $args['id'];

    	$cf7rt_rich_text_params['type'] = $type;

    	ob_start();
        $cf7rt->template_loader->get_template_part('rich-text-field-settings');
        echo ob_get_clean();
	
	}

}