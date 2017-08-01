<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<div class="pys-box">
	<div class="pys-col pys-col-full">
		<h2 class="section-title">Dynamic Events (Pro Option)</h2>

		<p>Add standard or custom events that will fire when a visitor performs an important action on your site.</p>

		<div class="pys-col-narrow">
			<h2>Start using Dynamic Events with the PRO version of the plugin</h2>

			<p>Dynamic Events will not fire when a page is loaded, but when the user does a desired action on that page
				(clicks on something, scrolls the page or moves the mouse over some key element).</p>

			<p>This way you can <strong>optimize your ads for actions on your website</strong>, like filling up forms
				(contact forms and popup plugins are supported), newsletter signups, clicks on links and buttons (useful
				for affiliate sites).</p>

			<p>Incorporating Dynamic Events in your strategy is a proven way to increase your ads ROI and the overall
				profitability of your Facebook campaigns.</p>

			<h2>Trigger Dynamic Events:</h2>

			<ul style="list-style: initial; padding-left: 20px;">
				<li><strong>On Clicks</strong>: When the users <strong>clicks on links or buttons</strong> (html links or CSS Selector)</li>
				<li>On <strong>Page Scroll</strong>: when the user scrolls the page up to a defined percent</li>
				<li>On <strong>MouseOver</strong>: when the user moves the mouse on a page element</li>
			</ul>

			<p><strong>If you are not using Facebook Dynamic Events you are literally leaving money on the table.</strong></p>

			<p><strong>Super Offer Ending Soon: <a href="http://www.pixelyoursite.com/facebook-pixel-plugin"
			                                       target="_blank">Update now and start to benefit from Dynamic Events</a></strong></p>

			<p>Or Learn more about <a href="http://www.pixelyoursite.com/facebook-pixel-dynamic-events" target="_blank">How to use Dynamic Events - Complete guide</a></p>
		</div>

		<div style="text-align: center; margin-top: 40px; margin-bottom: 40px;">
			<a href="#" class="pys-btn pys-btn-big pys-btn-blue disabled">Add New Dynamic Event</a>
		</div>


		<hr>

		<table class="layout">
			<tr>
				<td class="alignright"><p class="label big">Activate Dynamic Events</p></td>
				<td><input type="checkbox" disabled="disabled"></td>
			</tr>

			<tr>
				<td class="alignright"><p class="label">Process links</p></td>
				<td>
					<input type="checkbox" disabled="disabled">Process links in Post Content
					<span class="help">The <code>the_content()</code> hook.</span>

					<input type="checkbox" disabled="disabled">Process links in Widgets Text
					<span class="help">The <code>widget_text()</code> hook.</span>
				</td>
			</tr>
		</table>

		<button class="pys-btn pys-btn-blue pys-btn-big aligncenter disabled">Save Settings</button>

	</div>
</div>