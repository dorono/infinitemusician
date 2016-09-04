<?php
if(function_exists( 'wp_enqueue_media' )){
    wp_enqueue_media();
} else {
    wp_enqueue_style('thickbox');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
}
?>
<div class="wrap">
<div id="poststuff">
    <div id="post-body">
        <?php screen_icon(); ?>
        <div class="tnc-upload-container">
            <h1>Upload any PDF file & get the link below</h1>
            <a href="#" class="tnc_quick_upload button button-primary">Click here to upload PDF file & get url below</a>
            <br />
            <h4 class="">Find the url of the selected file below:</h4>
            <br>
            <input id="fileurl" class="uploaded_file_url" type="text" name="tnc_quick_upload_file" value="" onclick="this.select();" /><a href="#" onClick="copyInstr()" class="button button-primary copy_btn">Copy</a><br />
            <p id="copy-instruction"></p>
        </div>
        
         <div class="tnc-instructions">
         	<h3>Instructions</h3>
         	<ol>
         		<li>Click on the Big Blue Button to Upload PDF File</li>
         		<li>A popup will appear, select the file you want to upload & click on Get Link</li>
         		<li>A link will appear in the input field below button.</li>
         		<li>Copy the link (Press ctrl+c to copy) & use in any shortcode as file url</li>
         		<li>All Done....</li>
         	</ol>
         </div>
        </div> <!-- postbody -->
    </div><!--poststuff-->
</div><!--/.wrap-->
<script type="text/javascript">
function copyInstr() {
    document.getElementById("fileurl").select();
    document.getElementById("copy-instruction").innerHTML = "Please Press ctrl+c (cmd+c on mac) on your keyboard now to copy.";
}
jQuery(document).ready(function(jQuery){
    jQuery('.tnc_quick_upload').click(function(e) {
        e.preventDefault();
        var pdf_uploader = wp.media({
            title: 'Upload File',
            button: {
                text: 'Get Link'
            },
            multiple: false  // Set this to true to allow multiple files to be selected
        })
        .on('select', function() {
            var attachment = pdf_uploader.state().get('selection').first().toJSON();
            jQuery('.uploaded_file_url').val(attachment.url);
        })
        .open();
    });
});
</script>