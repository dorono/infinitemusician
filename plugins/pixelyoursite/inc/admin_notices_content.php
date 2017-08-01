<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

define( 'PYS_FREE_NOTICES_VERSION', '5.0.0' );

## WOO activated or WOO and EDD
$GLOBALS['PYS_FREE_WOO_NOTICES'] = array(
	
	array(
		'from'    => 1,
		'to'      => 1,
		'content' => '<strong>Customize WooCommerce Conversion Value:</strong>  With PixelYourSite Pro you can <strong>fine tune WooCommerce Events Values</strong> and improve conversion tracking. Include/Exclude Tax or Shipping, turn ON/OFF and fine-tune WooCommerce Events Value (full product price, price percent or a global value). Advance Purchase Event with super-useful parameters for Custom Audiences and retargeting.<br><a href="http://www.pixelyoursite.com/facebook-pixel-plugin?utm_source=wpa-woo&utm_medium=wp&utm_campaign=wp-woo1" target="_blank"><strong>Click to download PixelYourSite Pro for a serious discount</strong></a>.',
		'visible' => true
	),

	array(
		'from'    => 2,
		'to'      => 2,
		'content' => '<strong>PixelYourSite PRO + Product Catalog Feed Plugin Bundle:</strong> Track Conversion Value and start with Facebook Dynamic Ads for WooCommerce in minutes. <br>Get <strong>PixelYourSite Pro</strong> (<em>fine tune Conversion Value</em>, optimize WooCommerce events for Custom Audiences, track tags, product tags, <strong>Traffic Source</strong> and UTM, enable Advanced Matching and use <em>Dynamic Events</em>)<br>Get <strong>Product Catalog Feed Plugin</strong> (create WooCommerce Dynamic Ads Product Catalog XML feeds)<br><a href="http://www.pixelyoursite.com/bundle-offer?utm_source=wp-woo-bundle&utm_medium=wp&utm_campaign=wp-woo-bundle2" target="_blank"><strong>Click For The Bundle Offer (best deal)</strong></a>.',
		'visible' => true
	),

	array(
		'from'    => 3,
		'to'      => 3,
		'content' => '<strong>Secret Offer for WordPress Users: </strong> Get PixelYourSite PRO for WooCommerce with <a href="http://www.pixelyoursite.com/secret-offer?utm_source=wp-secret-offer-woo&utm_medium=wp&utm_campaign=secret-offer-woo3" target="_blank">this Exclusive Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 4,
		'to'      => 7,
		'content' => '<strong>PixelYourSite PRO Secret Offer:</strong> With PixelYourSite PRO you can build super-powerful Custom Audiences for improved retargeting. Track <strong>Traffic Source</strong> and <strong>URL Parameters,</strong> product name, category and tags, or the use of coupons. Enable <strong>Advanced Matching</strong> for better conversion attribution.<br><a href="http://www.pixelyoursite.com/facebook-pixel-plugin?utm_source=wp-secret-offer-woo&utm_medium=wp&utm_campaign=secret-offer-woo4-7" target="_blank">Discover Your Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 8,
		'to'      => 12,
		'content' => '<strong>Your Facebook Pixel FREE Guide:</strong> After <em>more than 25 000 users</em> and many hours spent on answering questions, we decided to make a comprehensive guide about the new Facebook Pixel. <br>Have you got it yet? <a href="http://www.pixelyoursite.com/facebook-pixel-pdf-guide?utm_source=wp-pixel-guide&utm_medium=wp&utm_campaign=wp-pixel-guide" target="_blank">Click here for your own FREE copy</a>.',
		'visible' => true
	),

);

