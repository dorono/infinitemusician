<?php
$tnc_viewer_bg         = "viewer_bg_color";
$tnc_topbar_bg         = "topbar_bg_color";

if(isset($_POST["submit"])){ 
    $viewer_bg   = $_POST[$tnc_viewer_bg];
    update_option($tnc_viewer_bg, $viewer_bg);
    
    $topbar_bg   = $_POST[$tnc_topbar_bg];
    update_option($tnc_topbar_bg, $topbar_bg);
    
    echo '<div id="message" class="updated fade"><p>Options Updated</p></div>';
} else {
    $viewer_bg       = get_option($tnc_viewer_bg);
    $topbar_bg       = get_option($tnc_topbar_bg);
} 

//media uploader

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
            <div class="tnc-pdf-column-left">
                <div class="postbox">
                    <h3>Customize Viewer Colors - PDF viewer for WordPress</h3>
                    <div class="inside">
                        <p>
                            Developed by <a href="http://themencode.com" title="ThemeNcode" target="_blank"> ThemeNcode</a>
                        </p>
                    </div><!--/.inside--> 
                </div><!--/.postbox-->
                <div class="postbox">
                    <fieldset>
                        <h3>Customize Colors of Viewer</h3>
                        <div class="inside">
                            <form method="post" action="">
                                <strong>Viewer Background color</strong><br><br />
                                
                                <input type="text" name="<?php echo $tnc_viewer_bg; ?>" value="<?php echo $viewer_bg; ?>" class="viewer_bg" data-default-color="#404040" /><br><br />
                                <strong>Top Bar Background color</strong><br><br />
                                
                                <input type="text" name="<?php echo $tnc_topbar_bg; ?>" value="<?php echo $topbar_bg; ?>" class="topbar_bg" data-default-color="#474747" /><br><br />
                                
                                <p><input type="submit" value="Save" class="button button-primary" name="submit" /></p>
                            </form>
                        </div><!--/.inside--> 
                    </fieldset>
                </div>
            </div> <!-- column left -->
            
            <div class="tnc-pdf-column-right">
                <div class="postbox">
                    <h3>Useful Resources</h3>
                    <div class="inside">
                        <ul>
                            <li><a href="http://goo.gl/v0B6gA">Codecanyon Plugin Page</a></li>
                            <li><a href="https://themencode.com/live-preview/pdf-viewer-for-wordpress/">Plugin Live Demo</a></li>
                            <li><a href="https://themencode.com/docs/pdf-viewer-for-wordpress/">Plugin Documentation</a></li>
                            <li><a href="http://youtube.com/channel/UC0mkhMK6fTx1BCovV6M_E4w">Video Documentations</a></li>
                            <li><a href="https://themencode.com/helpdesk/">HelpDesk</a></li>
                        </ul>
                    </div><!--/inside--> 
                </div><!--/.postbox-->

                <div class="postbox">
                    <h3>Latest updates from ThemeNcode</h3>
                    <div class="inside">
                        <iframe src="https://themencode.com/updates/" frameborder="0" width="325" height="400"></iframe>
                    </div><!--/.inside--> 
                </div>
                <!--/.postbox other_plugins -->
                <!-- Subscribe -->
                <div class="postbox">
                    <h3>Stay Updated with Latest Products and News from ThemeNcode</h3>
                    <div class="inside">
                        <div class="newsletter newsletter-subscription">
                            <form method="post" action="https://themencode.com/wp-content/plugins/newsletter/do/subscribe.php" onsubmit="return newsletter_check(this)">
                                <table cellspacing="0" cellpadding="3" border="0">
                                    <!-- first name -->
                                    <tr>
                                        <th>Name</th>
                                        <td>
                                            <input class="newsletter-firstname" type="text" name="nn" size="30"required></td>
                                    </tr>
                                    <!-- email -->
                                    <tr>
                                        <th>Email</th>
                                        <td align="left">
                                            <input class="newsletter-email" type="email" name="ne" size="30" required></td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="newsletter-td-submit">
                                            <input class="newsletter-submit button button-primary" type="submit" value="Subscribe"/>
                                        </td>
                                    </tr>
                                </table>
                            </form>
                        </div><!--/.newsletter--> 
                    </div><!--/.inside --> 
                </div><!-- /.postbox Subscribe End -->
            </div> <!-- tnc-pdf-column-right -->
        </div> <!-- postbody -->
    </div><!--poststuff-->
</div><!--/.wrap-->
<style type="text/css">
    a{
        text-decoration: none;
    }
    #poststuff h3{
        border-bottom: 1px solid #f4f4f4;
    }
</style>
<script type="text/javascript">
    //<![CDATA[
    if (typeof newsletter_check !== "function") {
    window.newsletter_check = function (f) {
        var re = /^([a-zA-Z0-9_\.\-\+])+\@(([a-zA-Z0-9\-]{1,})+\.)+([a-zA-Z0-9]{2,})+$/;
        if (!re.test(f.elements["ne"].value)) {
            alert("The email is not correct");
            return false;
        }
        if (f.elements["nn"] && (f.elements["nn"].value == "" || f.elements["nn"].value == f.elements["nn"].defaultValue)) {
            alert("The name is not correct");
            return false;
        }
        if (f.elements["ny"] && !f.elements["ny"].checked) {
            alert("You must accept the privacy statement");
            return false;
        }
        return true;
    }
    }
    //]]>
</script>