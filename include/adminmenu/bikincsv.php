<?php
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
} else {
	die('Could not load WordPress');
}

if (!current_user_can('manage_options')) { die; }

$user = $wpdb->get_results("SELECT * FROM `wp_member` ORDER BY `id_user`",ARRAY_N);
$head = array('id_user','idwp','id_referral','id_tianshi','ktp','nama','tgl_lahir','alamat','kota','provinsi','kodepos','telp','ktp_istri','nama_istri','tgl_lahir_istri','tgl_daftar','tgl_upgrade','downline_lngsg','jml_downline','jml_voucher','sisa_voucher','ac','bank','rekening','kelamin','username','password','email','subdomain','judulhome','homepage','read','membership','val','ip','lastupdate','whatsapp','uplines','pic_profil');


header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="member.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');			

$fp = fopen('php://output', 'wb');
fputcsv($fp, $head, ';');

foreach ($user as $user) {
	$custom = unserialize($user[30]);
	if (is_array($custom)) {
		ksort($custom);
		if (isset($custom['whatsapp'])) { 
			$user[36] = formatwa($custom['whatsapp']); 
			unset($custom['whatsapp']);
		} else { 
			$user[36] = ''; 
		}
		if (isset($custom['uplines'])) { 
			$user[37] = $custom['uplines'];
			unset($custom['uplines']); 
		} else { 
			$user[37] = ''; 
		}
		if (isset($custom['pic_profil'])) { 
			$user[38] = $custom['pic_profil']; 
			unset($custom['pic_profil']);
		} else { 
			$user[38] = ''; 
		}
		$line = array_merge($user,$custom);
	} else {
		$line = $user;
	}
	fputcsv($fp, $line, ';');
}
fclose($fp);
exit();
?>