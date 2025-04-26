<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; }
$options = get_option('cb_pengaturan');
$konfemail = get_option('konfemail');
$blogurl = site_url();
$data = $wpdb->get_row("SELECT * FROM `cb_produklain`,`wp_member` 
	WHERE `cb_produklain`.`id`=".$id2up." 
	AND `cb_produklain`.`id_user`=`wp_member`.`id_user`");
if (!isset($data->membership)) {
	$data = $wpdb->get_row("SELECT * FROM `cb_produklain`,`wp_member` 
	WHERE `cb_produklain`.`id`=".$id2up." 
	AND `cb_produklain`.`idwp`=`wp_member`.`idwp`");
}

if (isset($data->status) && $data->status == 0) {
	// Pastikan punya idwp dulu
	if ($data->idwp == 0) {
		// Bikin user wordpress dulu
		$idwp = wp_create_user($data->username, $data->password, $data->email);
		if (is_wp_error($idwp)) {
		    $error = $idwp->get_error_message();
		    #echo "Gagal membuat pengguna: " . $error;
		}		
	} else {
		$idwp = $data->idwp;
	}

	if (isset($options['autopremium']) && $options['autopremium'] == 1) {
		$up = "`membership`=2, `tgl_upgrade`='".wp_date('Y-m-d H:i:s')."',";
	} elseif ($data->idproduk == 0) {
		$up = "`membership`=2, `tgl_upgrade`='".wp_date('Y-m-d H:i:s')."',";				
	} elseif ($data->idwp == 0) {
		$up = "`membership`=1,";
	} else {
		$up = '';
	}
	
	if (isset($idwp) && is_numeric($idwp)) {
		$passdb = md5($data->password);
		$wpdb->query("UPDATE `wp_member` SET ".$up." `password`='".$passdb."', `idwp`=".$idwp." 
				WHERE `id_user` = ".$data->id_user);
	} else {
		if (!isset($error)) {
			$error = 'Gagal mendapatkan data member';
		}
	}	

	// Proses order
	if (isset($idwp) && is_numeric($idwp)) {		
		$kredit = 0; # Nilai default jika opsi harga atau harga produk dikosongi oleh admin
		if ($data->idproduk == 0) {
			if (isset($options['harga']) && is_numeric($options['harga'])) {
				$kredit = $options['harga'];
			} 
			$namaproduk = 'Upgrade Premium';
		} else {
			$dataproduk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$data->idproduk);	
			if (isset($dataproduk->harga) && is_numeric($dataproduk->harga)) {
				$kredit = $dataproduk->harga;
			}
			$namaproduk = $dataproduk->nama;
		}

		$wpdb->query("UPDATE `cb_produklain` SET `idwp`=".$idwp.", `status`=1, `tgl_bayar`='".wp_date('Y-m-d H:i:s')."' WHERE `id`=".$id2up);

		$custom = unserialize($data->homepage);
		if (!isset($custom['uplines'])) {
			$custom['uplines'] = cbaff_uplines($data->id_referral);
			$customdb = serialize($custom);
			$wpdb->query("UPDATE `wp_member` SET `homepage`='".$customdb."' WHERE `idwp`=".$idwp);
		}
		$iduplines = $custom['uplines'];
		$uplines = $wpdb->get_results("SELECT * FROM `wp_member` WHERE `idwp` IN (".$iduplines.") ORDER BY FIELD(`idwp`,".$iduplines.")");
		$komisi = get_option('komisi');
		$i = 0;

		foreach ($uplines as $upline) {
			# Reset Jumlah Komisi
			$jmlkomisi = 0;
			if ($upline->idwp != 0) {
				# Tentukan ini upgrade atau beli produk lain
				if ($data->idproduk == 0) {
					# Handle Upgrade
					if ($upline->membership == 2) {
						# Atur komisi upgrade utk premium member
						if (isset($komisi['pps'][$i]['premium']) && $komisi['pps'][$i]['premium'] != '') {
							if (substr($komisi['pps'][$i]['premium'], -1) == '%') {
								$jmlkomisi = (str_replace('%','',$komisi['pps'][$i]['premium'])/100)*$kredit;
							} else {
								$jmlkomisi = $komisi['pps'][$i]['premium'];
							}
							//echo $jmlkomisi.' untuk '.$upline->nama.'<br/>';
							$i++;
						}
					} else {
						# Atur komisi upgrade utk free member
						if (isset($komisi['pps'][$i]['free']) && $komisi['pps'][$i]['free'] != '') {
							if (substr($komisi['pps'][$i]['free'], -1) == '%') {
								$jmlkomisi = (str_replace('%','',$komisi['pps'][$i]['free'])/100)*$kredit;
							} else {
								$jmlkomisi = $komisi['pps'][$i]['free'];
							}
							//echo $jmlkomisi.' untuk '.$upline->nama.'<br/>';
							$i++;
						}
					}
				} else {
					# Handle Produk Lain
					if ($upline->membership == 2) {
						# Atur komisi upgrade utk premium member
						if (isset($komisi['pps'][$i]['lainpremium']) && $komisi['pps'][$i]['lainpremium'] != '') {
							if (substr($komisi['pps'][$i]['lainpremium'], -1) == '%') {
								$jmlkomisi = (str_replace('%','',$komisi['pps'][$i]['lainpremium'])/100)*$kredit;
							} else {
								$jmlkomisi = $komisi['pps'][$i]['lainpremium'];
							}
							//echo $jmlkomisi.' untuk '.$upline->nama.'<br/>';
							$i++;
						}
					} else {
						# Atur komisi upgrade utk free member
						if (isset($komisi['pps'][$i]['lainfree']) && $komisi['pps'][$i]['lainfree'] != '') {
							if (substr($komisi['pps'][$i]['lainfree'], -1) == '%') {
								$jmlkomisi = (str_replace('%','',$komisi['pps'][$i]['lainfree'])/100)*$kredit;
							} else {
								$jmlkomisi = $komisi['pps'][$i]['lainfree'];
							}
							//echo $jmlkomisi.' untuk '.$upline->nama.'<br/>';
							$i++;
						}
					}
				}

				# Masukkan Database

				$id_referral = $upline->idwp;

				if (isset($jmlkomisi) && $jmlkomisi > 0) {
					$wpdb->query("UPDATE `wp_member` SET `jml_downline`=`jml_downline`+1,`jml_voucher`=`jml_voucher`+".$jmlkomisi.", `sisa_voucher`=`sisa_voucher`+".$jmlkomisi." WHERE `idwp` = ".$id_referral);					
				
					$transaksi = 'Komisi Lvl '.$i.' Order: '.$namaproduk.' oleh: '.$data->nama;						
					$wpdb->query("INSERT INTO `cb_laporan` (`tanggal`,`transaksi`,`kredit`,`komisi`,`keterangan`,`id_user`,`id_sponsor`,`id_order`) VALUES ('".wp_date('Y-m-d H:i:s')."','".esc_sql($transaksi)."',".$kredit.",".$jmlkomisi.",'cbaff',".$idwp.",".$id_referral.",".$id2up.")");
				}
			}
		}

		if ($data->idproduk == 0) {
			cb_notif($data->id_user,'upgrade');
		} else {			
			$datalain = array(
				'produk_orderid' =>	$id2up,
				'produk_nama' => $dataproduk->nama,
				'produk_diskripsi' => $dataproduk->diskripsi,
				'produk_harga' => number_format($dataproduk->harga),
				'produk_hargaunik' => number_format($dataproduk->harga)
			);
			cb_notif($data->id_user,'prosesbeli',$datalain);
		}

		// Persilahkan tambahkan action di sini
		do_action('wpaff_aktivasi',$id2up);
		
		$sukses = 'Order telah selesai diproses!';
		$sukses = apply_filters('cbaff_aktifasi_sukses',$sukses,$idwp);

		$konfautoresponder = get_option('konfautoresponder');
		// Selesai, sekarang kirim ke autoresponder	
		if (isset($konfautoresponder['premium']['action']) && $konfautoresponder['premium']['action'] != '') {
			// Jika autoresponder dipasang, maka kirim data ke autoresponder menggunakan postData
			$url = $konfautoresponder['premium']['action'];			
			foreach ($konfautoresponder['premium'] as $key => $value) {
				if (is_numeric($key) && $value['field'] != '') {
					$post[$value['field']] = $value['value'];
				}
			}

			$pesan = json_encode($post);
			$pesan = formatdata($data->id_user,$pesan);
			$pesan = json_decode($pesan,true);

			postData($url, $_SERVER['HTTP_USER_AGENT'], $pesan);
		} 
	}
} else {
	$error = 'Order sudah diproses sebelumnya';
}

if (isset($error) && $error != '') {
	echo '<div id="message2" class="notice notice-warning is-dismissible"><p>'.$error.'</p></div>';
} else {
	echo '<div id="message2" class="notice notice-success is-dismissible"><p>'.$sukses.'</p></div>';
}
?>