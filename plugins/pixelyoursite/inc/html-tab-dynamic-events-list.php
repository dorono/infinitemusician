<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
  <div class="pys-col pys-col-full">
    <h2 class="section-title">Active Dynamic Events</h2>

    <div class="tablenav top">
      <a href="#" class="button button-primary action thickbox disabled" title="Add new event">Add new event</a>
      <a href="#" class="button button-delete action disabled">Delete selected</a>
    </div>
  
    <table class="widefat fixed pys-list disabled">
      <thead>
        <tr>
	        <td class="check-column"><input type="checkbox"></td>
	        <th scope="col" class="column-type">Trigger On</th>
	        <th scope="col" class="column-url">URL / CSS / Position</th>
	        <th scope="col" class="column-type">Type</th>
	        <th scope="col" class="column-code">Code</th>
	        <th scope="col" class="column-actions">Actions</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>
</div>