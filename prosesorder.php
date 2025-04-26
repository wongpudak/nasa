<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (isset($_GET['idorder'])) {
$order = $wpdb->get_row("SELECT * FROM `cb_produklain` 
	LEFT JOIN `wp_member` ON `cb_produklain`.`id_user` = `wp_member`.`id_user` 
	WHERE `cb_produklain`.`id`=".$_GET['idorder']);

$datalain = unserialize($order->homepage);

if (isset($order->status)) {
	if ($order->status == 0) {
		if ($order->idproduk == 0) {
			$namaproduk = 'Upgrade Premium Member';
			$diskripsiproduk = '';
			if (isset($options['harga'])) {
				$harga = $options['harga'];
			} else {
				$harga = 0;
			}
		} else {
			$produk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$order->idproduk);
			$namaproduk = $produk->nama;
			$diskripsiproduk = $produk->diskripsi;
			$harga = $produk->harga;
		}

		if (isset($namaproduk) && $namaproduk != '') {			
			$idorder = $_GET['idorder']; // Masukkan Variabel biar gak usah ngetik GET berkali-kali :D
			$panjang = strlen($idorder);
			if ($panjang >= 3) {
				$angka = substr($idorder,-3); 
			} else {
				$angka = $idorder;
			}
			
			switch ($options['angkaunik']) {
				case 2:
					$hargaunik = $harga + $angka;
					break;
				case 1:
					$hargaunik = ($harga - 1000) + $angka;
					break;				
				default:
					$hargaunik = $harga;
					break;
			}
			

			if (isset($_GET['bank']) && $_GET['bank'] != '') {
				# Proses Order
				$konten = '';
				$duitkucode = array('VC','BK','M1','BT','A1','B1','I1','VA','FT','OV','DN','SP','SA','AG','S1');
				if ($_GET['bank'] == 'bca') {
					$data2 = getBCA();
					$trx_id = $options['key_trx'].$idorder.'XX';
					foreach ($data2 as $data) {
						if (isset($data['ket'])) {
							if (stristr($data['ket'],$trx_id)) {
								if ((int)str_replace(',','',$data['mutasi']) >= $hargaunik) { 
									$ok = 'Ditemukan dan siap diaktifkan'; 
								} else {
									$kurang = '<p>Pembayaran yang anda lakukan kurang</p>'; 
								}
							}
						}			
					}
					
					if (isset($ok)) {
						aktivasi($idorder,'BCA');
					} else {
						$konten .= '
						<h2>Upgrade Gagal</h2>
						<p>Transaksi tidak ditemukan. Apakah anda sudah transfer dan menyertakan kode <strong>'.$trx_id.'</strong> di kolom berita 
						saat transfer? Jika belum, silahkan hubungi admin untuk aktifasi secara manual</p>';
						if (isset($kurang)) { $konten .= $kurang; }
					}

				} elseif ($_GET['bank'] == 'paypal') {
					$harga = number_format($harga/$options['pp_price']);		
					include('paypal.php');
				} elseif (in_array($_GET['bank'], $duitkucode)) {
					$metode = $_GET['bank'];
					$datamember = $wpdb->get_row("SELECT * FROM `cb_produklain`,`wp_member` WHERE `cb_produklain`.`id_user` = `wp_member`.`id_user` AND `cb_produklain`.`id` = ".$idorder);
					include('duitku.php');
				}
				# Proses Order Done
			} else {			
				# Tampilkan Petunjuk Order
				$konten = '
				<h2>Pembayaran Order</h2>
				<table>
				<tr><td>Nomor Order</td><td>: 
				<strong>'; 

				if (isset($options['key_trx'])) {
					$konten .= $options['key_trx'].$idorder.'XX';
				} else {
					$konten .= 'ORDER'.$idorder.'XX';
				}

				$konten .= '</strong></td></tr>
				<tr><td>Nama Produk</td><td>: '.$namaproduk.'</td></tr>
				<tr><td>Harga</td><td>: '.$options['matauang'].' '.number_format($hargaunik).'</td></tr>
				</table>';

				# Siapkan Metode Pembayaran
				$showtxt = '';
				if (isset($options['banklain']) && $options['banklain'] != '') { 
					$showtxt .= '<option value="lainnya">Transfer Manual</option>'; 
				}

				if (isset($options['pp_email']) && $options['pp_email'] != '') { 
					$showtxt .= '<option value="paypal">PayPal</option>'; 
				}

				if (isset($options['duitku']['merchant']) && $options['duitku']['merchant'] != '') { 
					$duitpay = array(
						'VC' => 'Credit Card (Visa / Master)',
						'BK' => 'BCA KlikPay',
						'M1' => 'Mandiri Virtual Account',
						'BT' => 'Permata Bank Virtual Account',
						'A1' => 'ATM Bersama',
						'B1' => 'CIMB Niaga Virtual Account',
						'I1' => 'BNI Virtual Account',
						'VA' => 'Maybank Virtual Account',
						'FT' => 'Ritel',
						'OV' => 'OVO',
						'DN' => 'Indodana Paylater',
						'SP' => 'Shopee Pay',
						'SA' => 'Shopee Pay Apps',
						'AG' => 'Bank Artha Graha',
						'S1' => 'Bank Sahabat Sampoerna');

					foreach ($duitpay as $key => $value) {
						if (isset($options['duitku']['payment'][$key])) { 
							$showtxt .= '<option value="'.$key.'">'.$value.'</option>'; 
						}
					}
				}				

				if ($showtxt != '') {
					$konten .= '
				<p><select id="payment">
					<option>Pilih Metode Pembayaran</option>'.$showtxt.'</select></p>';
				} else {
					$konten .= 'Maaf, Metode pembayaran belum ditentukan oleh admin';
				}


				$konten .= '
				<div id="text"></div>';

				if (isset($options['duitku']['payment']) && isset($duitpay)) {
					foreach ($options['duitku']['payment'] as $key => $value) {
						$konten .= '<div id="text'.$key.'">
						<p><a href="?page=order&idorder='.$idorder.'&bank='.$key.'" class="tombolhijau">Klik untuk Membayar dg '.$duitpay[$key].'</a></p>
					</div>';
					}
				}

				if (isset($options['pp_email']) && $options['pp_email'] != '') { 
					$konten .= '
					<div id="textpaypal">
					<h3>Instruksi cara Pembayaran di PayPal</h3>
					<p>Harga Produk dalam dollar: $'.number_format($harga/$options['pp_price']).'</p>
					<p><a href="?page=order&idorder='.$idorder.'&bank=paypal" class="tombolhijau">Klik Disini untuk Membayar dengan PayPal</a></p>
					</div>';
				}

				if (isset($options['banklain']) && $options['banklain'] != '') { 
					$konten .= '
					<div id="textlainnya">
					<h3>Cara Pembayaran Transfer Manual</h3>';

					$gantiharga = $options['matauang'].' '.number_format($hargaunik);
					$manual = str_replace('[hargaunik]',$gantiharga,$options['banklain']);
					$konten .= nl2br(stripslashes($manual));
					$konten .= '
					</div>';
				}

				$konten .= '
				<script>
					var $j = jQuery.noConflict();
					$j(function(){
						$j("#textbca").hide();';

				if (isset($options['duitku']['payment'])) {	
					foreach ($options['duitku']['payment'] as $key => $value) {
						$konten .= '$j("#text'.$key.'").hide();';
					}
				}
			
				$konten .= '
						$j("#textpaypal").hide();
						$j("#textlainnya").hide();
					    $j("#payment").change(function () {
							$j("#textbca").hide();';

				if (isset($options['duitku']['payment'])) {				
					foreach ($options['duitku']['payment'] as $key => $value) {
						$konten .= '$j("#text'.$key.'").hide();';
					}
				}

				$konten .= '
							$j("#textpaypal").hide();
							$j("#textlainnya").hide();
							var text = $j("#payment").val();
							$j("#text"+text).show();
					    });
					});
				</script>';
			} // Tampilkan Petunjuk Order Done
		} 

	} else {
		$konten = 'Terima kasih, Order telah dilunasi';
	}
} else {
	$konten = 'Order tidak ditemukan';
}
}
