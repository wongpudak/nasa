<?php /*
Bismillahirrahmaanirrahiim
Alhamdulillahirobbil 'alamiin

Plugin Name: WP Affiliasi Multi Level
Plugin URI: https://cafebisnis.com/produk/wp-affiliasi
Description: Mengubah WordPress biasa menjadi sebuah web affiliasi dengan sistem multi level yang bekerja secara otomatis. Mampu bekerja bersama plugin Woocommerce sehingga anda bisa membangun sebuah toko online dengan system affiliasi.
Version: 3.6.2
Author: Lutvi Avandi
Author URI: https://LutviAvandi.com/
*/
require 'include/update/plugin-update-checker.php';
$options = get_option('cb_pengaturan');
if (isset($options['lisensi'])) {
	$MyUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
	    'https://cafebisnis.com/update.php?id='.$options['lisensi'],
	    __FILE__,
	    'wp-affiliasi'
	);
}
define('IS_IN_SCRIPT',1);
include_once('cb_fungsi.php');
?>