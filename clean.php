<?php
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
} else {
	die('Could not load WordPress');
}
$kemarin  = date("Y-m-d",(time()-86400)).' 00:00:00';
$wpdb->query("DELETE FROM `wp_member` WHERE `membership`=0 AND `tgl_daftar` <= '".$kemarin."' ORDER BY `tgl_daftar`");
?>