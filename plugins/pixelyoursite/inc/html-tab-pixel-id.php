<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
  <div class="pys-col pys-col-full">
    <h2 class="section-title">Add your Pixel ID:</h2>
    
    <table class="layout">
      <tr>
        <td class="legend"><p class="label">Add your Facebook Pixel ID:</p></td>
        <td>
          <input type="text" name="pys[general][pixel_id]"
            placeholder="Enter your Facebook Pixel ID"  
            value="<?php echo pys_get_option( 'general', 'pixel_id' ); ?>">
          <span class="help">Where to find the Pixel ID? <a href="http://www.pixelyoursite.com/facebook-pixel-plugin-help" target="_blank">Click here for help</a></span>
        </td>
      </tr>

	    <tr>
		    <td></td>
		    <td>
			    <input type="checkbox" name="" value="1" class="disabled">Track Traffic Source and UTM (URL Tags) - <strong>This is a PRO feature</strong> - <a href="http://www.pixelyoursite.com/facebook-pixel-plugin">Update NOW</a>
			    <span class="help">Add traffic source as <code>traffic_source</code> param on all your events. URL tags (UTM) will be also tracked for each event. Use this to segment your Custom Audiences and improve your retargeting (retarget people based on when they come from, like Google, Facebook or a particular ad, for example).</span>
		    </td>
	    </tr>

	    <tr>
		    <td></td>
		    <td>
			    <input type="checkbox" name="" value="1" class="disabled">Enable Advance Matching - <strong>This is a PRO feature</strong> - <a href="http://www.pixelyoursite.com/facebook-pixel-plugin">Update NOW</a>
			    <span class="help">Advance Matching can lead to 10% increase in attributed conversions and 20% increase in reach of retargeting campaigns - <a href="http://www.pixelyoursite.com/enable-advance-matching-woocommerce" target="_blank">Click to read more</a> </span>
		    </td>
	    </tr>

	    <tr>
		    <td class="legend"><p class="label">Output Pixel Code to:</p></td>
		    <td>
			    <p style="margin-top: 0;"><input type="radio" name="pys[general][in_footer]" value="0" <?php echo pys_radio_state( 'general', 'in_footer', 0 ); ?> >Head</p>
			    <p style="margin-top: 0;"><input type="radio" name="pys[general][in_footer]" value="1" <?php echo pys_radio_state( 'general', 'in_footer', 1 ); ?>>Footer</p>
		    </td>
	    </tr>

    </table>
  </div>
</div>