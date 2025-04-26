<?php if (!defined('IS_IN_SCRIPT')) { die();  exit; } 
//require_once(ABSPATH . WPINC . '/registration.php');
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
	if (isset($_POST['username'])) { 
		$username = sanitize_user(strtolower($_POST['username']),true); 
		$username = preg_replace("/[^A-Za-z0-9]/","",$username);
	}
	if (isset($_POST['password'])) { $password = sanitize_text_field($_POST['password']); }
	if (isset($_POST['subdomain'])) { $subdomain = txtonly(strtolower($_POST['subdomain'])); }
	if (isset($_POST['telp'])) { $telp = sanitize_text_field($_POST['telp']);}
	if (isset($_POST['customwhatsapp'])) { $lainlain['whatsapp'] = sanitize_text_field($_POST['customwhatsapp']); }

	if (isset($options['cap_secret']) && $options['cap_secret'] != '' && isset($options['cap_registrasi']) && $options['cap_registrasi'] != '') {
		if (isset($_POST["g-recaptcha-response"]) && $_POST['g-recaptcha-response'] != '')  {
			$val = 'secret='.$options['cap_secret'].'&response='.$_POST['g-recaptcha-response'];			
			$cek = postData('https://www.google.com/recaptcha/api/siteverify',$_SERVER['HTTP_USER_AGENT'], $val);
			$result = json_decode($cek, true);
			if ($result['success'] != 1) {
				switch ($result['error-codes'][0]) {
					case 'timeout-or-duplicate': $txterror = 'Waktu Captcha Habis atau Terjadi Duplikat';	break;
					case 'missing-input-secret': $txterror = 'Secret Key tidak ada, silahkan hubungi admin web ini';	break;
					case 'invalid-input-secret': $txterror = 'Secret Key Salah, silahkan hubungi admin web ini';	break;
					case 'missing-input-response': $txterror = 'Anda belum menyelesaikan reCaptcha';	break;
					case 'invalid-input-response': $txterror = 'reCaptcha yang anda lakukan salah';	break;
					case 'bad-request': $txterror = 'Rekues Tidak Valid';	break;
					default: $txterror = 'Ada masalah dengan reCaptcha: '.$result['error-codes'][0];
				}
			}
		} else {
			$txterror = 'Captcha harus diisi';
		}
	}

	$val = gettimeofday(true);
	$val = substr(MD5($val),0,15);
	$ip = realIP();
	if (isset($_POST['kodesponsor']) && $_POST['kodesponsor'] != '') {
		$id_referral = $wpdb->get_var("SELECT `idwp` FROM `wp_member` WHERE `subdomain`= '".sanitize_text_field($_POST['kodesponsor'])."'");
		if (is_numeric($id_referral) && $id_referral > 0) {
			// ok
		} else {
			$datasponsor = unserialize(CB_SPONSOR);
			$txterror = '<p>Kode Sponsor tidak ditemukan. Kode sponsor adalah kode reg atau subdomain URL Affiliasi sponsor anda.</p>
			<p>Contoh: URL Sponsor anda <code>'.site_url().'/?reg=<strong>'.$datasponsor['subdomain'].'</strong></code> maka kode sponsor anda adalah <code><strong>'.$datasponsor['subdomain'].'</strong></code></p>';
		}
	} elseif (isset($_POST['idrefer']) && is_numeric($_POST['idrefer'])) {
		$id_referral = $_POST['idrefer'];
	} else {
		$datasponsor = unserialize(CB_SPONSOR);
		$id_referral = $datasponsor['idwp'];
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
	
	$subdomain	= txtonly(strtolower($subdomain));

	$suffix = 1;
	$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$username'");
	if ( isset($username_exist) || username_exists( $username )) {
		if (isset($_POST['username']) && $_POST['username'] != '') {
			$txterror .= 'Maaf, username yang anda pilih sudah ada yang memakai. Silahkan pilih username lain.<br/>';
		} else {
			while (isset($username_exist) || username_exists( $usernow )) {
				$suffix++;
				$usernow = $username.$suffix;
				$username_exist = $wpdb->get_var("SELECT `username` FROM `wp_member` WHERE `username` =  '$usernow'");			
			}
			$username = $usernow;
		}
	}
	
	$suffix = 1;
	$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subdomain'");
	if (isset($subdomain_exist)) {
		if (isset($_POST['subdomain']) && $_POST['subdomain'] != '') {
			$txterror .= 'Maaf, kode affiliasi yang anda pilih sudah ada yang memakai. Silahkan pilih kode affiliasi lain.<br/>';
		} else {
			while (isset($subdomain_exist)) {
				$suffix++;
				$subnow = $subdomain.$suffix;
				$subdomain_exist = $wpdb->get_var("SELECT `subdomain` FROM `wp_member` WHERE `subdomain` =  '$subnow'");			
			}
			$subdomain = $subnow;
		}
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
			$lainlain[$key] = sanitize_text_field($custom);			
		}
	}

	if (!isset($txterror) || $txterror == '') {
		$lainlain['uplines'] = cbaff_uplines($id_referral);

		if (isset($lainlain)) {
			$homepage = serialize($lainlain);
		}
		if (!empty($username)) { $field .= "`username`,"; $value .= "'".$username."',"; }
		if (!empty($subdomain)) { $field .= "`subdomain`,"; $value .= "'".$subdomain."',"; }
		if (!empty($email)) { $field .= "`email`,"; $value .= "'".$email."',"; }
		if (!empty($password)) { $field .= "`password`,"; $value .= "'".$password."',"; }
		if (!empty($homepage)) { $field .= "`homepage`,"; $value .= "'".$homepage."',"; }
		
		$field = $field."`id_referral`,`tgl_daftar`,`ip`";
		$value = $value."$id_referral,'".wp_date('Y-m-d H:i:s')."','$ip'";	

		$wpdb->query("INSERT INTO `wp_member` ($field) VALUES ($value)");
		$id_user = $wpdb->insert_id;

		cb_notif($id_user,'registrasi');

		// Action Hook Registrasi		
		$showtxt = apply_filters('cbaff_registrasi_sukses',$showtxt,$id_user);

		$konfautoresponder = get_option('konfautoresponder');
		// Selesai, sekarang kirim ke autoresponder	
		if (isset($konfautoresponder['free']['action']) && $konfautoresponder['free']['action'] != '') {
			// Jika autoresponder dipasang, maka kirim data ke autoresponder menggunakan postData
			$url = $konfautoresponder['free']['action'];			
			foreach ($konfautoresponder['free'] as $key => $value) {
				if (is_numeric($key) && $value['field'] != '') {
					$post[$value['field']] = $value['value'];
				}
			}

			$pesan = json_encode($post);
			$pesan = formatdata($id_user,$pesan);
			$pesan = json_decode($pesan,true);

			postData($url, $_SERVER['HTTP_USER_AGENT'], $pesan);
		} 

		# Jika ada data order produk, buat ordernya

		if (isset($_POST['orderproduk']) && is_numeric($_POST['orderproduk'])) {
			// Bikin ordernya
			if (is_numeric($_POST['orderproduk']) && $_POST['orderproduk'] > 0) {
				$produk = $wpdb->get_row("SELECT * FROM `cb_produk` WHERE `id`=".$_POST['orderproduk']);
				$hargaproduk = $produk->harga;
				$id_produk = $_POST['orderproduk'];
			} else {
				$hargaproduk = $options['harga'];
				$id_produk = 0;
			}				

			$wpdb->query("INSERT INTO `cb_produklain` (`id_user`,`idproduk`,`status`,`tgl_order`,`hargaproduk`) VALUES (".$id_user.",".$id_produk.",0,'".wp_date('Y-m-d H:i:s')."',".$hargaproduk.")");
			$id_order = $wpdb->insert_id;

			# Jika ini order produk lain, siapkan notif-nya
			if (is_numeric($_POST['orderproduk']) && $_POST['orderproduk'] > 0) {
				if (strlen($id_order) > 3) {
					$angka = substr($id_order,-3);
				} else {
					$angka = $id_order;
				}
				$hargaunik = $produk->harga + $angka;
				$datalain = array(
					'produk_orderid' =>	$id_order,
					'produk_nama' => $produk->nama,
					'produk_diskripsi' => $produk->diskripsi,
					'produk_harga' => $produk->harga,
					'produk_hargaunik' => $hargaunik
				);

				cb_notif($id_user,'beli',$datalain);
				$showtxt .= apply_filters('cbaff_beli_sukses',$showtxt,$id_user);
			} 

			$urlsukses = $blogurl.'/?page_id='.$options['order'].'&idorder='.$id_order;
		} else {
			$urlsukses = $blogurl.'/?page_id='.$options['successpage'];
		}
		
		$showtxt .= '
		<script type="text/javascript">
		<!--
		window.location = "'.$urlsukses.'"
		//-->
		</script>';	
		
	} else {
		$showtxt .= $txterror.'<p>Silahkan Back dan perbaiki isian anda</p>';
	}
	
} else {

	if (isset($_GET['page']) && $_GET['page']=='sukses') {
		$showtxt .= '
			<p>Selamat, anda sudah bergabung sebagai free member di '.get_bloginfo('name').'</p>
			<p>Demi keamanan, kami telah mengirimkan username dan password ke email anda. Apabila tidak ada, silahkan cek spam folder</p>';		
		
	} elseif (isset($_GET['page']) && $_GET['page']=='validasi') {
		$showtxt .= '
			<p>Terima kasih sudah mendaftar, silahkan periksa email anda dan klik link validasi yang kami berikan untuk validasi email anda.</p>';
		
	} else {

		if (isset($txterror)) {
			$showtxt .= '<font color="red"><b>'.$txterror.'</b></font>';
		}
		
		$aturform = get_option('aturform');
		$form = unserialize($aturform);
		if (is_array($form)) {
			$c = 1;
			$showtxt .= '<form action="'.home_url().'/?page_id='.$options['registrasi'].'" method="post"
			onsubmit="document.getElementById(\'cbform-submit\').disabled=true;
			document.getElementById(\'cbform-submit\').value=\'Tunggu sebentar...\';">
			<div class="formprofil">';
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
						case 'nama' : $label = 'Nama Lengkap'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'" required/>'; break;
						case 'id_tianshi' : $label = 'ID MLM'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'email' : $label = 'Email'; $input = '<input type="email" class="cbform-input" name="'.$form['field'].'" required />'; break;
						case 'ktp' : $label = 'No. KTP'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'tgl_lahir' : $label = 'Tanggal Lahir'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'alamat' : $label = 'Alamat'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'kota' : $label = 'Kota'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'provinsi' : $label = 'Provinsi'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'kodepos' : $label = 'Kodepos'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'telp' : $label = 'No. Telp / HP'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'ktp_istri' : $label = 'No. KTP Pasangan'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'nama_istri' : $label = 'Nama Pasangan'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'tgl_lahir_istri' : $label = 'Tgl Lahir Pasangan'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'ac' : $label = 'Atas Nama'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'bank' : $label = 'Nama Bank'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'rekening' : $label = 'No. Rekening'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'kelamin' : $label = 'Jenis Kelamin'; $input = '<select class="cbform-input" name="'.$form['field'].'"'.$required.'><option value="1">Pria</option><option value="0">Wanita</option></select>'; break;
						case 'username' : $label = 'Username'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'password' : $label = 'Password'; $input = '<input type="password" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'subdomain' : $label = 'URL Affiliasi'; 
							if (get_option('affsub') == 1) {
								$input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>.'.cbdomain();
							} else {
								$input = site_url().'/?reg=<input type="text" class="cbform-input" style="max-width:200px" name="'.$form['field'].'"'.$required.'/>'; 
							}
							break;
						case 'customwhatsapp': $label = 'WhatsApp'; $input = '<input type="text" class="cbform-input" name="customwhatsapp"'.$required.'/>'; break;
						case 'ym' : $label = 'Yahoo Messenger'; $input = '<input type="text" class="cbform-input" name="'.$form['field'].'"'.$required.'/>'; break;
						case 'kodesponsor' : $label = 'Kode Sponsor'; 
							$input = '<input type="text" class="cbform-input" name="'.$form['field'].'"';
							if (defined('CB_SPONSOR')) {
								$datasponsor = unserialize(CB_SPONSOR);
								$input .= ' value="'.$datasponsor['subdomain'].'"';
							}
							$input .= $required.'/>'; 
							break;						
						case 'custom' : $label = 'Custom'; $input = '<input type="text" class="cbform-input" name="custom['.$c.']"'.$required.'/>'; break;
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
							$input = '<select class="cbform-input" name="'.$field.'"'.$required.'>';	
							foreach ($optionfield as $optionfield) {
								$input .= '<option value="'.trim($optionfield).'">'.trim($optionfield).'</option>';
							}
							$input .= '</select>';
						}
					} 

					if ($form['field'] == 'keterangan') {
						$showtxt .= "\n".'<div class="cbform-info">'.$label.'</div>';
					} else {
						if ($required != '') {
							$label .= ' *';
						}
						$showtxt .= "\n".'<div class="cbform-row"><div class="cbform-label">'.$label.'</div><div class="cbform-field">'.$input;
						if (isset($form['info']) && $form['info'] != '') {
							$showtxt .= '<br/><small>'.$form['info'].'</small>';
						}
						$showtxt .='</div></div>';	
					}
				}

				if (isset($form['field']) && $form['field'] == 'custom') {
					$c++;
				}
			}
			
			$showtxt = apply_filters('cbaff_registrasi_form',$showtxt);

			if (isset($options['cap_key']) && $options['cap_key'] != '' && isset($options['cap_registrasi']) && $options['cap_registrasi'] != '') {
				$showtxt .= '<div class="cbform-row"><div class="g-recaptcha" data-sitekey="'.$options['cap_key'].'"></div></div>';
			}
			if (isset($orderproduk) && is_numeric($orderproduk)) {
				$showtxt .= '<input type="hidden" name="orderproduk" value="'.$orderproduk.'"/>';
			}
			$showtxt .= '
			<div class="infoform">Tanda * wajib diisi</div>
			<input type="submit" id="cbform-submit" value="Registrasi"/>
			</div>';
			
			$showtxt .= '
			<p style="visibility:hidden;"><small>Powered by : Cafe <a href="https://cafebisnis.com/">Bisnis Online</a></small></p>
			</form>';
			
		} else {
			$showtxt .= 'Form Pengaturan Profil blm diaktifkan. Silahkan hubungi admin web ini';
		}
	}
}
?>