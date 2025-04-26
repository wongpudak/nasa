<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; }
global $wpdb, $user_ID;
global $subdomain, $nama, $username, $password, $urlreseller;
global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
global $namaprospek, $usernameprospek, $status, $blogurl;
if (!current_user_can('manage_options')) { die();  exit; }
if (isset($_GET['profil']) && is_numeric($_GET['profil']) && $_GET['profil'] > 0) {

	include('memberedit.php');

} else {

echo '
<div class="wrap">
	<h1 class="wp-heading-inline">Member List</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=22" target="_blank" role="button" aria-expanded="false">Bantuan</a>';
$options = get_option('cb_pengaturan');
if (isset($_GET['aktif']) && is_numeric($_GET['aktif']) && $user_ID) {
	$idwp = $_GET['aktif'];
	$cek = $wpdb->get_row("SELECT `id`,`status` FROM `cb_produklain` WHERE `idwp`=".$idwp." AND `idproduk`=0");
	if (isset($cek->status) && $cek->status == 1) {
		echo '<div id="message2" class="notice notice-warning is-dismissible"><p>Order sudah diproses sebelumnya</p></div>';
	} else {
		if (!isset($cek->id)) {
			$id_user = $wpdb->get_var("SELECT `id_user` FROM `wp_member` WHERE `idwp`=".$idwp);			
			$wpdb->query("INSERT INTO `cb_produklain` (`idwp`,`id_user`,`idproduk`,`status`,`tgl_order`,`tgl_bayar`,`hargaproduk`) 
				VALUES (".$idwp.",".$id_user.",0,0,'".wp_date('Y-m-d H:i:s')."','".wp_date('Y-m-d H:i:s')."',".($options['harga']??=0).")");
			$orderid = $wpdb->insert_id;
		} else {
			$orderid = $cek->id;
		}
		//echo '<div class="updated"><p>';
		aktivasi($orderid);
		//echo '</p></div>';
	}
} elseif (isset($_GET['validasi']) && is_numeric($_GET['validasi']) && $user_ID) {
	$data = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$_GET['validasi']." AND `membership`=0");
	if (isset($data->username)) {
		$idwp = wp_create_user($data->username, $data->password, $data->email);
		if (is_numeric($idwp)) {
			$passdb = md5($data->password);
			$wpdb->query("UPDATE `wp_member` SET `membership` = 1, `password`='".$passdb."', `idwp`=".$idwp." 
				WHERE `id_user` = ".$_GET['validasi']);
			echo '<div id="message2" class="notice notice-success is-dismissible"><p>'.$data->nama.' telah divalidasi</p></div>';
		} else {
			echo '<div id="message2" class="notice notice-warning is-dismissible"><p>'.$idwp->get_error_message().'</p></div>';
		}
	} else {
		echo '<div id="message2" class="notice notice-warning is-dismissible"><p>Data tidak ditemukan atau sudah divalidasi sebelumnya</p></div>';
	}
} elseif (isset($_GET['valaktif']) && is_numeric($_GET['valaktif']) && isset($user_ID)) {
	$data = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_user`=".$_GET['valaktif']." AND `membership`=0");
	if (isset($data->username)) {
		$idwp = wp_create_user($data->username, $data->password, $data->email);
		if (is_numeric($idwp)) {
			$passdb = md5($data->password);
			$wpdb->query("UPDATE `wp_member` SET `membership` = 1, `password`='".$passdb."', `idwp`=".$idwp." 
				WHERE `id_user` = ".$_GET['valaktif']);
			$wpdb->query("INSERT INTO `cb_produklain` (`idwp`,`id_user`,`idproduk`,`status`,`tgl_order`,`tgl_bayar`,`hargaproduk`) 
				VALUES (".$idwp.",".$_GET['valaktif'].",0,0,'".wp_date('Y-m-d H:i:s')."','".wp_date('Y-m-d H:i:s')."',".$options['harga'].")");
			$orderid = $wpdb->insert_id;			
			echo '<div id="message2" class="notice notice-success is-dismissible"><p>'.$data->nama.' telah divalidasi</p></div>';
			aktivasi($orderid);
		} else {
			echo '<div id="message2" class="notice notice-warning is-dismissible"><p>'.$idwp->get_error_message().'</p></div>';
		}		
	} else {
		echo '<div id="message2" class="notice notice-warning is-dismissible"><p>Order sudah diproses sebelumnya</p></div>';
	}
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
	$iduser = $_GET['hapus'];
	$wpdb->query("DELETE FROM `wp_member` WHERE `idwp`='$iduser'");
	$wpdb->query("UPDATE `wp_member` SET `id_referral`=".$user_ID." WHERE `id_referral`=".$iduser);
	wp_delete_user($iduser,1);
	echo '<div id="message2" class="notice notice-success is-dismissible"><p>Member '.$_GET['hapus'].' telah dihapus</p></div>';
} elseif (isset($_GET['hapusid']) && is_numeric($_GET['hapusid'])) {
	$iduser = $_GET['hapusid'];
	$wpdb->query("DELETE FROM `wp_member` WHERE `id_user`='$iduser'");
	echo '<div id="message2" class="notice notice-success is-dismissible"><p>Member '.$iduser.' telah dihapus</p></div>';
} 

$where = '';
if (isset($_POST['nama']) && isset($_POST['field'])) {
	$field = $_POST['field'];
	$nama = $_POST['nama'];
	if ($field == 'idwp' || $field == 'username') {
		$query = "SELECT * FROM `wp_member` WHERE `$field` = '$nama' ORDER BY `membership` ASC, `idwp` DESC";
	} else {
		$query = "SELECT * FROM `wp_member` WHERE `$field` LIKE '%$nama%' ORDER BY `membership` ASC, `idwp` DESC";
	}
} else {
	if (isset($_GET['start']) && is_numeric($_GET['start'])) {
		$start = ($_GET['start']-1)*20;
	} else {
		$start = 0;
	}

	if (isset($_GET['membership']) && is_numeric($_GET['membership'])) {
		$where = "WHERE `membership`=".$_GET['membership'];
	}

	if (isset($_GET['by']) && isset($_GET['sort'])) {
		$sort ="ORDER BY `".$_GET['by']."` ".$_GET['sort'];
	} else {
		$sort = "ORDER BY `tgl_daftar` DESC";
	}

	$query = "SELECT * FROM `wp_member` ".$where." ".$sort." LIMIT ".$start.",20";
}

$datausers = $wpdb->get_results($query);

?>

<form action="" method="post">
<p style="text-align: center;">
Cari Member : <input type="text" name="nama" size="15"> di 
<select name="field">
	<option value="nama">Nama</option>
	<option value="username">Username</option>
	<option value="email">Email</option>
	<option value="subdomain">Kode Affiliasi</option>
	<option value="idwp">No. ID</option>
	<option value="telp">No. HP</option>
</select>
<input type="submit" class="button button-primary" value="Cari">
</p>
</form>
<p><strong>Member: </strong>
	<a href="admin.php?page=cbaf_memberlist&membership=2">Premium</a> | 
	<a href="admin.php?page=cbaf_memberlist&membership=1">Free</a> | 
	<a href="admin.php?page=cbaf_memberlist&membership=0">Blm Validasi</a> |
	<a href="../wp-content/plugins/wp-affiliasi/include/adminmenu/bikincsv.php">Download</a>
</p>
<table class="widefat">
	<thead>
	<tr>
		<?php 
		if(isset($_GET['sort']) && $_GET['sort'] == 'ASC') {
			$sort = 'DESC';
		} else {
			$sort = 'ASC';
		}
		?>
		<th scope="col" width="5%"><a href="admin.php?page=cbaf_memberlist&by=idwp&sort=<?php echo $sort;?>">ID</a></th>
		<th scope="col" width="15%"><a href="admin.php?page=cbaf_memberlist&by=nama&sort=<?php echo $sort;?>">Nama</a> / <a href="admin.php?page=cbaf_memberlist&by=tgl_daftar&sort=<?php echo $sort;?>">Tgl.Daftar</a></th>
		<th scope="col" width="10%"><a href="admin.php?page=cbaf_memberlist&by=id_referral&sort=<?php echo $sort;?>">Sponsor</a></th>
		<th scope="col" width="40%"><a href="admin.php?page=cbaf_memberlist&by=alamat&sort=<?php echo $sort;?>">Alamat</a> / <a href="admin.php?page=cbaf_memberlist&by=telp&sort=<?php echo $sort;?>">No. HP</a></th>
		<th scope="col" width="15%"><a href="admin.php?page=cbaf_memberlist&by=membership&sort=<?php echo $sort;?>">Status</a></th>
		<th scope="col" width="15%">Action</th>
	</tr>
	</thead>
	<tbody>

		<?php
	if ($datausers) {
		$hitung = $i = 0;
		foreach ($datausers as $view) {	
		$hitung++;
		$custom = unserialize($view->homepage);
		if (isset($custom['whatsapp']) && $custom['whatsapp'] != '') {
			$telp = 'WA: <a href="https://wa.me/'.formatwa($custom['whatsapp']).'" target="blank">'.$custom['whatsapp'].'</a>';
		} else {
			$telp = 'Telp. '.$view->telp;
		}
		echo '
			<tr';
		if ($i == 0) { echo ' class="alternate"'; $i=1;} else {$i=0;}
		echo '>
			<td>'.$view->idwp.'</td>
			<td>';
		if ($view -> membership > 0) {	
			echo '<a href="admin.php?page=cbaf_memberlist&profil='.$view->idwp.'">'.$view -> nama.'</a>';
		} else {
			echo '<a href="admin.php?page=cbaf_memberlist&profil='.$view->id_user.'&val=0">'.$view -> nama.'</a>';
		}

		echo ' ('.$view -> username.')<br/>
			'.date('d-m-Y h:i',strtotime($view -> tgl_daftar)).'</td>
			<td><a href="admin.php?page=cbaf_memberlist&profil='.$view->id_referral.'">Lihat Sponsor</a></td>
			<td>'.$view -> alamat.' '.$view -> kota.' '.$view -> provinsi.' '.$view -> kodepos.'<br/>
			'.$telp.'</td>';
			
			if ($view->membership == 2) {
		echo '<td>Premium Member</td>';
			} elseif ($view->membership == 1) {
		echo '<td>Free Member<br/>
		<a href="admin.php?page=cbaf_memberlist&aktif='.$view->idwp.'" onclick="javascript:return confirm(\'Anda yakin ingin mengupgrade \\\''.$view->nama.'\\\' ?\')">Upgrade</a>
		</td>';
			} else {
		echo '<td>Belum Login<br/>
		<a href="admin.php?page=cbaf_memberlist&validasi='.$view->id_user.'" onclick="javascript:return confirm(\'Anda yakin ingin mengaktifkan \\\''.$view->nama.'\\\' ?\')">Validasi</a> | 
		<a href="admin.php?page=cbaf_memberlist&valaktif='.$view->id_user.'" onclick="javascript:return confirm(\'Anda yakin ingin mengaktifkan \\\''.$view->nama.'\\\' ?\')">Upgrade</a></td>';
			}

		echo '<td>';
			if ($view->idwp != $user_ID) {
				if (!empty($view->idwp) && $view->idwp > 0) {					
		echo '<a href="admin.php?page=cbaf_memberlist&hapus='.$view->idwp.'" onclick="javascript:return confirm(\'Anda yakin ingin menghapus \\\''.$view->nama.'\\\' ?\')">Hapus</a>
				</td></tr>';				
				} else {
		echo '<a href="admin.php?page=cbaf_memberlist&hapusid='.$view->id_user.'" onclick="javascript:return confirm(\'Anda yakin ingin menghapus \\\''.$view->nama.'\\\' ?\')">Hapus</a></td></tr>';	
				} 
			} else {
		echo '&nbsp;</td></tr>';
			}
		}
	} else {
	echo '
		<tr><td colspan="6" class="center">User yang anda cari tidak ada</td></tr>';
	}
echo'
	</tbody>
</table>';

$jml = $wpdb->get_var("SELECT count(*) FROM `wp_member`"); 
$jmlpage = floor(($jml/20)+1);

echo '
Jumlah Member :'.$jml.'
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
}
cbcek();
?>
</div>