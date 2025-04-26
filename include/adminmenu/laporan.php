<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } ?>
<?php if (!current_user_can('manage_options')) { die; exit(); } ?>
<?php
if (isset($_POST['transaksi'])) {
	$transaksi = esc_sql($_POST['transaksi']);
	$debet = $_POST['debet'];
	$kredit = $_POST['kredit'];
	$cek = $wpdb->query("INSERT INTO `cb_laporan` VALUES ('','".wp_date('Y-m-d H:i:s')."','".$transaksi."','".$debet."','".$kredit."',0,'trx',0,".$user_ID.",0)");
	if ($cek === false) {
		echo $wpdb->print_error;
	} else {
		echo '<div class="notice notice-success is-dismissible">
		<p>Update berhasil</p>
		</div>';
	}
}
$bulan = $tahun = '';
if (isset($_GET['bulan']) && is_numeric($_GET['bulan'])) { $bulan = $_GET['bulan']; } else { $bulan = date('n'); }
if (isset($_GET['tahun']) && is_numeric($_GET['tahun'])) { $tahun = $_GET['tahun']; } else { $tahun = date('Y'); }
$start = $tahun.'-'.$bulan.'-1 00:00:00';
$end = $tahun.'-'.$bulan.'-31 23:59:59';
$stringmonth = date("F", mktime(0, 0, 0, $bulan, 1, $tahun)); 
?>
<div class="wrap">
<h2>Laporan Keuangan</h2>
<form action="" method="get">
	<select name="bulan">
		<?php
		$bln = array('','Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember');
		for($i=1;$i<=12;$i++) {
			echo '<option value="'.$i.'"';
			if (isset($bulan) && $bulan==$i) { echo ' selected'; }
			echo '>'.$bln[$i].'</option>';
		}
		?>
	</select>
	<select name="tahun">
		<?php 
		$thn = date('Y') + 5;
		for($i=2010; $i<=$thn; $i++) { 
			echo '<option value="'.$i.'"';
			if (isset($tahun) && $tahun==$i) { echo ' selected'; }
			echo '>'.$i.'</option>'; 
		} 
		?>
	</select>
	<input type="hidden" name="page" value="cbaf_laporan"/>
	<input type="submit" class="button button-primary" value="Cek laporan"/>
</form>
<h3>Laporan Keuangan Bulan <?php echo $stringmonth.' '.$tahun;?> </h3>
<table class="widefat">
	<thead>
	<tr>
		<th scope="col" width="3%">No.</th>
		<th scope="col" width="17%">Tanggal</th>
		<th scope="col" width="50%">Transaksi</th>
		<th scope="col" width="10%">Debet</th>
		<th scope="col" width="10%">Kredit</th>
		<th scope="col" width="10%">Saldo</th>
	</tr>
	</thead>
	<tbody>
<?php
$laporan = $wpdb->get_results("SELECT * 
	FROM `cb_laporan`
	WHERE MONTH(`tanggal`) = ".$bulan." AND YEAR(`tanggal`) = " .$tahun."
	ORDER BY `tanggal`");
$hitung = 0;
$saldo = 0;
foreach ($laporan as $laporan) {		
	if ($laporan->keterangan == 'cbaff') {
		if (substr($laporan->transaksi, 0,12) == 'Komisi Lvl 1') {		
			$transaksi = str_replace('Komisi Lvl 1 Order: ', '', $laporan->transaksi);
			$transaksi .= ' (<a href="admin.php?page=cbaf_memberlist&profil='.$laporan->id_user.'" target="_blank" title="Lihat Profil Pembeli"><span class="dashicons dashicons-search"></span></a>)';
		} 
	} elseif ($laporan->keterangan == 'refund') {
		if (substr($laporan->transaksi, 0,19) == 'Cancel Komisi Lvl 1') {		
			$transaksi = str_replace('Cancel Komisi Lvl 1 Order: ', 'Cancel ', $laporan->transaksi);
			$transaksi .= ' (<a href="admin.php?page=cbaf_memberlist&profil='.$laporan->id_user.'" target="_blank" title="Lihat Profil Pembeli"><span class="dashicons dashicons-search"></span></a>)';
		} 	
	} elseif ($laporan->keterangan == 'woo') {
		if (substr($laporan->transaksi, -7) == 'Level 1') {
			$transaksi = 'Pembelian Produk via Woocommerce ID Order: <a href="post.php?post='.$laporan->id_order.'&action=edit">'.$laporan->id_order.'</a>';
		} 
	} elseif ($laporan->keterangan == 'wd') {
		$transaksi = $laporan->transaksi. ' (<a href="admin.php?page=cbaf_memberlist&profil='.$laporan->id_sponsor.'" target="_blank" title="Lihat Profil Pembeli"><span class="dashicons dashicons-search"></span></a>)';
	} else {
		$transaksi = $laporan->transaksi;
		if (isset($laporan->id_user) && $laporan->id_user > 0) {
			$transaksi .= ' (<a href="admin.php?page=cbaf_memberlist&profil='.$laporan->id_user.'" target="_blank" title="Lihat Profil Pembeli"><span class="dashicons dashicons-search"></span></a>)';
		}
	}
	
	if (isset($transaksi) && $transaksi != '') {
		$hitung++;
		$saldo = $saldo + $laporan->kredit - $laporan->debet;
		echo '<tr>
		<td>'.$hitung.'</td>
		<td>'.$laporan->tanggal.'</td>
		<td>'.$transaksi.'</td>
		<td align="right">'.number_format($laporan->debet).'</td>
		<td align="right">'.number_format($laporan->kredit).'</td>
		<td align="right">'.number_format($saldo).'</td>
		</tr>';
		$transaksi = '';
	}
}
?>
	</tbody>
</table>
<h2>Tambah Transaksi</h2>
<form action="" method="post">
	<table>
		<tr><td>Transaksi</td><td>: <input type="text" name="transaksi"/></td></tr>
		<tr><td>Debet</td><td>: <input type="text" name="debet"/></td></tr>
		<tr><td>Kredit</td><td>: <input type="text" name="kredit"/></td></tr>
		<tr><td colspan="2"><input type="submit" class="button button-primary" value="Tambah"/></td></tr>
	</table>
</form>
</div>