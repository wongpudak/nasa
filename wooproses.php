<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; }
$order = wc_get_order($order_id);
$kredit = $order->get_total() - $order->get_total_shipping();
$id_user = $order->get_customer_id();
$id_sponsor = get_post_meta($order_id,'Sponsor ID',true);
if ($id_user > 0) {
	$status = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$id_user);
} else {
	$status = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `email`= '".$order->get_billing_email()."'");
}

if (!isset($status->id_user)) {
	# Buatkan akun wp_member
	
	$nama = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
	$email =  $order->get_billing_email();
	$pos = stripos($email,'@');
	$username = substr($email, 0, $pos);
	$password = gettimeofday(true);
	$password = substr(MD5($password),0,10);
	$subdomain	= txtonly(strtolower($subdomain));
	$alamat = $order->get_billing_address_1().' '.$order->get_billing_address_2();
	$provinsi = $order->get_billing_state();
	$kota = $order->get_billing_city();
	$telp = $lainlain['whatsapp'] = $order->get_billing_phone();
	$homepage = serialize($lainlain);

	$suffix = 1;
	$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$username'");
	if ( isset($username_exist) || username_exists( $username )) {
		if (isset($_POST['username']) && $_POST['username'] != '') {
			$txterror .= 'Maaf, username yang anda pilih sudah ada yang memakai. Silahkan pilih username lain.<br/>';
		} else {
			while (isset($username_exist) || username_exists( $usernow )) {
				$suffix++;
				$usernow = $username.$suffix;
				$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$usernow'");			
			}
			$username = $usernow;
		}
	}
	
	$suffix = 1;
	$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subdomain'");
	if (isset($subdomain_exist)) {
		if (isset($_POST['subdomain']) && $_POST['subdomain'] != '') {
			$txterror .= 'Maaf, kode affiliasi yang anda pilih sudah ada yang memakai. Silahkan pilih kode affiliasi lain.<br/>';
		} else {
			while (isset($subdomain_exist)) {
				$suffix++;
				$subnow = $subdomain.$suffix;
				$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subnow'");			
			}
			$subdomain = $subnow;
		}
	}

	$wpdb->query("INSERT INTO `wp_member` 
			   (`id_referral`,`nama`,`alamat`,`kota`,`provinsi`,`telp`,`tgl_daftar`,`username`,`password`,`email`,`subdomain`,`homepage`,`membership`) 
				VALUES (".$id_sponsor.",'".$nama."','".$alamat."','".$kota."','".$provinsi."','".$telp."','".wp_date('Y-m-d H:i:s')."','".$username."','".$password."','".$email."','".txtonly($username)."','".$homepage."',0)");

	$id_user = $wpdb->insert_id;
} else {
	$id_user = $status->id_user;
}

# Kirim Notif ke Upline langsung
$datalain = array(
	'produk_orderid' =>	$order_id,
	'produk_harga' => $kredit
);

cb_notif($id_user,'woo',$datalain);

# Auto Update Premium
$options = get_option('cb_pengaturan');
if (isset($options['autopremiumwoo']) && $options['autopremiumwoo'] == 1) {
	$wpdb->query("UPDATE `wp_member` SET `membership`=2 WHERE `id_user` = ".$id_user);
}

$custom = unserialize($status->homepage);
if (!isset($custom['uplines'])) {
	$custom['uplines'] = cbaff_uplines($id_sponsor);
	$customdb = serialize($custom);
	$wpdb->query("UPDATE `wp_member` SET `homepage`='$customdb' WHERE `idwp`=$id_sponsor");
}

if ($custom['uplines'] != 0) {
	$iduplines = $id_sponsor.','.$custom['uplines'];
	$uplines = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `idwp` IN ($iduplines) ORDER BY FIELD(`idwp`,$iduplines)");
} else {
	$uplines = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `idwp`=$id_sponsor");
}
$komisi = get_option('komisi');
$i = 0;

foreach ($uplines as $upline) {
	if ($upline->idwp != 0) {
		$jmlkomisi = 0;
	    $id_referral = '';
		if ($komisi['pps'][$i]['woofree']==0 && $upline->membership==1) {
			// Lewati
		} else {
			if ($upline->membership == 2) {
				if (isset($komisi['pps'][$i]['woopremium']) && $komisi['pps'][$i]['woopremium'] > 0) {
					$jmlkomisi = ($komisi['pps'][$i]['woopremium']/100)*$kredit;
				} else {
					$jmlkomisi = ($komisi['pps'][$i]['premium']/100)*$kredit;
				}
			} else {
				if (isset($komisi['pps'][$i]['woofree']) && $komisi['pps'][$i]['woofree'] > 0) {
					$jmlkomisi = ($komisi['pps'][$i]['woofree']/100)*$kredit;
				} else {
					$jmlkomisi = ($komisi['pps'][$i]['free']/100)*$kredit;
				}
			}

			$id_referral = $upline->idwp;

			if ($i==0) {
				$wpdb->query("UPDATE `wp_member` SET `downline_lngsg`=`downline_lngsg`+1,`jml_downline`=`jml_downline`+1,`jml_voucher`=`jml_voucher`+'$jmlkomisi', `sisa_voucher`=`sisa_voucher`+'$jmlkomisi' WHERE `idwp` = '$id_referral'");
				
			} else {
				$wpdb->query("UPDATE `wp_member` SET `jml_downline`=`jml_downline`+1,`jml_voucher`=`jml_voucher`+'$jmlkomisi', `sisa_voucher`=`sisa_voucher`+'$jmlkomisi' WHERE `idwp` = '$id_referral'");
			}

			if ($jmlkomisi > 0) {
				$transaksi = 'Penjualan Produk Order No. '.$order_id.' Level '.($i+1);
				$wpdb->query("INSERT INTO `cb_laporan` (`tanggal`,`transaksi`,`kredit`,`komisi`,`keterangan`,`id_user`,`id_sponsor`,`id_order`) VALUES (NOW(),'$transaksi','$kredit','$jmlkomisi','woo','$id_user','$id_referral','$order_id')");
			}
			$i++;
		}
	}
}
?>