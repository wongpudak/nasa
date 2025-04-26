<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
global $wpdb, $user_ID, $member;
global $subdomain, $nama, $username, $password, $urlreseller;
global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
global $namaprospek, $usernameprospek, $bayar, $namamember;
global $val, $blogurl, $options;
$konten = '';
$options = get_option('cb_pengaturan');
if (!isset($options['matauang']) || $options['matauang'] == '') {
	$options['matauang'] = 'Rp.';
}
if (!isset($options['angkaunik']) || $options['angkaunik'] == '') {
	$options['angkaunik'] = 0;
}

#Cek apakah memproses order atau membuat order baru
if (isset($_GET['idorder']) && is_numeric($_GET['idorder'])) {
	# Proses Order tidak perlu login
	include('prosesorder.php');
} else {
	# Buat Order Baru	
	# Cek apakah ini order produk lain atau upgrade
	if (isset($_GET['orderproduk']) && $_GET['orderproduk'] > 0) {
		# Order produk lain, dapatkan data produknya
		$produk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$_GET['orderproduk']);
		if (isset($produk->id)) {
			$idproduk = $produk->id;
			$hargaproduk = $produk->harga;
		} 	
	} else {		
		if (!isset($_GET['post']) && !isset($_GET['elementor-preview'])) {
			$idproduk = 0;
			$hargaproduk = $options['harga'];	
		}	
	}

	if (isset($idproduk)) {
		# Jika member login, cek apakah sudah membuat order atau belum, jika sudah, lempar ke idorder
		if (isset($user_ID) && $user_ID > 0) {
			$datauser = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$user_ID);
			$order = $wpdb->get_row("SELECT * FROM `cb_produklain` WHERE `id_user`=".$datauser->id_user." AND `idproduk`=".$idproduk);
			if (isset($order->id) && $order->id > 0) {
				# Redirect ke idorder
				header("Location:".site_url()."/?page_id=".get_the_ID()."&idorder=".$order->id);
				exit;
			} else {
				# Buat ordernya lalu kirim ke idorder
				$wpdb->query("INSERT INTO `cb_produklain` (`idwp`,`id_user`,`idproduk`,`status`,`tgl_order`,`hargaproduk`)
					VALUES (".$user_ID.",".$datauser->id_user.",".$idproduk.",0,'".wp_date('Y-m-d H:i:s')."',".$hargaproduk.")");
				$idorder = $wpdb->insert_id;

				if ($idproduk > 0) {
					# Kirim Notif Beli Produk Lain
					if (strlen($idorder) > 3) {
						$angka = substr($idorder,-3);
					} else {
						$angka = $idorder;
					}

					switch ($options['angkaunik']) {
						case 2:
							$hargaunik = $hargaproduk + $angka;
							break;
						case 1:
							$hargaunik = ($hargaproduk - 1000) + $angka;
							break;				
						default:
							$hargaunik = $hargaproduk;
							break;
					}

					$datalain = array(
						'produk_orderid' =>	$idorder,
						'produk_nama' => $produk->nama,
						'produk_diskripsi' => $produk->diskripsi,
						'produk_harga' => number_format($produk->harga),
						'produk_hargaunik' => number_format($hargaunik)
					);

					cb_notif($datauser->id_user,'beli',$datalain);
					$konten .= apply_filters('cbaff_beli_sukses',$konten,$datauser->id_user);
				}
				header("Location:".site_url()."/?page_id=".get_the_ID()."&idorder=".$idorder);
				exit;
			}
		} else {
			# Lakukan login dan kembali ke sini untuk cek idorder lalu redirect ke idorder
			# atau registrasi dan bikin order lalu langsung ke idorder
			$konten = cb_loginreg($idproduk);
			//$konten = 'Halaman Login';
		}
	} else {
		$konten = 'Produk tidak ditemukan';
	}
}
?>