<?php

    register_sidebar( array(
		'name'          => __( 'Homepage 1/3 Left', 'storefront-homepage-extra-sections' ),
		'id'            => 'shesw-1',
		'description'   => __( 'Left widget area - part of the homepage triple widgets section', 'storefront-homepage-extra-sections' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
		
	register_sidebar( array(
		'name'          => __( 'Homepage 1/3 Center', 'storefront-homepage-extra-sections' ),
		'id'            => 'shesw-2',
		'description'   => __( 'Center widget area - part of the homepage triple widgets section', 'storefront-homepage-extra-sections' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
		
	register_sidebar( array(
		'name'          => __( 'Homepage 1/3 Right', 'storefront-homepage-extra-sections' ),
		'id'            => 'shesw-3',
		'description'   => __( 'Right widget area - part of the homepage triple widgets section', 'storefront-homepage-extra-sections' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
		
	register_sidebar( array(
		'name'          => __( 'Homepage Fullwidth Widget', 'storefront-homepage-extra-sections' ),
		'id'            => 'shesw-4',
		'description'   => __( 'A fullwidth homepage widget - use it to make a statement or a call to action prompt!', 'storefront-homepage-extra-sections' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );