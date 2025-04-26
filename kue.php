<?php
session_start();
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
    require_once $wp_load;
} else {
    die('Could not load WordPress');
}
if (isset($_GET['reg'])) {
    setcookie('subdomain',$_GET['reg'],time()+(3600*24*30));
}
?>