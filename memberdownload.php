<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!isset($user_ID) || $user_ID == 0) { 
	$refer = $_SERVER['REQUEST_URI'];
	header("Location: ".wp_login_url($refer));
}
if (isset($_GET['hal']) && $_GET['hal'] == 'download') {
	$showtxt .= '<h2>Download Area</h2>';
}
$options = get_option('cb_pengaturan');
$showtxt .= '<p>Klik folder dan nama file yang ingin anda download. Informasi dan link download akan muncul di kolom sebelah kanan</p>';

$folder = '';
$file = '';
$produks = $wpdb->get_results("SELECT * FROM `cb_produk_cat` ORDER BY `id_cat` ASC");
foreach ($produks as $produk) {			
	$folder .= '
	d.add('.$produk->id_cat.','.$produk->id_parent.',\''.str_replace("'","",$produk->name).'\',\'\',\'\',\'\');';	
}
$produks = $wpdb->get_results("SELECT * FROM `cb_produk` ORDER BY `id` ASC");
$target= site_url().'/?page_id='.get_the_ID().'&hal=download&id=';
if ($produks) {
foreach ($produks as $produk) {			
	$file .= '
	d.add('.(1000+($produk->id)).','.$produk->id_cat.',\''.str_replace("'","",$produk->nama).'\',\''.$target.$produk->id.'\',\'\',\'\');';	
}
}

$showtxt .= '<div style="float:right; width:100%; max-width:300px">';
if ($user_ID && isset($_GET['id']) && is_numeric($_GET['id'])) {
	$idproduk = $_GET['id'];
	$member = $wpdb->get_var("SELECT `membership` FROM `wp_member` WHERE `idwp`='$user_ID'");
	
	//Ambil data produk
	$produk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id` = '$idproduk'");
	$membership = $produk->membership;
	$showtxt .='
	<h3>'.$produk->nama.'</h3>
	<p>';
	if ($produk->thumb_file) { $showtxt .= '<img style="float: left; margin-right: 5px;" src="'.$produk->thumb_file.'" alt="'.$produk->nama.'" />'; }
 	$showtxt .= $produk->diskripsi.'</p>';
	
	if (isset($produk->harga) && $membership == 3) {
		$showtxt .= '<p>Harga : '.$options['matauang'].' '.number_format($produk->harga).'</p>';
	}
	
	// Jika sesuai dengan keanggotaannya, munculkan tombol download
	if ($member >= $membership) {
		if (isset($produk->password) && $produk->password != '') {
			$showtxt .= '<p><b>Password: </b>'.$produk->password.'</p>';
		}
		$showtxt .= '
		<p><a href="'.esc_url( add_query_arg( 'download_id', $idproduk, home_url( '/' ) ) ).'">
		    <img src="'.esc_url( plugins_url('wp-affiliasi/download.gif') ).'" alt="Download" style="box-shadow:none;"/>
		</a></p>';
	} else {
		if ($membership == 3) {
			// Cek apakah dia sudah bayar produk ini
			$cek = $wpdb->get_var("SELECT `status` FROM `cb_produklain` WHERE `idproduk`=$idproduk AND `idwp`=$user_ID");
			if ($cek == 1) {
				if (isset($produk->password) && $produk->password != '') {
					$showtxt .= '<p><b>Password: </b>'.$produk->password.'</p>';
				}
				$showtxt .= '				
				<p><a href="'.esc_url( add_query_arg( 'download_id', $idproduk, home_url( '/' ) ) ).'">
				    <img src="'.esc_url( plugins_url('wp-affiliasi/download.gif') ).'" alt="Download" style="box-shadow:none;"/>
				</a></p>';
			} else {
				$showtxt .= 'Produk ini dijual terpisah. Silahkan <a href="'.site_url().'/?page_id='.$options['order'].'&orderproduk='.$idproduk.'">Order disini</a>';
			}
		} else {		
		$showtxt .= 'Produk Khusus Premium Member. <a href="'.site_url().'/?page_id='.$options['order'].'&orderproduk=premium">Upgrade</a>';
		}
	}
	
}
$showtxt .= '</div>
<div class="dtree">
<p><a href="javascript: d.openAll();">open all</a> | <a href="javascript: d.closeAll();">close all</a></p>

<script type="text/javascript">
	<!--
	d = new dTree(\'d\');
	d.add(0,-1,\'Download\');';
	$showtxt .= $folder;
	$showtxt .= $file;	
	$showtxt .= 'document.write(d);
	//-->
</script>
</div>';