## EDD but not WOO activated
$GLOBALS['PYS_FREE_EDD_NOTICES'] = array(

	array(
		'from'    => 1,
		'to'      => 1,
		'content' => '<strong>Customize Easy Digital Downloads Conversion Value:</strong> With PixelYourSite Pro you can <strong>fine tune EDD Events Values</strong> and improve conversion tracking. Include/Exclude Tax, turn ON/OFF and fine-tune EDD Events Value (full product price, price percent or a global value). Advance Purchase Event with super-useful parameters for Custom Audiences and retargeting.<br><a href="http://www.pixelyoursite.com/facebook-pixel-plugin?utm_source=wpa-edd&utm_medium=wp&utm_campaign=wp-edd1" target="_blank"><strong>Click to download PixelYourSite Pro for a serious discount</strong></a>.',
		'visible' => true
	),

	array(
		'from'    => 2,
		'to'      => 2,
		'content' => '<strong>Secret Offer for WordPress Users:</strong> Get PixelYourSite PRO for Easy Digital Downloads with this exclusive secret offer: <a href="http://www.pixelyoursite.com/secret-offer?utm_source=wp-secret-offer-edd&utm_medium=wp&utm_campaign=secret-offer-edd2" target="_blank">this Exclusive Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 3,
		'to'      => 3,
		'content' => '<strong>PixelYourSite PRO Secret Offer:</strong> With PixelYourSite PRO you can build super-powerful EDD Custom Audiences for improved retargeting. Track <strong>Traffic Source</strong> and <strong>URL Parameters,</strong> product name, category and tags, or the use of discount codes. Enable <strong>Advanced Matching</strong> for better conversion attribution.<br><a href="http://www.pixelyoursite.com/facebook-pixel-plugin?utm_source=wp-secret-offer-edd&utm_medium=wp&utm_campaign=secret-offer-edd3" target="_blank">Discover Your Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 4,
		'to'      => 7,
		'content' => '<strong>Your Facebook Pixel FREE Guide:</strong> After <em>more than 25 000 users</em> and many hours spent on answering questions, we decided to make a comprehensive guide about the new Facebook Pixel.</br>Have you got it yet? <strong>Download it now for free:</strong> <a href="http://www.pixelyoursite.com/facebook-pixel-pdf-guide?utm_source=wp-pixel-guide&utm_medium=wp&utm_campaign=wp-pixel-guide" target="_blank">Click here for your Guide</a>.',
		'visible' => true
	),

	array(
		'from'    => 8,
		'to'      => 12,
		'content' => '<strong>PixelYourSite PRO Secret Offer:</strong> Get your FREE guide about the new Facebook Pixel, because it will help you to make better ads.</br> Make sure you read it too: <a href="http://www.pixelyoursite.com/secret-offer?utm_source=wp-secret-offer-edd&utm_medium=wp&utm_campaign=secret-offer-edd7-12" target="_blank">Click to find more</a>.',
		'visible' => true
	),

);

## Both WOO and EDD not activated
$GLOBALS['PYS_FREE_NO_WOO_NO_EDD_NOTICES'] = array(

	array(
		'from'    => 1,
		'to'      => 1,
		'content' => '<strong>Upgrade to PixelYourSite PRO</strong> and optimize your FB ads for clicks on links or buttons with <strong>Dynamic Events</strong>. Improve conversion attribution and retargeting reach by using <strong>Advanced Matching</strong>. Track the <strong>Traffic Source</strong> and URL parameters (UTM) for better retargeting with Custom Audiences. Don’t miss this limited discount:<br> <a href="http://www.pixelyoursite.com/facebook-pixel-plugin?utm_source=wp-non-woo-non-edd&utm_medium=wp&utm_campaign=wp1" target="_blank"><strong>Click to download PixelYourSite Pro for a serious discount</strong></a>.',
		'visible' => true
	),

	array(
		'from'    => 2,
		'to'      => 2,
		'content' => '<strong>Secret Offer for WordPress Users:</strong> Upgrade to PixelYourSite PRO with <a href="http://www.pixelyoursite.com/secret-offer?utm_source=wp-secret-offer-non-woo-non-edd&utm_medium=wp&utm_campaign=secret-offer-wp2" target="_blank">this Exclusive Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 3,
		'to'      => 3,
		'content' => '<strong>PixelYourSite Secret Offer:</strong> Optimize your FB ads for key actions on your site wit Dynamic Events (fired on clicks, page scroll, or mouse over). Create super-powerful Custom Audiences based on Traffic Source or URL Parameters - <a href="http://www.pixelyoursite.com/secret-offer?utm_source=wp-secret-offer-non-woo-non-edd&utm_medium=wp&utm_campaign=secret-offer-wp3" target="_blank">Discover Your Secret Offer</a>.',
		'visible' => true
	),

	array(
		'from'    => 4,
		'to'      => 7,
		'content' => '<strong>Your Facebook Pixel FREE Guide:</strong> After <em>more than 25 000 users</em> and many hours spent on answering questions, we decided to make a comprehensive guide about the new Facebook Pixel.</br>Have you got it yet? <strong>Download it now for free:</strong> <a href="http://www.pixelyoursite.com/facebook-pixel-pdf-guide?utm_source=wp-pixel-guide&utm_medium=wp&utm_campaign=wp-pixel-guide" target="_blank">Click here for your Guide</a>.',
		'visible' => true
	),

	array(
		'from'    => 8,
		'to'      => 12,
		'content' => '<strong>PixelYourSite FREE Guide:</strong> Get your FREE guide about the new Facebook Pixel, because it will help you to make better ads.</br> Make sure you read it too: <a href="http://www.pixelyoursite.com/facebook-pixel-pdf-guide?utm_source=wp-pixel-guide&utm_medium=wp&utm_campaign=wp-pixel-guide" target="_blank">Download it now for free</a>.',
		'visible' => true
	),

);