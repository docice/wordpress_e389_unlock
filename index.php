<?php
/*
Plugin Name: e398
Plugin URI: https://docice.bohramt.de
Description: E389 Unlock Plugin
Author: Björn Birkholz
Version: 1.0
Author URI: https://docice.bohramt.de
Text Domain: e389
Domain Path: /languages
License:     GPL3

    wordpress_e389_unlock
    Copyright (C) 2018  Björn Birkholz

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

function e389_init() {
    $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages'; /* Relative to WP_PLUGIN_DIR */
    load_plugin_textdomain( 'e389', false, $plugin_rel_path );
}
add_action('plugins_loaded', 'e389_init');
class hw {
    public function calculate($imei, $mode){
            $arrayofbytes = array();
            $digesthash = md5($imei.$this->mode($mode));
            $arrayofbytes = $this->bytearray($digesthash);
            return $this->xorbytes($arrayofbytes);
    }
    private function mode($arg){
            $this->unlock = "5e8dd316726b0335";
            $this->flash = "97b7bc6be525ab44";
            if($arg == 'unlock'){
                return $this->unlock;
            }
            else{
                return $this->flash;
            }
    }
    private function bytearray($hash){
        $splitdigest = substr(chunk_split($hash,2,":"),0,-1);
        $arrdigest = explode(":",$splitdigest);
        return $arrdigest;
    }
    private function xorbytes($arr){
        foreach (range(0,3) as $i) {
            $code = dechex(hexdec($arr[$i]) ^ hexdec($arr[4+$i]) ^ hexdec($arr[8+$i])  ^ hexdec($arr[12+$i]));
            if(strlen($code)< 2) {
                $code = "0" . $code;
            }
            $codes = $codes . $code;
        }
        $tmpcdec = hexdec($codes);
        $tmp1dec = hexdec("1ffffff");
        $tmp2dec = hexdec("2000000");
        $c = $tmpcdec & $tmp1dec;
        $c = $c | $tmp2dec;
        return $c;
    }
}
add_action( 'wp_ajax_e389', 'e389_action' );
add_action( 'wp_ajax_nopriv_e389', 'e389_action' );
function e389_action() {
    check_ajax_referer( 'e389-special-string', 'security' );
    $imei = htmlspecialchars($_POST['imei']);
    if (strlen($imei)!=15){
        _e('<h4>The IMEI is 15 characters long!</h4>','e389');
    }else {
        if (is_numeric($imei)){
            $hw = new hw();
            print "IMEI : <b>".$imei."</b><br /><br />";
            print "UNLOCK CODE : <b>".$hw->calculate($imei,"unlock")."</b><br />";
            print "FLASH CODE : <b>".$hw->calculate($imei,"flash")."</b><br />";
            print "<br />";
        }else {
            _e('<h4>The IMEI consists only of numbers!</h4>','e389');
        }
    }
    wp_die();
}



function e389(){
    $form = '<div id="e389output" name="e389output"></div>IMEI : <input name="imei" id="imei" size="15" type="text"><br /><br />
    <div class="btn_solid btn_primary fusion-button button-flat fusion-button-round button-large button-default button btn e389submit">'.__('Sent','e389').'</div>';
    return $form;
}

add_shortcode('e389', 'e389');

function e389_scripts()
{

 wp_enqueue_script( 'script-e389', plugins_url('',__FILE__) . '/js/e389.js', array('jquery'), '1.0.0', true );
 wp_localize_script( 'script-e389', 'e389Ajax', array(
    'ajaxurl' => admin_url( 'admin-ajax.php' ),
    'security' => wp_create_nonce( 'e389-special-string' )
  ));
}
add_action( 'wp_enqueue_scripts', 'e389_scripts' );


?>
