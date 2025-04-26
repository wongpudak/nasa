<?php
$wp_load = substr( dirname( __FILE__ ), 0, strpos( dirname( __FILE__ ), 'wp-content' ) ) . 'wp-load.php';
if ( ! empty( $wp_load ) && file_exists( $wp_load ) ) {
	require_once $wp_load;
} else {
	die('Could not load WordPress');
}

if (is_numeric(get_current_user_id())) {	
	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$id = $_GET['id'];
		$data = $wpdb->get_results("SELECT `nama`,`idwp`,`membership` FROM `wp_member` WHERE `id_referral`=$id");
		echo '<div class="geserkanan" id="downline'.$id.'">';
		foreach ($data as $data) {
		echo '
		<div class="listmember" id="member'.$data->idwp.'">
		<img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/join.gif" />';
			if ($data->membership > 0) {
				echo '<a class="folder" id="'.$data->idwp.'">
				<img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif" style="display: inline;" id="down'.$data->idwp.'"/></a>
				<a class="detil" id="detil'.$data->idwp.'">'.$data->nama.'</a></div>';
			} else {
				echo ' <img src="'.site_url().'/wp-content/plugins/wp-affiliasi/img/folder.gif" style="display: inline;" id="down'.$data->idwp.'"/>
				'.$data->nama.' <em>(blm valid)</em></div>';
			}
		}
		echo '</div>';
	} else {
		$idmember = str_replace('detil','',$_GET['member']);
		if (is_numeric($idmember)) {
			$user = $wpdb->get_row("SELECT * FROM `wp_member` WHERE `idwp`=".$idmember);
			$custom = unserialize($user->homepage);
			$aturform = get_option('aturform');
			$form = unserialize($aturform);
			if (is_array($form)) {
				$c = 0;
				echo '<div id="themember">
				<a class="close">Tutup</a>
				<h2>Profil '.$user->nama.'</h2>
				<table cellspacing="2">';
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
								case 'password' : $label = 'Password'; break;
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
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.urlaff($user->{$form['field']}).'</td>
						</tr>';
						} elseif ($form['field'] == 'ym') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$custom[0]??=''.'</td>
						</tr>';
						} elseif ($form['field'] == 'customwhatsapp') {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$custom['whatsapp']??=''.'</td>
						</tr>';
						} elseif ($form['field'] == 'password') {
							echo '
							<tr>
							  <td>'.$label.'</td>
							  <td><em>disembunyikan</em></td>
							</tr>';
						} elseif ($form['field'] == 'kelamin') {
							$pria = $wanita = '';
							if ($user->kelamin == 1) { $kelamin = 'Pria'; } else { $kelamin = 'Wanita'; }
							echo '
							<tr>
							  <td>'.$label.'</td>
							  <td>'.$kelamin.'</td>
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
						$c++;
						} else {
						echo '
						<tr>
						  <td>'.$label.'</td>
						  <td>'.$user->{$form['field']}.'</td>
						</tr>';
						}
					}
				}
				echo '
				<tr>
				  <td>Tanggal Daftar</td>
				  <td>'.date('d-m-Y h:i:s',strtotime($user->tgl_daftar)).'</td>
				</tr>
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
				</tr>
		
				</table>
				</div>';
			} else {
				echo 'Form Pengaturan Profil blm diaktifkan. Silahkan hubungi admin web ini';
			}
		}		
	}
}
?>