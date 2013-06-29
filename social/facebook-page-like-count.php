<?php
/*
 * Facebook Page Like Count
 * http://presshive.com
 * <a href="http://presshive.com/">Presshive</a>
 * 1.0
 * Shows the Facebook page like count via shortcode or template tag. 
 * facebook, fb, like, like count, Facebook Page Like Count, facebook page likes
 * GPLv2 or later
 */

/*  Copyright 2013  

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
?>
<?php
global $flc_message;
add_action( 'admin_menu', 'flc_admin_menu_page' );

/**
 * Add a submenu page inside settings
 */
function flc_admin_menu_page(){
    add_submenu_page('options-general.php', 'Facebook Page Like Count', 'Facebook Page Like Count', 'manage_options', 'flc_option_page', 'flc_option_page');
}

/**
 * Display setting of Facebook Page Like Count,
 *
 * Settings to auto update Facebook Page Like Counter by using cron
 * update manually by button click.
 *
 */
function flc_option_page(){
    global $flc_message;
    flc_page_settings_update();
    $flc_fb_like_count = get_option('flc_fb_like_count');
    $flc_fb_like_page = get_option('flc_fb_like_page');
    $fb_fan_check_frequency = get_option('fb_fan_check_frequency');
    $formating_option = get_option('flc_fb_like_count_number_formate');
    if( !is_array($formating_option) || count($formating_option) == 0){
        $formating_option = array('apply'=>'', 'thousand_saprator'=>'');
    }
    ?>
    <div class="wrap">
        <script>
            function flc_update(){
                jQuery('.flc_loader').html('Updating...').show();
                var page = jQuery('input[name="fb_fan_page"]').val();
                var frequency = jQuery('select[name="fb_fan_check_frequency"] option:selected').val();
                var no_error = true;
                if( page=='' || typeof(page) == 'undefined'){
                    no_error = false;
                    jQuery('.flc_loader').html('Please enter page url.').show().css('color', '#FF0000');
                }
                if( no_error ){
                    var form = jQuery('#flc_settings_form').serialize();
                    jQuery.ajax({
                        type    :   'POST',
                        url     :   '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                        data    :   'action=flc_update_counter_ajax&'+form,
                        success :   function (res){
                            jQuery('.flc_loader').html('Updating...').hide();
                            res = jQuery.parseJSON(res);
                            if( res.error != '' && typeof( res.error ) != 'undefined' ){
                                jQuery('.flc_loader').html( res.error ).show().css('color', '#FF0000');
                            }
                            else{
                                jQuery('#flc_page_like_counter').html(res.counter );
                                jQuery('.flc_loader').html('Updated').show().css('color', '#008000');
                            }
                        }
                    });
                }
            }
        </script>
        <h2>Facebook Page Like Count Setting</h2>
        <?php
        if(!empty ($flc_message)){
            echo '<div class="updated"><p><strong>'.$flc_message.'</strong></p></div>';
        }
        ?>
        <form action="?page=flc_option_page" method="post" id="flc_settings_form">
            <?php
            echo '<p>Total Likes: <span id="flc_page_like_counter">';
            if( $flc_fb_like_count ){
                $number_formate = get_option('flc_fb_like_count_number_formate');
                if( !is_array($number_formate) || count($number_formate) == 0){
                    $number_formate = array('apply'=>'',  'thousand_saprator'=>'');
                }
                $number_formate_apply = $number_formate['apply'];
                if( $number_formate_apply == 'checked' ){
                    $thousand_saprator = isset( $number_formate['thousand_saprator']) ? $number_formate['thousand_saprator'] : '';
                    $flc_fb_like_count = number_format($flc_fb_like_count, 0 , '', $thousand_saprator);
                }
                echo $flc_fb_like_count;
            }
            else{
                echo '0';
            }
            echo '</span></p>';
            ?>
            <table width="100%">
                <tr>
                    <td width="20%">
                        <label for="fb_fan_page">Page Link</label>
                    </td>
                    <td width="80%">
                        <input type="text" name="fb_fan_page" value="<?php echo $flc_fb_like_page; ?>" id="fb_fan_page" class="widefat"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="fb_fan_check_frequency">Update Frequency</label>
                    </td>
                    <td>
                        <select name="fb_fan_check_frequency"  id="fb_fan_check_frequency" class="">
                            <option value=""><--select--></option>
                            <?php $schedules = wp_get_schedules();
                            if(is_array($schedules)){
                                foreach ( $schedules as $key => $value ){
                                    echo '<option '.  selected($fb_fan_check_frequency, $key, false).' value="'.$key.'">'.$value['display'].'</option>';
                                }
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td valign="top">
                        <label for="formating_option_1">Number Formating</label>
                    </td>
                    <td>
                        <input  type="checkbox" name="formating_option[apply]" value="checked" id="formating_option_1" <?php checked($formating_option['apply'],'checked') ?> /> Apply Number Formating
                        <table >
                            <tr>
                                <td><label for="formating_option_4">Thousand separator</label></td>
                                <td><input type="text" name="formating_option[thousand_saprator]" value="<?php echo $formating_option['thousand_saprator']; ?>" id="formating_option_4"/></td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="submit" value="Save" class="button"/>
                    </td>
                    <td>
                        <input type="submit" name="submit" value="Update Now" class="button" onclick="flc_update(); return false;"/>
                        <br /><span class="flc_loader" style="Display:none;">Updating...</span>
                    </td>
                </tr>
            </table>
        </form>
    </div>
<?php
}

/**
 * Featch Facebook Page Like Count by using CURL
 *
 * @param url of the like page
 * @return int returns number of facebook page likes, returns error string if like page not found or any error occurred.
 */
function flc_update_like_count( $page_link ){
    global $flc_message;
    if( !$page_link ){
        $page_link = get_option('flc_fb_like_page');
    }
    $fan_count = 0;

    $url = str_replace('https://www.facebook.com/', '', $page_link);

    $curl_url = 'https://graph.facebook.com/' . $url;
    try{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curl_url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        $results = json_decode($result, true);
        curl_close($ch);

        if(array_key_exists( 'error', $results)){
            $flc_message = 'Error - '.$results['error']['message'];
            return $flc_message;
        }
        else{
            update_option('flc_fb_like_count', $results['likes']);
            $flc_message = 'Like count updated';
            return (int)$results['likes'];
        }
    }
    catch( Exception $e){
        $flc_message = $e->getMessage();
    }
}
/**
 * Save the settings of like page
 *
 * @return string return message after setting saved.
 */
function flc_page_settings_update(){
    global $flc_message;

    $submit = isset( $_POST['submit'] ) ? $_POST['submit'] : '';
    if( $submit == 'Save'){
        $prev_link = get_option('flc_fb_like_page');
        $curr_link = $_POST['fb_fan_page'];
        $fb_fan_check_frequency = $_POST['fb_fan_check_frequency'];

        wp_clear_scheduled_hook( 'nme_update_fb_fan_counter' );

        if( $fb_fan_check_frequency ){
            wp_schedule_event( time(), $fb_fan_check_frequency, 'nme_update_fb_fan_counter');
        }
        update_option('flc_fb_like_page', $_POST['fb_fan_page']);
        update_option('fb_fan_check_frequency', $_POST['fb_fan_check_frequency'] );

        $nu_opt= array();
        $apply_no_formate = $_POST['formating_option']['apply'];
        if( $apply_no_formate == 'checked'){
            $nu_opt = $_POST['formating_option'];
        }
        update_option('flc_fb_like_count_number_formate', $nu_opt);
        if( $curr_link != $prev_link){
            flc_update_like_count( $curr_link );
        }
        if( empty($flc_message ) )
            $flc_message = 'Setting saved successfully';
    }
}

add_action('nme_update_fb_fan_counter', 'flc_update_like_count');

add_action('wp_ajax_flc_update_counter_ajax', 'flc_update_counter_ajax_callback');

/**
 * Update the value of Facebook Page Like Count by ajax
 *
 * @return json total count of like page or error if error occurred.
 */
function flc_update_counter_ajax_callback(){
    global $flc_message;
    if( isset($_POST['fb_fan_check_frequency'])){
        update_option('fb_fan_check_frequency', $_POST['fb_fan_check_frequency'] );
    }
    update_option('flc_fb_like_page', $_POST['fb_fan_page']);
    $nu_opt= array();
    $apply_no_formate = $_POST['formating_option']['apply'];
    if( $apply_no_formate == 'checked'){
        $nu_opt = $_POST['formating_option'];
    }
    update_option('flc_fb_like_count_number_formate', $nu_opt);
    $counter = flc_update_like_count();

    if( !is_numeric( $counter )){
        die(json_encode(array('error'=> $counter )));
    }
    else{
        $number_formate = get_option('flc_fb_like_count_number_formate');
        if( !is_array($number_formate) || count($number_formate) == 0){
            $number_formate = array('apply'=>'', 'thousand_saprator'=>'');
        }
        $count = 0;
        $number_formate_apply = $number_formate['apply'];
        if( $number_formate_apply == 'checked' ){
            $thousand_saprator = isset( $number_formate['thousand_saprator']) ? $number_formate['thousand_saprator'] : '';
            $counter = number_format($counter, 0 , '', $thousand_saprator);
        }

        die(json_encode(array( 'counter' => $counter )));
    }
}

/**
* Show like counts of facebook fan page by shortcode
*
* @param $echo true/false
*
* @return string this function returns total like counts
*/

function flc_count($echo = false ){
    $flc_fb_like_count = get_option('flc_fb_like_count');
    $number_formate = get_option('flc_fb_like_count_number_formate');
    if( !is_array($number_formate) || count($number_formate) == 0){
        $number_formate = array('apply'=>'','thousand_saprator'=>'');
    }
    $count = 0;
    $number_formate_apply = $number_formate['apply'];
    if( $flc_fb_like_count ){
        if( $number_formate_apply == 'checked' ){
            $thousand_saprator = isset( $number_formate['thousand_saprator']) ? $number_formate['thousand_saprator'] : '';
            $count = '<span class="flc_like_count">'.number_format($flc_fb_like_count, 0 , '', $thousand_saprator).'</span>';
        }
        else{
            $count = '<span class="flc_like_count">'.$flc_fb_like_count.'</span>';
        }
    }
    if( $echo ){
        echo apply_filters('flc_count', $count);
    }
    else{
        return apply_filters('flc_count', $count);
    }
}

/**
* Show like counts of facebook fan page by shortcode
*/
function flc_count_shortcode(){
    flc_count(true);
}

/**
* Creates shortcode [flc_count] to show Facebook Page Like Count.
*/
add_shortcode('flc_count', 'flc_count_shortcode');
?>