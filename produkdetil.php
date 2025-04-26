<?php 
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
} else {
	die('Could not load WordPress');
}

if ($user_ID && is_numeric($_GET['id'])) {
	$idproduk = $_GET['id'];
	$member = $wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`='$user_ID'");
	
	//Ambil data produk
	$produk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id` = '$idproduk'");
	$membership = $produk->membership;
	
	echo'
	<h1>'.$produk->nama.'</h1>
	<p>';
	if ($produk->thumb_file) { echo '<img style="float: left; margin-right: 5px;" src="'.$produk->thumb_file.'" alt="'.$produk->nama.'" />'; }
 	echo $produk->diskripsi.'</p>';
	
	// Jika sesuai dengan keanggotaannya, munculkan tombol download
	if ($member >= $membership) {
		if ($produk->password) {
		echo '<p><b>Password: </b>'.$produk->password.'</p>';
		}
		echo '
		<p><a href="?download='.$idproduk.'"><img src="'.home_url().'/wp-content/plugins/wp-affiliasi/download.gif" alt="Download" border="0"></a></p>';
	} else {
		if ($membership == 3) {
			// Cek apakah dia sudah beli produk ini
			$cek = $wpdb->get_var("SELECT `status` FROM `cb_produklain` WHERE `id`=$idproduk AND `idwp`=$userID");
			if ($cek == 1) {
				echo '
		<p><a href="?download='.$idproduk.'"><img src="'.home_url().'/wp-content/plugins/wp-affiliasi/download.gif" alt="Download" border="0"></a></p>';
			} else {
				$options = get_option('cb_pengaturan');
				echo 'Produk ini dijual terpisah. Silahkan <a href="'.site_url().'/?page_id='.$options['order'].'&orderproduk='.$idproduk.'">Order disini</a>';
			}
		} else {		
		echo 'Produk Khusus Premium Member. <a href="'.site_url().'/?page_id='.$options['order'].'">Upgrade</a>';
		}
	}
	
} elseif ($user_ID && $_GET['download']) {
	$hash = strip_tags(substr($_GET['download'],0,32));
	$download = $wpdb->get_var("SELECT `id_produk` FROM `cb_download` WHERE `hash`='$hash' AND `id_user`='$user_ID'");
	$url = $wpdb->get_var("SELECT `url_file` FROM `cb_produk` WHERE `id`='$download'");
	// Hitung berapa kali dia download
	$wpdb->query("UPDATE `cb_download` SET `count`=`count`+1 WHERE `hash`='$hash'");
	if (substr($url,0,7)=='http://') {
		header("Location:".$url);
	} elseif ($url) {
		$newfile = $user_ID.$url;
		include('downloader.php');
		//header("Location:downloader.php?f=".$url."&fc=".$user_ID.$url);
	} else {
		$header = 'From: Plugin WP Affiliasi<wp_affiliasi@cafebisnis.com>';
		$body = 'Seorang member melakukan sharing link di '.$_SERVER['HTTP_REFERER']. ' HASH: '.$hash;
		@wp_mail('admin@cafebisnis.com', 'Laporan Pelanggaran', $body, $header);
	}
}
?>