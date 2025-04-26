<?php
if (!defined('IS_IN_SCRIPT')) { die();  exit; }
?>
<div class="wrap">
<h1 class="wp-heading-inline">Daftar Order</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=16" target="_blank" role="button" aria-expanded="false">Bantuan</a>
<?php
$options = get_option('cb_pengaturan');
if (isset($_GET['id'])) {
	if (is_numeric($_GET['id']) && is_admin()) {
		aktivasi($_GET['id']);
	} 
} elseif (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
	$wpdb->query("DELETE FROM `cb_produklain` WHERE `id`=".$_GET['hapus']);
	echo '<div class="notice notice-success is-dismissible"><p>Order '.$_GET['hapus'].' telah dihapus</p></div>';
} elseif (isset($_GET['batal']) && is_numeric($_GET['batal'])) {
	# Batalkan Order dan Refund Dana
	$id2up = $_GET['batal'];
	$data = $wpdb->get_row("SELECT * FROM `cb_produklain`,`wp_member` 
		WHERE `cb_produklain`.`id`=".$id2up." 
		AND `cb_produklain`.`id_user`=`wp_member`.`id_user`");
	if (!isset($data->membership)) {
		$data = $wpdb->get_row("SELECT * FROM `cb_produklain`,`wp_member` 
		WHERE `cb_produklain`.`id`=".$id2up." 
		AND `cb_produklain`.`idwp`=`wp_member`.`idwp`");
	}

	if (isset($data->status) && $data->status == 1) {
		if ($data->idproduk == 0) {
			$kredit = $options['harga'];
			$namaproduk = 'Upgrade Premium';
			# Downgrade member jadi free lagi
			$wpdb->query("UPDATE `wp_member` SET `membership`=1 WHERE `idwp`=".$data->idwp."");
		} else {
			$dataproduk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$data->idproduk);	
			$kredit = $dataproduk->harga;
			$namaproduk = $dataproduk->nama;
		}

		$wpdb->query("UPDATE `cb_produklain` SET `status`=0 WHERE `id`=".$id2up);

		# Dapatkan data laporan keuangan sebelumnya
		$lapkeu = $wpdb->get_results("SELECT * FROM `cb_laporan` WHERE `id_order`=".$id2up);
		if (count($lapkeu) > 0) {
			$ins = '';
			foreach ($lapkeu as $lap) {
				$komisi = (-1)*$lap->komisi;
				$kredit = (-1)*$lap->kredit;
				$ins .= "('".wp_date('Y-m-d H:i:s')."','Cancel ".esc_sql($lap->transaksi)."',".$kredit.",".$komisi.",'refund',".$lap->id_user.",".$lap->id_sponsor.",".$id2up."),";
				$wpdb->query("UPDATE `wp_member` SET `jml_downline`=`jml_downline`-1,`jml_voucher`=`jml_voucher`-".$lap->komisi.", `sisa_voucher`=`sisa_voucher`-".$lap->komisi." WHERE `idwp` = ".$lap->id_sponsor);
			}

			$wpdb->query("INSERT INTO `cb_laporan` 
				(`tanggal`,`transaksi`,`kredit`,`komisi`,`keterangan`,`id_user`,`id_sponsor`,`id_order`) 
				VALUES ".substr($ins,0,-1));
		}

		echo '<div class="notice notice-success is-dismissible"><p>Order '.$id2up.' sudah dibatalkan</p></div>';
	} else {
		echo '<div class="notice notice-warning is-dismissible"><p>Order '.$id2up.' belum lunas</p></div>';
	}
	
}
?>
<form action="" method="post">
<p style="text-align: center;">
Cari Order : <input type="text" name="cari" size="20" /> berdasar 
<select name="field">
	<option value="id">ID Order</option>
	<option value="idwp">ID Member</option>
	<option value="nama">Nama</option>
	<option value="username">Username</option>	
	<option value="email">Email</option>
</select>
<input type="submit" class="button button-primary" value="Cari"/>
</p>
</form>
<table class="widefat">
	<thead>
	<tr>
		<th scope="col" width="10%">No. Order</th>
		<th scope="col" width="20%">Produk</th>
		<th scope="col" width="20%">Nama</th>
		<th scope="col" width="10%">Sponsor ID</th>
		<th scope="col" width="10%">Harga</th>
		<th scope="col" width="10%">Tgl Order</th>
		<th scope="col" width="20%">Action</th>
	</tr>
	</thead>
	<tbody>
<?php
$listproduk = $wpdb->get_results("SELECT `id`,`nama`,`harga` FROM `cb_produk`");
foreach ($listproduk as $listproduk) {
	$produk[$listproduk->id]['nama'] = $listproduk->nama;
	$produk[$listproduk->id]['harga'] = $listproduk->harga;
}

if (isset($_GET['start']) && is_numeric($_GET['start'])) {
	$start = ($_GET['start']-1)*20;
} else {
	$start = 0;
}

if (isset($_POST['cari']) && is_numeric($_POST['cari']) && isset($_POST['field']) && $_POST['field'] == 'id') {
	$order = $wpdb->get_results("
	SELECT `cb_produklain`.`id`, 
	`cb_produklain`.`idproduk`,
	`cb_produklain`.`tgl_order`,
	`cb_produklain`.`tgl_bayar`,
	`cb_produklain`.`status`,
	`wp_member`.`nama`,
	`wp_member`.`id_user`,
	`wp_member`.`id_referral`
	FROM `cb_produklain`
	INNER JOIN `wp_member` 
	ON `wp_member`.`id_user`=`cb_produklain`.`id_user`
	WHERE `cb_produklain`.`id` = ".$_POST['cari']);
} elseif (isset($_POST['cari']) && is_numeric($_POST['cari']) && $_POST['cari'] > 0 && isset($_POST['field']) == 'idwp') {
	$order = $wpdb->get_results("
		SELECT `cb_produklain`.`id`, 
		`cb_produklain`.`idproduk`,
		`cb_produklain`.`tgl_order`,
		`cb_produklain`.`tgl_bayar`,
		`cb_produklain`.`status`,
		`wp_member`.`nama`,
		`wp_member`.`id_user`,
		`wp_member`.`idwp`,
		`wp_member`.`id_referral`
		FROM 
		`cb_produklain`
		INNER JOIN `wp_member` 
		ON `wp_member`.`idwp`=`cb_produklain`.`idwp`
		WHERE `cb_produklain`.`idwp` = ".$_POST['cari']."
		ORDER BY `cb_produklain`.`tgl_order` DESC");
} elseif (isset($_POST['cari']) && (isset($_POST['field']) == 'nama' || $_POST['field'] == 'username' || $_POST['field'] == 'email')) {
	$order = $wpdb->get_results("
		SELECT `cb_produklain`.`id`, 
		`cb_produklain`.`idproduk`,
		`cb_produklain`.`tgl_order`,
		`cb_produklain`.`tgl_bayar`,
		`cb_produklain`.`status`,
		`wp_member`.`nama`,
		`wp_member`.`id_user`,
		`wp_member`.`idwp`,
		`wp_member`.`id_referral`
		FROM 
		`cb_produklain`
		INNER JOIN `wp_member` 
		ON `wp_member`.`id_user`=`cb_produklain`.`id_user`
		WHERE `wp_member`.`".$_POST['field']."` LIKE '%".$_POST['cari']."%'
		ORDER BY `cb_produklain`.`tgl_order` DESC
		LIMIT ".$start.",20");
} else {
	$order = $wpdb->get_results("
		SELECT `cb_produklain`.`id`, 
		`cb_produklain`.`idproduk`,
		`cb_produklain`.`tgl_order`,
		`cb_produklain`.`tgl_bayar`,
		`cb_produklain`.`status`,
		`wp_member`.`nama`,
		`wp_member`.`id_user`,
		`wp_member`.`id_referral`
		FROM 
		`cb_produklain`
		INNER JOIN `wp_member` 
		ON `wp_member`.`id_user`=`cb_produklain`.`id_user`
		ORDER BY `cb_produklain`.`tgl_order` DESC
		LIMIT ".$start.",20");
}

if (is_array($order)) {
foreach ($order as $order) {
	if ($order->idproduk == 0) {
		$namaproduk = "Upgrade Premium";
		$hargaproduk = $options['harga'];
	} else {
		$namaproduk = $produk[$order->idproduk]['nama'];
		$hargaproduk = $produk[$order->idproduk]['harga'];
	}
	
	if ($order->status == 0) {
		$aktiflink = '<a href="admin.php?page=cbaf_orderlist&id='.$order->id.'">Proses</a> | <a href="admin.php?page=cbaf_orderlist&hapus='.$order->id.'">Hapus</a>';
	} else {
		$aktiflink = 'Lunas : '.date('d-m-Y',strtotime($order->tgl_bayar)).' | <a href="admin.php?page=cbaf_orderlist&batal='.$order->id.'" onclick="javascript:return confirm(\'Anda yakin ingin membatalkan order: '.$order->id.' atas nama '.$order->nama.' ?\')">Batal</a>';
	}
	
	echo '<tr><td>'.$order->id.'</td>
	<td>'.$namaproduk.'</td>
	<td><a href="admin.php?page=cbaf_memberlist&profil='.$order->id_user.'&val=0" target="_blank">'.$order->nama.'</a></td>
	<td><a href="admin.php?page=cbaf_memberlist&profil='.$order->id_referral.'" target="_blank">Lihat Sponsor</a></td>
	<td style="text-align:right">'.number_format($hargaproduk).'</td>
	<td>'.date('d-m-Y',strtotime($order->tgl_order)).'</td>
	<td>'.$aktiflink.'</td></tr>';
}
}
echo '
	</tbody>
</table>';

$jml = $wpdb->get_var("SELECT count(*) FROM `cb_produklain`"); 
$jmlpage = floor(($jml/20)+1);

echo '
Jumlah Order : '.$jml.'
<nav aria-label="Page navigation example" style="margin-top:20px">
  <ul class="pagination">';

$urlpage = '';
if (isset($_GET['start']) && is_numeric($_GET['start'])) {
	$page = $_GET['start'];
} else {
	$page = 1;
}

foreach ($_GET as $get => $value) {
	if ($get != 'start') {
		$urlpage .= $get.'='.$value.'&';
	}
}

if ($jmlpage > 10) {
	if ($page <= 7){
		for ($i=1;$i<=10;$i++) {
			echo '<li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$i.'">'.$i.'</a></li> ';
		}
		echo '... <li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$jmlpage.'">'.$jmlpage.'</a></li> ';
	} elseif ($page > 5 && $page < ($jmlpage-7)) {
		echo '<li class="page-item"><a class="page-link" href=?page=cbaf_memberlist&start=1">1</a></li> ... ';
		for ($i=($page-5);$i<=($page+5);$i++) {
			if ($i == $page) {
			echo '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
			} else {
			echo '<li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$i.'">'.$i.'</a></li> ';
			}
		}
		echo '... <li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$jmlpage.'">'.$jmlpage.'</a></li> ';
	} else {
		echo '<li class="page-item"><a class="page-link" href="?page='.$urlpage.'start=1">1</a></li> ... ';
		for ($i=($jmlpage-10);$i<=$jmlpage;$i++) {
			if ($i == $page) {
			echo '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
			} else {
			echo '<li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$i.'">'.$i.'</a></li> ';
			}
		}
	}
} else {
	for ($i=1;$i<=$jmlpage;$i++) {
		if ($i == $page) {
		echo '<li class="page-item active"><span class="page-link">'.$i.'</span></li>';
		} else {
		echo '<li class="page-item"><a class="page-link" href="?'.$urlpage.'start='.$i.'">'.$i.'</a></li> ';
		}
	}
}

echo '
  </ul>
</nav>';

?>
</div>