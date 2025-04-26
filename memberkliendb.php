<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; }
global $wpdb, $user_ID;
global $subdomain, $nama, $username, $password, $urlreseller;
global $telp, $kota, $provinsi, $bank, $rekening, $ac, $komisi;
global $namaprospek, $usernameprospek, $status, $blogurl;
if (!current_user_can('manage_options')) { die();  exit; }
if (isset($_GET['profil']) && is_numeric($_GET['profil'])) {
	if ($_GET['profil'] != 0) {
		$iduser = $_GET['profil'];
		if (isset($_GET['val']) && $_GET['val'] == 0) {
			$user = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_referral`=".$user_ID." AND `id_user`=".$iduser);
		} else {
			$user = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `id_referral`=".$user_ID." AND `idwp`=".$iduser);
		}
		$custom = unserialize($user->homepage);
		$aturform = get_option('aturform');
		$form = unserialize($aturform);
		if (is_array($form)) {
		$c = 0;
		echo '<div class="wrap">		
		<h2>Profil '.$user->nama.'</h2>';
		if (isset($custom['pic_profil']) && $custom['pic_profil'] != '') { 
			echo '<p><img src="'.$custom['pic_profil'].'?rand='.rand(0,999).'" alt="Foto Profil" 
			style="width:100%;max-width:300px;margin:0 auto" /></p>';
		}
		echo '
		<table width="80%" class="table" cellspacing="5">';
		if (isset($user->idwp) && $user->idwp > 0) {
			echo '
			<tr>
			  <td>ID Member</td>
			  <td>'.number_format($user->idwp).'</td>
			</tr>
			';
		}
		foreach ($form as $form) {
			if (isset($form['jaringan']) && $form['jaringan'] == 1) {
				if (!$form['label']) {
					switch ($form['field']) {
						case 'nama' : $label = 'Nama Lengkap'; $required = 'required'; break;
						case 'id_tianshi' : $label = 'ID MLM'; break;
						case 'email' : $label = 'Email'; break;
						case 'ktp' : $label = 'No. KTP'; break;
						case 'tgl_lahir' : $label = 'Tanggal Lahir'; break;
						case 'alamat' : $label = 'Alamat'; break;
						case 'kota' : $label = 'Kota'; break;
						case 'provinsi' : $label = 'Provinsi'; break;
						case 'kodepos' : $label = 'Kodepos'; break;
						case 'telp' : $label = 'No. Telp / HP'; break;
						case 'ktp_istri' : $label = 'No. KTP Pasangan'; break;
						case 'nama_istri' : $label = 'Nama Pasangan'; break;
						case 'tgl_lahir_istri' : $label = 'Tgl Lahir Pasangan'; break;
						case 'ac' : $label = 'Atas Nama'; break;
						case 'bank' : $label = 'Nama Bank'; break;
						case 'rekening' : $label = 'No. Rekening'; break;
						case 'kelamin' : $label = 'Jenis Kelamin'; break;
						case 'username' : $label = 'Username'; break;
						case 'subdomain' : $label = 'URL Affiliasi'; break;
						case 'customwhatsapp' : $label = 'WhatsApp'; break;
						case 'ym' : $label = 'Yahoo Messenger'; break;
					}
				} else {
					$label = $form['label'];
				}
				if ($form['field'] == 'keterangan') {
				echo '
				<tr>
				  <td colspan="2"><strong>'.$label.'</strong></td>
				</tr>';	
				} elseif ($form['field'] == 'username') {
				echo '
				<tr>
				  <td>'.$label.'</td>
				  <td>'.$user->{$form['field']}.'</td>
				</tr>';
				} elseif ($form['field'] == 'subdomain') {
					if (get_option('affsub') == 1) {
						echo '
				<tr>
				  <td>'.$label.'</td>
				  <td><a href="http://'.$user->{$form['field']}.'.'.$_SERVER['SERVER_NAME'].'" target="_blank">http://'.$user->{$form['field']}.'.'.$_SERVER['SERVER_NAME'].'</a></td>
				</tr>';
					} else {
						echo '
				<tr>
				  <td>'.$label.'</td>
				  <td><a href="'.site_url().'/?reg='.$user->{$form['field']}.'" target="blank">'.site_url().'/?reg='.$user->{$form['field']}.'</a></td>
				</tr>';
					}

				} elseif ($form['field'] == 'ym') {
				echo '
				<tr>
				  <td>'.$label.'</td>
				  <td>'.$custom[0].'</td>
				</tr>';
				} elseif ($form['field'] == 'password') {
					#diam aja
				} elseif ($form['field'] == 'kelamin') {
					$pria = $wanita = '';
					if ($user->kelamin == 1) { $kelamin = 'Pria'; } else { $kelamin = 'Wanita'; }
					echo '
					<tr>
					  <td>'.$label.'</td>
					  <td>'.$kelamin.'</td>
					</tr>';
				} elseif ($form['field'] == 'customwhatsapp') {
					echo '
					<tr>
					  <td>'.$label.'</td>
					  <td>';
					  if (isset($custom['whatsapp'])) {
					  echo '<a href="https://wa.me/'.formatwa($custom['whatsapp']).'" target="blank">'.$custom['whatsapp'].'</a>';
					  }
					  echo '</td>
					</tr>';
				} elseif ($form['field'] == 'custom') {
				echo '
				<tr>
				  <td>'.$label.'</td>
				  <td>';
				  if (isset($custom[$c+1])) {
				  echo $custom[$c+1];
				  }
				  echo '</td>
				</tr>';
				
				} else {
				echo '
				<tr>
				  <td>'.$label.'</td>
				  <td>'.$user->{$form['field']}.'</td>
				</tr>';
				}
			}

			if ($form['field'] == 'custom') { $c++; }
		}
		echo '
		<tr>
		  <td>Tanggal Daftar</td>
		  <td>'.date('d-m-Y h:i:s',strtotime($user->tgl_daftar)).'</td>
		</tr>';
		if ($user->membership==2) {
			echo '
			<tr>
			  <td>Tanggal Upgrade</td>
			  <td>'.date('d-m-Y h:i:s',strtotime($user->tgl_upgrade)).'</td>
			</tr>
			';
		}
		echo '
		<tr>
		  <td>Status</td>
		  <td>';
			if ($user->membership==1) {
				echo 'Free Member';
			} elseif ($user->membership==2) {
				echo 'Premium Member';
			} else {
				echo 'Belum Validasi';
			}
		echo '</td>
		</tr>';
		echo '
		</table>';
		} else {
			echo '<div class="wrap"><h2>Form Belum Diatur</h2>
			<p>Form Pengaturan Profil blm diaktifkan. Silahkan setting dulu di menu <a href="admin.php?page=cbaf_daftar">Form Pendaftaran</a></p>';
		}
	} else {
		echo '<div class="wrap"><h2>Bukan Member</h2>
		<p>Profil yang hendak anda lihat bukan member</p>';
	}
} else {

echo '
<div class="wrap">
	<h1 class="wp-heading-inline">Klien List</h1>';
$where = '';
if (isset($_POST['nama']) && isset($_POST['field'])) {
	$field = $_POST['field'];
	$nama = $_POST['nama'];
	if ($field == 'idwp' || $field == 'username') {
		$query = "SELECT * FROM `wp_member` WHERE `$field` = '$nama' AND `id_referral`=".$user_ID." ORDER BY `membership` ASC, `idwp` DESC";
	} else {
		$query = "SELECT * FROM `wp_member` WHERE `$field` LIKE '%$nama%' AND `id_referral`=".$user_ID." ORDER BY `membership` ASC, `idwp` DESC";
	}
} else {
	if (isset($_GET['start']) && is_numeric($_GET['start'])) {
		$start = ($_GET['start']-1)*20;
	} else {
		$start = 0;
	}

	if (isset($_GET['membership']) && is_numeric($_GET['membership'])) {
		$where = "AND `membership`=".$_GET['membership'];
	}

	if (isset($_GET['by']) && isset($_GET['sort'])) {
		$sort ="ORDER BY `".$_GET['by']."` ".$_GET['sort'];
	} else {
		$sort = "ORDER BY `tgl_daftar` DESC";
	}

	$query = "SELECT * FROM `wp_member` WHERE `id_referral`=".$user_ID." ".$where." ".$sort." LIMIT ".$start.",20";
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
	<a href="admin.php?page=cbaf_klienlist&membership=2">Premium</a> | 
	<a href="admin.php?page=cbaf_klienlist&membership=1">Free</a> | 
	<a href="admin.php?page=cbaf_klienlist&membership=0">Blm Validasi</a>
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
		<th scope="col" width="5%"><a href="admin.php?page=cbaf_klienlist&by=idwp&sort=<?php echo $sort;?>">ID</a></th>
		<th scope="col" width="15%"><a href="admin.php?page=cbaf_klienlist&by=nama&sort=<?php echo $sort;?>">Nama</a> / <a href="admin.php?page=cbaf_klienlist&by=tgl_daftar&sort=<?php echo $sort;?>">Tgl.Daftar</a></th>	
		<th scope="col" width="40%"><a href="admin.php?page=cbaf_klienlist&by=alamat&sort=<?php echo $sort;?>">Alamat</a> / <a href="admin.php?page=cbaf_klienlist&by=telp&sort=<?php echo $sort;?>">No. HP</a></th>
		<th scope="col" width="15%"><a href="admin.php?page=cbaf_klienlist&by=membership&sort=<?php echo $sort;?>">Status</a></th>
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
			echo '<a href="admin.php?page=cbaf_klienlist&profil='.$view->idwp.'" target="_blank">'.$view -> nama.'</a>';
		} else {
			echo '<a href="admin.php?page=cbaf_klienlist&profil='.$view->id_user.'&val=0" target="_blank">'.$view -> nama.'</a>';
		}

		echo ' ('.$view -> username.')<br/>
			'.date('d-m-Y h:i',strtotime($view -> tgl_daftar)).'</td>			
			<td>'.$view -> alamat.' '.$view -> kota.' '.$view -> provinsi.' '.$view -> kodepos.'<br/>
			'.$telp.'</td>';
			
			if ($view->membership == 2) {	echo '<td>Premium Member</td>';	} 
			elseif ($view->membership == 1) {	echo '<td>Free Member</td>'; } 
			else { echo '<td>Belum Login</td>';	}

		echo '</tr>';
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
		echo '<li class="page-item"><a class="page-link" href=?page=cbaf_klienlist&start=1">1</a></li> ... ';
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