<?php 
if(isset($_POST['import_file_url'])){
	$file_url = $_POST['import_file_url'];
	function import_pdf_file( $file_url , $post_id , $desc = '' ) {
	
		if ( ! empty($file_url)) {
			$tmp = download_url( $file_url );
	
			preg_match('/[^\?]+\.(pdf)/', $file_url, $matches);
			$file_array['name'] = basename($matches[0]);
			$file_array['tmp_name'] = $tmp;
	
			if ( is_wp_error( $tmp ) ) {
				@unlink($file_array['tmp_name']);
				$file_array['tmp_name'] = '';
				return false;
			}
			$desc = $file_array['name'];
			$id = media_handle_sideload( $file_array, $post_id, $desc );
			if ( is_wp_error($id) ) {
				@unlink($file_array['tmp_name']);
				return false;
			} else {
				$src = wp_get_attachment_url( $id );
			}
			
		}

		if ( !empty( $src )) 
			return $src;
		else 
			return false;
	}

	$new_file_url = import_pdf_file($file_url, 0, 'Imported PDF File');
	//echo $do_upload;

}
?>
<div class="wrap">
	<div id="poststuff">
	    <div id="post-body">
		    <?php screen_icon(); ?>
		    <div class="tnc-upload-container">
				<form action="" method="POST">
					<h1>Put link to any pdf file and click on import</h1>
					<input type="text" name="import_file_url" class="uploaded_file_url">
					<input type="submit" value="Import" class="button button-primary copy_btn" />
				</form>
				<?php if(!empty($new_file_url)){ ?>
					<h1>Imported File</h1>
					<h3>Copy the url in the input below and use that in your shortcode</h3>
					<input id="fileurl" class="uploaded_file_url" type="text" name="tnc_quick_upload_file" value="<?php echo $new_file_url; ?>" onclick="this.select();" /><a href="#" onClick="copyInstr()" class="button button-primary copy_btn">Copy</a><br />
            		<p id="copy-instruction"></p>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
function copyInstr() {
    document.getElementById("fileurl").select();
    document.getElementById("copy-instruction").innerHTML = "Please Press ctrl+c (cmd+c on mac) on your keyboard now to copy.";
}
</script>