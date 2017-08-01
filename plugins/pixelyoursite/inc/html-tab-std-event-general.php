<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
  <div class="pys-col pys-col-full">
  
  <table class="layout">
    <tr>
      <td class="alignright">
        <p class="label big">Activate Events</p>
      </td>
      <td>
        <input type="checkbox" name="pys[std][enabled]" value="1" <?php pys_checkbox_state( 'std', 'enabled' ); ?> >
      </td>
    </tr>
  </table>
  
  <button class="pys-btn pys-btn-blue pys-btn-big aligncenter">Save Settings</button>
  
  </div>
</div>