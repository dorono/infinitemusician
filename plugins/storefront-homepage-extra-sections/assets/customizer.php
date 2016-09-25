<?php

    /**
	 * Add a new section
	 */
    $wp_customize->add_section( 'shes_extra_section' , array(
	    'title'      	=> __( 'Homepage Slider Section', 'storefront-homepage-extra-sections' ),
	    'priority'   	=> 55,
	) );
	
	$wp_customize->add_section( 'shes_tripple_section' , array(
	    'title'      	=> __( 'Homepage Tripple Widget Section', 'storefront-homepage-extra-sections' ),
	    'priority'   	=> 200,
	) );
	
	// Slider Options
	
	$wp_customize->add_setting( 'shes_slider_content', array( 
		'default' => 'posts', 
		'sanitize_callback' => 'sanitize_text_field' 
	) );
	
	$wp_customize->add_control( 'shes_slider_content', array(
		'type' => 'radio',
		'label' => __( 'Display products or posts', 'shes' ),
		'description' => __( 'Choose to either display Woo Products or sticky posts as slider contents!', 'storefront-homepage-extra-sections' ),
		'section' => 'shes_extra_section',
		'priority'   => 2,
		'choices' => array(			
			'products'	=> __('Products', 'shes'),
			'posts'	=> __('Sticky Posts', 'shes')
	    ) 
	) );

    if ( class_exists( 'woocommerce' ) ) {
        $wp_customize->add_setting( 'shes_slider_area', array(
	        'default' => 'recent',
	        'sanitize_callback' => 'sanitize_text_field',
	    ));
	    
	    $wp_customize->add_control( 'effect_select_box', array(
	        'settings' => 'shes_slider_area',
	        'label'    => __( 'What products to show:', 'shes' ),
			'description' => __( 'Set the slider to either show your featured products, best selling, special offers e.t.c!', 'storefront-homepage-extra-sections' ),
	        'section'  => 'shes_extra_section',
	        'type'     => 'radio',
	        'choices'  => array(
	            'featured'    => __('Featured Products', 'shes'),
	            'total_sales' => __('Best Selling Products', 'shes'),
	            'recent'      => __('Recent Products', 'shes'),
	            'top_rated'   => __('Top Rated Products', 'shes'),
	            'sale'        => __('On Sale Products', 'shes'),
	        ),
	        'priority' => 3,
	    ));
	}
		
	$wp_customize->add_setting( 'shes_slider_num_show', array(
	   	'default' => 5,
    	'sanitize_callback' => 'absint',
	) );
	    
	$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'shes_slider_num_show', array(
	    'type' => 'number',
		'label'       => __( 'Slides show at most', 'storefront-homepage-extra-sections' ),
		'description' => __( 'Set how many items to rotate in the slider! This setting is for both products or posts as the slider items.', 'storefront-homepage-extra-sections' ),
	    'section'     => 'shes_extra_section',
	    'settings'    => 'shes_slider_num_show',
	    'priority'    => 3,
		'style' => 'width: 55',
	) ) 
	);
	
	$wp_customize->add_setting( 'shes_slider_direction', array( 
		'default' => 'true', 
		'sanitize_callback' => 'sanitize_text_field' 
	) );
	
	$wp_customize->add_control( 'shes_slider_direction', array(
		'type' => 'radio',
		'label' => __( 'Slider Direction', 'shes' ),
		'description' => __( 'Set slider direction to either from left to right or right to left. Has no effect if slider is set to fadein below!', 'storefront-homepage-extra-sections' ),
		'section' => 'shes_extra_section',
		'priority'   => 2,
		'choices' => array(			
			'none'	=> __( 'Choose Direction', 'storefront-homepage-extra-sections' ),
			'true'	=> __( 'Left to Right', 'storefront-homepage-extra-sections' ),
			'false'	=> __( 'Right to Left', 'storefront-homepage-extra-sections' )
	    ) 
	) );
	
	$wp_customize->add_setting( 'shes_slider_animation', array( 
		'default' => 'slide', 
		'sanitize_callback' => 'sanitize_text_field' 
	) );
	
	$wp_customize->add_control( 'shes_slider_animation', array(
		'type' => 'radio',
		'label' => __( 'Slider Animation', 'storefront-homepage-extra-sections' ),
		'description' => __( 'Set weather the slides slidein or fadein!', 'storefront-homepage-extra-sections' ),
		'section' => 'shes_extra_section',
		'priority'   => 2,
		'choices' => array(			
			'slide'	=> __( 'Slide In', 'storefront-homepage-extra-sections' ),
			'fade'	=> __( 'Fade In', 'storefront-homepage-extra-sections' )
	    ) 
	) );
	
	$wp_customize->add_setting( 'shes_tripple_title', array( 
		'default' => '', 
		'sanitize_callback' => 'sanitize_text_field',
        'capability' => 'edit_theme_options',
	) );
	
	$wp_customize->add_control( 'shes_tripple_title', array(
		'type'      => 'text',
		'label'     => __( 'Add a section title to the tripple widget area on the homepage - leave empty for none.', 'storefront-homepage-extra-sections' ),
		'section'   => 'shes_tripple_section',
		'priority'  => 10,
		) 
	);
	
	$wp_customize->add_setting( 'shes_tripple_tagline', array( 
		'default' => '', 
		'sanitize_callback' => 'sanitize_text_field',
        'capability' => 'edit_theme_options',
	) );
	
	$wp_customize->add_control( 'shes_tripple_tagline', array(
		'type'      => 'text',
		'label'     => __( 'Add a section tagline for the tripple widget area on the homepage - leave empty for none.', 'storefront-homepage-extra-sections' ),
		'section'   => 'shes_tripple_section',
		'priority'  => 20,
		) 
	);