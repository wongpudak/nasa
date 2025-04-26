<?php 
if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
if (!current_user_can('manage_options')) { die; exit(); } 

echo '<div class="wrap">
<h1 class="wp-heading-inline">Bayar Komisi</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=33" target="_blank" role="button" aria-expanded="false">Bantuan</a>';
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
	$data = $wpdb->get_results("SELECT * FROM `cb_laporan` WHERE `id_sponsor`=".$_GET['id']." ORDER BY `tanggal`");
	if (is_array($data) && count($data) > 0) {
		$total = $totalkredit = $totaldebet = 0;
		echo '
		<table class="table">
		<thead class="thead thead-dark">
			<tr><th>Tanggal</th>
			<th>Transaksi</th>
			<th class="text-right">Debet</th>
			<th class="text-right">Kredit</th>
			<th class="text-right">Saldo</th>
			</tr>
		</thead>
		<tbody>';
		foreach ($data as $data) {			
			if ($data->keterangan != 'trx') {
				echo '<tr><td>'.$data->tanggal.'</td>
				<td>'.$data->transaksi.'</td>';
				if ($data->keterangan == 'refund') {
					echo '
					<td class="text-right">'.number_format(($data->komisi)*-1).'</td>
					<td class="text-right">0</td>';
					$total = $total+($data->komisi);
				} else {
					echo '
					<td class="text-right">'.number_format($data->debet).'</td>
					<td class="text-right">'.number_format($data->komisi).'</td>';
					$total = $total+($data->komisi-$data->debet);
				}
				$totalkredit = $totalkredit + $data->komisi;
				$totaldebet = $totaldebet + $data->debet;
				echo '
				<td class="text-right">'.number_format($total).'</td>
				</tr>';
			}
		}
		echo '
		<tr>
		<td colspan="2" class="text-right"><strong>TOTAL</strong></td>
		<td class="text-right">'.number_format($totaldebet).'</td>
		<td class="text-right">'.number_format($totalkredit).'</td>
		<td>&nbsp;</td></tr>
		</tbody>
		</table>';
	}
} else {
	if (isset($_POST['bayar']) && is_numeric($_POST['bayar'])) {
		$wpdb->query("UPDATE `wp_member` SET `sisa_voucher`=`sisa_voucher`-".$_POST['bayar']." WHERE `idwp`=".$_POST['iduser']);
		
		// Ambil data member	
		$checkupline = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp` = ".$_POST['iduser']);
		
		// Masukkan laporan keuangan 
		$id_order = time();
		$transaksi = 'Pembayaran Komisi '.addslashes($checkupline->nama);
		$wpdb->query("INSERT INTO `cb_laporan` 
					(`tanggal`,`transaksi`,`debet`,`kredit`,`komisi`,`keterangan`,`id_user`,`id_sponsor`,`id_order`)
					VALUES  ('".wp_date('Y-m-d H:i:s')."','".$transaksi."',".$_POST['bayar'].",0,0,'wd',0,".$_POST['iduser'].",".$id_order.")");
		
		$datalain = array(
			'komisi' => $_POST['bayar']
		);

		cb_notif($checkupline->id_user,'komisi',$datalain);

		echo '<div id="message2" class="notice notice-success is-dismissible"><p><b>'.$checkupline->nama.'</b> Sudah Dibayar</p></div>';
		$showtxt = '';
		$showtxt = apply_filters('cbaff_bayar_sukses',$showtxt,$checkupline->id_user);
	}

	$options = get_option("cb_pengaturan");
	$limit = $options['limit'];
	if ($limit == '') { $limit = 1; }
	$laporan = $wpdb->get_results($wpdb->prepare("
	    SELECT id_sponsor, 
	           (SUM(komisi) - SUM(debet)) AS belumdibayar
	    FROM cb_laporan
	    WHERE keterangan IN ('cbaff', 'ppl', 'woo', 'wd', 'refund')
	    GROUP BY id_sponsor
	    HAVING belumdibayar > %d
	    ORDER BY belumdibayar DESC
	    LIMIT 100
	", $limit), ARRAY_A);
		
	// Ambil hanya kolom id_sponsor
	$id_sponsor_list = array_column($laporan, 'id_sponsor');

	// Jika tidak ada sponsor yang memenuhi syarat, hentikan
	if (empty($id_sponsor_list)) {
	    return [];
	}

	// Convert array ke format SQL IN clause
	$id_sponsor_placeholders = implode(',', array_fill(0, count($id_sponsor_list), '%d'));

	// Query untuk mengambil data member berdasarkan id_sponsor
	$sponsors = $wpdb->get_results($wpdb->prepare("
	    SELECT idwp, nama, email, username, ac, bank, rekening
	    FROM wp_member
	    WHERE idwp IN ($id_sponsor_placeholders)
	", $id_sponsor_list), ARRAY_A);
		
	foreach ($sponsors as $sp) {
		$sponsorlist[$sp['idwp']] = $sp;
	}	
	
	/*
	$member = $wpdb->get_results("
    SELECT m.idwp, m.nama, m.username, m.ac, m.bank, m.rekening,
           (SUM(l.komisi) - SUM(l.debet)) AS belumdibayar
    FROM cb_laporan l
    INNER JOIN wp_member m ON m.idwp = l.id_sponsor
    WHERE l.keterangan IN ('cbaff', 'ppl', 'woo', 'wd', 'refund')
    GROUP BY l.id_sponsor, m.idwp, m.nama
    HAVING belumdibayar > 50000
    ORDER BY belumdibayar DESC
    LIMIT 100");
	*/
	echo'
		<table class="widefat">
		<thead>
		<tr>
			<th scope="col"  width="35%">Nama Lengkap</th>
			<th scope="col"  width="35%">Data Bank</th>
			<th scope="col"  width="30%">Jumlah Uang</th>
		</tr>
		</thead>
		<tbody>';
	if (isset($laporan) && count($laporan) > 0) {
		$totalbayar = 0;
		foreach ($laporan as $laporan) {
		echo '
		<tr>
			<td><a href="'.get_bloginfo('url').'/wp-admin/admin.php?page=cbaf_memberlist&profil='.$sponsorlist[$laporan['id_sponsor']]['idwp'].'">'.$sponsorlist[$laporan['id_sponsor']]['nama'].'</a> <br>
			('.$sponsorlist[$laporan['id_sponsor']]['username'].')</td>';
		if ($sponsorlist[$laporan['id_sponsor']]['rekening'] && $sponsorlist[$laporan['id_sponsor']]['bank']) {
		echo '
			<td><b>'.$sponsorlist[$laporan['id_sponsor']]['bank'].'</b><br>a/n. '.$sponsorlist[$laporan['id_sponsor']]['ac'].'<br>'.$sponsorlist[$laporan['id_sponsor']]['rekening'].'</td>';
		} else {
		echo '
			<td>Belum mengisi rekening</td>';
		}
		echo'
			<td>
			<form action="" method="post">
			Sisa : <a href="admin.php?page=cbaf_bayar&id='.$sponsorlist[$laporan['id_sponsor']]['idwp'].'">'.number_format($laporan['belumdibayar']).'</a><br>
			Bayar : <input type="text" name="bayar" size="5" value="'.$laporan['belumdibayar'].'">
			<input type="hidden" name="iduser" value="'.$sponsorlist[$laporan['id_sponsor']]['idwp'].'">
			<input type="submit" class="button button-primary" value="Go">
			</form>
			</td>
		</tr>';	
		$totalbayar = $totalbayar+$laporan['belumdibayar'];
		}
	} else {
		echo '<tr><td colspan="3" align="center">Belum ada member yang dibayar</td></tr>';
	}
	echo '
		</tbody>
		</table>
		<p align="center">TOTAL PEMBAYARAN : <b>Rp. '.number_format($totalbayar).',-</b></p>
		<p>&nbsp;</p>';
	
}
echo '</div>'; //End Wrap
?>