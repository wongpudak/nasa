<?php
//session_start();
add_action('activate_wp-affiliasi/wp-affiliasi.php', 'cb_install');
$options = get_option('cb_pengaturan');

if (!isset($options['lisensi'])) {
	add_action('admin_menu', 'cafebisnis_lisensi');
	if (isset($_GET['page']) && $_GET['page'] != 'lisensi_cb') {
		add_action('admin_notices', 'wpaff_blmaktif');
	}
} else {
	add_action('admin_head', 'cb_head');
	add_action('admin_menu', 'adminarea');
	add_action('admin_menu', 'konfigurasi');
	add_action('admin_menu', 'memberarea');
}

function wpaff_blmaktif() {
	echo '<div class="notice notice-warning is-dismissible">
    <p>WP Affiliasi belum aktif. Silahkan masuk menu <a href="admin.php?page=lisensi_cb">Lisensi</a></p>
	</div>';
}

function lisensi_cb() {	
	global $options;
	$options = get_option('cb_pengaturan');
	echo '<div class="wrap">';
	if (isset($_POST['username']) && isset($_POST['password'])) {
		$url = 'https://lisensi'.'.cafe'.'bisnis'.'.com/';
		$post = 'username='.urlencode($_POST['username']).'&password='.urlencode($_POST['password']).'&url='.site_url();
		$cek = postData($url,$_SERVER['HTTP_USER_AGENT'],$post);
		echo '<h2>Aktifasi Plugin WP-Affiliasi</h2>';
		if(strlen($cek) == 36) {
			$options['lisensi'] = $cek;			
			update_option('cb_pengaturan',$options);
			echo '<div class="notice notice-success is-dismissible">
			<p>Lisensi telah sukses didaftarkan. Silahkan memulai <a href="admin.php?page=pengaturan">pengaturan</a></p>
			</div>';
		} else {
			echo '<p>Ada kesalahan saat menghubungi Cafebisnis</p><p>'.$cek.'</p>';
		}
	} else {
	echo '
	<div class="form-wrap">
	<h2>Aktifasi Plugin WP-Affiliasi</h2>
	<p>Untuk mengaktifkan plugin ini, silahkan login menggunakan username dan password di Cafebisnis.com. <br/>Pastikan web ini benar-benar akan anda pakai, karena lisensi yang telah dipakai <strong>tidak dapat dihapus ataupun dipindahkan</strong> ke website lain.</p>
	<form action="" method="post">
	<div class="form-field form-required term-name-wrap">
		<label for="tag-name">Username:</label>
		<input type="text" style="width:100%; max-width:200px" name="username"/>
		<p>Username anda di Cafebisnis.</p>
	</div>
	<div class="form-field form-required term-name-wrap">
		<label for="tag-password">Password:</label>
		<input type="password" style="width:100%; max-width:200px" name="password"/>
		<p>Password anda di Cafebisnis.</p>
	</div>
	<input type="submit" value="Aktifkan Lisensi" class="button button-primary"/>
	</form>';
	}
	echo '</div>
	</div>';
}

function cafebisnis_lisensi() {
	global $options;
	add_menu_page('Lisensi', 'Lisensi', 'lisensi_cb', 'lisensi.php','',plugins_url('wp-affiliasi/admin_icon.png'));
	add_submenu_page('lisensi.php', 'Kode Lisensi', 'Lisensi', 'manage_options', 'lisensi_cb', 'lisensi_cb');
	}
?>