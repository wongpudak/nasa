<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
echo '<div class="wrap">';
$showtxt = $txterror = $field = $value = '';
$options = get_option('cb_pengaturan');
$konfemail = get_option('konfemail');
$blogurl = get_bloginfo('url');

if (isset($_POST['email']) && isset($_POST['nama'])) {
	if (isset($_POST['id_tianshi'])) { $field .= "`id_tianshi`,"; $value.="'".sanitize_text_field($_POST['id_tianshi'])."',";}
	if (isset($_POST['ktp'])) { $field .= "`ktp`,"; $value.="'".sanitize_text_field($_POST['ktp'])."',";}
	if (isset($_POST['nama'])) { $nama = sanitize_text_field($_POST['nama']); $field .= "`nama`,"; $value.="'".sanitize_text_field($_POST['nama'])."',";}
	if (isset($_POST['tgl_lahir'])) { $field .= "`tgl_lahir`,"; $value.="'".sanitize_text_field($_POST['tgl_lahir'])."',";}
	if (isset($_POST['alamat'])) { $field .= "`alamat`,"; $value.="'".sanitize_text_field($_POST['alamat'])."',";}
	if (isset($_POST['kota'])) { $field .= "`kota`,"; $value.="'".sanitize_text_field($_POST['kota'])."',";}
	if (isset($_POST['provinsi'])) { $field .= "`provinsi`,"; $value.="'".sanitize_text_field($_POST['provinsi'])."',";}
	if (isset($_POST['kodepos'])) { $field .= "`kodepos`,"; $value.="'".sanitize_text_field($_POST['kodepos'])."',";}
	if (isset($_POST['telp'])) { $field .= "`telp`,"; $value.="'".sanitize_text_field($_POST['telp'])."',";}
	if (isset($_POST['nama_istri'])) { $field .= "`nama_istri`,"; $value.="'".sanitize_text_field($_POST['nama_istri'])."',";}
	if (isset($_POST['ktp_istri'])) { $field .= "`ktp_istri`,"; $value.="'".sanitize_text_field($_POST['ktp_istri'])."',";}
	if (isset($_POST['tgl_lahir_istri'])) { $field .= "`tgl_lahir_istri`,"; $value.="'".sanitize_text_field($_POST['tgl_lahir_istri'])."',";}
	if (isset($_POST['kelamin'])) { $field .= "`kelamin`,"; $value.="'".sanitize_text_field($_POST['kelamin'])."',";}
	if (isset($_POST['bank'])) { $field .= "`bank`,"; $value.="'".sanitize_text_field($_POST['bank'])."',";}
	if (isset($_POST['rekening'])) { $field .= "`rekening`,"; $value.="'".sanitize_text_field($_POST['rekening'])."',";}
	if (isset($_POST['ac'])) { $field .= "`ac`,"; $value.="'".sanitize_text_field($_POST['ac'])."',";}

	if (isset($_POST['email'])) { $email = sanitize_email(strtolower($_POST['email'])); }
	if (isset($_POST['username'])) { $username = sanitize_user(strtolower($_POST['username']),true); }
	if (isset($_POST['password'])) { $password = sanitize_text_field($_POST['password']); }
	if (isset($_POST['subdomain'])) { $subdomain = sanitize_text_field(strtolower($_POST['subdomain'])); }
	if (isset($_POST['telp'])) { $telp = sanitize_text_field($_POST['telp']);}
	if (isset($_POST['customwhatsapp'])) { $lainlain['whatsapp'] = sanitize_text_field($_POST['customwhatsapp']); }

	$val = gettimeofday(true);
	$val = substr(MD5($val),0,15);
	$ip = $_SERVER['REMOTE_ADDR'];
	if (isset($_POST['idrefer']) && is_numeric($_POST['idrefer'])) {
		$id_referral = $_POST['idrefer'];
	} else {
		//$datasponsor = unserialize(stripslashes($_COOKIE['sponsor']));
		$id_referral = $_COOKIE['idsponsor'];
	}

	if (!isset($username) || $username == '') {
		$pos = stripos($email,'@');
		$username = substr($email, 0, $pos);
	}
		
	if (!isset($password) || $password == '') {
		$password = gettimeofday(true);
		$password = substr(MD5($password),0,10);		
	}
	
	if (!isset($subdomain) || $subdomain == '') {
		$subdomain = $username;
	}
	
	$subdomain	= strtolower($subdomain);
	$subdomain	= preg_replace('/[^\w@,.;]/', '', $subdomain);

	$suffix = 1;
	$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$username'");
	if ( isset($username_exist) || username_exists( $username )) {
		while (isset($username_exist) || username_exists( $usernow )) {
			$suffix++;
			$usernow = $username.$suffix;
			$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$usernow'");			
		}
		$username = $usernow;
	}
	
	$suffix = 1;
	$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subdomain'");
	if (isset($subdomain_exist)) {
		while (isset($subdomain_exist)) {
			$suffix++;
			$subnow = $subdomain.$suffix;
			$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subnow'");			
		}
		$subdomain = $subnow;
	}
	
	if (!is_email($email)) {
	    $txterror .= 'Email anda salah. Format yang benar username@domain.com contoh: bejo@gmail.com<br/>';
	}
	
	$check_email = $wpdb->get_var("SELECT `email` FROM `wp_member` WHERE `email` =  '$email'");
	
	if ($check_email || email_exists( $email )) {
		$txterror .= 'Email sudah pernah dipakai, anda hanya boleh menggunakan 1 alamat email per akun<br/>';
	}

	if ($username == $password) {
		$txterror .= 'Username dan Password tidak boleh sama. Rawan di hack lho<br/>';
	}
	
	$c = 1;
	if (isset($_POST['ym'])) {
	$lainlain[0] = sanitize_text_field($_POST['ym']);
	}

	if (isset($_POST['custom'])) {
		foreach ($_POST['custom'] as $key => $custom) {
			$lainlain[$c] = sanitize_text_field($custom);
			$c++;
		}
	}

	$lainlain['uplines'] = cbaff_uplines($id_referral);

	if (isset($lainlain)) {
		$homepage = serialize($lainlain);
	}
	
	if (!$txterror) {
		if (!empty($username)) { $field .= "`username`,"; $value .= "'".$username."',"; }
		if (!empty($subdomain)) { $field .= "`subdomain`,"; $value .= "'".$subdomain."',"; }
		if (!empty($email)) { $field .= "`email`,"; $value .= "'".$email."',"; }
		if (!empty($password)) { $field .= "`password`,"; $value .= "'".$password."',"; }
		if (!empty($homepage)) { $field .= "`homepage`,"; $value .= "'".$homepage."',"; }
		
		$field = $field."`id_referral`,`tgl_daftar`,`ip`,`membership`";
		$value = $value.$id_referral.",'".wp_date('Y-m-d H:i:s')."','".$ip."',".$_POST['status'];	
		$daftar = wp_create_user($username, $password, $email);
		if (is_numeric($daftar)) {
			$field .= ",`idwp`";
			$value .= ",".$daftar;
			$wpdb->query("INSERT INTO `wp_member` ($field) VALUES ($value)");
			$id_user = $wpdb->insert_id;
			cb_notif($id_user,'registrasi');

			// Action Hook Registrasi		
			$showtxt = apply_filters('cbaff_registrasi_sukses',$showtxt,$id_user);
			
			// Selesai, sekarang kirim ke autoresponder	
			if (isset($konfautoresponder['free']['action']) && $konfautoresponder['free']['action'] != '') {
				$showtxt .= '<form method="POST" name="result" action="'.$konfautoresponder['free']['action'].'">';
				foreach ($konfautoresponder['free'] as $key => $value) {
					if (is_numeric($key) && $value['field'] != '') {
						$showtxt .= '<input type="hidden" name="'.$value['field'].'" value="'.$value['value'].'"/>';
					}
				}
				$showtxt .= '</form>
				<script type="text/javascript">
				document.result.submit();
				</script>
				';
			} 
			
			echo '<div id="message2" class="notice notice-success is-dismissible"><p>Member telah ditambahkan</p></div>';
			
		} else {
			$showtxt .= '<div id="message2" class="notice notice-warning is-dismissible"><p>Username atau Email sudah ada yg memakai<p>
		<p>Silahkan Back dan perbaiki isian anda</p></div>';
		}
	} else {
		$showtxt .= '<div id="message2" class="notice notice-warning is-dismissible"><p>'.$txterror.'<p>
		<p>Silahkan Back dan perbaiki isian anda</p></div>';
	}
}

echo '
<h1 class="wp-heading-inline">Tambah Member Baru</h1>
<a class="page-title-action" href="https://cafebisnis.com/pwa.php?id=28" target="_blank" role="button" aria-expanded="false">Bantuan</a>
<hr class="wp-header-end">
<p>Anda bisa menambahkan member baru menggunakan form ini. Member akan langsung aktif sesuai keanggotaan yang anda tentukan. Nomor ID Sponsor juga bisa dimasukkan, namun sponsor tidak mendapatkan komisi PPL</p>';
$aturform = get_option('aturform');
$form = unserialize($aturform);
if (is_array($form)) {
	$c = 1;
	$showtxt .= '<form action="" method="post">
	<div class="form-group row">
		<label class="col-sm-2 col-form-label">No. ID Sponsor</label>
		<div class="col-sm-6"><input type="text" class="form-control" name="idrefer" /></div>
	</div>';
	foreach ($form as $form) {
		if (isset($form['register']) && $form['register'] == 1) {
			$required = $label = $input = '';
			// Cek apakah inputnya wajib diisi
			if (isset($form['required']) && $form['required'] == 1) {
				$required = ' required';
			} else {
				$required = '';
			}

			switch ($form['field']) {
				case 'nama' : $label = 'Nama Lengkap'; $input = '<input type="text" class="form-control" name="'.$form['field'].'" required/>'; break;
				case 'id_tianshi' : $label = 'ID MLM'; $input = '<input type="text" class="form-control" name="'.$form['field'].'" required/>'; break;
				case 'email' : $label = 'Email'; $input = '<input type="email" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'ktp' : $label = 'No. KTP'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'tgl_lahir' : $label = 'Tanggal Lahir'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'alamat' : $label = 'Alamat'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'kota' : $label = 'Kota'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'provinsi' : $label = 'Provinsi'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'kodepos' : $label = 'Kodepos'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'telp' : $label = 'No. Telp / HP'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'ktp_istri' : $label = 'No. KTP Pasangan'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'nama_istri' : $label = 'Nama Pasangan'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'tgl_lahir_istri' : $label = 'Tgl Lahir Pasangan'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'ac' : $label = 'Atas Nama'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'bank' : $label = 'Nama Bank'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'rekening' : $label = 'No. Rekening'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'kelamin' : $label = 'Jenis Kelamin'; $input = '<select class="form-control" name="'.$form['field'].'"'.$required.'><option value="1">Pria</option><option value="0">Wanita</option></select>'; break;
				case 'username' : $label = 'Username'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'password' : $label = 'Password'; $input = '<input type="password" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'subdomain' : 
					$label = 'URL Affiliasi';
					if (get_option('affsub') == 1) {
						$input = '<div class="input-group"><div class="input-group-prepend">
							    <span class="input-group-text">http://</span>
							  </div><input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>
							  <div class="input-group-append">
							    <span class="input-group-text">.'.cbdomain().'</span>
							  </div></div>';
					} else {
						$input = '<div class="input-group"><div class="input-group-prepend">
							    <span class="input-group-text" id="basic-addon3">'.site_url().'/?reg=</span>
							  </div><input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/></div>'; 
					}
					break;
				case 'ym' : $label = 'Yahoo Messenger'; $input = '<input type="text" class="form-control" name="'.$form['field'].'"'.$required.'/>'; break;
				case 'customwhatsapp' : $label = 'WhatsApp'; $input = '<input type="text" class="form-control" name="customwhatsapp"'.$required.'/>'; break;
				case 'custom' : $label = 'Custom'; $input = '<input type="text" class="form-control" name="custom['.$c.']"'.$required.'/>'; break;
			}

			// Create Label
			if (isset($form['label']) && $form['label'] != '') {
				$label = $form['label']; // Gunakan Label yang Ditentukan Member
			} 

			// Create Option Field
			if (isset($form['option']) && $form['option'] != '') {
				$optionfield = explode(',',$form['option']);
				if (count($optionfield) > 1) {
					if ($form['field'] == 'custom') { 
						$field = 'custom['.$c.']';								
					} else { 
						$field = $form['field']; 
					}
					$input = '<select name="'.$field.'"'.$required.'>';	
					foreach ($optionfield as $optionfield) {
						$input .= '<option value="'.trim($optionfield).'">'.trim($optionfield).'</option>';
					}
					$input .= '</select>';
				}
			} 


			if ($form['field'] == 'keterangan') {
				$showtxt .= '<div class="form-group row">
		<label class="col-sm-9 col-form-label"><strong>'.$label.'</strong></label></div>';
			} else {
				$showtxt .= '<div class="form-group row">
		<label class="col-sm-2 col-form-label">'.$label.'</label><div class="col-sm-6">'.$input.'</div></div>';	
			}
		}

		if (isset($form['field']) && $form['field'] == 'custom') {
			$c++;
		}
	}
	$showtxt .= '
		<div class="form-group row">
		<label class="col-sm-2 col-form-label">Status</label>
		<div class="col-sm-6">
		<select class="form-control" name="status">
		<option value="1">Free Member</option>
		<option value="2">Premium Member</option>	
	    </select></div>
	    </div>
		<input type="submit" class="button button-primary" value="Tambah Member"/>
	</form>';
} else {
	$showtxt .= 'Form Pengaturan Profil blm diaktifkan. Silahkan hubungi admin web ini';
}

echo $showtxt.'</div>';
?>